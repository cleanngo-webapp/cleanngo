<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentSettings;

class CustomerDashboardController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Get customer ID
        $customerId = DB::table('customers')->where('user_id', $user->id)->value('id');
        
        // Fetch customer addresses
        $addresses = DB::table('addresses')
            ->where('user_id', $user->id)
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();

        // Fetch customer bookings with related data
        $bookings = collect();
        if ($customerId) {
            $bookings = DB::table('bookings as b')
                ->leftJoin('services as s', 's.id', '=', 'b.service_id')
                ->leftJoin('addresses as a', 'a.id', '=', 'b.address_id')
                ->leftJoin('booking_staff_assignments as bsa', 'bsa.booking_id', '=', 'b.id')
                ->leftJoin('employees as e', 'e.id', '=', 'bsa.employee_id')
                ->leftJoin('users as eu', 'eu.id', '=', 'e.user_id')
                ->leftJoin('payment_proofs as pp', function($join) {
                    $join->on('pp.booking_id', '=', 'b.id')
                         ->whereRaw('pp.id = (SELECT MAX(id) FROM payment_proofs WHERE booking_id = b.id)');
                })
                ->where('b.customer_id', $customerId)
                ->select([
                    'b.id', 'b.code', 'b.scheduled_start', 'b.scheduled_end', 
                    'b.status', 'b.payment_status', 'b.total_due_cents',
                    's.name as service_name', 's.description as service_description',
                    DB::raw("CONCAT(a.line1, ', ', COALESCE(a.barangay, ''), ', ', COALESCE(a.city, ''), ', ', COALESCE(a.province, '')) as full_address"),
                    DB::raw("CONCAT(eu.first_name, ' ', eu.last_name) as employee_name"),
                    'pp.payment_method', 'pp.status as payment_proof_status',
                    'b.created_at'
                ])
                ->orderByDesc('b.scheduled_start')
                ->get();
        }

        // Build receipt data and service summaries for the receipt modal
        $receiptData = [];
        $serviceSummaries = [];
        $bookingIds = $bookings->pluck('id')->all();
        if (!empty($bookingIds)) {
            $rows = DB::table('booking_items')
                ->whereIn('booking_id', $bookingIds)
                ->orderBy('booking_id')
                ->get(['booking_id','item_type','quantity','area_sqm','unit_price_cents','line_total_cents']);
            $grouped = [];
            foreach ($rows as $r) {
                // Detailed lines with same structure as admin/employee
                $grouped[$r->booking_id][] = [
                    'item_type' => $r->item_type,
                    'quantity' => (int)($r->quantity ?? 0),
                    'area_sqm' => $r->area_sqm !== null ? (float)$r->area_sqm : null,
                    'unit_price' => $r->unit_price_cents !== null ? ((int)$r->unit_price_cents)/100 : null,
                    'line_total' => $r->line_total_cents !== null ? ((int)$r->line_total_cents)/100 : null,
                ];
            }
            foreach ($grouped as $bid => $lines) {
                $total = 0.0;
                $serviceCategories = [];
                foreach ($lines as $ln) { 
                    $total += (float)($ln['line_total'] ?? 0);
                    // Map item types to service categories
                    $itemType = $ln['item_type'];
                    $category = '';
                    
                    if (strpos($itemType, 'sofa') === 0) {
                        $category = 'Sofa Mattress Deep Cleaning';
                    } elseif (strpos($itemType, 'mattress') === 0) {
                        $category = 'Mattress Deep Cleaning';
                    } elseif (strpos($itemType, 'carpet') === 0) {
                        $category = 'Carpet Deep Cleaning';
                    } elseif (strpos($itemType, 'car') === 0) {
                        $category = 'Home Service Car Interior Detailing';
                    } elseif (strpos($itemType, 'post_construction') === 0) {
                        $category = 'Post Construction Cleaning';
                    } elseif (strpos($itemType, 'disinfect') === 0) {
                        $category = 'Home/Office Disinfection';
                    } elseif (strpos($itemType, 'glass') === 0) {
                        $category = 'Glass Cleaning';
                    } elseif (strpos($itemType, 'house') === 0) {
                        $category = 'House Cleaning';
                    } elseif (strpos($itemType, 'curtain') === 0) {
                        $category = 'Curtain Cleaning';
                    } else {
                        $category = ucwords(str_replace('_', ' ', $itemType));
                    }
                    
                    if (!in_array($category, $serviceCategories)) {
                        $serviceCategories[] = $category;
                    }
                }
                $receiptData[$bid] = [ 'lines' => $lines, 'total' => $total ];
                $serviceSummaries[$bid] = implode(', ', $serviceCategories);
            }
        }

        // Get payment settings for displaying QR code
        $paymentSettings = PaymentSettings::getActive();

        return view('customer.profile', [
            'addresses' => $addresses,
            'bookings' => $bookings,
            'receiptData' => $receiptData,
            'serviceSummaries' => $serviceSummaries,
            'paymentSettings' => $paymentSettings,
        ]);
    }

    public function searchBookings()
    {
        $user = Auth::user();
        $search = request('search', '');
        $sort = request('sort', 'date_desc');
        
        // Get customer ID
        $customerId = DB::table('customers')->where('user_id', $user->id)->value('id');
        
        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
                'bookings' => [],
                'serviceSummaries' => []
            ]);
        }

        // Build the base query
        $query = DB::table('bookings as b')
            ->leftJoin('services as s', 's.id', '=', 'b.service_id')
            ->leftJoin('addresses as a', 'a.id', '=', 'b.address_id')
            ->leftJoin('booking_staff_assignments as bsa', 'bsa.booking_id', '=', 'b.id')
            ->leftJoin('employees as e', 'e.id', '=', 'bsa.employee_id')
            ->leftJoin('users as eu', 'eu.id', '=', 'e.user_id')
            ->leftJoin('payment_proofs as pp', function($join) {
                $join->on('pp.booking_id', '=', 'b.id')
                     ->whereRaw('pp.id = (SELECT MAX(id) FROM payment_proofs WHERE booking_id = b.id)');
            })
            ->where('b.customer_id', $customerId);

        // Apply search filter
        if (!empty($search)) {
            $searchTerm = strtolower($search);
            $query->where(function($q) use ($search, $searchTerm) {
                $q->where('b.code', 'like', "%{$search}%")
                  ->orWhere('s.name', 'like', "%{$search}%")
                  ->orWhere('s.description', 'like', "%{$search}%")
                  ->orWhere('s.category', 'like', "%{$search}%")
                  ->orWhere(DB::raw("LOWER(CONCAT(eu.first_name, ' ', eu.last_name))"), 'like', "%{$searchTerm}%")
                  ->orWhere(DB::raw("LOWER(eu.first_name)"), 'like', "%{$searchTerm}%")
                  ->orWhere(DB::raw("LOWER(eu.last_name)"), 'like', "%{$searchTerm}%")
                  ->orWhere(DB::raw("LOWER(a.line1)"), 'like', "%{$searchTerm}%")
                  ->orWhere(DB::raw("LOWER(a.city)"), 'like', "%{$searchTerm}%")
                  ->orWhere(DB::raw("LOWER(a.province)"), 'like', "%{$searchTerm}%")
                  ->orWhere(DB::raw("LOWER(a.barangay)"), 'like', "%{$searchTerm}%")
                  // Search in booking items for service types like car cleaning
                  ->orWhereExists(function($subQuery) use ($search, $searchTerm) {
                      $subQuery->select(DB::raw(1))
                               ->from('booking_items as bi')
                               ->whereRaw('bi.booking_id = b.id')
                               ->where(function($itemQuery) use ($search, $searchTerm) {
                                   $itemQuery->where('bi.item_type', 'like', "%{$search}%")
                                            ->orWhere(DB::raw("LOWER(bi.item_type)"), 'like', "%{$searchTerm}%")
                                            // Search for common service patterns
                                            ->orWhere(function($patternQuery) use ($searchTerm) {
                                                if (strpos($searchTerm, 'car') !== false) {
                                                    $patternQuery->where('bi.item_type', 'like', '%car%');
                                                }
                                                if (strpos($searchTerm, 'sofa') !== false) {
                                                    $patternQuery->orWhere('bi.item_type', 'like', '%sofa%');
                                                }
                                                if (strpos($searchTerm, 'mattress') !== false) {
                                                    $patternQuery->orWhere('bi.item_type', 'like', '%mattress%');
                                                }
                                                if (strpos($searchTerm, 'carpet') !== false) {
                                                    $patternQuery->orWhere('bi.item_type', 'like', '%carpet%');
                                                }
                                                if (strpos($searchTerm, 'glass') !== false) {
                                                    $patternQuery->orWhere('bi.item_type', 'like', '%glass%');
                                                }
                                                if (strpos($searchTerm, 'disinfect') !== false) {
                                                    $patternQuery->orWhere('bi.item_type', 'like', '%disinfect%');
                                                }
                                                if (strpos($searchTerm, 'construction') !== false) {
                                                    $patternQuery->orWhere('bi.item_type', 'like', '%post_construction%');
                                                }
                                            });
                               });
                  });
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'date_asc':
                $query->orderBy('b.scheduled_start', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('b.total_due_cents', 'desc');
                break;
            case 'amount_asc':
                $query->orderBy('b.total_due_cents', 'asc');
                break;
            case 'status':
                $query->orderBy('b.status', 'asc');
                break;
            case 'service':
                $query->orderBy('s.name', 'asc');
                break;
            case 'employee':
                $query->orderBy(DB::raw("CONCAT(eu.first_name, ' ', eu.last_name)"), 'asc');
                break;
            default: // date_desc
                $query->orderBy('b.scheduled_start', 'desc');
                break;
        }

        $bookings = $query->select([
            'b.id', 'b.code', 'b.scheduled_start', 'b.scheduled_end', 
            'b.status', 'b.payment_status', 'b.total_due_cents',
            's.name as service_name', 's.description as service_description',
            DB::raw("CONCAT(a.line1, ', ', COALESCE(a.barangay, ''), ', ', COALESCE(a.city, ''), ', ', COALESCE(a.province, '')) as full_address"),
            DB::raw("CONCAT(eu.first_name, ' ', eu.last_name) as employee_name"),
            'pp.payment_method', 'pp.status as payment_proof_status',
            'b.created_at'
        ])->get();

        // Build service summaries for the search results
        $serviceSummaries = [];
        $bookingIds = $bookings->pluck('id')->all();
        if (!empty($bookingIds)) {
            $rows = DB::table('booking_items')
                ->whereIn('booking_id', $bookingIds)
                ->orderBy('booking_id')
                ->get(['booking_id','item_type']);
            
            $grouped = [];
            foreach ($rows as $r) {
                $grouped[$r->booking_id][] = $r->item_type;
            }
            
            foreach ($grouped as $bid => $itemTypes) {
                $serviceCategories = [];
                foreach ($itemTypes as $itemType) {
                    $category = '';
                    
                    if (strpos($itemType, 'sofa') === 0) {
                        $category = 'Sofa Cleaning';
                    } elseif (strpos($itemType, 'mattress') === 0) {
                        $category = 'Mattress Cleaning';
                    } elseif (strpos($itemType, 'carpet') === 0) {
                        $category = 'Carpet Deep Cleaning';
                    } elseif (strpos($itemType, 'car') === 0) {
                        $category = 'Car Interior Detailing';
                    } elseif (strpos($itemType, 'post_construction') === 0) {
                        $category = 'Post Construction Cleaning';
                    } elseif (strpos($itemType, 'disinfect') === 0) {
                        $category = 'Home/Office Disinfection';
                    } elseif (strpos($itemType, 'glass') === 0) {
                        $category = 'Glass Cleaning';
                    } elseif (strpos($itemType, 'house') === 0) {
                        $category = 'House Cleaning';
                    } elseif (strpos($itemType, 'curtain') === 0) {
                        $category = 'Curtain Cleaning';
                    } else {
                        $category = ucwords(str_replace('_', ' ', $itemType));
                    }
                    
                    if (!in_array($category, $serviceCategories)) {
                        $serviceCategories[] = $category;
                    }
                }
                $serviceSummaries[$bid] = implode(', ', $serviceCategories);
            }
        }

        return response()->json([
            'success' => true,
            'bookings' => $bookings,
            'serviceSummaries' => $serviceSummaries,
            'debug' => [
                'search_term' => $search,
                'total_results' => $bookings->count(),
                'customer_id' => $customerId
            ]
        ]);
    }
}

?>


