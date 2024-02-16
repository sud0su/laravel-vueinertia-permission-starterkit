<?php

namespace Razinal\Satusehatsync\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razinal\Satusehatsync\Models\Patient;
use Razinal\Satusehatsync\Models\SatusehatToken;

class PatientService extends OAuth2RsClient
{

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
        $query = Patient::leftJoin($this->klienTableName, "$this->patientTableName.rsklien_id", '=', "$this->klienTableName.id")
            ->select([
                "$this->patientTableName.id",
                "$this->klienTableName.clientname",
                DB::raw("$this->patientTableName.identifier->>'$[0].value' as identifier"),
                DB::raw('CASE
                        WHEN ' . $this->patientTableName . '.flag = 0 THEN "Error"
                        WHEN ' . $this->patientTableName . '.flag = 1 THEN "Waiting"
                        WHEN ' . $this->patientTableName . '.flag = 2 THEN "Success"
                        WHEN ' . $this->patientTableName . '.flag = 3 THEN "Exist"
                        ELSE "Unknown"
                    END AS flag_text'),
                DB::raw('NULL as action'),
                "$this->patientTableName.created_at",
                "$this->patientTableName.updated_at",
            ])
            ->where("$this->patientTableName.rsklien_id", $id);

        // Apply search filter
        $search = $request->input('search.value');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("$this->patientTableName.flag", 'LIKE', "%$search%")
                    ->orWhere(DB::raw("$this->patientTableName.identifier->>'$[0].value'"), 'LIKE', "%$search%");
            });
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
                    $resourceType ?? 'Patient',
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
        $dataPatient = Patient::leftJoin($this->klienTableName, $this->patientTableName . '.rsklien_id', '=', $this->klienTableName . '.id')
            ->where(function ($query) {
                $query->where('flag', 0)
                    ->orWhere('flag', 1);
            })
            ->select([
                $this->patientTableName . '.*',
            ])
            ->get();

        $dataPatient->makeHidden(['id', 'flag', 'created_at', 'updated_at', 'rsklien_id', 'orgid_prod', 'clientid_prod', 'clientsecret_prod']);

        $responses = [];

        foreach ($dataPatient as $patient) {
            $patientId = $patient->id;

            $bodyData = [
                [
                    'resourceType' => 'Patient',
                    'identifier' => $patient->identifier,
                    'active' => $patient->active,
                    'name' => $patient->name,
                    'telecom' => $patient->telecom,
                    'gender' => $patient->gender,
                    'birthDate' => $patient->birthDate,
                    'deceasedBoolean' => $patient->deceasedBoolean,
                    'address' => $patient->address,
                    'maritalStatus' => $patient->maritalStatus,
                    'multipleBirthInteger' => $patient->multipleBirthInteger,
                    'contact' => $patient->contact,
                    'communication' => $patient->communication,
                    'extension' => $patient->extension,
                ]
            ];

            $body = json_encode($bodyData);

            $result = $this->postPatients($patientId, $rsid, $body, $tabId);

            if ($result['success'] == 1) {
                $responses[] = ['status' => 'success', 'message' => $result['response']];
            } elseif ($result['error'] == 2) {
                $responses[] = ['status' => 'error', 'message' => $result['response']];
            }
        }
    }

    public function postPatients($dataid, $rsid, $body, $cron_id)
    {
        $access_token = SatusehatToken::orderBy('created_at', 'desc')
            ->where('rsklien_id', '=', $rsid)
            ->where('created_at', '>', now()->subMinutes(50))->first();

        if (!isset($access_token->token)) {
            return 'Error, not authenticated';
        }

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $access_token->token,
        ];

        $url = $this->base_url . '/Patient';
        $request = new Psr7Request('POST', $url, $headers, $body);

        try {
            $res = $client->sendAsync($request)->wait();

            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if ($response->resourceType == 'OperationOutcome' || $statusCode >= 400) {
                $id = 'Error ' . $statusCode;
                $result = ['error' => 2, 'success' => 0, 'statusCode' => $statusCode, 'response' => $response];
            } else {
                $id = $response->id;

                $patient = Patient::find($dataid);
                if ($patient) {
                    $patient->flag = 2;
                    $patient->save();
                }

                $result = ['success' => 1, 'error' => 0, 'id' => $id, 'statusCode' => $statusCode, 'response' => $response];
            }

            $this->log($id, 'POST', $url, (array)$body, (array)$response, $rsid, $cron_id);

            return $result;
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $res = json_decode($e->getResponse()->getBody()->getContents());

            $dtcheck = Patient::find($dataid);

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

        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();
    }
}
