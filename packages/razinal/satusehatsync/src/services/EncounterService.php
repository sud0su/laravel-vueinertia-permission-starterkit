<?php

namespace Razinal\Satusehatsync\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Razinal\Satusehatsync\Models\Getdatasync;
use Razinal\Satusehatsync\Models\SatusehatToken;

class EncounterService extends OAuth2RsClient
{

    public $encounter = ['resourceType' => 'Encounter'];


    public string $klienTableName;
    public string $patientTableName;

    public function __construct()
    {
        parent::__construct();
        $this->klienTableName = config('satusehatsimrs.klien_table_name');
        $this->patientTableName = config('satusehatsimrs.patient_table_name');
    }


    public function dataTables(Request $request)
    {
        $id = $request->input('id');
        $cronid = $request->input('tab');
        $url = $request->input('endpoint');
        $resourceType = $request->input('resourceType');
        $search = $request->input('search.value');

        // Retrieve patient data
        $this->getAndStoreData($url, $id, $resourceType, $cronid);

        // Retrieve query
        $query = Getdatasync::select([
            'id', 'identifier', 'resourceType', 'created_at', 'updated_at',
            DB::raw('CASE
                WHEN flag = 0 THEN "Error"
                WHEN flag = 1 THEN "Waiting"
                WHEN flag = 2 THEN "Success"
                WHEN flag = 3 THEN "Exist"
                ELSE "Unknown"
            END AS flag_text'),
            DB::raw('NULL as action')
        ])
            ->where('cron_id', $cronid);;

        // Apply search filter
        $search = $request->input('search.value');
        if ($search) {
            $query->where("flag", 'LIKE', "%$search%");
        }

        // Count total records
        $totalRecords = $query->count();

        // Apply pagination
        $start = $request->input('start');
        $length = $request->input('length');
        $query->skip($start)->take($length);

        // Retrieve data
        $data = $query->get();

        // Add index numbering
        $data->each(function ($item, $index) use ($start) {
            $item->index = $start + $index + 1;
        });

        // Format flag_text
        $data->transform(function ($item) {
            $item->flag_text = match ($item->flag_text) {
                'Error' => '<span class="badge bg-danger">Error</span>',
                'Waiting' => '<span class="badge bg-warning">Waiting</span>',
                'Success' => '<span class="badge bg-success">Success</span>',
                default => '<span class="badge bg-info">Exist</span>',
            };

            $item->action = '<nobr>
                                <button class="btn btn-xs btn-default text-teal mx-1 shadow btn-view"
                                data-toggle="modal" data-target="#jsonitemPreview" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                                </button>
                                </nobr>';
            return $item;
        });

        $response = [
            'data' => $data->map(function ($item) {
                return [
                    $item->index,
                    $item->id,
                    $item->identifier,
                    $resourceType ?? 'Encounter',
                    $item->flag_text,
                    $item->created_at,
                    $item->updated_at,
                    $item->action,
                ];
            })->toArray(),
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
        ];

        return response()->json($response);
    }

    public function syncData($tabId, $rsid)
    {
        $data = Getdatasync::select(['data', 'id'])
            ->where('cron_id', $tabId)
            ->where(function ($query) {
                $query->where('flag', 0)
                    ->orWhere('flag', 1);
            })
            ->get();

        foreach ($data as $item) {
            $dataArray = $item->data;

            if (isset($dataArray['nomorTransaksiSimrs'])) {
                unset($dataArray['nomorTransaksiSimrs']);
            }

            $item->data = $dataArray;

            $body = json_encode($item->data);

            $result = $this->postData($item->id, $rsid, $body, $tabId);

            if ($result['success'] == 1) {
                $responses[] = ['status' => 'success', 'message' => $result['response']];
            } elseif ($result['error'] == 2) {
                $responses[] = ['status' => 'error', 'message' => $result['response']];
            }
        }
    }


    // public function postData($dataid, $rsid, $body, $cron_id)
    // {
    //     $access_token = SatusehatToken::orderBy('created_at', 'desc')
    //         ->where('rsklien_id', '=', $rsid)
    //         ->where('created_at', '>', now()->subMinutes(50))->first();

    //     if (!isset($access_token->token)) {
    //         return 'Error, not authenticated';
    //     }

    //     $client = new Client();
    //     $headers = [
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $access_token->token,
    //     ];

    //     $url = $this->base_url . '/Encounter';
    //     $request = new Psr7Request('POST', $url, $headers, $body);

    //     try {
    //         $res = $client->sendAsync($request)->wait();

    //         $statusCode = $res->getStatusCode();
    //         $response = json_decode($res->getBody()->getContents());

    //         if ($response->resourceType == 'OperationOutcome' || $statusCode >= 400) {
    //             $id = 'Error ' . $statusCode;
    //             $result = ['error' => 2, 'success' => 0, 'statusCode' => $statusCode, 'response' => $response];
    //         } else {
    //             $id = $response->id;

    //             $patient = Getdatasync::find($dataid);
    //             if ($patient) {
    //                 $patient->flag = 2;
    //                 $patient->save();
    //             }

    //             $result = ['success' => 1, 'error' => 0, 'id' => $id, 'statusCode' => $statusCode, 'response' => $response];
    //         }

    //         $this->log($id, 'POST', $url, (array)$body, (array)$response, $rsid, $cron_id);

    //         return $result;
    //     } catch (ClientException $e) {
    //         $statusCode = $e->getResponse()->getStatusCode();
    //         $res = json_decode($e->getResponse()->getBody()->getContents());

    //         $dtcheck = Getdatasync::find($dataid);

    //         if ($res && isset($res->issue)) {
    //             $issueData = $res->issue;
    //             if ($issueData[0]->code === 'duplicate') {
    //                 if ($dtcheck) {
    //                     $dtcheck->flag = 3;
    //                     $dtcheck->save();
    //                 }
    //             } else {
    //                 if ($dtcheck) {
    //                     $dtcheck->flag = 0;
    //                     $dtcheck->save();
    //                 }
    //             }
    //         }

    //         $this->log('Error ' . $statusCode, 'POST', $url, (array)$body, (array)$res, $rsid, $cron_id);
    //         $result = ['error' => 2, 'success' => 0, 'statusCode' => $statusCode, 'response' => $res->issue[0]->code];

    //         return $result;
    //     }

    //     $res = $client->sendAsync($request)->wait();
    //     echo $res->getBody();
    // }

    public function postData($dataid, $rsid, $body, $cron_id)
    {
        $access_token = SatusehatToken::orderBy('created_at', 'desc')
            ->where('rsklien_id', '=', $rsid)
            ->where('created_at', '>', now()->subMinutes(50))->first();

        if (!$access_token || !isset($access_token->token)) {
            return 'Error, not authenticated';
        }

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $access_token->token,
        ];

        $url = $this->base_url . '/Encounter';
        $request = new Psr7Request('POST', $url, $headers, $body);

        try {
            $res = $client->sendAsync($request, ['timeout' => 10])->wait(); // Set timeout to 10 seconds

            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if ($response->resourceType == 'OperationOutcome' || $statusCode >= 400) {
                $id = 'Error ' . $statusCode;
                $result = ['error' => 2, 'success' => 0, 'statusCode' => $statusCode, 'response' => $response];
            } else {
                $id = $response->id;

                $patient = Getdatasync::find($dataid);
                if ($patient) {
                    $patient->flag = 2;
                    $patient->save();
                }

                $result = ['success' => 1, 'error' => 0, 'id' => $id, 'statusCode' => $statusCode, 'response' => $response];
            }

            $this->log($id, 'POST', $url, (array)$body, (array)$response, $rsid, $cron_id);

            return $result;
        } catch (RequestException $e) { // Use RequestException for Guzzle errors
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $res = json_decode($e->getResponse()->getBody()->getContents());
            } else {
                $statusCode = 500; // Internal server error
                $res = 'Internal server error';
            }

            $dtcheck = Getdatasync::find($dataid);

            if ($res && isset($res->issue)) {
                $issueData = $res->issue;
                if ($issueData[0]->code === 'duplicate') {
                    if ($dtcheck) {
                        $dtcheck->flag = 3;
                        $dtcheck->save();
                    }
                } else {
                    if ($dtcheck) {
                        $dtcheck->flag = 0;
                        $dtcheck->save();
                    }
                }
            }

            $this->log('Error ' . $statusCode, 'POST', $url, (array)$body, (array)$res, $rsid, $cron_id);
            $result = ['error' => 2, 'success' => 0, 'statusCode' => $statusCode, 'response' => $res->issue[0]->code];

            return $result;
        }
    }
}
