<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = Report::query()->with(['reporter', 'property'])->latest()->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function update(Request $request, Report $report, AuditLogger $logger): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,reviewing,resolved,dismissed'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $report->update([
            'status' => ReportStatus::from($data['status']),
            'admin_notes' => $data['admin_notes'] ?? $report->admin_notes,
            'handled_by' => $request->user()->id,
            'handled_at' => now(),
        ]);

        $logger->record($request->user(), 'report.updated', $report, $data);

        return back()->with('status', 'Laporan diperbarui.');
    }
}
