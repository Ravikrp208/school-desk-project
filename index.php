<?php
// index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'includes/common.php';

$extra_css = '<link rel="stylesheet" href="assets/css/home.css">';
require_once 'header.php';
?>

<!-- Hero Section -->
<section class="hero-section relative overflow-hidden min-h-[650px] flex items-center bg-gradient-to-br from-[#F8FAFF] to-[#E0E9FF]">
    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-400/10 blur-[150px] rounded-full -translate-y-1/2 translate-x-1/4"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-400/10 blur-[120px] rounded-full translate-y-1/2 -translate-x-1/4"></div>

    <div class="container relative z-10 mx-auto px-4 py-12">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <!-- Left Content -->
            <div class="lg:w-[55%] text-left">
                <span class="inline-block bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-lg uppercase tracking-widest mb-6">ADMISSIONS 2026-27</span>
                <h1 class="text-4xl md:text-6xl font-black text-slate-900 mb-6 leading-[1.1]">
                    Find the Perfect <span class="text-blue-600 italic">Future</span> for Your Child.
                </h1>
                <p class="text-xl text-slate-600 mb-10 font-medium leading-relaxed max-w-xl">
                    Discover, Compare, and Apply to the top schools in your city with India's most trusted school discovery platform.
                </p>

                <!-- Search Bar -->
                <div class="max-w-4xl mb-12 animate-fade-in-up">
                    <form action="search.php" method="GET" class="bg-white/70 backdrop-blur-2xl p-4 rounded-[32px] shadow-2xl border border-white/50 flex flex-col md:flex-row items-center gap-4">
                        <div class="flex-1 w-full grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- State Dropdown -->
                            <div class="relative group">
                                <label class="absolute left-6 top-3 text-[8px] font-black text-blue-600/40 uppercase tracking-widest transition-all group-focus-within:text-blue-600">State</label>
                                <select id="state" name="state" required class="w-full bg-slate-50/50 border-none rounded-2xl px-6 pt-7 pb-3 text-sm font-black text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 appearance-none !bg-none cursor-pointer">
                                    <option value="">Select State</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
                            </div>

                            <!-- City Dropdown -->
                            <div class="relative group">
                                <label class="absolute left-6 top-3 text-[8px] font-black text-blue-600/40 uppercase tracking-widest transition-all group-focus-within:text-blue-600">City / District</label>
                                <select id="district" name="location" required disabled class="w-full bg-slate-50/50 border-none rounded-2xl px-6 pt-7 pb-3 text-sm font-black text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 appearance-none !bg-none cursor-pointer disabled:opacity-50">
                                    <option value="">Select City</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
                            </div>

                            <!-- Board Dropdown -->
                            <div class="relative group">
                                <label class="absolute left-6 top-3 text-[8px] font-black text-blue-600/40 uppercase tracking-widest transition-all group-focus-within:text-blue-600">Board</label>
                                <select name="board" class="w-full bg-slate-50/50 border-none rounded-2xl px-6 pt-7 pb-3 text-sm font-black text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 appearance-none !bg-none cursor-pointer">
                                    <option value="">All Boards</option>
                                    <?php foreach(msd_board_options() as $b): ?>
                                        <option value="<?php echo strtolower($b); ?>"><?php echo $b; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
                            </div>
                        </div>

                        <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-black px-10 py-5 rounded-2xl shadow-xl shadow-blue-500/30 transition-all hover:scale-[1.05] active:scale-95 flex items-center justify-center gap-3 group">
                            <span class="uppercase tracking-widest text-xs">Search</span>
                            <i class="fa-solid fa-magnifying-glass text-sm group-hover:rotate-12 transition-transform"></i>
                        </button>
                    </form>
                </div>


                <!-- Trust Indicator -->
                <div class="flex items-center gap-6 mt-8 animate-fade-in pl-2">
                    <div class="flex -space-x-4">
                        <div class="w-12 h-12 rounded-full border-4 border-white bg-slate-200 overflow-hidden shadow-sm hover:-translate-y-2 transition-all cursor-pointer group">
                            <img src="assets/images/logo_boy.png" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                        </div>
                        <div class="w-12 h-12 rounded-full border-4 border-white bg-slate-200 overflow-hidden shadow-sm hover:-translate-y-2 transition-all cursor-pointer group">
                            <img src="assets/images/logo_boy.png" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                        </div>
                        <div class="w-12 h-12 rounded-full border-4 border-white bg-slate-200 overflow-hidden shadow-sm hover:-translate-y-2 transition-all cursor-pointer group">
                            <img src="assets/images/logo_boy.png" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                        </div>
                    </div>
                    <p class="text-[14px] font-bold text-slate-500">
                        Trusted by <span class="text-blue-600">50K+ Parents</span> in Vadodara
                    </p>
                </div>
            </div>

            <!-- Right Illustration -->
            <div class="lg:w-[45%] relative">
                <div class="relative z-10 rounded-3xl overflow-hidden shadow-2xl border-8 border-white group max-h-[600px]">
                    <img src="assets/addiamge/image copy 2.png" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-1000" alt="Happy Family Illustration">
                    <div class="absolute inset-0 bg-blue-600/5 mix-blend-overlay"></div>
                </div>
                <!-- Floating Card -->
                <div class="absolute -bottom-10 -left-10 bg-white p-6 rounded-2xl shadow-2xl border border-slate-50 z-20 hidden md:block animate-bounce-slow">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center text-xl">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Enrollment Status</p>
                            <p class="text-base font-black text-slate-900">Admission Confirmed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Problem/Solution Section -->
