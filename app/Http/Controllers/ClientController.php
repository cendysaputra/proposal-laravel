<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        // Filter by month if specified
        if ($request->has('month') && $request->month !== '') {
            $query->where('month', $request->month);
        }

        // Filter by year if specified
        if ($request->has('year') && $request->year !== '') {
            $query->whereHas('years', function ($q) use ($request) {
                $q->where('year', $request->year);
            });
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get distinct months and years for filters
        $months = Client::distinct()->pluck('month')->filter()->sort()->values();
        $years = \App\Models\Year::orderBy('year', 'desc')->get();

        return view('clients.index', compact('clients', 'months', 'years'));
    }

    public function show($slug)
    {
        $client = Client::where('slug', $slug)->firstOrFail();
        return view('clients.show', compact('client'));
    }
}
