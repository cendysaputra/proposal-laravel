<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::whereNotNull('published_at')
            ->orderBy('invoice_date', 'desc')
            ->paginate(12);

        return view('invoices.index', compact('invoices'));
    }

    public function show($slug, Request $request)
    {
        $invoice = Invoice::where('slug', $slug)
            ->whereNotNull('published_at')
            ->firstOrFail();

        // Check if invoice is locked
        if ($invoice->is_locked) {
            // Check if already authenticated in session
            if (!$request->session()->has('invoice_authenticated_' . $invoice->id)) {
                return view('invoices.lock', compact('invoice'));
            }
        }

        return view('invoices.show', compact('invoice'));
    }

    public function unlock($slug, Request $request)
    {
        $invoice = Invoice::where('slug', $slug)
            ->whereNotNull('published_at')
            ->firstOrFail();

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Verify credentials
        if ($request->username === $invoice->lock_username &&
            $request->password === $invoice->lock_password) {
            // Store authentication in session
            $request->session()->put('invoice_authenticated_' . $invoice->id, true);
            return redirect()->route('invoices.show', $invoice->slug);
        }

        return back()->withErrors([
            'credentials' => 'Username atau password salah.',
        ])->withInput();
    }

    public function calculateTotal($itemDetails)
    {
        if (!is_array($itemDetails)) {
            return 0;
        }

        $total = 0;
        foreach ($itemDetails as $item) {
            if (isset($item['qty']) && isset($item['price'])) {
                $total += $item['qty'] * $item['price'];
            }
        }

        return $total;
    }
}
