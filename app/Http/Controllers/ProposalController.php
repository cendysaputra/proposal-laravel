<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    public function index()
    {
        $proposals = Proposal::whereNotNull('submitted_at')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('proposals.index', compact('proposals'));
    }

    public function show($slug, Request $request)
    {
        $proposal = Proposal::where('slug', $slug)
            ->whereNotNull('submitted_at')
            ->firstOrFail();

        // Check if proposal is locked
        if ($proposal->is_locked) {
            // Check if already authenticated in session
            if (!$request->session()->has('proposal_authenticated_' . $proposal->id)) {
                return view('proposals.lock', compact('proposal'));
            }
        }

        return view('proposals.show', compact('proposal'));
    }

    public function unlock($slug, Request $request)
    {
        $proposal = Proposal::where('slug', $slug)
            ->whereNotNull('submitted_at')
            ->firstOrFail();

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Verify credentials
        if ($request->username === $proposal->lock_username &&
            $request->password === $proposal->lock_password) {
            // Store authentication in session
            $request->session()->put('proposal_authenticated_' . $proposal->id, true);
            return redirect()->route('proposals.show', $proposal->slug);
        }

        return back()->withErrors([
            'credentials' => 'Username atau password salah.',
        ])->withInput();
    }
}
