<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCronRequest;
use App\Http\Resources\CronResource;
use App\Http\Resources\HospitalSharedResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Razinal\Satusehatsync\Models\Crontask;
use Razinal\Satusehatsync\Models\Rsklinik;

class CronTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Settings/cronjob/CronjobIndex', [
            'crons' => CronResource::collection(Crontask::with('rsklinik')->get()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Settings/cronjob/CronjobCreate', [
            'hospitals' => HospitalSharedResource::collection(Rsklinik::select('id','clientname')->get()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCronRequest $request)
    {
        $role = Crontask::create([
            'rsklien_id' => $request->input('rsklien_id.id'),
            'crontitle' => $request->crontitle,
            'endpoint' => $request->endpoint,
            'croncat' => 'daily',
            'day' => '00',
            'hour' => '00',
            'minute' => '00',
        ]);

        return to_route('crons.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cron = Crontask::with('rsklinik')->where('id', $id)->first();

        return Inertia::render('Settings/cronjob/CronjobEdit', [
            'cron' => new CronResource($cron),
            'hospitals' => HospitalSharedResource::collection(Rsklinik::select('id','clientname')->get()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateCronRequest $request, string $id)
    {
        $cron = Crontask::where('id', $id)->first();
        $cron->update([
            'rsklien_id' => $request->input('rsklien_id.id'),
            'crontitle' => $request->crontitle,
            'endpoint' => $request->endpoint,
        ]);
        return to_route('crons.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cron = Crontask::where('id',$id);
        $cron->delete();
        return back();
    }
}
