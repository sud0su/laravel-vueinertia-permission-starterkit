<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Razinal\Satusehatsync\services\OAuth2Client;

class WelcomeController extends Controller
{
    public function __invoke(): Response {
        $client = new OAuth2Client;
        $token = $client->token();

        return Inertia::render('Dashboard', [
            'token' => $token
        ]);
    }
}
