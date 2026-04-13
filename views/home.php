<?php
$extra_css = '<link rel="stylesheet" href="/MySchoolDesk/assets/css/home.css">';
require_once 'header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container animate-up">
        <h1 class="hero-title">Find the Perfect school for Your Child</h1>
        <p class="hero-subtitle">Search from thousands of schools across India and compare them side by side.</p>
        
        <!-- Search Widget -->
        <div class="search-widget">
            <form action="/MySchoolDesk/search" method="GET" class="search-form">
                <div class="search-group">
                    <label><i class="fa-solid fa-location-dot"></i> Location</label>
                    <input type="text" name="location" placeholder="e.g. Vadodara" value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>" required>
                </div>
                <div class="search-divider"></div>
                <div class="search-group">
                    <label><i class="fa-solid fa-book-open"></i> Class</label>
                    <select name="class">
                        <option value="">Any Class</option>
                        <option value="playgroup">Playgroup</option>
                        <option value="nursery">Nursery</option>
                        <option value="1">Class 1</option>
                        <option value="10">Class 10</option>
                    </select>
                </div>
                <div class="search-divider"></div>
                <div class="search-group border-none!">
                    <label><i class="fa-solid fa-graduation-cap"></i> Board</label>
                    <select name="board">
                        <option value="">Any Board</option>
                        <option value="cbse">CBSE</option>
                        <option value="icse">ICSE</option>
                        <option value="state">State Board</option>
                    </select>
                </div>
                <button type="submit" class="search-btn btn btn-primary">Search Schools</button>
            </form>
        </div>
    </div>
</section>

<!-- Featured Schools Section -->
<section class="container py-24 px-4 sm:px-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
        <div class="max-w-xl">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-4">Explore Top Rated Schools</h2>
            <p class="text-slate-600 font-medium text-lg leading-relaxed">Discover our hand-picked selection of institutions known for academic excellence and holistic development.</p>
        </div>
        <a href="/MySchoolDesk/search" class="group flex items-center gap-2 text-blue-600 font-bold text-lg hover:text-blue-700 transition-colors whitespace-nowrap">
            View All Schools <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>

    <!-- Premium School Cards -->
    <div class="schools-grid">
        <!-- School Card 1 -->
        <div class="school-card rounded-[28px]">
            <div class="card-img-wrapper">
                <img src="/myschooldesk/assets/images/school2.png" alt="St. Xavier's International">
                <div class="card-overlay"></div>
                <div class="verified-badge">
                    <i class="fa-solid fa-star text-[10px]"></i> VERIFIED
                </div>
                <div class="admissions-badge">
                    <i class="fa-solid fa-clock-rotate-left mr-1.5"></i> Admissions Open
                </div>
            </div>
            <div class="card-body">
                <div class="card-tags">
                    <span class="tag tag-blue">CBSE</span>
                    <span class="tag tag-indigo">Class 1-12</span>
                </div>
                <h3 class="card-title">St. Xavier's International</h3>
                <p class="card-location">
                    <i class="fa-solid fa-location-dot text-blue-500"></i> Gotri, Vadodara
                </p>
                <div class="card-footer">
                    <div class="fee-info">
                        <span class="fee-label">Annual Fees</span>
                        <span class="fee-value">₹45k - ₹85k</span>
                    </div>
                    <a href="/MySchoolDesk/school/st-xavier" class="action-btn">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- School Card 2 -->
        <div class="school-card rounded-[28px]">
            <div class="card-img-wrapper">
                <img src="/myschooldesk/assets/images/school1.png" alt="Bright Day School">
                <div class="card-overlay"></div>
                <div class="verified-badge">
                    <i class="fa-solid fa-star text-[10px]"></i> VERIFIED
                </div>
                <div class="admissions-badge">
                    <i class="fa-solid fa-clock-rotate-left mr-1.5"></i> Admissions Open
                </div>
            </div>
            <div class="card-body">
                <div class="card-tags">
                    <span class="tag tag-blue">ICSE</span>
                    <span class="tag tag-indigo">Pre-K - 10</span>
                </div>
                <h3 class="card-title">Bright Day School</h3>
                <p class="card-location">
                    <i class="fa-solid fa-location-dot text-blue-500"></i> Vasna Road, Vadodara
                </p>
                <div class="card-footer">
                    <div class="fee-info">
                        <span class="fee-label">Annual Fees</span>
                        <span class="fee-value">₹35k - ₹75k</span>
                    </div>
                    <a href="/MySchoolDesk/school/bright-day" class="action-btn">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- School Card 3 (Promotional/Quick Compare) -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[28px] p-10 flex flex-col justify-between shadow-xl relative overflow-hidden group">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all"></div>
            <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all"></div>
            
            <div class="relative">
                <div class="h-14 w-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-8 border border-white/30 shadow-inner">
                    <i class="fa-solid fa-shuffle text-white text-2xl"></i>
                </div>
                <h3 class="text-3xl font-black text-white leading-tight mb-4">Can't decide which school?</h3>
                <p class="text-blue-100 font-medium text-lg">Use our smart comparison engine to find the perfect fit for your child's needs.</p>
            </div>
            
            <a href="/MySchoolDesk/compare" class="relative bg-white text-blue-700 font-black py-4 px-8 rounded-2xl text-center shadow-lg hover:shadow-2xl hover:scale-[1.02] active:scale-95 transition-all">
                Compare Schools Now
            </a>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>