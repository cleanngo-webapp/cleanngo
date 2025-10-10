@extends('layouts.admin')

@section('title','Manage Employees')

@section('content')

<div class="max-w-6xl mx-auto px-2 sm:px-0">
    <h1 class="text-2xl sm:text-3xl font-extrabold text-center">Manage Employees</h1>

    {{-- Success Message --}}
    @if (session('status'))
        <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            <div class="flex items-center">
                <i class="ri-check-line mr-2"></i>
                {{ session('status') }}
            </div>
        </div>
    @endif

    {{-- Employee Statistics Cards - Responsive grid layout --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mt-6">
        {{-- Employees Assigned Today Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Employees Assigned Today</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($employeesAssignedToday ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Working on jobs today</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Completed Jobs Today Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed Jobs Today</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($completedJobsToday ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Jobs finished today</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Today's Bookings Card --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Today's Bookings</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($todayBookings ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Scheduled for today</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Sort Section - Responsive layout --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
        <div class="p-4 sm:p-6 border-b border-gray-100">
            {{-- Mobile: Stacked layout, Desktop: Side by side --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <input type="text" 
                           id="search-employees" 
                           value="{{ $search ?? '' }}"
                           placeholder="Search employees by Name, Employee ID, or Phone" 
                           class="w-full px-4 py-2 border border-gray-100 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                {{-- Mobile: Full width buttons, Desktop: Compact buttons --}}
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="button" 
                            class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'employee_id') === 'employee_id' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('employee_id')">
                        <i class="ri-user-settings-line mr-2"></i>
                        <span class="hidden sm:inline">Sort by Employee ID</span>
                        <span class="sm:hidden">Employee ID</span>
                        <i class="ri-arrow-{{ ($sort ?? 'employee_id') === 'employee_id' && ($sortOrder ?? 'asc') === 'desc' ? 'down' : 'up' }}-line ml-2"></i>
                    </button>
                    <button type="button" 
                            class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ ($sort ?? 'employee_id') === 'name' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            onclick="toggleSort('name')">
                        <i class="ri-user-line mr-2"></i>
                        <span class="hidden sm:inline">Sort by Name</span>
                        <span class="sm:hidden">Name</span>
                        <i class="ri-arrow-{{ ($sort ?? 'employee_id') === 'name' && ($sortOrder ?? 'asc') === 'desc' ? 'down' : 'up' }}-line ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Employee Records Section - Responsive header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-4">
        <div class="p-4 sm:p-6 border-b border-gray-100">
            {{-- Mobile: Stacked layout, Desktop: Side by side --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Employee Records</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage employee information and job assignments</p>
                </div>
                <div>
                    <button type="button" 
                            onclick="openAddEmployeeModal()"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer">
                        <i class="ri-add-line mr-2"></i>
                        Add Employee
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Mobile Card View (hidden on larger screens) --}}
        <div class="block sm:hidden">
            @forelse ($employees as $emp)
            <div class="p-4 border-b border-gray-100 last:border-b-0">
                <div class="space-y-3">
                    {{-- Employee Header --}}
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-gray-900">{{ $emp->employee_code ?? ($emp->employee_id ? sprintf('EMP-%03d', $emp->employee_id) : '—') }}</div>
                        @php
                            $status = ($emp->is_active ?? true) ? 'Active' : 'Inactive';
                            $statusColors = [
                                'Active' => 'bg-green-100 text-green-800',
                                'Inactive' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $status }}
                        </span>
                    </div>
                    
                    {{-- Employee Name and Contact --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Full Name</div>
                            <div class="text-sm text-gray-900">{{ trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')) ?: $emp->username }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Contact</div>
                            <div class="text-sm text-gray-900">{{ $emp->contact_number ?? $emp->phone ?? '—' }}</div>
                        </div>
                    </div>
                    
                    {{-- Job Statistics --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Total Bookings</div>
                            <div class="text-sm text-gray-900">{{ number_format($emp->total_bookings ?? 0) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">Jobs Today</div>
                            <div class="text-sm text-gray-900">{{ number_format($emp->jobs_assigned_today ?? 0) }}</div>
                        </div>
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex gap-2">
                        <a href="{{ route('admin.employee.show', $emp->user_id) }}" class="flex-1 inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" aria-label="View Employee Information">
                            <i class="ri-eye-line mr-1"></i>
                            View Details
                        </a>
                        <button onclick="deleteEmployee({{ $emp->user_id }}, this)" class="flex-1 inline-flex items-center justify-center px-3 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors cursor-pointer" aria-label="Delete Employee">
                            <i class="ri-delete-bin-line mr-1"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <!-- Empty State Icon -->
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="ri-user-settings-line text-2xl text-gray-400"></i>
                    </div>
                    
                    <!-- Empty State Content -->
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Employees Found</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            @if(request()->has('search') || request()->has('sort'))
                                No employees match your current filters. Try adjusting your search criteria.
                            @else
                                No employees have been registered yet. Add employees to start managing your workforce.
                            @endif
                        </p>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                            @if(request()->has('search') || request()->has('sort'))
                                <button onclick="clearFilters()" 
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors cursor-pointer">
                                    <i class="ri-refresh-line mr-2"></i>
                                    Clear Filters
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        
        {{-- Desktop Table View (hidden on mobile) --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bookings</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jobs Assigned Today</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="employees-table-body" class="bg-white divide-y divide-gray-200">
                    @forelse ($employees as $emp)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $emp->employee_code ?? ($emp->employee_id ? sprintf('EMP-%03d', $emp->employee_id) : '—') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')) ?: $emp->username }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $emp->contact_number ?? $emp->phone ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                // Use is_active field to determine status since employment_status was removed
                                $status = ($emp->is_active ?? true) ? 'Active' : 'Inactive';
                                $statusColors = [
                                    'Active' => 'bg-green-100 text-green-800',
                                    'Inactive' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($emp->total_bookings ?? 0) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($emp->jobs_assigned_today ?? 0) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.employee.show', $emp->user_id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors cursor-pointer" aria-label="View Employee Information">
                                    <i class="ri-eye-line mr-1"></i>
                                    View Details
                                </a>
                                <button onclick="deleteEmployee({{ $emp->user_id }}, this)" class="inline-flex items-center px-3 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors cursor-pointer" aria-label="Delete Employee">
                                    <i class="ri-delete-bin-line mr-1"></i>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-4">
                                <!-- Empty State Icon -->
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="ri-user-settings-line text-2xl text-gray-400"></i>
                                </div>
                                
                                <!-- Empty State Content -->
                                <div class="text-center">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Employees Found</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if(request()->has('search') || request()->has('sort'))
                                            No employees match your current filters. Try adjusting your search criteria.
                                        @else
                                            No employees have been registered yet. Add employees to start managing your workforce.
                                        @endif
                                    </p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center justify-center space-x-3">
                                        @if(request()->has('search') || request()->has('sort'))
                                            <button onclick="clearFilters()" 
                                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors cursor-pointer">
                                                <i class="ri-refresh-line mr-2"></i>
                                                Clear Filters
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="pagination-container" class="px-4 sm:px-6 py-4 border-t border-gray-100">
            {{ $employees->links() }}
        </div>
    </div>

    {{-- Add Employee Modal --}}
    <div id="addEmployeeModal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Add New Employee</h3>
                    <button type="button" 
                            onclick="closeAddEmployeeModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="mt-6">
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="addEmployeeForm" method="POST" action="{{ route('admin.employees.store') }}" class="space-y-4">
                        @csrf
                        
                        {{-- Name Fields --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" 
                                       name="first_name" 
                                       value="{{ old('first_name') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                       placeholder="Enter first name" 
                                       required />
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" 
                                       name="last_name" 
                                       value="{{ old('last_name') }}" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                       placeholder="Enter last name" 
                                       required />
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Username --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                   placeholder="Choose a username" 
                                   required />
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                   placeholder="Enter email address" 
                                   required />
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contact Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input type="text" 
                                   name="contact" 
                                   value="{{ old('contact') }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                   placeholder="Enter contact number" />
                            @error('contact')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Fields --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <div class="relative">
                                    <input id="add_employee_password" 
                                           type="password" 
                                           name="password" 
                                           class="w-full border border-gray-300 rounded-lg px-3 pr-10 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                           placeholder="Enter password" 
                                           required />
                                    <button type="button" 
                                            class="absolute inset-y-0 right-2 my-auto text-gray-500 hover:text-gray-700" 
                                            aria-label="Toggle password visibility" 
                                            data-toggle-password 
                                            data-target="#add_employee_password">
                                        <i class="ri-eye-line text-xl cursor-pointer"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <div class="relative">
                                    <input id="add_employee_password_confirmation" 
                                           type="password" 
                                           name="password_confirmation" 
                                           class="w-full border border-gray-300 rounded-lg px-3 pr-10 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                           placeholder="Confirm password" 
                                           required />
                                    <button type="button" 
                                            class="absolute inset-y-0 right-2 my-auto text-gray-500 hover:text-gray-700" 
                                            aria-label="Toggle password visibility" 
                                            data-toggle-password 
                                            data-target="#add_employee_password_confirmation">
                                        <i class="ri-eye-line text-xl cursor-pointer"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" 
                            onclick="closeAddEmployeeModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" 
                            onclick="submitAddEmployeeForm()"
                            class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors cursor-pointer">
                        Add Employee
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Loading dots animation for AJAX operations */
.loading-dots {
    animation: loading-dots 1.4s infinite ease-in-out both;
}

.loading-dots:nth-child(1) {
    animation-delay: -0.32s;
}

.loading-dots:nth-child(2) {
    animation-delay: -0.16s;
}

@keyframes loading-dots {
    0%, 80%, 100% {
        transform: scale(0);
    }
    40% {
        transform: scale(1);
    }
}
</style>
@endpush

@push('scripts')
<script>
// Global variables for search and sort
let currentSort = '{{ $sort ?? "employee_id" }}';
let currentSortOrder = '{{ $sortOrder ?? "asc" }}';
let searchTimeout;

// Search and sort functionality
function toggleSort(sortType) {
    if (currentSort === sortType) {
        // Toggle sort order if same sort type
        currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
    } else {
        // Set new sort type with default ascending order
        currentSort = sortType;
        currentSortOrder = 'asc';
    }
    
    // Update button styles and icons
    updateSortButtons();
    
    // Perform search/sort
    performSearch();
}

function updateSortButtons() {
    const buttons = document.querySelectorAll('[onclick^="toggleSort"]');
    buttons.forEach(btn => {
        btn.classList.remove('bg-emerald-600', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
        
        // Update arrow icons
        const icon = btn.querySelector('i:last-child');
        if (btn.onclick.toString().includes(currentSort)) {
            btn.classList.remove('bg-gray-100', 'text-gray-700');
            btn.classList.add('bg-emerald-600', 'text-white');
            icon.className = `ri-arrow-${currentSortOrder === 'desc' ? 'down' : 'up'}-line ml-2`;
        } else {
            icon.className = 'ri-arrow-up-line ml-2';
        }
    });
}

// Auto-search on input (with debounce)
document.getElementById('search-employees').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 300); // 300ms delay for faster response
});

// AJAX search function
function performSearch() {
    const searchTerm = document.getElementById('search-employees').value;
    const url = new URL('{{ route("admin.employees") }}', window.location.origin);
    
    if (searchTerm) {
        url.searchParams.set('search', searchTerm);
    }
    url.searchParams.set('sort', currentSort);
    url.searchParams.set('sort_order', currentSortOrder);
    
    // Show loading state
    const tableBody = document.getElementById('employees-table-body');
    const paginationContainer = document.getElementById('pagination-container');
    tableBody.innerHTML = `
        <tr>
            <td colspan="7" class="px-6 py-8 text-center">
                <div class="flex justify-center items-center space-x-2 mb-4">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                </div>
                <p class="text-gray-500 text-sm">Searching...</p>
            </td>
        </tr>
    `;
    paginationContainer.innerHTML = '';
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Parse the response HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract table body content
            const newTableBody = doc.getElementById('employees-table-body');
            const newPagination = doc.getElementById('pagination-container');
            
            if (newTableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
            }
            if (newPagination) {
                paginationContainer.innerHTML = newPagination.innerHTML;
            }
            
            // Update URL without page refresh
            window.history.pushState({}, '', url);
        })
        .catch(error => {
            console.error('Search error:', error);
            tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-red-500">Error loading results</td></tr>';
        });
}

// Refresh employees table after adding new employee
function refreshEmployeesTable() {
    const url = new URL('{{ route("admin.employees") }}', window.location.origin);
    
    // Preserve current search and sort parameters
    const searchTerm = document.getElementById('search-employees').value;
    if (searchTerm) {
        url.searchParams.set('search', searchTerm);
    }
    url.searchParams.set('sort', currentSort);
    url.searchParams.set('sort_order', currentSortOrder);
    
    // Show loading state
    const tableBody = document.getElementById('employees-table-body');
    const paginationContainer = document.getElementById('pagination-container');
    tableBody.innerHTML = `
        <tr>
            <td colspan="7" class="px-6 py-8 text-center">
                <div class="flex justify-center items-center space-x-2 mb-4">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full loading-dots"></div>
                </div>
                <p class="text-gray-500 text-sm">Refreshing...</p>
            </td>
        </tr>
    `;
    paginationContainer.innerHTML = '';
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Parse the response HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract table body content
            const newTableBody = doc.getElementById('employees-table-body');
            const newPagination = doc.getElementById('pagination-container');
            
            if (newTableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
            }
            if (newPagination) {
                paginationContainer.innerHTML = newPagination.innerHTML;
            }
            
            // Update URL without page refresh
            window.history.pushState({}, '', url);
        })
        .catch(error => {
            console.error('Refresh error:', error);
            tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-red-500">Error refreshing table</td></tr>';
        });
}

// Clear all filters function
function clearFilters() {
    // Clear search input
    const searchInput = document.getElementById('search-employees');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Reset sort
    currentSort = 'employee_id';
    currentSortOrder = 'asc';
    updateSortButtons();
    
    // Perform search to refresh results
    performSearch();
}

// Add Employee Modal Functions
function openAddEmployeeModal() {
    const modal = document.getElementById('addEmployeeModal');
    if (modal) {
        modal.classList.remove('hidden');
        
        // Attach password toggle event listeners when modal opens
        const toggleButtons = modal.querySelectorAll('[data-toggle-password]');
        toggleButtons.forEach(button => {
            // Remove any existing listeners to prevent duplicates
            button.removeEventListener('click', handlePasswordToggle);
            // Add new listener
            button.addEventListener('click', handlePasswordToggle);
        });
        
        // Focus on first input
        const firstInput = modal.querySelector('input[name="first_name"]');
        if (firstInput) {
            firstInput.focus();
        }
    }
}

// Handle password toggle click
function handlePasswordToggle(e) {
    e.preventDefault();
    e.stopPropagation();
    const button = e.currentTarget;
    togglePasswordVisibility(button);
}

function closeAddEmployeeModal() {
    const modal = document.getElementById('addEmployeeModal');
    if (modal) {
        modal.classList.add('hidden');
        // Clear form data
        const form = document.getElementById('addEmployeeForm');
        if (form) {
            form.reset();
        }
    }
}

function submitAddEmployeeForm() {
    const form = document.getElementById('addEmployeeForm');
    if (!form) return;
    
    // Get form data for validation
    const formData = new FormData(form);
    const password = formData.get('password');
    const passwordConfirmation = formData.get('password_confirmation');
    const firstName = formData.get('first_name');
    const lastName = formData.get('last_name');
    const email = formData.get('email');
    const contact = formData.get('contact');
    const username = formData.get('username');
    
    // Validate password match first
    if (password !== passwordConfirmation) {
        Swal.fire({
            title: 'Password Mismatch',
            text: 'Password and Confirm Password do not match. Please check and try again.',
            icon: 'error',
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show confirmation dialog
    Swal.fire({
        title: 'Confirm Employee Creation',
        html: `
            <div class="text-left">
                <p class="mb-2"><strong>Please confirm the employee details:</strong></p>
                <p class="mb-1"><strong>Name:</strong> ${firstName} ${lastName}</p>
                <p class="mb-1"><strong>Username:</strong> ${username}</p>
                <p class="mb-1"><strong>Email:</strong> ${email}</p>
                <p class="mb-1"><strong>Contact:</strong> ${contact || 'Not provided'}</p>
                <p class="mt-3 text-sm text-gray-600">Are you sure these details are correct?</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#047857',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'Yes, Add Employee',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the form via AJAX
            submitFormViaAjax(form);
        }
    });
}

// Submit form via AJAX and handle response
function submitFormViaAjax(form) {
    const formData = new FormData(form);
    const submitButton = document.querySelector('button[onclick="submitAddEmployeeForm()"]');
    
    // Disable submit button and show loading state
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Adding Employee...';
    }
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success alert that auto-disappears
            showSuccessAlert(data.message);
            
            // Close modal and reset form
            closeAddEmployeeModal();
            
            // Refresh the table data via AJAX instead of page reload
            setTimeout(() => {
                refreshEmployeesTable();
            }, 1000);
        } else {
            // Handle validation errors
            showErrorAlert(data.message || 'An error occurred while creating the employee.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorAlert('An error occurred while creating the employee. Please try again.');
    })
    .finally(() => {
        // Re-enable submit button
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Add Employee';
        }
    });
}

// Show success alert that auto-disappears
function showSuccessAlert(message) {
    const alert = document.createElement('div');
    alert.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 transform transition-all duration-300 ease-in-out';
    alert.style.transform = 'translateX(100%)';
    
    alert.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="ri-check-line text-xl"></i>
            <span class="font-medium">${message}</span>
        </div>
    `;
    
    document.body.appendChild(alert);
    
    // Animate in
    setTimeout(() => {
        alert.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        alert.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 300);
    }, 3000);
}

// Show error alert
function showErrorAlert(message) {
    Swal.fire({
        title: 'Error',
        text: message,
        icon: 'error',
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'OK'
    });
}

// Password visibility toggle functionality
function togglePasswordVisibility(button) {
    const targetId = button.getAttribute('data-target');
    const targetInput = document.querySelector(targetId);
    const icon = button.querySelector('i');
    
    if (targetInput && icon) {
        if (targetInput.type === 'password') {
            targetInput.type = 'text';
            icon.className = 'ri-eye-off-line text-xl cursor-pointer';
        } else {
            targetInput.type = 'password';
            icon.className = 'ri-eye-line text-xl cursor-pointer';
        }
    }
}

// Delete employee function
function deleteEmployee(userId, buttonElement) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this! This will permanently delete the employee and all their data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete employee!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state on the delete button
            const deleteButton = buttonElement;
            const originalButtonContent = deleteButton.innerHTML;
            deleteButton.disabled = true;
            deleteButton.innerHTML = '<div class="w-4 h-4 border-2 border-red-300 border-t-transparent rounded-full animate-spin mr-1 inline-block"></div>Deleting...';
            
            fetch(`/admin/employees/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success alert that auto-disappears
                    showSuccessAlert(data.message);
                    
                    // Remove the table row
                    const tableRow = deleteButton.closest('tr');
                    if (tableRow) {
                        tableRow.remove();
                    }
                    
                    // Refresh the page after a short delay to show updated data
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    // Handle errors
                    showErrorAlert(data.message || 'An error occurred while deleting the employee.');
                    
                    // Reset button on error
                    deleteButton.disabled = false;
                    deleteButton.innerHTML = originalButtonContent;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorAlert('An error occurred while deleting the employee. Please try again.');
                
                // Reset button on error
                deleteButton.disabled = false;
                deleteButton.innerHTML = originalButtonContent;
            });
        }
    });
}

// Initialize modal functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when clicking outside
    document.getElementById('addEmployeeModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddEmployeeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('addEmployeeModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeAddEmployeeModal();
            }
        }
    });
    
    // Password toggle event listeners are now attached when modal opens
});
</script>
@endpush

{{-- Mobile-specific styles for employees page responsiveness - Moved from top --}}
<style>
	/* Mobile responsive styles for employees page */
	@media (max-width: 640px) {
		/* Ensure modals are mobile-friendly */
		#addEmployeeModal .relative {
			width: 95vw !important;
			max-width: 95vw !important;
			margin: 0.5rem !important;
		}
		
		/* Make modal content stack vertically on mobile */
		#addEmployeeModal .grid-cols-1 {
			grid-template-columns: 1fr !important;
		}
		
		/* Ensure table doesn't cause horizontal overflow */
		.overflow-x-auto {
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
		}
		
		/* Make action buttons more touch-friendly */
		.flex .inline-flex {
			min-height: 2.5rem;
		}
	}
</style>
@endsection


