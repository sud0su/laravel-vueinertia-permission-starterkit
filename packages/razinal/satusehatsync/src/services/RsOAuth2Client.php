<?php

namespace Razinal\Satusehatsync\services;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Cache;
use Razinal\Satusehatsync\Models\Getdatasync;
use Razinal\Satusehatsync\Models\Patient;
use Razinal\Satusehatsync\Models\SatusehatLog;
use Razinal\Satusehatsync\Models\SatusehatToken;

class RsOAuth2Client
{

    public string $auth_url;
    public string $base_url;
    public string $client_id;
    public string $client_secret;
    public string $organization_id;

    public function __construct()
    {
        $dotenv = Dotenv::createUnsafeImmutable(getcwd());
        $dotenv->safeLoad();

        $this->auth_url = getenv('SATUSEHAT_AUTH_PROD', 'https://api-satusehat.kemkes.go.id/oauth2/v1');
        $this->base_url = getenv('SATUSEHAT_FHIR_PROD', 'https://api-satusehat.kemkes.go.id/fhir-r4/v1');
        $this->client_id = getenv('CLIENTID_PROD');
        $this->client_secret = getenv('CLIENTSECRET_PROD');
        $this->organization_id = getenv('ORGID_PROD');

        if ($this->organization_id == null) {
            return 'Add your organization_id at environment first';
        }
    }

    public function token($id = null, $organization_id = null, $client_id = null, $client_secret = null)
    {
        $this->organization_id = $organization_id;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;

        $token = SatusehatToken::orderBy('created_at', 'desc')
            ->where('created_at', '>', now()->subMinutes(50))->first();

        if ($token) {
            // dd($token);
            return $token->token;
        }

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
        $options = [
            'form_params' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
            ],
        ];

        // Create session
        $url = $this->auth_url . '/accesstoken?grant_type=client_credentials';
        $request = new Request('POST', $url, $headers);

        try {
            $res = $client->sendAsync($request, $options)->wait();
            $contents = json_decode($res->getBody()->getContents());

            if (isset($contents->access_token)) {
                SatusehatToken::create([
                    'environment' => 'PROD',
                    'token' => $contents->access_token,
                    'rsklien_id'=> $id,
                ]);

                return $contents->access_token;
            } else {
                return null;
            }
        } catch (ClientException $e) {
            $res = json_decode($e->getResponse()->getBody()->getContents());
            $issue_information = $res->issue[0]->details->text;

            return $issue_information;
        }

    }

    public function getAndStoreData($url, $rsid, $resourceType, $cronid)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            die(curl_error($ch));
        }

        curl_close($ch);

        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON format'], 400);
        }

        foreach ($decoded as $item) {
            $identifierValue = $item['identifier'][0]['value'] ?? '';
            $nomorTransaksi = $item['nomorTransaksiSimrs'] ?? '';

            switch ($resourceType) {
                case 'Patient':
                    $model = new Patient([
                        'resourceType' => $item['resourceType'],
                        'identifier' => $item['identifier'],
                        'active' => $item['active'] ?? null,
                        'name' => $item['name'] ?? null,
                        'telecom' => $item['telecom'] ?? null,
                        'gender' => $item['gender'] ?? null,
                        'birthDate' => $item['birthDate'] ?? null,
                        'deceasedBoolean' => $item['deceasedBoolean'] ?? null,
                        'address' => $item['address'] ?? null,
                        'maritalStatus' => $item['maritalStatus'] ?? null,
                        'multipleBirthInteger' => $item['multipleBirthInteger'] ?? null,
                        'contact' => $item['contact'] ?? null,
                        'communication' => $item['communication'] ?? null,
                        'extension' => $item['extension'] ?? null,
                        'rsklien_id' => $rsid,
                        'flag' => 1,
                    ]);
                    break;
                default:
                    $model = new Getdatasync([
                        'resourceType' => $resourceType,
                        'data' => $item,
                        'identifier' => $identifierValue,
                        'cron_id' => $cronid,
                        'flag' => 1,
                    ]);
                    break;
            }

            // Check if data already exists before saving
            if ($resourceType === 'Patient') {
                $existing = Patient::where('identifier', 'like', '%"value":"' . $identifierValue . '"%')->first();
            } else {
                $existing = Getdatasync::whereJsonContains('data->nomorTransaksiSimrs', $nomorTransaksi)->first();
            }

            if (!$existing) {
                $model->save();
            }
        }

        return response()->json(['message' => 'Data saved successfully'], 200);
    }

    public function log($id, $action, $url, $payload, $response, $rsklien_id, $cronid)
    {
        $status = new SatusehatLog();
        $status->response_id = $id;
        $status->action = $action;
        $status->url = $url;
        $status->payload = $payload;
        $status->response = $response;
        $status->rsklien_id = $rsklien_id;
        $status->cron_id = $cronid;
        $status->user_id = auth()->user() ? auth()->user()->id : 'Cron Job';
        $status->save();
    }

}
