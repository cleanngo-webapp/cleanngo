<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Admin: return all bookings as calendar events.
     */
    public function adminEvents(Request $request)
    {
        [$start, $end] = $this->parseRange($request);

        $query = Booking::query()->with(['service', 'customer']);
        if ($start) {
            $query->where('scheduled_start', '>=', $start);
        }
        if ($end) {
            $query->where('scheduled_start', '<', $end);
        }

        $events = $query->orderBy('scheduled_start', 'asc')->get()->map(function (Booking $b) {
            $titleParts = [];
            if ($b->service) { $titleParts[] = $b->service->name; }
            if ($b->customer) { $titleParts[] = $b->customer->full_name ?? ('Customer #' . $b->customer_id); }
            $title = implode(' – ', array_filter($titleParts));

            return [
                'id' => $b->id,
                'title' => $title !== '' ? $title : 'Booking #' . $b->id,
                'start' => optional($b->scheduled_start)->toIso8601String(),
                'end' => optional($b->scheduled_end ?? $b->scheduled_start)->toIso8601String(),
                'extendedProps' => [
                    'status' => $b->status,
                    'code' => $b->code,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Employee: return only bookings assigned to the authenticated employee.
     */
    public function employeeEvents(Request $request)
    {
        $user = Auth::user();
        $employeeId = $user?->employee?->id;
        if (!$employeeId) {
            return response()->json([]);
        }

        [$start, $end] = $this->parseRange($request);

        $query = Booking::query()
            ->with(['service', 'customer'])
            ->whereHas('staffAssignments', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            });

        if ($start) {
            $query->where('scheduled_start', '>=', $start);
        }
        if ($end) {
            $query->where('scheduled_start', '<', $end);
        }

        $events = $query->orderBy('scheduled_start', 'asc')->get()->map(function (Booking $b) {
            $titleParts = [];
            if ($b->service) { $titleParts[] = $b->service->name; }
            if ($b->customer) { $titleParts[] = $b->customer->full_name ?? ('Customer #' . $b->customer_id); }
            $title = implode(' – ', array_filter($titleParts));

            return [
                'id' => $b->id,
                'title' => $title !== '' ? $title : 'Booking #' . $b->id,
                'start' => optional($b->scheduled_start)->toIso8601String(),
                'end' => optional($b->scheduled_end ?? $b->scheduled_start)->toIso8601String(),
                'extendedProps' => [
                    'status' => $b->status,
                    'code' => $b->code,
                ],
            ];
        });

        return response()->json($events);
    }

    private function parseRange(Request $request): array
    {
        $start = $request->query('start');
        $end = $request->query('end');
        return [
            $start ? date('Y-m-d H:i:s', strtotime($start)) : null,
            $end ? date('Y-m-d H:i:s', strtotime($end)) : null,
        ];
    }
}


