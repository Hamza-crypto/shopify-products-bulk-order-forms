<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AsanaController extends Controller
{
    public function call($endpoint, $method = 'GET', $body = [])
    {
        $url = sprintf(
            '%s/%s',
            env('ASANA_BASE_URL'),
            $endpoint
        );

        $response = Http::withToken(env('ASANA_TOKEN'))->post($url, $body);

        return $response->json();
    }
}
