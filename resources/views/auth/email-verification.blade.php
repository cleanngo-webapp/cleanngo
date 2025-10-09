{{-- Email Verification Form --}}
@extends('layouts.auth')

@section('title','Email Verification')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center">
	<img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Clean Saver" class="h-20 mb-6" />
	<div class="bg-white/90 rounded-xl shadow p-6 w-full max-w-md">
		<h1 class="text-2xl font-bold text-center">Verify Your Email</h1>
		<p class="text-center text-gray-600 mt-2 mb-6">We've sent a verification code to your email address</p>
		
		@if ($errors->any())
			<div class="mt-4 p-3 bg-red-100 text-red-700 rounded">
				{{ $errors->first() }}
			</div>
		@endif

		@if (session('success'))
			<div class="mt-4 p-3 bg-green-100 text-green-700 rounded">
				{{ session('success') }}
			</div>
		@endif

		<!-- Send OTP Form -->
		@if (!session('otp_sent'))
			<form method="POST" action="{{ route('email.verification.send') }}" class="mt-6 space-y-4" id="sendOtpForm">
				@csrf
				<div>
					<label class="block text-sm font-medium">Email Address</label>
					<input type="email" name="email" value="{{ $email }}" class="mt-1 w-full border border-gray-200 rounded px-3 py-2" placeholder="Enter your email" required readonly />
					<p class="mt-1 text-sm text-gray-600">We'll send a verification code to this email address</p>
				</div>
				<div class="flex justify-between items-center">
					<a href="{{ route('register') }}" class="text-emerald-700 cursor-pointer hover:text-brand-highlight">Back to Registration</a>
					<button id="sendOtpButton" class="bg-emerald-700 text-white px-4 py-2 rounded cursor-pointer hover:bg-brand-highlight" type="submit">Send Verification Code</button>
				</div>
			</form>
		@else
			<!-- Verify OTP Form -->
			<form method="POST" action="{{ route('email.verification.verify') }}" class="mt-6 space-y-4" id="verifyOtpForm">
				@csrf
				<input type="hidden" name="email" value="{{ $email }}" />
				
				<div>
					<label class="block text-sm font-medium">Verification Code</label>
					<input type="text" name="otp_code" class="mt-1 w-full border border-gray-200 rounded px-3 py-2 text-center text-2xl tracking-widest" placeholder="000000" maxlength="6" required autocomplete="off" />
					<p class="mt-1 text-sm text-gray-600">Enter the 6-digit code sent to your email</p>
					@error('otp_code')
						<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<div class="text-center">
					<p class="text-sm text-gray-600">Didn't receive the code?</p>
					<form method="POST" action="{{ route('email.verification.resend') }}" class="inline">
						@csrf
						<input type="hidden" name="email" value="{{ $email }}" />
						<button type="submit" class="text-emerald-700 hover:text-brand-highlight underline">Resend Code</button>
					</form>
				</div>

				<div class="flex justify-between items-center">
					<a href="{{ route('register') }}" class="text-emerald-700 cursor-pointer hover:text-brand-highlight">Back to Registration</a>
					<button id="verifyOtpButton" class="bg-emerald-700 text-white px-4 py-2 rounded cursor-pointer hover:bg-brand-highlight" type="submit">Verify & Complete Registration</button>
				</div>
			</form>
		@endif
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Handle OTP input formatting
		const otpInput = document.querySelector('input[name="otp_code"]');
		if (otpInput) {
			otpInput.addEventListener('input', function(e) {
				// Only allow numbers
				e.target.value = e.target.value.replace(/[^0-9]/g, '');
				
				// Auto-submit when 6 digits are entered
				if (e.target.value.length === 6) {
					document.getElementById('verifyOtpForm').submit();
				}
			});

			// Focus on the input
			otpInput.focus();
		}

		// Handle send OTP form submission
		const sendOtpForm = document.getElementById('sendOtpForm');
		if (sendOtpForm) {
			sendOtpForm.addEventListener('submit', function(e) {
				e.preventDefault();
				
				const submitButton = document.getElementById('sendOtpButton');
				if (submitButton) {
					submitButton.disabled = true;
					submitButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Sending...';
				}
				
				// Submit the form
				sendOtpForm.submit();
			});
		}

		// Handle verify OTP form submission
		const verifyOtpForm = document.getElementById('verifyOtpForm');
		if (verifyOtpForm) {
			verifyOtpForm.addEventListener('submit', function(e) {
				e.preventDefault();
				
				const otpCode = document.querySelector('input[name="otp_code"]').value;
				if (otpCode.length !== 6) {
					alert('Please enter a valid 6-digit verification code.');
					return;
				}
				
				const submitButton = document.getElementById('verifyOtpButton');
				if (submitButton) {
					submitButton.disabled = true;
					submitButton.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline-block"></div>Verifying...';
				}
				
				// Submit the form
				verifyOtpForm.submit();
			});
		}
	});
</script>
@endsection
