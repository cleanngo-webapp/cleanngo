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

        $query = Booking::query()->with(['service', 'customer.user', 'staffAssignments.employee.user']);
        if ($start) {
            $query->where('scheduled_start', '>=', $start);
        }
        if ($end) {
            $query->where('scheduled_start', '<', $end);
        }

        $events = $query->orderBy('scheduled_start', 'asc')->get()->map(function (Booking $b) {
            $startIso = optional($b->scheduled_start)->toIso8601String();
            $startText = optional($b->scheduled_start)->format('m/d g:i A');
            $code = $b->code ?: ('B' . date('Y', strtotime($b->created_at ?? now())) . str_pad((string)$b->id, 3, '0', STR_PAD_LEFT));
            $customerName = $b->customer?->user?->first_name . ' ' . $b->customer?->user?->last_name;
            $customerName = trim($customerName) !== '' ? trim($customerName) : ($b->customer_id ? ('Customer #' . $b->customer_id) : 'Customer');
            $assigned = optional($b->staffAssignments->first()?->employee?->user);
            $employeeName = trim(($assigned->first_name ?? '') . ' ' . ($assigned->last_name ?? '')) ?: null;

            // Define colors based on booking status
            $statusColors = [
                'confirmed' => '#3B82F6', // Blue
                'in_progress' => '#A855F7', // Purple
                'completed' => '#10B981', // Green
                'pending' => '#F59E0B', // Gold
                'no_show' => '#8B5CF6', // Purple
            ];

            return [
                'id' => $b->id,
                'title' => $startText . ' • ' . $code,
                'start' => $startIso,
                'end' => optional($b->scheduled_end ?? $b->scheduled_start)->toIso8601String(),
                'backgroundColor' => $statusColors[$b->status] ?? '#3B82F6', // Default to blue
                'borderColor' => $statusColors[$b->status] ?? '#3B82F6', // Default to blue
                'textColor' => '#FFFFFF', // White text for all events
                'extendedProps' => [
                    'status' => $b->status,
                    'code' => $code,
                    'start_text' => $startText,
                    'customer_name' => $customerName,
                    'employee_name' => $employeeName,
                    'customer_name' => $customerName,
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
            ->with(['service', 'customer.user', 'staffAssignments.employee.user'])
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
            $startIso = optional($b->scheduled_start)->toIso8601String();
            $startText = optional($b->scheduled_start)->format('m/d g:i A');
            $code = $b->code ?: ('B' . date('Y', strtotime($b->created_at ?? now())) . str_pad((string)$b->id, 3, '0', STR_PAD_LEFT));
            $customerName = $b->customer?->user?->first_name . ' ' . $b->customer?->user?->last_name;
            $customerName = trim($customerName) !== '' ? trim($customerName) : ($b->customer_id ? ('Customer #' . $b->customer_id) : 'Customer');
            $assigned = optional($b->staffAssignments->first()?->employee?->user);
            $employeeName = trim(($assigned->first_name ?? '') . ' ' . ($assigned->last_name ?? '')) ?: null;

            // Define colors based on booking status
            $statusColors = [
                'confirmed' => '#3B82F6', // Blue
                'in_progress' => '#A855F7', // Purple
                'completed' => '#10B981', // Green
                'pending' => '#6B7280', // Gray
                'cancelled' => '#EF4444', // Red
                'no_show' => '#8B5CF6', // Purple
            ];

            return [
                'id' => $b->id,
                'title' => $startText . ' • ' . $code,
                'start' => $startIso,
                'end' => optional($b->scheduled_end ?? $b->scheduled_start)->toIso8601String(),
                'backgroundColor' => $statusColors[$b->status] ?? '#3B82F6', // Default to blue
                'borderColor' => $statusColors[$b->status] ?? '#3B82F6', // Default to blue
                'textColor' => '#FFFFFF', // White text for all events
                'extendedProps' => [
                    'status' => $b->status,
                    'code' => $code,
                    'start_text' => $startText,
                    'customer_name' => $customerName,
                    'employee_name' => $employeeName,
                    'customer_name' => $customerName,
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


