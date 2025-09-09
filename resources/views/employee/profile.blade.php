@extends('layouts.employee')

@section('title','My Profile')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->username }}</h1>
    <p class="text-center text-gray-600">Employee ID: {{ $employee?->employee_code ?? sprintf('EMP-%03d', $employee?->id ?? 0) }}</p>

    @if (session('status'))
        <div class="mt-4 p-3 bg-emerald-100 text-emerald-900 rounded">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('employee.profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h2 class="text-xl font-semibold flex items-center">
                <i class="ri-user-line mr-2"></i>
                Personal Information
                <span class="ml-2 text-sm text-emerald-600 font-normal">(Employee Editable)</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium">Position</label>
                    <input type="text" name="position" value="{{ old('position', $employee?->position) }}" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($employee?->date_of_birth)->format('Y-m-d')) }}" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Gender</label>
                    <select name="gender" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select</option>
                        @foreach (['male'=>'Male','female'=>'Female','other'=>'Other'] as $gVal => $gLabel)
                            <option value="{{ $gVal }}" @selected(old('gender', $employee?->gender) === $gVal)>{{ $gLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $employee?->contact_number) }}" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Email Address</label>
                    <input type="email" name="email_address" value="{{ old('email_address', $employee?->email_address ?? $user->email) }}" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Home Address</label>
                    <input type="text" name="home_address" value="{{ old('home_address', $employee?->home_address) }}" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee?->emergency_contact_name) }}" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Emergency Contact Number</label>
                    <input type="text" name="emergency_contact_number" value="{{ old('emergency_contact_number', $employee?->emergency_contact_number) }}" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button class="bg-emerald-700 text-white px-4 py-2 rounded cursor-pointer">Save Changes</button>
        </div>
    </form>

    <!-- Employment Details - Read Only for Employee -->
    <div class="mt-6 space-y-6">
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h2 class="text-xl font-semibold flex items-center">
                <i class="ri-briefcase-line mr-2"></i>
                Employment Details
                <span class="ml-2 text-sm text-gray-500 font-normal">(View Only - Admin can edit)</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Department</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $employee?->department ?? 'Not specified' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Employment Type</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ ucfirst(str_replace('-', ' ', $employee?->employment_type ?? 'Not specified')) }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Date Hired</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ optional($employee?->date_hired)->format('M d, Y') ?? 'Not specified' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Employment Status</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ ucfirst($employee?->employment_status ?? 'Not specified') }}</div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Work Schedule</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $employee?->work_schedule ?? 'Not specified' }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h2 class="text-xl font-semibold flex items-center">
                <i class="ri-history-line mr-2"></i>
                Work History / Records
                <span class="ml-2 text-sm text-gray-500 font-normal">(View Only - Admin can edit)</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Jobs Completed</label>
                    <div class="mt-1 p-3 bg-emerald-50 border border-emerald-200 rounded text-emerald-800 font-semibold">
                        {{ $employee?->jobs_completed ?? 0 }} jobs
                        <span class="text-sm text-emerald-600 ml-2">(Auto-calculated)</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Recent Job</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $employee?->recent_job ?? 'Not specified' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Attendance</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $employee?->attendance_summary ?? 'Not specified' }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Performance Rating</label>
                    <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-700">{{ $employee?->performance_rating ?? 'Not specified' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


