<?php

namespace App\Http\Controllers;

use App\Http\Resources\HospitalResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Razinal\Satusehatsync\Models\Rsklinik;
use Razinal\Satusehatsync\services\OAuth2Client;

class WelcomeController extends Controller
{
    public function __invoke(): Response {
        $client = new OAuth2Client;
        $token = $client->token();

        // $test = Rsklinik::with('crontasks')->get();
        // dd($test);

        return Inertia::render('Dashboard', [
            'token' => $token,
            'clients' => HospitalResource::collection(Rsklinik::with('crontasks')->get())
        ]);
    }
}
