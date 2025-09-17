@extends('layouts.employee')

@section('title','Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center mb-8">Settings</h1>

    <div class="bg-white rounded-xl p-8 shadow-lg">
        <!-- Password Change Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Change Password</h2>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('employee.settings.password.update') }}" class="space-y-6" id="passwordForm">
                @csrf
                @method('PUT')
                
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Current Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               required
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                        <button type="button" 
                                onclick="togglePasswordVisibility('current_password')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                            <i class="ri-eye-line" id="current_password_icon"></i>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                        New Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               required
                               minlength="8"
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                        <button type="button" 
                                onclick="togglePasswordVisibility('new_password')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                            <i class="ri-eye-line" id="new_password_icon"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Password must be at least 8 characters long</p>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation" 
                               required
                               minlength="8"
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                        <button type="button" 
                                onclick="togglePasswordVisibility('new_password_confirmation')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                            <i class="ri-eye-line" id="new_password_confirmation_icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="button" 
                            onclick="showPasswordConfirmation()"
                            class="bg-emerald-600 text-white px-6 py-3 rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors font-medium cursor-pointer">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Information Section -->
        <div class="border-t pt-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Account Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                        {{ auth()->user()->username }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                        {{ auth()->user()->email }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                        {{ auth()->user()->first_name }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                        {{ auth()->user()->last_name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Update Confirmation Modal -->
<div id="passwordModal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative mx-auto p-5 w-96 shadow-lg rounded-md bg-white mt-20">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100">
                <i class="ri-question-line text-emerald-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Confirm Password Update</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to update your password? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmPasswordUpdate" 
                        class="px-4 py-2 bg-emerald-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-300 cursor-pointer">
                    Yes
                </button>
                <button onclick="hidePasswordConfirmation()" 
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'ri-eye-off-line';
    } else {
        field.type = 'password';
        icon.className = 'ri-eye-line';
    }
}

// Show password confirmation modal
function showPasswordConfirmation() {
    // Validate passwords match before showing modal
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('new_password_confirmation').value;
    
    if (!currentPassword) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Current Password',
            text: 'Please enter your current password.',
            confirmButtonColor: '#10b981'
        });
        return;
    }
    
    if (!newPassword) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing New Password',
            text: 'Please enter your new password.',
            confirmButtonColor: '#10b981'
        });
        return;
    }

    if (currentPassword === newPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Same Password',
            text: 'New password cannot be the same as your current password. Please choose a different password.',
            confirmButtonColor: '#10b981'
        });
        return;
    }
    
    if (newPassword !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'New Password Mismatch',
            text: 'Passwords do not match. Please check and try again.',
            confirmButtonColor: '#10b981'
        });
        return;
    }
    
    if (newPassword.length < 8) {
        Swal.fire({
            icon: 'warning',
            title: 'New Password Too Short',
            text: 'Password must be at least 8 characters long.',
            confirmButtonColor: '#10b981'
        });
        return;
    }
    
    const modal = document.getElementById('passwordModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'items-center', 'justify-center');
    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
}

// Hide password confirmation modal
function hidePasswordConfirmation() {
    const modal = document.getElementById('passwordModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'items-center', 'justify-center');
    // Restore background scrolling
    document.body.style.overflow = 'auto';
}

// Confirm password update
document.getElementById('confirmPasswordUpdate').addEventListener('click', function() {
    document.getElementById('passwordForm').submit();
});
</script>
@endsection
