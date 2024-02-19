<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHospitalRequest;
use App\Http\Resources\HospitalResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Razinal\Satusehatsync\Models\Rsklinik;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Settings/hospital/HospitalIndex', [
            'hospitals' => HospitalResource::collection(Rsklinik::all()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Settings/hospital/HospitalCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateHospitalRequest $request)
    {
        Rsklinik::create($request->validated());
        return to_route('hospitals.index');
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
        $hospital = Rsklinik::where('id', $id)->first();

        return Inertia::render('Settings/hospital/HospitalEdit', [
            'hospital' => new HospitalResource($hospital)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateHospitalRequest $request, string $id):RedirectResponse
    {

        $hospital = Rsklinik::where('id', $id)->first();
        $hospital->update($request->validated());
        return to_route('hospitals.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hospital = Rsklinik::where('id',$id);
        $hospital->delete();
        return back();
    }
}
