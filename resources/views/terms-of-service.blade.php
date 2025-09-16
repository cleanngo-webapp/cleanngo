@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="max-w-4xl mx-auto pt-20 px-6 py-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-emerald-900 mb-6">Terms of Service</h1>
        
        <div class="text-sm text-gray-600 mb-8">
            <p><strong>Effective Date:</strong> {{ date('F j, Y') }}</p>
            <p><strong>Last Updated:</strong> {{ date('F j, Y') }}</p>
        </div>

        <div class="prose prose-lg max-w-none">
            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">1. Acceptance of Terms</h2>
            <p class="mb-6">
                By booking or using our cleaning services, or interacting with us through our Facebook page, website, or other channels, you agree to be bound by these Terms of Service ("Terms"). If you do not agree, please do not use our services.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">2. Services</h2>
            <p class="mb-6">
                We provide professional cleaning services including carpet deep cleaning, enhanced disinfection, glass cleaning, car interior detailing, post construction cleaning, and sofa/mattress deep cleaning. When you request a service, we will provide you with details such as scope, timing, cost, and any special conditions.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">3. Booking and Payment</h2>
            <div class="mb-6 space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-emerald-700 mb-2">Booking Process:</h3>
                    <p>You may book via our website, phone, Facebook page, or in person.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-emerald-700 mb-2">Payment Terms:</h3>
                    <p>Payment is due after service delivery. We accept cash, bank transfer, and other agreed payment methods.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-emerald-700 mb-2">Cancellation / Rescheduling:</h3>
                    <p>If you need to cancel or reschedule, please give us at least 24 hours' notice. Late cancellations or no-shows may incur a fee of 50% of the service cost.</p>
                </div>
            </div>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">4. Pricing</h2>
            <p class="mb-6">
                Prices are as quoted at time of booking. We reserve the right to adjust prices due to unforeseen circumstances, but we will inform you beforehand where possible.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">5. Access and Customer Responsibilities</h2>
            <p class="mb-6">
                You agree to provide access to the premises at the scheduled time. You must ensure the safety of our staff, inform us of any hazards, and remove valuable or fragile items beforehand.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">6. Service Satisfaction</h2>
            <p class="mb-6">
                If you are not satisfied with our service, let us know within 48 hours of completion, and we will work with you to resolve the issue (e.g. offer re-clean, refund or discount as appropriate).
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">7. Liability</h2>
            <ul class="list-disc pl-6 mb-6 space-y-2">
                <li>We will perform services with reasonable care and skill.</li>
                <li>We are not liable for damage or loss unless caused by our negligence.</li>
                <li>We are not responsible for pre-existing damage or items you did not disclose.</li>
            </ul>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">8. Intellectual Property</h2>
            <p class="mb-6">
                All content (e.g. photos, text, logos) displayed on our website, social media, or promotional materials are our property or used with permission. You may not use them without consent.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">9. Privacy</h2>
            <p class="mb-6">
                Use of your data is governed by our Privacy Policy. By accepting these Terms, you also consent to how we collect, use, and share information as described in the Privacy Policy.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">10. Termination</h2>
            <p class="mb-6">
                We may decline service or terminate a booking at our discretion (e.g. for safety, non-payment, inappropriate behavior). You may also terminate service by cancelling according to our cancellation policy.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">11. Governing Law</h2>
            <p class="mb-6">
                These Terms are governed by the laws of the Philippines. Any dispute shall be resolved by the courts of Naga City.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">12. Changes to Terms</h2>
            <p class="mb-6">
                We may modify these Terms from time to time. We will post updated versions with new "Last Updated" dates. Your continued use of our services after changes means you accept those changes.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">13. Contact Us</h2>
            <p class="mb-4">If you have any questions about these Terms of Service, contact us:</p>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p><strong>Company:</strong> CleanSaver Naga</p>
                <p><strong>Address:</strong> 0772 Maescoy Compound, San Felipe Naga City</p>
                <p><strong>Email:</strong> <a href="mailto:cleansaverph.naga@gmail.com" class="text-emerald-600 hover:text-emerald-800">cleansaverph.naga@gmail.com</a></p>
                <p><strong>Phone:</strong> <a href="tel:+639951120443" class="text-emerald-600 hover:text-emerald-800">(+63) 995 112 0443</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
