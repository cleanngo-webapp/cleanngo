{{-- Simple Login --}}

@extends('layouts.auth')

@section('title','Sign In')

@section('content')
	<div class="min-h-screen flex flex-col items-center justify-center">
		<img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Clean N' Go" class="h-20 mb-6" />
		<div class="bg-white rounded-xl shadow p-6 w-full max-w-md">
			<h1 class="text-2xl font-bold text-center">Sign In</h1>
			@if (session('success'))
				<div class="mt-4 p-3 bg-green-100 text-green-700 rounded">
					{{ session('success') }}
				</div>
			@endif
			@if ($errors->any())
				<div class="mt-4 p-3 bg-red-100 text-red-700 rounded">
					{{ $errors->first() }}
				</div>
			@endif
			<form method="POST" action="{{ route('login.post') }}" class="mt-6 space-y-4" id="loginForm">
				@csrf
				<div>
					<label class="block text-sm font-medium">Email or Username</label>
					<input type="text" name="login" value="{{ old('login') }}" class="mt-1 w-full border border-gray-200 rounded px-3 py-2" placeholder="Enter your email or username" required />
					@error('login')
						<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>
				<div>
					<label class="block text-sm font-medium">Password</label>
					<div class="relative">
						<input id="login_password" type="password" name="password" class="mt-1 w-full border border-gray-200 rounded px-3 pr-10 py-2" placeholder="Enter your password" required />
						<button type="button" class="absolute inset-y-0 right-2 my-auto text-gray-500 hover:text-gray-700" aria-label="Toggle password visibility" data-toggle-password data-target="#login_password">
							<i class="ri-eye-line text-xl cursor-pointer"></i>
						</button>
					</div>
					@error('password')
						<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>
				<div class="flex justify-between items-center">
					<a href="{{ route('register') }}" class="text-emerald-700 cursor-pointer hover:text-brand-highlight">Sign Up</a>
					<button id="loginButton" class="bg-brand-green text-white px-4 py-2 rounded cursor-pointer hover:bg-brand-highlight" type="submit">Sign In</button>
				</div>
			</form>
		</div>
		</div>

@push('scripts')
<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Login form preloader functionality - using register form pattern
    const loginForm = document.getElementById('loginForm');
    const submitButton = document.getElementById('loginButton');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            // Get form data for validation
            const formData = new FormData(loginForm);
            const login = formData.get('login');
            const password = formData.get('password');
            
            // Basic validation
            if (!login || !password) {
                // Show error if fields are empty
                if (!login) {
                    Swal.fire({
                        title: 'Missing Information',
                        text: 'Please enter your email or username',
                        icon: 'warning',
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'OK'
                    });
                } else if (!password) {
                    Swal.fire({
                        title: 'Missing Information',
                        text: 'Please enter your password',
                        icon: 'warning',
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'OK'
                    });
                }
                return;
            }
            
            // Show preloader and submit the form immediately (no confirmation needed for login)
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<div style="display: inline-block; width: 16px; height: 16px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite; margin-right: 8px;"></div>Signing In...';
                
                // Force a reflow to ensure the DOM update is visible
                submitButton.offsetHeight;
            }
            
            // Submit the form after a small delay to ensure spinner is visible
            setTimeout(() => {
                loginForm.submit();
            }, 100);
        });
    }

    // Password toggle functionality
    const toggleButtons = document.querySelectorAll('[data-toggle-password]');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.querySelector(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput && icon) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.className = 'ri-eye-off-line text-xl cursor-pointer';
                } else {
                    passwordInput.type = 'password';
                    icon.className = 'ri-eye-line text-xl cursor-pointer';
                }
            }
        });
    });
});
</script>
@endpush
@endsection


