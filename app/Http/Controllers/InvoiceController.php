<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request): View
    {
        $query = Invoice::with(['reservation.vehicle', 'reservation.user', 'returnRecord']);

        // Non-admin can only see their invoices
        if (!Auth::user()->hasAdminPrivileges()) {
            $query->whereHas('reservation', fn($q) => $q->where('user_id', Auth::id()));
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('reservation', function ($rq) use ($search) {
                      $rq->where('reservation_code', 'LIKE', "%{$search}%")
                        ->orWhereHas('user', fn($uq) => $uq->where('name', 'LIKE', "%{$search}%"));
                  });
            });
        }

        $invoices = $query->latest()->paginate(10)->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice): View
    {
        if (!Auth::user()->hasAdminPrivileges() && $invoice->reservation->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak melihat invoice ini.');
        }

        $invoice->load(['reservation.vehicle', 'reservation.user', 'returnRecord']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Generate and download official PDF invoice.
     */
    public function downloadPdf(Invoice $invoice): Response
    {
        if (!Auth::user()->hasAdminPrivileges() && $invoice->reservation->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengunduh invoice ini.');
        }

        $invoice->load(['reservation.vehicle', 'reservation.user', 'returnRecord']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $pdf->setPaper('a4', 'portrait');

        $cleanFilename = str_replace('/', '-', $invoice->invoice_number) . '.pdf';

        return $pdf->download($cleanFilename);
    }

    /**
     * Stream PDF in browser.
     */
    public function streamPdf(Invoice $invoice): Response
    {
        if (!Auth::user()->hasAdminPrivileges() && $invoice->reservation->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak melihat invoice ini.');
        }

        $invoice->load(['reservation.vehicle', 'reservation.user', 'returnRecord']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $pdf->setPaper('a4', 'portrait');

        $cleanFilename = str_replace('/', '-', $invoice->invoice_number) . '.pdf';

        return $pdf->stream($cleanFilename);
    }

    /**
     * Mark an invoice as paid.
     */
    public function markAsPaid(Invoice $invoice): RedirectResponse
    {
        $invoice->update([
            'payment_status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);

        return back()->with('success', "Invoice {$invoice->invoice_number} telah ditandai Lunas.");
    }
}
