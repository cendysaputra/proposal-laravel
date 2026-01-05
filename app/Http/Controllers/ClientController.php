<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('created_at', 'desc')->paginate(12);

        return view('clients.index', compact('clients'));
    }

    public function show($slug)
    {
        $client = Client::where('slug', $slug)->firstOrFail();
        return view('clients.show', compact('client'));
    }
}
