<?php

namespace App\Http\Controllers;

use App\Http\Resources\CronResource;
use App\Http\Resources\HospitalSharedResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Razinal\Satusehatsync\Models\Crontask;
use Razinal\Satusehatsync\Models\Rsklinik;
use Razinal\Satusehatsync\services\EncounterService;
use Razinal\Satusehatsync\services\RsOAuth2Client;

class DetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(String $id): Response
    {
        $client = new RsOAuth2Client;
        $hospital = Rsklinik::with('crontasks')->where('id', $id)->first();

        $token = $client->token(
            $hospital->id,
            $hospital->orgid_prod,
            $hospital->clientid_prod,
            $hospital->clientsecret_prod
        );

        return Inertia::render('Detail/DetailIndex', [
            'hospital' => new HospitalSharedResource($hospital)
        ]);
    }

    public function services(Request $request): Response
    {
        $id = $request->id;
        $service = $request->service;
        $hospital = Rsklinik::with('crontasks')->where('id', $id)->first();

        return Inertia::render('Detail/DetailData', [
            'hospital' => new HospitalSharedResource($hospital),
            'crons' => new CronResource(Crontask::where('rsklien_id', $id)->where('crontitle', $service)->first()),
        ]);
    }

    public function getDataTables(Request $request)
    {
        $encounter = new EncounterService();
        $service = $request->service;

        if ($service == 'Patient') {
            // return $this->patient->syncData($tabId, $rsid);
        } else {
            return $encounter->dataTables($request);
        }
    }


    public function syncDataTable(string $cronid, string $resourceType = null, string $rsid = null)
    {
        $encounter = new EncounterService();

        if ($resourceType == 'Patient') {
            // return $this->patient->syncData($tabId, $cronid);
        } else {
            return $encounter->syncData($cronid, $rsid);
        }
    }


    public function getJsonId($id, $service = null)
    {
        if ($service == 'Patient') {
            $data = \Razinal\Satusehatsync\Models\Patient::find($id);
            $data->makeHidden(['flag', 'created_at', 'updated_at', 'rsklien_id']);
        } else {
            $data = \Razinal\Satusehatsync\Models\Getdatasync::select('data')->where('id', $id)->first();

            $dataArray = $data->data;

            if (isset($dataArray['nomorTransaksiSimrs'])) {
                unset($dataArray['nomorTransaksiSimrs']);
            }

            $data = $dataArray;
        }
        return response()->json($data);
    }
}
