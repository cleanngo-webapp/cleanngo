{{-- Customer Registration Form - Role defaults to customer --}}
@extends('layouts.auth')

@section('title','Sign Up')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center">
	<img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Clean N' Go" class="h-20 mb-6" />
	<div class="bg-white/90 rounded-xl shadow p-6 md:w-full max-w-md">
		<h1 class="text-2xl font-bold text-center">Sign Up</h1>
		@if ($errors->any())
			<div class="mt-4 p-3 bg-red-100 text-red-700 rounded">
				{{ $errors->first() }}
			</div>
		@endif
		<form method="POST" action="{{ route('register.post') }}" class="mt-6 space-y-4" id="registerForm">
			@csrf
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
					<label class="block text-sm font-medium">First Name</label>
					<input type="text" name="first_name" value="{{ old('first_name') }}" class="mt-1 w-full border border-gray-200 rounded px-3 py-2" placeholder="Enter your first name" required />
					@error('first_name')
						<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>
				<div>
					<label class="block text-sm font-medium">Last Name</label>
					<input type="text" name="last_name" value="{{ old('last_name') }}" class="mt-1 w-full border border-gray-200 rounded px-3 py-2" placeholder="Enter your last name" required />
					@error('last_name')
						<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror	
				</div>
			</div>
			<div>
				<label class="block text-sm font-medium">Username</label>
				<input type="text" name="username" value="{{ old('username') }}" class="mt-1 w-full border border-gray-200 rounded px-3 py-2" placeholder="Choose a username" required />
				@error('username')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>
			<div>
				<label class="block text-sm font-medium">Email</label>
				<input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full border border-gray-200 rounded px-3 py-2" placeholder="Enter your email" required />
			</div>
			<div>
				<label class="block text-sm font-medium">Contact Number</label>
				<input type="text" name="contact" value="{{ old('contact') }}" class="mt-1 w-full border border-gray-200 rounded px-3 py-2" placeholder="Enter your contact number" />
			</div>
			{{-- Hidden field to default role to customer --}}
			<input type="hidden" name="role" value="customer" />
			<div>
				<label class="block text-sm font-medium">Password</label>
				<div class="relative">
					<input id="register_password" type="password" name="password" class="mt-1 w-full border border-gray-200 rounded px-3 pr-10 py-2" placeholder="Enter your password" required />
					<button type="button" class="absolute inset-y-0 right-2 my-auto text-gray-500 hover:text-gray-700" aria-label="Toggle password visibility" data-toggle-password data-target="#register_password">
						<i class="ri-eye-line text-xl cursor-pointer"></i>
					</button>
				</div>
			</div>
			<div>
				<label class="block text-sm font-medium">Confirm Password</label>
				<div class="relative">
					<input id="register_password_confirmation" type="password" name="password_confirmation" class="mt-1 w-full border border-gray-200 rounded px-3 pr-10 py-2" placeholder="Confirm your password" required />
					<button type="button" class="absolute inset-y-0 right-2 my-auto text-gray-500 hover:text-gray-700" aria-label="Toggle password visibility" data-toggle-password data-target="#register_password_confirmation">
						<i class="ri-eye-line text-xl cursor-pointer"></i>
					</button>
				</div>
			</div>
			<div class="flex justify-between items-center">
				<a href="{{ route('landing') }}" class="text-emerald-700 cursor-pointer hover:text-brand-highlight">Cancel</a>
				<button id="registerButton" class="bg-emerald-700 text-white px-4 py-2 rounded cursor-pointer hover:bg-brand-highlight" type="submit">Sign Up</button>
			</div>
		</form>
	</div>
	</div>

	<script>
		// Handle form submission with SweetAlert confirmation and preloader
		document.addEventListener('DOMContentLoaded', function() {
			const form = document.getElementById('registerForm');
			const submitButton = document.getElementById('registerButton');
			
			form.addEventListener('submit', function(e) {
				e.preventDefault(); // Prevent default form submission
				
				// Get form data for validation and confirmation
				const formData = new FormData(form);
				const password = formData.get('password');
				const passwordConfirmation = formData.get('password_confirmation');
				const firstName = formData.get('first_name');
				const lastName = formData.get('last_name');
				const email = formData.get('email');
				const contact = formData.get('contact');
				
				// Validate password match first
				if (password !== passwordConfirmation) {
					Swal.fire({
						title: 'Password Mismatch',
						text: 'Password and Confirm Password do not match. Please check and try again.',
						icon: 'error',
						confirmButtonColor: '#dc2626', // red-600 color
						confirmButtonText: 'OK'
					});
					return; // Stop execution if passwords don't match
				}
				
				// Show SweetAlert confirmation dialog only if passwords match
				Swal.fire({
					title: 'Confirm Registration',
					html: `
						<div class="text-left">
							<p class="mb-2"><strong>Please confirm your details:</strong></p>
							<p class="mb-1"><strong>Name:</strong> ${firstName} ${lastName}</p>
							<p class="mb-1"><strong>Email:</strong> ${email}</p>
							<p class="mb-1"><strong>Contact:</strong> ${contact}</p>
							<p class="mb-1"><strong>Role:</strong> Customer</p>
							<p class="mt-3 text-sm text-gray-600">Are you sure these credentials are correct?</p>
						</div>
					`,
					icon: 'question',
					showCancelButton: true,
					confirmButtonColor: '#047857', // emerald-700 color
					cancelButtonColor: '#dc2626', // red-600 color
					confirmButtonText: 'Yes, Sign Up',
					cancelButtonText: 'Cancel',
					reverseButtons: true
				}).then((result) => {
					if (result.isConfirmed) {
						// Show preloader and submit the form
						if (submitButton) {
							submitButton.disabled = true;
							submitButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Creating Account...';
						}
						
						// Submit the form
						form.submit();
					}
					// If cancelled, do nothing (form stays on page)
				});
			});
			
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
@endsection


