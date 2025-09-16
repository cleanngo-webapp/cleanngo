@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="max-w-4xl mx-auto pt-20 px-6 py-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-emerald-900 mb-6">Privacy Policy</h1>
        
        <div class="text-sm text-gray-600 mb-8">
            <p><strong>Effective Date:</strong> {{ date('F j, Y') }}</p>
            <p><strong>Last Updated:</strong> {{ date('F j, Y') }}</p>
        </div>

        <div class="prose prose-lg max-w-none">
            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">1. Introduction</h2>
            <p class="mb-6">
                We ("we," "us," "our," or "CleanSaver Naga") respect your privacy. This Privacy Policy explains how we collect, use, disclose, and protect your personal information when you use our cleaning services or interact with us (including via our Facebook page, website, phone, or in person).
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">2. Information We Collect</h2>
            <p class="mb-4">We may collect the following types of information:</p>
            <ul class="list-disc pl-6 mb-6 space-y-2">
                <li><strong>Personal Information:</strong> Name, address, phone number, email address.</li>
                <li><strong>Service Details:</strong> Type of cleaning service requested, schedule, location.</li>
                <li><strong>Payment Information:</strong> Billing address, payment method (credit card, bank transfer, etc.).</li>
                <li><strong>Usage and Interaction Data:</strong> How you interact with our website or Facebook page, messages, reviews.</li>
                <li><strong>Other Information:</strong> Any other information you provide when communicating with us or using our services.</li>
            </ul>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">3. How We Use Your Information</h2>
            <p class="mb-4">We use your personal information for these purposes:</p>
            <ul class="list-disc pl-6 mb-6 space-y-2">
                <li>To provide, maintain, and improve our cleaning services.</li>
                <li>To schedule and carry out appointments.</li>
                <li>To process payments and send invoices or receipts.</li>
                <li>To respond to your inquiries, feedback, or complaints.</li>
                <li>To send updates, confirmations, reminders, or promotional materials if you agree.</li>
                <li>For administrative, legal, or security purposes.</li>
            </ul>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">4. Sharing Your Information</h2>
            <p class="mb-4">We may share your information in the following circumstances:</p>
            <ul class="list-disc pl-6 mb-6 space-y-2">
                <li>With service personnel or subcontractors as needed to perform the cleaning service.</li>
                <li>With third-party payment processors to handle payments.</li>
                <li>With legal authorities if required by law or to protect our rights.</li>
                <li>With business partners or suppliers when necessary for service delivery.</li>
                <li>With your consent.</li>
            </ul>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">5. Data Security</h2>
            <p class="mb-6">
                We take reasonable steps to protect your information from unauthorized access, loss, misuse, or alteration. These include secure storage, access controls, and staff training.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">6. Data Retention</h2>
            <p class="mb-6">
                We retain your personal information only as long as necessary for the purposes it was collected for, or as required by law. Once no longer needed, we securely delete or anonymize it.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">7. Your Rights</h2>
            <p class="mb-4">Depending on your jurisdiction, you may have rights such as:</p>
            <ul class="list-disc pl-6 mb-4 space-y-2">
                <li>Access to your personal data</li>
                <li>Correction of inaccurate or incomplete data</li>
                <li>Deletion of data</li>
                <li>Restriction of processing</li>
                <li>Objection to certain uses of your data</li>
                <li>Data portability</li>
            </ul>
            <p class="mb-6">
                If you want to exercise any of these rights, contact us at <a href="mailto:cleansaverph.naga@gmail.com" class="text-emerald-600 hover:text-emerald-800">cleansaverph.naga@gmail.com</a>.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">8. Cookies and Tracking Technologies</h2>
            <p class="mb-4">We may use cookies or similar technology to:</p>
            <ul class="list-disc pl-6 mb-4 space-y-2">
                <li>Enable certain features</li>
                <li>Remember preferences</li>
                <li>Analyze website usage</li>
            </ul>
            <p class="mb-6">
                You can usually disable cookies through your browser settings, but this may affect how our site works.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">9. Children's Privacy</h2>
            <p class="mb-6">
                Our services are intended for users aged 18 or older. We do not knowingly collect personal information from children under this age. If we learn we have done so, we will delete it.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">10. Changes to This Policy</h2>
            <p class="mb-6">
                We may update this Privacy Policy from time to time. We will post the updated version with a new "Last Updated" date. Continued use of our services after changes means you accept those changes.
            </p>

            <h2 class="text-2xl font-semibold text-emerald-800 mb-4">11. Contact Us</h2>
            <p class="mb-4">If you have questions, concerns, or requests regarding this Privacy Policy, contact us:</p>
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
