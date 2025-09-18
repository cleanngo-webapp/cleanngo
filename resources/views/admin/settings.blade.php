@extends('layouts.admin')

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

            <form method="POST" action="{{ route('admin.settings.password.update') }}" class="space-y-6" id="passwordForm">
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
            
            <form method="POST" action="{{ route('admin.settings.profile.update') }}" class="space-y-6" id="profileForm">
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

        <!-- Payment Settings Section -->
        <div class="border-t pt-8 mt-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Payment Settings</h2>
            
            @if($paymentSettings)
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-green-100 rounded-full p-2">
                        <i class="ri-qr-code-line text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-green-800">Current Payment Settings</h3>
                        <p class="text-sm text-green-600">Your GCash payment information is configured</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-green-700">GCash Name:</span>
                        <span class="text-green-600">{{ $paymentSettings->gcash_name }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-green-700">GCash Number:</span>
                        <span class="text-green-600">{{ $paymentSettings->gcash_number }}</span>
                    </div>
                </div>
                @if($paymentSettings->qr_code_path)
                <div class="mt-3">
                    <span class="font-medium text-green-700">QR Code:</span>
                    <div class="mt-2">
                        <img src="{{ Storage::url($paymentSettings->qr_code_path) }}" 
                             alt="GCash QR Code" 
                             class="w-32 h-32 object-contain border border-green-200 rounded-lg">
                    </div>
                </div>
                @endif
            </div>
            @endif
            
            <form method="POST" action="{{ route('admin.settings.payment.update') }}" class="space-y-6" id="paymentForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="gcash_name" class="block text-sm font-medium text-gray-700 mb-2">
                            GCash Account Name
                        </label>
                        <input type="text" 
                               id="gcash_name" 
                               name="gcash_name" 
                               value="{{ old('gcash_name', $paymentSettings->gcash_name ?? '') }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                               placeholder="Enter GCash account name">
                    </div>
                    
                    <div>
                        <label for="gcash_number" class="block text-sm font-medium text-gray-700 mb-2">
                            GCash Phone Number
                        </label>
                        <input type="text" 
                               id="gcash_number" 
                               name="gcash_number" 
                               value="{{ old('gcash_number', $paymentSettings->gcash_number ?? '') }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                               placeholder="Enter GCash phone number">
                    </div>
                </div>

                <div>
                    <label for="qr_code" class="block text-sm font-medium text-gray-700 mb-2">
                        GCash QR Code
                    </label>
                    <div id="qr-upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-emerald-400 transition-colors cursor-pointer" 
                         onclick="document.getElementById('qr_code').click()"
                         ondrop="handleDrop(event)" 
                         ondragover="handleDragOver(event)" 
                         ondragenter="handleDragEnter(event)" 
                         ondragleave="handleDragLeave(event)">
                        <div class="space-y-1 text-center">
                            <i class="ri-upload-cloud-2-line text-4xl text-gray-400"></i>
                            <div class="flex text-sm text-gray-600">
                                <label for="qr_code" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500">
                                    <span>Upload QR Code</span>
                                    <input id="qr_code" name="qr_code" type="file" class="sr-only" accept="image/*" onchange="previewQRCode(this)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">JPEG, PNG, JPG, GIF, WebP up to 10MB</p>
                        </div>
                    </div>
                    <div id="qr-preview" class="mt-3 hidden">
                        <div class="relative inline-block">
                            <img id="qr-preview-img" src="" alt="QR Code Preview" class="w-32 h-32 object-contain border border-gray-200 rounded-lg mx-auto">
                            <button type="button" onclick="removeQRPreview()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors cursor-pointer">
                                <i class="ri-close-line text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="button" 
                            id="updatePaymentBtn"
                            onclick="showPaymentConfirmation()"
                            disabled
                            class="bg-gray-400 text-white px-6 py-3 rounded-lg focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-medium cursor-not-allowed">
                        <i class="ri-save-line mr-2"></i>
                        Update Payment Settings
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

<!-- Payment Settings Update Confirmation Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative p-5 w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100">
                <i class="ri-qr-code-line text-emerald-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Confirm Payment Settings Update</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to update your payment settings? This will change your GCash information for customer payments.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmPaymentUpdate" 
                        class="px-4 py-2 bg-emerald-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-300 cursor-pointer">
                    Yes
                </button>
                <button onclick="hidePaymentConfirmation()" 
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
    
    if (!email || !firstName || !lastName) {
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
    gcash_name: document.getElementById('gcash_name').value,
    gcash_number: document.getElementById('gcash_number').value
};

// Function to check if any field has changed
function checkForChanges() {
    const currentValues = {
        email: document.getElementById('email').value,
        first_name: document.getElementById('first_name').value,
        last_name: document.getElementById('last_name').value
    };
    
    const hasChanges = (
        currentValues.email !== originalValues.email ||
        currentValues.first_name !== originalValues.first_name ||
        currentValues.last_name !== originalValues.last_name
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

// Function to check if payment settings have changed
function checkPaymentChanges() {
    const currentValues = {
        gcash_name: document.getElementById('gcash_name').value,
        gcash_number: document.getElementById('gcash_number').value
    };
    
    // Check if QR code file has been selected
    const qrCodeInput = document.getElementById('qr_code');
    const hasQRCodeChange = qrCodeInput.files && qrCodeInput.files.length > 0;
    
    const hasChanges = (
        currentValues.gcash_name !== originalValues.gcash_name ||
        currentValues.gcash_number !== originalValues.gcash_number ||
        hasQRCodeChange
    );
    
    const updateBtn = document.getElementById('updatePaymentBtn');
    
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

// Add event listeners to payment settings input fields
document.getElementById('gcash_name').addEventListener('input', checkPaymentChanges);
document.getElementById('gcash_number').addEventListener('input', checkPaymentChanges);
document.getElementById('qr_code').addEventListener('change', checkPaymentChanges);

// Payment Settings Functions
function previewQRCode(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('qr-preview-img').src = e.target.result;
            document.getElementById('qr-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
    // Check for changes when QR code is selected
    checkPaymentChanges();
}

// Remove QR preview
function removeQRPreview() {
    document.getElementById('qr-preview').classList.add('hidden');
    document.getElementById('qr_code').value = '';
    checkPaymentChanges();
}

// Drag and Drop Functions
function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleDragEnter(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('qr-upload-area').classList.add('border-emerald-500', 'bg-emerald-50');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('qr-upload-area').classList.remove('border-emerald-500', 'bg-emerald-50');
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('qr-upload-area').classList.remove('border-emerald-500', 'bg-emerald-50');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        if (file.type.startsWith('image/')) {
            // Create a new FileList with the dropped file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById('qr_code').files = dataTransfer.files;
            previewQRCode(document.getElementById('qr_code'));
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please drop an image file (JPEG, PNG, JPG, GIF, WebP).',
                confirmButtonColor: '#10b981'
            });
        }
    }
}

// Paste functionality
document.addEventListener('paste', function(e) {
    // Check if the paste event is happening in the payment settings area
    const activeElement = document.activeElement;
    const isInPaymentArea = activeElement && (
        activeElement.id === 'gcash_name' || 
        activeElement.id === 'gcash_number' || 
        activeElement.id === 'qr-upload-area' ||
        activeElement.closest('#qr-upload-area')
    );
    
    if (isInPaymentArea && e.clipboardData && e.clipboardData.items) {
        const items = e.clipboardData.items;
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            if (item.type.indexOf('image') !== -1) {
                e.preventDefault();
                const file = item.getAsFile();
                if (file) {
                    // Create a new FileList with the pasted file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    document.getElementById('qr_code').files = dataTransfer.files;
                    previewQRCode(document.getElementById('qr_code'));
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Image Pasted',
                        text: 'QR code image has been pasted successfully.',
                        confirmButtonColor: '#10b981',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                break;
            }
        }
    }
});

function showPaymentConfirmation() {
    // Validate required fields
    const gcashName = document.getElementById('gcash_name').value;
    const gcashNumber = document.getElementById('gcash_number').value;
    
    if (!gcashName || !gcashNumber) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please fill in all required fields.',
            confirmButtonColor: '#10b981'
        });
        return;
    }
    
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'items-center', 'justify-center');
    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
}

function hidePaymentConfirmation() {
    const modal = document.getElementById('paymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'items-center', 'justify-center');
    // Restore background scrolling
    document.body.style.overflow = 'auto';
}

// Confirm payment settings update
document.getElementById('confirmPaymentUpdate').addEventListener('click', function() {
    document.getElementById('paymentForm').submit();
});
</script>
@endsection


