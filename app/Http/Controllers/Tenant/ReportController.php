<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Report;
use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function store(Request $request, Property $property): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $report = Report::query()->create([
            'reporter_id' => $request->user()->id,
            'property_id' => $property->id,
            'type' => 'listing',
            'reason' => $data['reason'],
        ]);

        if ($property->owner) {
            $property->owner->notify(new AppNotification(
                title: 'Pemberitahuan Laporan Listing',
                message: 'Terdapat laporan/komplain baru terkait listing "'.$property->name.'". Silakan periksa detailnya.',
                url: route('owner.kost.index'),
                type: 'report',
                icon: 'alert-triangle'
            ));
        }

        $admins = User::query()->where('role', UserRole::Admin)->get();
        foreach ($admins as $admin) {
            $admin->notify(new AppNotification(
                title: 'Laporan Baru Perlu Ditinjau',
                message: 'Laporan baru untuk kost "'.$property->name.'": '.Str::limit($data['reason'], 50),
                url: route('admin.reports.index'),
                type: 'report',
                icon: 'flag'
            ));
        }

        return back()->with('status', 'Laporan terkirim ke admin dan diteruskan ke pemilik kost.');
    }
}
