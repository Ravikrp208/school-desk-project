<?php
// about.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'includes/common.php';

$title = "About Us - MySchoolDesk";
require_once 'header.php';
?>

<div class="bg-gradient-to-br from-[#F8FAFF] to-[#E0E9FF] min-h-screen py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <span class="inline-block bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-lg uppercase tracking-widest mb-4">OUR STORY</span>
                <h1 class="text-4xl md:text-6xl font-black text-slate-900 mb-6">The Digital Atelier of <span class="text-blue-600 italic">Discovery.</span></h1>
                <p class="text-xl text-slate-600 font-medium leading-relaxed">We are India's most trusted school discovery platform, dedicated to simplifying the complex journey of school admissions for every parent.</p>
            </div>

            <div class="bg-white rounded-[48px] p-8 md:p-16 shadow-2xl border-8 border-white/50 space-y-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 mb-6">Our Mission</h2>
                        <p class="text-slate-600 font-medium leading-relaxed mb-6">Choosing a school is one of the most significant decisions a parent makes. At MySchoolDesk, our mission is to provide parents with 100% verified information, transparent comparisons, and expert guidance to find the perfect educational fit for their children.</p>
                        <div class="flex items-center gap-4 p-6 rounded-2xl bg-blue-50 border border-blue-100">
                            <div class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center text-xl shrink-0">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <p class="text-sm font-black text-blue-900 uppercase tracking-tight leading-tight">Empowering 50,000+ Parents Every Month</p>
                        </div>
                    </div>
                    <div class="rounded-3xl overflow-hidden shadow-xl border-4 border-slate-50">
                        <img src="assets/addiamge/image copy 2.png" class="w-full h-auto object-cover" alt="Discovery">
                    </div>
                </div>

                <div class="pt-16 border-t border-slate-100 grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                    <div>
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl">
                            <i class="fa-solid fa-shield-check"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 mb-2">100% Verified</h3>
                        <p class="text-sm text-slate-500 font-medium">Every school listing is manually verified by our audit team for accuracy.</p>
                    </div>
                    <div>
                        <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl">
                            <i class="fa-solid fa-people-group"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 mb-2">Parent-Led</h3>
                        <p class="text-sm text-slate-500 font-medium">Real-world reviews and ratings from our massive parent community.</p>
                    </div>
                    <div>
                        <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 mb-2">Fast Tracking</h3>
                        <p class="text-sm text-slate-500 font-medium">Priority visit bookings and direct application support through our portal.</p>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-[32px] p-10 text-white text-center relative overflow-hidden group">
                    <div class="relative z-10">
                        <h2 class="text-3xl font-black mb-4">Ready to find the right school?</h2>
                        <p class="text-slate-400 mb-8 max-w-lg mx-auto font-medium">Start your journey today with India's #1 specialized school discovery engine.</p>
                        <a href="search.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-black py-4 px-12 rounded-xl shadow-xl shadow-blue-500/20 transition-all hover:scale-105 active:scale-95 uppercase tracking-tighter">Start Searching Now</a>
                    </div>
                    <div class="absolute right-0 top-0 h-full w-1/3 bg-white/5 skew-x-12 translate-x-1/2 group-hover:translate-x-1/3 transition-transform duration-700"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
