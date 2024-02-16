<?php

namespace Razinal\Satusehatsync\services;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Razinal\Satusehatsync\Models\SatusehatToken;

// Guzzle HTTP Package
// SATUSEHAT Model & Log

class OAuth2Client
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

        if (getenv('SATUSEHAT_ENV') == 'PROD') {
            $this->auth_url = getenv('SATUSEHAT_AUTH_PROD', 'https://api-satusehat.kemkes.go.id/oauth2/v1');
            $this->base_url = getenv('SATUSEHAT_FHIR_PROD', 'https://api-satusehat.kemkes.go.id/fhir-r4/v1');
            $this->client_id = getenv('CLIENTID_PROD');
            $this->client_secret = getenv('CLIENTSECRET_PROD');
            $this->organization_id = getenv('ORGID_PROD');
        } elseif (getenv('SATUSEHAT_ENV') == 'STG') {
            $this->auth_url = getenv('SATUSEHAT_AUTH_STG', 'https://api-satusehat-stg.dto.kemkes.go.id/oauth2/v1');
            $this->base_url = getenv('SATUSEHAT_FHIR_STG', 'https://api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1');
            $this->client_id = getenv('CLIENTID_STG');
            $this->client_secret = getenv('CLIENTSECRET_STG');
            $this->organization_id = getenv('ORGID_STG');
        } elseif (getenv('SATUSEHAT_ENV') == 'DEV') {
            $this->auth_url = getenv('SATUSEHAT_AUTH_DEV', 'https://api-satusehat-dev.dto.kemkes.go.id/oauth2/v1');
            $this->base_url = getenv('SATUSEHAT_FHIR_DEV', 'https://api-satusehat-dev.dto.kemkes.go.id/fhir-r4/v1');
            $this->client_id = getenv('CLIENTID_DEV');
            $this->client_secret = getenv('CLIENTSECRET_DEV');
            $this->organization_id = getenv('ORGID_DEV');
        }

        if ($this->organization_id == null) {
            return 'Add your organization_id at environment first';
        }
    }

    public function token()
    {
        $token = SatusehatToken::where('environment', getenv('SATUSEHAT_ENV'))->orderBy('created_at', 'desc')
            ->where('created_at', '>', now()->subMinutes(50))->first();

        if ($token) {
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
        $url = $this->auth_url.'/accesstoken?grant_type=client_credentials';
        $request = new Request('POST', $url, $headers);

        try {
            $res = $client->sendAsync($request, $options)->wait();
            $contents = json_decode($res->getBody()->getContents());

            if (isset($contents->access_token)) {
                SatusehatToken::create([
                    'environment' => getenv('SATUSEHAT_ENV'),
                    'token' => $contents->access_token,
                    'rsklien_id' => '0',
                ]);

                return $contents->access_token;
            } else {
                return null;
            }
        } catch (ClientException $e) {
            // error.
            $res = json_decode($e->getResponse()->getBody()->getContents());
            $issue_information = $res->issue[0]->details->text;

            return $issue_information;
        }
    }

}
