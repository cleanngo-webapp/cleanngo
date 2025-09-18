@extends('layouts.app')

@section('title','Settings')

@section('content')
<div class="max-w-4xl mx-auto mt-20">
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

            <form method="POST" action="{{ route('customer.settings.password.update') }}" class="space-y-6" id="passwordForm">
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

        <!-- Profile Information Section -->
        <div class="border-t pt-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Profile Information</h2>
            
            <form method="POST" action="{{ route('customer.settings.profile.update') }}" class="space-y-6" id="profileForm">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                            {{ auth()->user()->username }}
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Username cannot be changed</p>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ auth()->user()->email }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    </div>
                    
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ auth()->user()->first_name }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ auth()->user()->last_name }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ auth()->user()->phone }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                               placeholder="Enter your Contact Number">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="button" 
                            id="updateProfileBtn"
                            onclick="showProfileConfirmation()"
                            disabled
                            class="bg-gray-400 text-white px-6 py-3 rounded-lg focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-medium cursor-not-allowed">
                        Update Profile
                    </button>
                </div>
            </form>
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

<!-- Profile Update Confirmation Modal -->
<div id="profileModal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative p-5 w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100">
                <i class="ri-question-line text-emerald-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Confirm Profile Update</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to update your profile information? This will change your account details.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmProfileUpdate" 
                        class="px-4 py-2 bg-emerald-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-300 cursor-pointer">
                    Yes
                </button>
                <button onclick="hideProfileConfirmation()" 
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

// Show profile confirmation modal
function showProfileConfirmation() {
    // Validate required fields
    const email = document.getElementById('email').value;
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    const phoneNumber = document.getElementById('phone').value;
    
    if (!email || !firstName || !lastName || !phoneNumber) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please fill in all required fields.',
            confirmButtonColor: '#10b981'
        });
        return;
    }
    
    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.',
            confirmButtonColor: '#10b981'
        });
        return;
    }
    
    const modal = document.getElementById('profileModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'items-center', 'justify-center');
}

// Hide profile confirmation modal
function hideProfileConfirmation() {
    const modal = document.getElementById('profileModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'items-center', 'justify-center');
}

// Confirm profile update
document.getElementById('confirmProfileUpdate').addEventListener('click', function() {
    document.getElementById('profileForm').submit();
});

// Track original values for comparison
const originalValues = {
    email: document.getElementById('email').value,
    first_name: document.getElementById('first_name').value,
    last_name: document.getElementById('last_name').value,
    phone: document.getElementById('phone').value
};

// Function to check if any field has changed
function checkForChanges() {
    const currentValues = {
        email: document.getElementById('email').value,
        first_name: document.getElementById('first_name').value,
        last_name: document.getElementById('last_name').value,
        phone: document.getElementById('phone').value
    };
    
    const hasChanges = (
        currentValues.email !== originalValues.email ||
        currentValues.first_name !== originalValues.first_name ||
        currentValues.last_name !== originalValues.last_name ||
        currentValues.phone !== originalValues.phone
    );
    
    const updateBtn = document.getElementById('updateProfileBtn');
    
    if (hasChanges) {
        // Enable button
        updateBtn.disabled = false;
        updateBtn.className = 'bg-emerald-600 text-white px-6 py-3 rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors font-medium cursor-pointer';
    } else {
        // Disable button
        updateBtn.disabled = true;
        updateBtn.className = 'bg-gray-400 text-white px-6 py-3 rounded-lg focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-medium cursor-not-allowed';
    }
}

// Add event listeners to all profile input fields
document.getElementById('email').addEventListener('input', checkForChanges);
document.getElementById('first_name').addEventListener('input', checkForChanges);
document.getElementById('last_name').addEventListener('input', checkForChanges);
document.getElementById('phone').addEventListener('input', checkForChanges);
</script>
@endsection
