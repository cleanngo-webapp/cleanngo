@extends('layouts.employee')

@section('title','My Profile')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->username }}</h1>
    <p class="text-center text-gray-600">Employee ID: {{ sprintf('EMP-%03d', $employee?->id ?? 0) }}</p>

    @if (session('status'))
        <div class="mt-4 p-3 bg-emerald-100 text-emerald-900 rounded">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('employee.profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h2 class="text-xl font-semibold">Personal Info</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium">Position</label>
                    <input type="text" name="position" value="{{ old('position', $employee?->position) }}" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($employee?->date_of_birth)->format('Y-m-d')) }}" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Gender</label>
                    <select name="gender" class="mt-1 w-full border rounded px-3 py-2">
                        <option value="">Select</option>
                        @foreach (['male'=>'Male','female'=>'Female','other'=>'Other'] as $gVal => $gLabel)
                            <option value="{{ $gVal }}" @selected(old('gender', $employee?->gender) === $gVal)>{{ $gLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $employee?->contact_number) }}" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Email Address</label>
                    <input type="email" name="email_address" value="{{ old('email_address', $employee?->email_address ?? $user->email) }}" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Home Address</label>
                    <input type="text" name="home_address" value="{{ old('home_address', $employee?->home_address) }}" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee?->emergency_contact_name) }}" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Emergency Contact Number</label>
                    <input type="text" name="emergency_contact_number" value="{{ old('emergency_contact_number', $employee?->emergency_contact_number) }}" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h2 class="text-xl font-semibold">Employment Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium">Department</label>
                    <input type="text" name="department" value="{{ old('department', $employee?->department) }}" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Employment Type</label>
                    <select name="employment_type" class="mt-1 w-full border rounded px-3 py-2">
                        @foreach (['full-time'=>'Full-time','part-time'=>'Part-time','contract'=>'Contract'] as $k => $v)
                            <option value="{{ $k }}" @selected(old('employment_type', $employee?->employment_type) === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Date Hired</label>
                    <input type="date" name="date_hired" value="{{ old('date_hired', optional($employee?->date_hired)->format('Y-m-d')) }}" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Employment Status</label>
                    <select name="employment_status" class="mt-1 w-full border rounded px-3 py-2">
                        @foreach (['active'=>'Active','inactive'=>'Inactive','terminated'=>'Terminated'] as $k => $v)
                            <option value="{{ $k }}" @selected(old('employment_status', $employee?->employment_status) === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Work Schedule</label>
                    <input type="text" name="work_schedule" value="{{ old('work_schedule', $employee?->work_schedule) }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="8:00 AM – 5:00 PM (Mon–Sat)" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h2 class="text-xl font-semibold">Work History / Records</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium">Jobs Completed</label>
                    <input type="number" min="0" name="jobs_completed" value="{{ old('jobs_completed', $employee?->jobs_completed) }}" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Recent Job</label>
                    <input type="text" name="recent_job" value="{{ old('recent_job', $employee?->recent_job) }}" class="mt-1 w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Attendance</label>
                    <input type="text" name="attendance_summary" value="{{ old('attendance_summary', $employee?->attendance_summary) }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="98% (Last 3 months)" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Performance Rating</label>
                    <input type="text" name="performance_rating" value="{{ old('performance_rating', $employee?->performance_rating) }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="4.5/5" />
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button class="bg-emerald-700 text-white px-4 py-2 rounded cursor-pointer">Save Changes</button>
        </div>
    </form>
</div>
@endsection


