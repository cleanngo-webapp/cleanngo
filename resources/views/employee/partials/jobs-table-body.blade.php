{{-- Jobs Table Body Partial for AJAX Updates --}}
@forelse($bookings as $b)
<tr class="hover:bg-gray-50 transition-colors" data-booking-id="{{ $b->id }}">
    <td class="px-4 py-4 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-900">{{ $b->code ?? ('B'.date('Y').str_pad($b->id,3,'0',STR_PAD_LEFT)) }}</div>
    </td>
    <td class="px-4 py-4 whitespace-nowrap">
        <div class="text-sm text-gray-900">
            {{ $b->scheduled_start ? \Carbon\Carbon::parse($b->scheduled_start)->format('M j, Y g:i A') : '—' }}
        </div>
    </td>
    <td class="px-4 py-4 whitespace-nowrap">
        <div class="text-sm text-gray-900">{{ $b->customer_name ?? '—' }}</div>
    </td>
    <td class="px-4 py-4 whitespace-nowrap">
        @php
            $statusColors = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'confirmed' => 'bg-blue-100 text-blue-800',
                'in_progress' => 'bg-purple-100 text-purple-800',
                'completed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800',
                'no_show' => 'bg-gray-100 text-gray-800'
            ];
        @endphp
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$b->status] ?? 'bg-gray-100 text-gray-800' }}">
            {{ $b->status === 'in_progress' ? 'In Progress' : ucfirst(str_replace('_', ' ', $b->status)) }}
        </span>
    </td>
    <td class="px-4 py-4">
        <div class="flex items-center gap-1 flex-wrap">
            @if($b->status === 'in_progress')
                @if($b->payment_approved)
                    <button type="button" onclick="confirmCompleteJob({{ $b->id }}, '{{ $b->code }}')" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors cursor-pointer" title="Mark as complete">
                        <i class="ri-check-line mr-1"></i>
                        <span class="hidden sm:inline">Complete</span>
                    </button>
                @else
                    <button class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-400 bg-gray-100 cursor-not-allowed" title="Payment proof required" disabled>
                        <i class="ri-check-line mr-1"></i>
                        <span class="hidden sm:inline">Complete</span>
                    </button>
                @endif
                @if($b->payment_status === 'declined' || !$b->payment_proof_id)
                    <button class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-colors cursor-pointer" onclick="openPaymentModal({{ $b->id }})" title="Attach Payment Proof">
                        <i class="ri-attachment-line mr-1"></i>
                        <span class="hidden sm:inline">Attachments</span>
                    </button>
                @else
                    <button class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-400 bg-gray-100 cursor-not-allowed" title="Payment proof already uploaded - waiting for admin review" disabled>
                        <i class="ri-attachment-line mr-1"></i>
                        <span class="hidden sm:inline">Attachments</span>
                    </button>
                @endif
            @elseif($b->status === 'pending' || $b->status === 'confirmed')
                @php
                    // Fix timezone issue: Check if job is scheduled for today in local timezone
                    // Parse scheduled_start as Manila time and compare with Manila time
                    $scheduledDate = \Carbon\Carbon::parse($b->scheduled_start, 'Asia/Manila');
                    $today = \Carbon\Carbon::now('Asia/Manila');
                    $isScheduledToday = $scheduledDate->isSameDay($today);
                    $canStartJob = $isScheduledToday || $b->status === 'in_progress';
                @endphp
                    @if($canStartJob)
                        @if($b->equipment_borrowed == 1)
                            <!-- Equipment already borrowed - show Items and Start Job buttons -->
                            <button type="button" onclick="getEquipment({{ $b->id }})" class="hidden" title="Get Equipment">
                                <i class="ri-tools-line mr-1"></i>
                                <span class="hidden sm:inline">Get Equipment</span>
                            </button>
                            <button type="button" onclick="openBorrowedItemsModal({{ $b->id }})" class="inline-flex items-center px-2 py-1 border border-purple-300 shadow-sm text-xs font-medium rounded text-purple-600 bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-colors cursor-pointer" title="View Borrowed Items">
                                <i class="ri-list-check mr-1"></i>
                                <span class="hidden sm:inline">Items</span>
                            </button>
                            <button type="button" onclick="confirmStartJob({{ $b->id }})" id="start-job-btn-{{ $b->id }}" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors cursor-pointer" title="Start Job">
                                <i class="ri-play-line mr-1"></i>
                                <span class="hidden sm:inline">Start Job</span>
                            </button>
                        @else
                            <!-- No equipment borrowed yet - show Get Equipment button -->
                            <button type="button" onclick="getEquipment({{ $b->id }})" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-blue-600 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors cursor-pointer" title="Get Equipment">
                                <i class="ri-tools-line mr-1"></i>
                                <span class="hidden sm:inline">Get Equipment</span>
                            </button>
                            <!-- Start Job button - hidden until equipment is borrowed -->
                            <button type="button" onclick="confirmStartJob({{ $b->id }})" id="start-job-btn-{{ $b->id }}" class="hidden" title="Start Job">
                                <i class="ri-play-line mr-1"></i>
                                <span class="hidden sm:inline">Start Job</span>
                            </button>
                        @endif
                    @else
                        <!-- Job not scheduled for today - show disabled buttons -->
                        <button class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-400 bg-gray-100 cursor-not-allowed" title="Job not scheduled for today" disabled>
                            <i class="ri-tools-line mr-1"></i>
                            <span class="hidden sm:inline">Get Equipment</span>
                        </button>
                        <button class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-400 bg-gray-100 cursor-not-allowed" title="Job not scheduled for today" disabled>
                            <i class="ri-play-line mr-1"></i>
                            <span class="hidden sm:inline">Start Job</span>
                        </button>
                    @endif
            @elseif($b->status === 'completed')
                <button type="button" onclick="openJobSummaryModal({{ $b->id }})" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-600 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 transition-colors cursor-pointer" title="View Job Summary">
                    <i class="ri-file-list-line mr-1"></i>
                    <span class="hidden sm:inline">Summary</span>
                </button>
            @endif
            
            <!-- Location button - always visible -->
            <button type="button" onclick="openLocationModal({{ $b->id }})" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-600 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 transition-colors cursor-pointer" title="View Location">
                <i class="ri-map-pin-line mr-1"></i>
                <span class="hidden sm:inline">Location</span>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
        <div class="flex flex-col items-center space-y-2">
            <i class="ri-calendar-line text-4xl text-gray-300"></i>
            <p class="text-lg font-medium">No job assignments found</p>
            <p class="text-sm">You don't have any job assignments yet.</p>
        </div>
    </td>
</tr>
@endforelse