<section class="py-24 px-4 bg-white relative overflow-hidden">
    <div class="container mx-auto">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2">
                <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-100 group">
                    <img src="assets/addiamge/image copy.png" class="w-full h-auto group-hover:scale-105 transition-transform duration-700" alt="Search Problems Illustration">
                </div>
            </div>
            <div class="lg:w-1/2">
                <span class="text-orange-500 font-black tracking-widest text-[10px] uppercase mb-4 block">THE PROBLEM VS SOLUTION</span>
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 mb-8 leading-tight">
                    Tired of Searching for the <span class="text-orange-500">Right</span> School?
                </h2>
                <div class="space-y-6">
                    <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-colors">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-900 mb-1">Too many options, confusing info</h4>
                            <p class="text-sm text-slate-500 font-medium">We filter the clutter to show only what matters to you.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-colors">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-hand-holding-heart"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-900 mb-1">No trusted reviews</h4>
                            <p class="text-sm text-slate-500 font-medium">Access verified parent feedback and community ratings.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-colors">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-900 mb-1">Wasting time visiting schools</h4>
                            <p class="text-sm text-slate-500 font-medium">Take virtual tours and book priority visits with one click.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-12">
                    <a href="search.php" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-black py-4 px-12 rounded-xl shadow-xl shadow-orange-500/20 transition-all uppercase tracking-tighter">
                        Let MySchoolDesk Help You!
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust & Stats Ribbon -->
<section class="max-w-6xl mx-auto px-4 relative z-20">
    <div class="bg-slate-900 rounded-3xl shadow-2xl p-10 grid grid-cols-2 md:grid-cols-4 gap-8 divide-x divide-white/10 text-white">
        <div class="text-center md:text-left px-4">
            <span class="block text-4xl font-black mb-1">10K+</span>
            <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Verified Schools</span>
        </div>
        <div class="text-center md:text-left px-8">
            <span class="block text-4xl font-black mb-1">50K+</span>
            <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Happy Parents</span>
        </div>
        <div class="text-center md:text-left px-8">
            <span class="block text-4xl font-black mb-1">98%</span>
            <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Success Rate</span>
        </div>
        <div class="text-center md:text-left px-8">
            <span class="block text-4xl font-black mb-1">#1</span>
            <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Direct Admission</span>
        </div>
    </div>
</section>

<!-- Promotion Banner Section -->
<section class="container mx-auto px-4 py-12 relative z-10">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-8 flex flex-col md:flex-row items-center justify-between text-white shadow-xl overflow-hidden group">
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="h-16 w-16 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30">
                <i class="fa-solid fa-bolt-lightning text-3xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black mb-1">Get Priority Admission Assistance</h3>
                <p class="text-blue-100 font-medium">Apply through MySchoolDesk and get exclusive help with documents & visits.</p>
            </div>
        </div>
        <a href="search.php" class="relative z-10 mt-6 md:mt-0 bg-white text-blue-700 font-black py-4 px-10 rounded-xl shadow-lg hover:shadow-2xl hover:scale-105 transition-all">
            Explore Premium Listings
        </a>
        <div class="absolute right-0 top-0 h-full w-1/3 bg-white/5 skew-x-12 translate-x-1/2 group-hover:translate-x-1/3 transition-transform duration-700"></div>
    </div>
