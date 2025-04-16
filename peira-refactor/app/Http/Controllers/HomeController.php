<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Example Drupal endpoint
        $response = Http::get('https://peira.space/web/api/news');
        $data = $response->json();

        return view('home', ['data' => $data]);
    }
}
