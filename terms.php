<?php
// terms.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'includes/common.php';

$title = "Terms of Service - MySchoolDesk";
require_once 'header.php';
?>

<div class="bg-gradient-to-br from-[#F8FAFF] to-[#E0E9FF] min-h-screen py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <span class="inline-block bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-lg uppercase tracking-widest mb-4">LEGAL DOCS</span>
                <h1 class="text-4xl md:text-6xl font-black text-slate-900 mb-6">Terms of <span class="text-blue-600 italic">Service.</span></h1>
                <p class="text-xl text-slate-600 font-medium">Clear, simple terms for using the MySchoolDesk platform.</p>
            </div>

            <div class="bg-white rounded-[48px] p-8 md:p-16 shadow-2xl border-8 border-white/50 prose prose-slate prose-lg max-w-none">
                <p class="font-bold text-slate-400 uppercase tracking-widest text-xs mb-8">LAST UPDATED: APRIL 2026</p>
                
                <h2 class="text-2xl font-black text-slate-900">1. Agreement to Terms</h2>
                <p class="text-slate-600 leading-relaxed font-semibold">By accessing or using MySchoolDesk, you agree to be bound by these terms of service. If you do not agree with any part of these terms, you may not access our services.</p>

                <h2 class="text-2xl font-black text-slate-900 mt-12">2. Use of Service</h2>
                <p class="text-slate-600 leading-relaxed font-semibold">You agree to use our website only for lawful purposes related to school discovery and admissions. You are prohibited from:</p>
                <ul class="text-slate-600 font-semibold list-disc pl-6 space-y-2">
                    <li>Using the service for any fraudulent or unauthorized purposes.</li>
                    <li>Attempting to gain unauthorized access to our systems or user accounts.</li>
                    <li>Interfering with the proper working of the service.</li>
                    <li>Scraping or harvesting data from the platform without permission.</li>
                </ul>

                <h2 class="text-2xl font-black text-slate-900 mt-12">3. School Listings & Information</h2>
                <p class="text-slate-600 leading-relaxed font-semibold">While we strive for 100% accuracy, MySchoolDesk does not guarantee the completeness or reliability of any school information provided by third parties. Users are encouraged to verify critical details directly with the educational institutions.</p>

                <h2 class="text-2xl font-black text-slate-900 mt-12">4. User Accounts</h2>
                <p class="text-slate-600 leading-relaxed font-semibold">When you create an account with us, you must provide information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the terms, which may result in immediate termination of your account on our service.</p>

                <h2 class="text-2xl font-black text-slate-900 mt-12">5. Limitation of Liability</h2>
                <p class="text-slate-600 leading-relaxed font-semibold">In no event shall MySchoolDesk be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, or other intangible losses resulting from your use of the service.</p>

                <h2 class="text-2xl font-black text-slate-900 mt-12">6. Changes to Terms</h2>
                <p class="text-slate-600 leading-relaxed font-semibold">We reserve the right, at our sole discretion, to modify or replace these terms at any time. We will provide notice of any changes by posting the new terms on this page.</p>

                <div class="mt-16 p-8 rounded-3xl bg-blue-50 border border-blue-100 flex items-center gap-6">
                    <div class="w-14 h-14 bg-white text-blue-600 rounded-2xl flex items-center justify-center text-2xl shrink-0 shadow-sm">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-blue-900">Questions?</h4>
                        <p class="text-blue-700/70 font-medium">Contact our legal team at <strong>legal@myschooldesk.com</strong> if you have any questions regarding these terms.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
