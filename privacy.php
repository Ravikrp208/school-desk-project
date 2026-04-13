<?php
// privacy.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'includes/common.php';

$title = "Privacy Policy - MySchoolDesk";
require_once 'header.php';
?>

<div class="bg-gradient-to-br from-[#F8FAFF] to-[#E0E9FF] min-h-screen py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <span class="inline-block bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-lg uppercase tracking-widest mb-4">LEGAL DOCS</span>
                <h1 class="text-4xl md:text-6xl font-black text-slate-900 mb-6">Privacy <span class="text-blue-600 italic">Policy.</span></h1>
                <p class="text-xl text-slate-600 font-medium">Your trust is our most valuable asset. Here's how we protect your data.</p>
            </div>

            <div class="bg-white rounded-[48px] p-8 md:p-16 shadow-2xl border-8 border-white/50 prose prose-slate prose-lg max-w-none">
                <p class="font-bold text-slate-400 uppercase tracking-widest text-xs mb-8">LAST UPDATED: APRIL 2026</p>
                
                <h2 class="text-2xl font-black text-slate-900">1. Introduction</h2>
                <p class="text-slate-600 leading-relaxed font-semibold">Welcome to MySchoolDesk. We respect your privacy and are committed to protecting your personal data. This privacy policy will inform you about how we look after your personal data when you visit our website and tell you about your privacy rights and how the law protects you.</p>

                <h2 class="text-2xl font-black text-slate-900 mt-12">2. The Data We Collect</h2>
                <p class="text-slate-600 leading-relaxed font-semibold">We may collect, use, store and transfer different kinds of personal data about you which we have grouped together as follows:</p>
                <ul class="text-slate-600 font-semibold list-disc pl-6 space-y-2">
                    <li><strong>Identity Data:</strong> includes first name, last name, username or similar identifier.</li>
                    <li><strong>Contact Data:</strong> includes email address and telephone numbers.</li>
                    <li><strong>Technical Data:</strong> includes internet protocol (IP) address, your login data, browser type and version.</li>
                    <li><strong>Usage Data:</strong> includes information about how you use our website and services.</li>
                </ul>

                <h2 class="text-2xl font-black text-slate-900 mt-12">3. How Your Data Is Used</h2>
                <p class="text-slate-600 leading-relaxed font-semibold">We will only use your personal data when the law allows us to. Most commonly, we will use your personal data in the following circumstances:</p>
                <ul class="text-slate-600 font-semibold list-disc pl-6 space-y-2">
                    <li>To provide school discovery services and admission assistance.</li>
                    <li>To notify you about changes to our service.</li>
                    <li>To allow you to participate in interactive features of our service.</li>
                    <li>To provide customer support.</li>
                </ul>

                <h2 class="text-2xl font-black text-slate-900 mt-12">4. Data Security</h2>
                <p class="text-slate-600 leading-relaxed font-semibold">We have put in place appropriate security measures to prevent your personal data from being accidentally lost, used or accessed in an unauthorized way, altered or disclosed. In addition, we limit access to your personal data to those employees, agents, contractors and other third parties who have a business need to know.</p>

                <h2 class="text-2xl font-black text-slate-900 mt-12">5. Contact Us</h2>
                <p class="text-slate-600 leading-relaxed font-semibold">If you have any questions about this privacy policy or our privacy practices, please contact our privacy compliance officer at <strong>privacy@myschooldesk.com</strong>.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
