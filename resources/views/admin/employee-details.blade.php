@extends('layouts.admin')

@section('title', 'Employee Details - ' . trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->username)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header with employee info -->
    <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
        <div class="flex items-center justify-between">
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-extrabold">{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->username }}</h1>
                <p class="text-gray-600">Employee ID: {{ $user->employee?->employee_code ?? sprintf('EMP-%03d', $user->employee?->id ?? 0) }}</p>
                <!-- <p class="text-sm text-gray-500">Role: {{ ucfirst($user->role) }}</p> -->
            </div>
            <div class="text-right">
                <a href="{{ route('admin.employees') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    ← Back to Employees
                </a>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 p-3 bg-emerald-100 text-emerald-900 rounded">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.employee.update', $user->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Personal Information - READ ONLY for admin -->
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-4 flex items-center">
                <i class="ri-user-line mr-2"></i>
                Personal Information
                <span class="ml-2 text-sm text-gray-500 font-normal">(View Only - Employee can edit)</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">First Name</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $user->first_name ?? 'Not provided' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Last Name</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $user->last_name ?? 'Not provided' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Username</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $user->username }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $user->email }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Position</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $user->employee?->position ?? 'Not specified' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Date of Birth</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ optional($user->employee?->date_of_birth)->format('M d, Y') ?? 'Not provided' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Gender</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ ucfirst($user->employee?->gender ?? 'Not specified') }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Contact Number</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $user->employee?->contact_number ?? $user->phone ?? 'Not provided' }}</div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Home Address</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $user->employee?->home_address ?? 'Not provided' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Emergency Contact Name</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $user->employee?->emergency_contact_name ?? 'Not provided' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Emergency Contact Number</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $user->employee?->emergency_contact_number ?? 'Not provided' }}</div>
                </div>
            </div>
        </div>

        <!-- Employment Details - ADMIN CAN EDIT -->
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-4 flex items-center">
                <i class="ri-briefcase-line mr-2"></i>
                Employment Details
                <span class="ml-2 text-sm text-emerald-600 font-normal">(Admin Editable)</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Department</label>
                    <input type="text" name="department" value="{{ old('department', $user->employee?->department) }}" 
                           class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                           placeholder="e.g., Operations, Customer Service" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Employment Type</label>
                    <select name="employment_type" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer">
                        <option value="">Select Type</option>
                        @foreach (['full-time'=>'Full-time','part-time'=>'Part-time','contract'=>'Contract'] as $k => $v)
                            <option value="{{ $k }}" @selected(old('employment_type', $user->employee?->employment_type) === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Date Hired</label>
                    <input type="date" name="date_hired" value="{{ old('date_hired', optional($user->employee?->date_hired)->format('Y-m-d')) }}" 
                           class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Employment Status</label>
                    <select name="employment_status" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer">
                        @foreach (['active'=>'Active','inactive'=>'Inactive','terminated'=>'Terminated'] as $k => $v)
                            <option value="{{ $k }}" @selected(old('employment_status', $user->employee?->employment_status) === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Work Schedule</label>
                    <input type="text" name="work_schedule" value="{{ old('work_schedule', $user->employee?->work_schedule) }}" 
                           class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                           placeholder="8:00 AM – 5:00 PM (Mon–Sat)" />
                </div>
            </div>
        </div>

        <!-- Work History / Records - ADMIN CAN EDIT -->
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-4 flex items-center">
                <i class="ri-history-line mr-2"></i>
                Work History / Records
                <span class="ml-2 text-sm text-emerald-600 font-normal">(Admin Editable)</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Jobs Completed</label>
                    <div class="mt-1 p-3 bg-emerald-50 border border-emerald-200 rounded text-emerald-800 font-semibold">
                        {{ $user->employee?->jobs_completed ?? 0 }} jobs
                        <span class="text-sm text-emerald-600 ml-2">(Auto-incremented)</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium">Recent Job</label>
                    <input type="text" name="recent_job" value="{{ old('recent_job', $user->employee?->recent_job) }}" 
                           class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                           placeholder="e.g., Office Cleaning - ABC Building" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Attendance</label>
                    <input type="text" name="attendance_summary" value="{{ old('attendance_summary', $user->employee?->attendance_summary) }}" 
                           class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                           placeholder="98% (Last 3 months)" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Performance Rating</label>
                    <input type="text" name="performance_rating" value="{{ old('performance_rating', $user->employee?->performance_rating) }}" 
                           class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                           placeholder="4.5/5" />
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.employees') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 cursor-pointer">
                Cancel
            </a>
            <button type="submit" class="bg-emerald-700 text-white px-6 py-2 rounded hover:bg-emerald-700/80 hover:text-white focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 cursor-pointer">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
