@extends('layouts.app')

@section('title','Settings')

@section('content')
<div class="max-w-4xl mx-auto mt-20">
    <h1 class="text-3xl font-extrabold text-center mb-8">Settings</h1>

    <div class="bg-white rounded-xl p-8 shadow-lg">
        <!-- Avatar Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Profile Picture</h2>
            
            <div class="flex items-center space-x-6 mb-6">
                <!-- Current Avatar Display -->
                <div class="flex-shrink-0">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" 
                             alt="Profile Picture" 
                             class="w-20 h-20 rounded-full object-cover border-4 border-emerald-200">
                    @else
                        <div class="w-20 h-20 rounded-full bg-emerald-100 border-4 border-emerald-200 flex items-center justify-center">
                            <span class="text-2xl font-semibold text-emerald-600">
                                {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>
                
                <!-- Avatar Upload Form -->
                <div class="flex-1">
                    <form method="POST" action="{{ route('customer.settings.avatar.update') }}" 
                          enctype="multipart/form-data" class="space-y-4" id="avatarForm">
                        @csrf
                        
                        <div>
                            <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload New Profile Picture
                            </label>
                            <div id="avatar-upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-emerald-400 transition-colors cursor-pointer" 
                                 onclick="document.getElementById('avatar').click()"
                                 ondrop="handleAvatarDrop(event)" 
                                 ondragover="handleAvatarDragOver(event)" 
                                 ondragenter="handleAvatarDragEnter(event)" 
                                 ondragleave="handleAvatarDragLeave(event)">
                                <div class="space-y-1 text-center">
                                    <i class="ri-upload-cloud-2-line text-4xl text-gray-400"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="avatar" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500">
                                            <span>Upload Profile Picture</span>
                                            <input id="avatar" name="avatar" type="file" class="sr-only" accept="image/*" onchange="previewAvatar(this)">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">JPEG, PNG, JPG, GIF up to 10MB</p>
                                </div>
                            </div>
                            <div id="avatar-preview" class="mt-3 hidden">
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <img id="avatar-preview-img" src="" alt="Preview" class="w-16 h-16 rounded-full object-cover border-2 border-emerald-200">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Selected Image</p>
                                        <p class="text-xs text-gray-500">Ready to upload</p>
                                    </div>
                                    <button type="button" onclick="removeAvatarPreview()" class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition-colors cursor-pointer">
                                        <i class="ri-close-line text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="submit" 
                                    id="uploadAvatarBtn"
                                    disabled
                                    class="bg-gray-400 text-white px-4 py-2 rounded-lg focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-medium cursor-not-allowed">
                                Update Profile Picture
                            </button>
                            
                            @if(auth()->user()->avatar)
                                <button type="button" 
                                        id="removeAvatarBtn"
                                        onclick="showRemoveAvatarConfirmation()"
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors font-medium cursor-pointer">
                                    Remove Picture
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Change Section -->
        <div class="border-t pt-8">
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
        <div class="border-t mt-5 pt-8">
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
    // Close the modal
    hidePasswordConfirmation();
    
    // Show spinner and disable main button
    const mainButton = document.querySelector('button[onclick="showPasswordConfirmation()"]');
    
    if (mainButton) {
        mainButton.disabled = true;
        mainButton.classList.add('opacity-50', 'cursor-not-allowed');
        mainButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Updating';
    }
    
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
    // Close the modal
    hideProfileConfirmation();
    
    // Show spinner and disable main button
    const mainButton = document.getElementById('updateProfileBtn');
    
    if (mainButton) {
        mainButton.disabled = true;
        mainButton.classList.add('opacity-50', 'cursor-not-allowed');
        mainButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Updating';
    }
    
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

// Paste functionality for avatar upload
document.addEventListener('paste', function(e) {
    const activeElement = document.activeElement;
    const isInAvatarArea = activeElement && (
        activeElement.id === 'avatar-upload-area' ||
        activeElement.closest('#avatar-upload-area')
    );
    
    if (isInAvatarArea && e.clipboardData && e.clipboardData.items) {
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
                    document.getElementById('avatar').files = dataTransfer.files;
                    previewAvatar(document.getElementById('avatar'));
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Image Pasted',
                        text: 'Profile picture has been pasted successfully.',
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

// Avatar Upload Functions
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview-img').src = e.target.result;
            document.getElementById('avatar-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
        
        // Enable upload button
        enableUploadButton();
    }
}

// Remove avatar preview
function removeAvatarPreview() {
    document.getElementById('avatar-preview').classList.add('hidden');
    document.getElementById('avatar').value = '';
    disableUploadButton();
}

// Enable upload button
function enableUploadButton() {
    const uploadBtn = document.getElementById('uploadAvatarBtn');
    uploadBtn.disabled = false;
    uploadBtn.className = 'bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors font-medium cursor-pointer';
}

// Disable upload button
function disableUploadButton() {
    const uploadBtn = document.getElementById('uploadAvatarBtn');
    uploadBtn.disabled = true;
    uploadBtn.className = 'bg-gray-400 text-white px-4 py-2 rounded-lg focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-medium cursor-not-allowed';
}

// Drag and Drop Functions for Avatar
function handleAvatarDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleAvatarDragEnter(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('avatar-upload-area').classList.add('border-emerald-500', 'bg-emerald-50');
}

function handleAvatarDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('avatar-upload-area').classList.remove('border-emerald-500', 'bg-emerald-50');
}

function handleAvatarDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('avatar-upload-area').classList.remove('border-emerald-500', 'bg-emerald-50');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        if (file.type.startsWith('image/')) {
            // Create a new FileList with the dropped file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById('avatar').files = dataTransfer.files;
            previewAvatar(document.getElementById('avatar'));
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please drop an image file (JPEG, PNG, JPG, GIF).',
                confirmButtonColor: '#10b981'
            });
        }
    }
}

// Show remove avatar confirmation with SweetAlert
function showRemoveAvatarConfirmation() {
    Swal.fire({
        title: 'Remove Profile Picture?',
        text: 'Are you sure you want to remove your profile picture? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Remove Picture',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            removeAvatar();
        }
    });
}

// Remove avatar function with preloader
function removeAvatar() {
    const removeBtn = document.getElementById('removeAvatarBtn');
    
    if (removeBtn) {
        removeBtn.disabled = true;
        removeBtn.classList.add('opacity-50', 'cursor-not-allowed');
        removeBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Removing Picture';
    }
    
    // Create a form to submit DELETE request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("customer.settings.avatar.remove") }}';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add method override for DELETE
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    form.appendChild(methodField);
    
    // Submit the form
    document.body.appendChild(form);
    form.submit();
}

// Handle avatar form submission with preloader
document.addEventListener('DOMContentLoaded', function() {
    const avatarForm = document.getElementById('avatarForm');
    const uploadBtn = document.getElementById('uploadAvatarBtn');
    
    if (avatarForm && uploadBtn) {
        avatarForm.addEventListener('submit', function(e) {
            // Show preloader on the upload button
            uploadBtn.disabled = true;
            uploadBtn.classList.add('opacity-50', 'cursor-not-allowed');
            uploadBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Uploading Picture';
        });
    }
});
</script>
@endsection