</section>

<!-- Featured Schools Section -->
<section class="container mx-auto py-24 px-4">
    <div class="flex items-end justify-between mb-16">
        <div>
            <span class="text-blue-600 font-black tracking-widest text-xs uppercase mb-3 block">PREMIUM LISTINGS</span>
            <h2 class="text-3xl md:text-5xl font-black text-slate-900 leading-tight">Featured Schools</h2>
        </div>
        <div class="flex gap-4 mb-2">
            <button class="w-14 h-14 rounded-xl border-2 border-slate-100 flex items-center justify-center text-slate-400 hover:border-blue-600 hover:text-blue-600 transition-all">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <button class="w-14 h-14 rounded-xl bg-blue-600 flex items-center justify-center text-white hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/30">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php
        $stmt = $pdo->query("SELECT * FROM schools WHERE is_featured = 1 LIMIT 3");
        $featured = $stmt->fetchAll();
        foreach($featured as $school):
            $rating = number_format(4.5 + (rand(0, 4) / 10), 1);
        ?>
        <div class="bg-white rounded-[40px] overflow-hidden border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 group">
            <div class="relative h-64 overflow-hidden p-3 bg-slate-50">
                <div class="w-full h-full rounded-3xl overflow-hidden bg-slate-100">
                    <?php 
                        $photos = json_decode($school['photos'] ?? '[]', true);
                        $school_img = !empty($photos) ? $photos[0] : 'assets/images/school1.png';
                    ?>
                        <a href="school.php?id=<?php echo $school['id']; ?>" class="block w-full h-full">
                            <img src="<?php echo htmlspecialchars($school_img); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </a>
                </div>
                <div class="absolute top-8 right-8 bg-white/95 backdrop-blur-md px-4 py-2 rounded-xl flex items-center gap-2 shadow-xl border border-white/50">
                    <i class="fa-solid fa-star text-amber-400 text-xs"></i>
                    <span class="text-sm font-black text-slate-800"><?php echo $rating; ?></span>
                </div>
                <div class="absolute bottom-6 left-8 flex gap-2">
                    <span class="bg-blue-600/90 backdrop-blur-md text-white text-[10px] font-black px-4 py-2 rounded-lg border border-white/20 uppercase tracking-widest shadow-lg"><?php echo htmlspecialchars($school['board']); ?></span>
                    <span class="bg-white/90 backdrop-blur-md text-slate-900 text-[10px] font-black px-4 py-2 rounded-lg border border-white/20 uppercase tracking-widest shadow-lg">CO-ED</span>
                </div>
            </div>
            <div class="px-8 pb-8 pt-4">
                <a href="school.php?id=<?php echo $school['id']; ?>" class="block group/title">
                    <h3 class="text-2xl font-black text-slate-900 mb-2 truncate group-hover/title:text-blue-600 transition-colors"><?php echo htmlspecialchars($school['name']); ?></h3>
                </a>
                <p class="text-slate-400 text-sm font-bold flex items-center gap-2 mb-8">
                    <i class="fa-solid fa-location-dot text-blue-500/50"></i> <?php echo htmlspecialchars($school['city']); ?>
                </p>
                
                <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                    <div>
                        <span class="block text-[10px] text-slate-400 font-bold mb-1 uppercase tracking-wider">Fees Start At</span>
                        <span class="text-blue-600 font-black text-xl"><?php echo msd_format_currency($school['fees_min']); ?> <span class="text-xs font-bold text-slate-400">/ yr</span></span>
                    </div>
                    <a href="school.php?id=<?php echo $school['id']; ?>" class="bg-slate-50 text-slate-900 font-black text-sm py-3 px-6 rounded-lg hover:bg-blue-600 hover:text-white transition-all">Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Simplified Journey Section -->
