<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AuditEvent;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'event' => (string) $request->string('event'),
            'actor' => trim((string) $request->string('actor')),
            'from' => (string) $request->string('from'),
            'to' => (string) $request->string('to'),
        ];

        $events = AuditEvent::labels();

        $logs = AuditLog::query()
            ->with('actor')
            ->when($filters['event'] !== '', fn ($query) => $query->where('event', $filters['event']))
            ->when($filters['actor'] !== '', fn ($query) => $query->where('actor_name', 'like', '%'.$filters['actor'].'%'))
            ->when($filters['from'] !== '', fn ($query) => $query->whereDate('created_at', '>=', $filters['from']))
            ->when($filters['to'] !== '', fn ($query) => $query->whereDate('created_at', '<=', $filters['to']))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.audit.index', [
            'logs' => $logs,
            'events' => $events,
            'filters' => $filters,
        ]);
    }
}