<section class="py-24 px-4 bg-slate-50 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-100/30 blur-[120px] rounded-full -translate-y-1/2 translate-x-1/2"></div>
    
    <div class="container mx-auto relative z-10 text-center">
        <div class="max-w-3xl mx-auto mb-16">
            <span class="text-blue-600 font-black tracking-widest text-[10px] uppercase mb-4 block">STREAMLINED PROCESS</span>
            <h2 class="text-3xl md:text-5xl font-black text-slate-900 mb-6 leading-tight">Finding the Perfect School <span class="text-blue-600">Made Easy!</span></h2>
            <p class="text-slate-500 text-lg font-medium opacity-80">Search, Compare, and Enroll within minutes through our verified platform.</p>
        </div>

        <div class="max-w-5xl mx-auto">
            <div class="rounded-[48px] overflow-hidden shadow-2xl border-8 border-white bg-white group p-4">
                <img src="assets/addiamge/image copy 4.png" class="w-full h-auto group-hover:scale-[1.02] transition-transform duration-700" alt="Journey Illustration">
            </div>
            
            <!-- Feature Highlights matching the image -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mt-16 px-4">
                <div class="group">
                    <div class="w-16 h-16 bg-indigo-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-indigo-500/20 group-hover:-rotate-6 transition-all">
                        <i class="fa-solid fa-layer-group text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-2">Compare & Choose</h3>
                    <p class="text-sm text-slate-500 font-medium">Side-by-side analysis of curriculum, fees, and safety.</p>
                </div>
                <div class="group">
                    <div class="w-16 h-16 bg-blue-500 text-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-blue-400/20 group-hover:rotate-6 transition-all">
                        <i class="fa-solid fa-star-half-stroke text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-2">Trusted Reviews</h3>
                    <p class="text-sm text-slate-500 font-medium">100% verified community feedback from real parents.</p>
                </div>
                <div class="group">
                    <div class="w-16 h-16 bg-orange-500 text-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/20 group-hover:-rotate-6 transition-all">
                        <i class="fa-solid fa-headset text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-2">Expert Counseling</h3>
                    <p class="text-sm text-slate-500 font-medium">Free expert guidance for your child's perfect school fit.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Admissions Assistance Banner -->
<section class="container mx-auto px-4 py-12">
    <div class="bg-[#1D4ED8] rounded-3xl overflow-hidden relative flex flex-col md:flex-row items-center justify-between text-white p-12 md:p-16">
        <div class="relative z-10 md:w-1/2">
            <span class="bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full text-[10px] font-black tracking-widest uppercase mb-6 inline-block">Limited Seats</span>
            <h2 class="text-3xl md:text-5xl font-black mb-6 leading-tight">
                Simplify Your School Admissions for 2026-27
            </h2>
            <p class="text-blue-100 text-lg mb-10 font-medium opacity-90 max-w-lg">
                Get expert counseling, priority school visits, and end-to-end application support from India's most trusted school discovery platform.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="#" class="bg-white text-blue-700 font-black py-4 px-10 rounded-xl shadow-lg hover:scale-105 transition-all text-lg">Apply Now</a>
                <a href="#" class="border-2 border-white/50 bg-white/10 backdrop-blur-sm text-white font-black py-4 px-10 rounded-xl hover:bg-white/20 transition-all text-lg">Speak to an expert</a>
            </div>
        </div>
        <div class="md:w-1/2 mt-12 md:mt-0 flex justify-end">
            <img src="assets/images/consultant.png" class="w-full max-w-md object-contain transform translate-y-8 md:translate-y-16" alt="Admission Expert">
        </div>
    </div>
</section>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stateSelect = document.getElementById('state');
            const districtSelect = document.getElementById('district');
            let locationData = null;

            // Fetch Indian states and districts from the same source as register/profile
            fetch('https://raw.githubusercontent.com/sab99r/Indian-States-And-Districts/master/states-and-districts.json')
                .then(response => response.json())
                .then(data => {
                    locationData = data.states;
                    locationData.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.state;
                        option.textContent = item.state;
                        stateSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching location data:', error));

            stateSelect.addEventListener('change', function() {
                districtSelect.innerHTML = '<option value="">Select City</option>';
                if (this.value && locationData) {
                    const state = locationData.find(s => s.state === this.value);
                    if (state && state.districts) {
                        state.districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district;
                            option.textContent = district;
                            districtSelect.appendChild(option);
                        });
                        districtSelect.disabled = false;
                    }
                } else {
                    districtSelect.disabled = true;
                }
            });
        });
    </script>
<?php require_once 'footer.php'; ?>