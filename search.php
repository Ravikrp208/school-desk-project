<?php
// search.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'includes/common.php';

$extra_css = '<link rel="stylesheet" href="assets/css/home.css">';
require_once 'header.php';

$location = trim($_GET['location'] ?? '');
$state = trim($_GET['state'] ?? '');
$keyword = trim($_GET['keyword'] ?? '');
$class = trim($_GET['class'] ?? '');
$board = trim($_GET['board'] ?? '');
$facilities_query = $_GET['facilities'] ?? [];
$max_fee = isset($_GET['max_fee']) ? (int) $_GET['max_fee'] : 500000;
$sort = $_GET['sort'] ?? 'popular';

// Base query
// Show active schools by default
$sql = 'SELECT * FROM schools WHERE status IN ("active", "approved")';
$params = [];

if ($location !== '') {
    $sql .= ' AND (city LIKE :loc1 OR address LIKE :loc2 OR district LIKE :loc3 OR state LIKE :loc4)';
    $params['loc1'] = '%' . $location . '%';
    $params['loc2'] = '%' . $location . '%';
    $params['loc3'] = '%' . $location . '%';
    $params['loc4'] = '%' . $location . '%';
}
if ($state !== '') {
    $sql .= ' AND state LIKE :state';
    $params['state'] = '%' . $state . '%';
}
if ($class !== '') {
    $sql .= ' AND classes_offered LIKE :class';
    $params['class'] = '%' . $class . '%';
}
if ($board !== '') {
    $sql .= ' AND board LIKE :board';
    $params['board'] = '%' . $board . '%';
}

// Fees filter
if ($max_fee < 500000) {
    $sql .= ' AND fees_min <= :max_fee';
    $params['max_fee'] = $max_fee;
}

// Facilities Filter (AND logic)
if (!empty($facilities_query)) {
    foreach ($facilities_query as $idx => $fac) {
        $key = "fac_" . $idx;
        $sql .= " AND facilities LIKE :$key";
        $params[$key] = '%' . trim($fac) . '%';
    }
}

// Sorting logic
switch ($sort) {
    case 'fee_asc':
        $sql_order = ' ORDER BY fees_min ASC, is_featured DESC, is_verified DESC';
        break;
    case 'rating_desc':
        $sql_order = ' ORDER BY view_rating DESC, is_featured DESC, created_at DESC';
        break;
    case 'popular':
    default:
        $sql_order = ' ORDER BY is_featured DESC, is_verified DESC, created_at DESC';
        break;
}
$stmt = $pdo->prepare($sql . $sql_order);
$stmt->execute($params);
$schools = $stmt->fetchAll();

// Fallback: If no results found, show all schools but with a message
$is_fallback = false;
if (empty($schools) && !empty($params)) {
    $stmt = $pdo->prepare('SELECT * FROM schools WHERE status IN ("active", "approved")' . $sql_order);
    $stmt->execute();
    $schools = $stmt->fetchAll();
    $is_fallback = true;
}
?>

<div class="bg-[#F8FAFC] min-h-screen">
    <!-- Search Results Content -->
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- Left Sidebar: Filters (Desktop) -->
            <aside class="hidden lg:block w-72 shrink-0">
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100 sticky top-24">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-lg font-black text-slate-900">Filters</h2>
                        <a href="search.php" class="text-xs font-bold text-blue-600 hover:underline">Clear all</a>
                    </div>

                    <form action="search.php" method="GET" class="space-y-8">

                        <!-- Location Filter -->
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">LOCATION</label>
                            <div class="space-y-3">
                                <div class="relative group">
                                    <label
                                        class="absolute left-4 top-2 text-[8px] font-black text-blue-600/40 uppercase tracking-widest transition-all">State</label>
                                    <select id="state-sidebar" name="state"
                                        class="w-full bg-slate-50 border-none rounded-xl px-4 pt-5 pb-2 text-xs font-black text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 appearance-none cursor-pointer">
                                        <option value="">Select State</option>
                                    </select>
                                    <i
                                        class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
                                </div>
                                <div class="relative group">
                                    <label
                                        class="absolute left-4 top-2 text-[8px] font-black text-blue-600/40 uppercase tracking-widest transition-all">City
                                        / District</label>
                                    <select id="district-sidebar" name="location" disabled
                                        class="w-full bg-slate-50 border-none rounded-xl px-4 pt-5 pb-2 text-xs font-black text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 appearance-none cursor-pointer disabled:opacity-50">
                                        <option value="">Select City</option>
                                    </select>
                                    <i
                                        class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Board Filter -->
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">BOARD</label>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach (msd_board_options() as $b): ?>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="board" value="<?php echo strtolower($b); ?>"
                                            class="hidden peer" <?php echo strtolower($board) === strtolower($b) ? 'checked' : ''; ?> onchange="this.form.submit()">
                                        <div
                                            class="px-4 py-2 rounded-lg border border-slate-100 bg-white text-[10px] font-black text-slate-500 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition-all">
                                            <?php echo $b; ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Annual Fees Range -->
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">ANNUAL
                                FEES (MAX: <span id="fee-val"
                                    class="text-blue-600">₹<?php echo $max_fee / 1000; ?>k</span>)</label>
                            <input type="range" name="max_fee" min="50000" max="500000" step="10000"
                                value="<?php echo $max_fee; ?>"
                                class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-blue-600"
                                oninput="document.getElementById('fee-val').innerText = '₹' + (this.value/1000) + 'k'"
                                onchange="this.form.submit()">
                            <div class="flex justify-between mt-2">
                                <span class="text-[10px] font-bold text-slate-400">₹50k</span>
                                <span class="text-[10px] font-bold text-slate-400">₹5L+</span>
                            </div>
                        </div>

                        <!-- Facilities Filter -->
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">FACILITIES</label>
                            <div class="space-y-3">
                                <?php foreach (msd_facility_options() as $f => $icon): ?>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="facilities[]" value="<?php echo $f; ?>"
                                            class="w-5 h-5 rounded-md border-slate-200 text-blue-600 focus:ring-blue-500/20"
                                            <?php echo in_array($f, $facilities_query) ? 'checked' : ''; ?>
                                            onchange="this.form.submit()">
                                        <span
                                            class="text-sm font-bold text-slate-600 group-hover:text-slate-900"><?php echo $f; ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Apply Button (Alternative to auto-submit) -->
                        <button type="submit"
                            class="w-full bg-slate-900 text-white font-black py-4 rounded-xl text-[10px] uppercase tracking-widest hover:bg-black transition-all">
                            Apply Filters
                        </button>

                    </form>
                </div>
            </aside>

            <!-- Main Content: Schools List -->
            <main class="flex-1">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div>
                        <span
                            class="text-blue-600 font-black tracking-widest text-[10px] uppercase block mb-1">DISCOVERY</span>
                        <h1 class="text-3xl font-black text-slate-900"><?php echo count($schools); ?> Schools
                            <?php echo $location ? "in " . htmlspecialchars($location) : "Found"; ?></h1>
                        <?php if ($is_fallback): ?>
                            <p class="text-xs font-black text-amber-600 bg-amber-50 px-3 py-1 rounded-lg inline-block mt-2">
                                <i class="fa-solid fa-circle-info mr-1"></i> No schools found for your search. Showing all available schools instead.
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Mobile Filter Toggle Button -->
                    <div class="lg:hidden">
                        <button onclick="toggleMobileFilter()"
                            class="flex items-center gap-3 bg-white border border-slate-200 px-6 py-3 rounded-2xl shadow-sm hover:shadow-md transition-all active:scale-95 group">
                            <div
                                class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white text-xs">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <span class="text-sm font-black text-slate-800 tracking-tight">Filter Results</span>
                            <?php if (!empty($facilities_query) || $board !== '' || $max_fee < 500000): ?>
                                <span
                                    class="bg-blue-600 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center">
                                    <?php echo (int) ($board !== '') + (int) ($max_fee < 500000) + count($facilities_query); ?>
                                </span>
                            <?php endif; ?>
                        </button>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold text-slate-400">Sort by:</span>
                        <div class="relative min-w-[140px]">
                            <select id="sort-select" name="sort" onchange="updateSort(this.value)"
                                class="w-full bg-white border border-slate-100 rounded-lg px-4 py-2 pr-8 text-xs font-black text-slate-700 focus:ring-2 focus:ring-blue-500/10 cursor-pointer appearance-none !bg-none">
                                <option value="popular" <?php echo $sort == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                                <option value="fees_low" <?php echo $sort == 'fees_low' ? 'selected' : ''; ?>>Fees: Low to High</option>
                                <option value="fees_high" <?php echo $sort == 'fees_high' ? 'selected' : ''; ?>>Fees: High to Low</option>
                                <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Top Rated</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <!-- Schools Horizontal Cards -->
                <div class="space-y-3">
                    <?php if (empty($schools)): ?>
                        <div class="bg-white rounded-3xl p-20 border border-slate-100 shadow-sm text-center">
                            <div class="w-24 h-24 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <i class="fa-solid fa-graduation-cap text-slate-300 text-4xl"></i>
                            </div>
                            <h2 class="text-3xl font-black text-slate-900 mb-2">No Schools Found</h2>
                            <p class="text-slate-500 max-w-md mx-auto">We couldn't find any schools matching your filters.
                            </p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($schools as $school): ?>
                            <div
                                class="bg-white rounded-[24px] overflow-hidden border border-slate-100 shadow-sm hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] transition-all duration-500 group flex flex-col md:flex-row md:min-h-[200px]">
                                <!-- Left side: Image -->
                                <div class="w-full md:w-[200px] shrink-0 relative overflow-hidden p-2 h-48 md:h-full">
                                    <div class="w-full h-full rounded-2xl overflow-hidden relative bg-slate-100">
                                        <?php
                                        $photos = json_decode($school['photos'] ?? '[]', true);
                                        $school_img = !empty($photos) ? $photos[0] : 'assets/images/school' . rand(1, 2) . '.png';
                                        ?>
                                        <a href="school.php?id=<?php echo $school['id']; ?>" class="block w-full h-full">
                                            <img src="<?php echo htmlspecialchars($school_img); ?>"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                        </a>
                                        <?php if ($school['is_verified']): ?>
                                            <div
                                                class="absolute top-4 left-4 bg-white shadow-xl px-3 py-1.5 rounded-lg border border-white/50 flex items-center gap-2">
                                                <div
                                                    class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center text-white text-[10px]">
                                                    <i class="fa-solid fa-check"></i>
                                                </div>
                                                <span
                                                    class="text-[9px] font-black text-slate-900 uppercase tracking-widest">VERIFIED</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Right side: Content -->
                                <div class="flex-1 p-4 pb-4 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <div class="flex gap-2 mb-2">
                                                    <span
                                                        class="bg-blue-600/10 text-blue-600 text-[8px] font-black px-2 py-1 rounded-md uppercase tracking-wider"><?php echo htmlspecialchars($school['board']); ?></span>
                                                    <span
                                                        class="bg-blue-600/10 text-blue-600 text-[8px] font-black px-2 py-1 rounded-md uppercase tracking-wider">CO-ED</span>
                                                </div>
                                                <a href="school.php?id=<?php echo $school['id']; ?>" class="block group/title">
                                                    <h3
                                                        class="text-2xl font-black text-slate-900 group-hover/title:text-blue-600 transition-colors">
                                                        <?php echo htmlspecialchars($school['name']); ?></h3>
                                                </a>
                                                <p class="text-slate-400 text-sm font-bold flex items-center gap-2 mt-1">
                                                    <i class="fa-solid fa-location-dot text-blue-500/50"></i>
                                                    <?php echo htmlspecialchars(($school['city'] ? $school['city'] . ', ' : '') . ($school['district'] ? $school['district'] . ', ' : '') . $school['state']); ?>
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <span
                                                    class="block text-[8px] text-slate-400 font-black uppercase tracking-widest mb-1">ANNUAL
                                                    FEES</span>
                                                <span
                                                    class="text-2xl font-black text-slate-900"><?php echo msd_format_currency($school['fees_min']); ?>
                                                    - <?php echo msd_format_currency($school['fees_max']); ?></span>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap gap-6 mt-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-blue-600 text-sm">
                                                    <i class="fa-solid fa-user-graduate"></i>
                                                </div>
                                                <div>
                                                    <span
                                                        class="block text-[8px] text-slate-400 font-black uppercase tracking-widest">CLASSES</span>
                                                    <span
                                                        class="text-[10px] font-black text-slate-700"><?php echo htmlspecialchars($school['classes_offered'] ?: 'Nursery - Grade 12'); ?></span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-blue-600 text-sm">
                                                    <i class="fa-solid fa-star text-amber-400"></i>
                                                </div>
                                                <div>
                                                    <span
                                                        class="block text-[8px] text-slate-400 font-black uppercase tracking-widest">RATING</span>
                                                    <span
                                                        class="text-[10px] font-black text-slate-700"><?php echo htmlspecialchars($school['view_rating'] ?? '4.8'); ?>
                                                        (<?php echo htmlspecialchars($school['view_reviews_count'] ?? '120'); ?>+
                                                        reviews)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 mt-4 pt-4 pb-1 border-t border-slate-50">
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer group/comp">
                                                <div class="relative flex items-center justify-center">
                                                    <input type="checkbox" 
                                                           class="compare-checkbox w-5 h-5 rounded-lg border-2 border-slate-200 text-blue-600 focus:ring-blue-500/20 transition-all cursor-pointer peer checked:border-blue-600" 
                                                           data-id="<?php echo $school['id']; ?>"
                                                           data-name="<?php echo htmlspecialchars($school['name']); ?>"
                                                           onclick="toggleCompare(this)">
                                                    <i class="fa-solid fa-check absolute text-[10px] text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                                </div>
                                                <span class="text-[10px] font-black text-slate-400 group-hover/comp:text-blue-600 transition-colors uppercase tracking-widest">Compare</span>
                                            </label>
                                        </div>

                                        <div class="flex-1"></div>

                                        <a href="school.php?id=<?php echo $school['id']; ?>"
                                            class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black py-2 px-5 rounded-lg shadow-lg shadow-blue-600/20 hover:shadow-blue-600/40 transition-all duration-300 h-10 flex items-center justify-center hover:scale-105 active:scale-95 whitespace-nowrap text-xs">
                                            Send Enquiry
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>


                </div>
            </main>
        </div>
    </div>
</div>

<!-- Mobile Filter Modal -->
<div id="mobile-filter-modal" class="fixed inset-0 z-[2000] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleMobileFilter()"></div>

    <!-- Modal Content -->
    <div class="absolute bottom-0 left-0 right-0 max-h-[90vh] bg-white rounded-t-[32px] shadow-2xl overflow-hidden flex flex-col translate-y-full transition-transform duration-300"
        id="filter-panel">
        <!-- Close Handle/Header -->
        <div class="p-4 flex items-center justify-between border-b border-slate-50 shrink-0">
            <div class="w-12 h-1.5 bg-slate-100 rounded-full mx-auto absolute left-1/2 -translate-x-1/2 top-3"></div>
            <h2 class="text-xl font-black text-slate-900 mt-2">Filters</h2>
            <button onclick="toggleMobileFilter()"
                class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors mt-2">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Filter Form Content -->
        <div class="flex-1 overflow-y-auto p-6 pb-24">
            <form action="search.php" method="GET" class="space-y-8">
                <!-- Location Filter (Mobile) -->
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">LOCATION</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative group">
                            <label
                                class="absolute left-4 top-2 text-[8px] font-black text-blue-600/40 uppercase tracking-widest transition-all">State</label>
                            <select id="state-mobile" name="state"
                                class="w-full bg-slate-50 border-none rounded-xl px-4 pt-5 pb-2 text-xs font-black text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 appearance-none !bg-none cursor-pointer">
                                <option value="">Select State</option>
                            </select>
                            <i
                                class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
                        </div>
                        <div class="relative group">
                            <label
                                class="absolute left-4 top-2 text-[8px] font-black text-blue-600/40 uppercase tracking-widest transition-all">City
                                / District</label>
                            <select id="district-mobile" name="location" disabled
                                class="w-full bg-slate-50 border-none rounded-xl px-4 pt-5 pb-2 text-xs font-black text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 appearance-none !bg-none cursor-pointer disabled:opacity-50">
                                <option value="">Select City</option>
                            </select>
                            <i
                                class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <!-- Board Filter -->
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">BOARD</label>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach (msd_board_options() as $b): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="board" value="<?php echo strtolower($b); ?>" class="hidden peer"
                                    <?php echo strtolower($board) === strtolower($b) ? 'checked' : ''; ?>>
                                <div
                                    class="px-4 py-2 rounded-lg border border-slate-100 bg-white text-[10px] font-black text-slate-500 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 transition-all">
                                    <?php echo $b; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Annual Fees Range -->
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">ANNUAL
                        FEES (MAX: <span id="fee-val-mob"
                            class="text-blue-600">₹<?php echo $max_fee / 1000; ?>k</span>)</label>
                    <input type="range" name="max_fee" min="50000" max="500000" step="10000"
                        value="<?php echo $max_fee; ?>"
                        class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-blue-600"
                        oninput="document.getElementById('fee-val-mob').innerText = '₹' + (this.value/1000) + 'k'">
                    <div class="flex justify-between mt-2">
                        <span class="text-[10px] font-bold text-slate-400">₹50k</span>
                        <span class="text-[10px] font-bold text-slate-400">₹5L+</span>
                    </div>
                </div>

                <!-- Facilities Filter -->
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">FACILITIES</label>
                    <div class="space-y-3">
                        <?php foreach (msd_facility_options() as $f => $icon): ?>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="facilities[]" value="<?php echo $f; ?>"
                                    class="w-5 h-5 rounded-md border-slate-200 text-blue-600 focus:ring-blue-500/20" <?php echo in_array($f, $facilities_query) ? 'checked' : ''; ?>>
                                <span
                                    class="text-sm font-bold text-slate-600 group-hover:text-slate-900"><?php echo $f; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Footer Buttons (Fixed in Modal) -->
                <div class="fixed bottom-0 left-0 right-0 p-6 bg-white border-t border-slate-50 flex gap-4">
                    <a href="search.php"
                        class="flex-1 bg-slate-100 text-slate-600 font-black py-4 rounded-xl text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all text-center">
                        Clear all
                    </a>
                    <button type="submit"
                        class="flex-[2] bg-blue-600 text-white font-black py-4 rounded-xl text-[10px] uppercase tracking-widest shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
<!-- Floating Comparison Bar -->
<div id="compare-bar" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[1000] hidden animate-fade-in-up">
    <div class="bg-slate-900/90 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-2xl flex items-center gap-6 min-w-[320px]">
        <div class="flex items-center gap-4 border-r border-white/10 pr-6">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-code-compare text-lg"></i>
            </div>
            <div>
                <span id="compare-count" class="block text-lg font-black text-white leading-none">0 Schools</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Selected to compare</span>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="clearComparison()" class="p-4 text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-trash-can text-sm"></i>
            </button>
            <a href="javascript:void(0)" onclick="compareSchools()" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-4 px-8 rounded-xl text-[10px] uppercase tracking-widest shadow-lg shadow-blue-500/20 transition-all hover:scale-105 active:scale-95 whitespace-nowrap">
                Compare Now
            </a>
        </div>
    </div>
</div>

<script>
    function updateSort(sortValue) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortValue);
        window.location.href = url.toString();
    }

    let compareIds = JSON.parse(localStorage.getItem('msd_compare_ids') || '[]');

    function toggleCompare(checkbox) {
        const id = parseInt(checkbox.dataset.id);
        if (checkbox.checked) {
            if (compareIds.length >= 3) {
                alert('You can only compare up to 3 schools at a time.');
                checkbox.checked = false;
                return;
            }
            if (!compareIds.includes(id)) {
                compareIds.push(id);
            }
        } else {
            compareIds = compareIds.filter(cid => cid !== id);
        }

        localStorage.setItem('msd_compare_ids', JSON.stringify(compareIds));
        
        // Immediate redirect to compare page with all selected IDs
        if (compareIds.length > 0) {
            window.location.href = 'compare.php?ids=' + compareIds.join(',');
        } else {
            updateCompareUI();
        }
    }

    function updateCompareUI() {
        const bar = document.getElementById('compare-bar');
        const countText = document.getElementById('compare-count');
        countText.innerText = compareIds.length + (compareIds.length === 1 ? ' School' : ' Schools');
        if (compareIds.length > 0) bar.classList.remove('hidden');
        else bar.classList.add('hidden');
        document.querySelectorAll('.compare-checkbox').forEach(cb => {
            cb.checked = compareIds.includes(parseInt(cb.dataset.id));
        });
    }

    function clearComparison() {
        compareIds = [];
        localStorage.setItem('msd_compare_ids', '[]');
        updateCompareUI();
    }

    function compareSchools() {
        if (compareIds.length < 2) {
            alert('Please select at least 2 schools to compare.');
            return;
        }
        window.location.href = 'compare.php?ids=' + compareIds.join(',');
    }

    function toggleMobileFilter() {
        const modal = document.getElementById('mobile-filter-modal');
        const panel = document.getElementById('filter-panel');

        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scroll
            setTimeout(() => {
                panel.classList.remove('translate-y-full');
            }, 10);
        } else {
            panel.classList.add('translate-y-full');
            document.body.style.overflow = ''; // Restore scroll
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        updateCompareUI();
        const stateSidebar = document.getElementById('state-sidebar');
        const districtSidebar = document.getElementById('district-sidebar');
        const stateMobile = document.getElementById('state-mobile');
        const districtMobile = document.getElementById('district-mobile');

        let locationData = null;
        const currentState = "<?php echo $state; ?>";
        const currentCity = "<?php echo $location; ?>";

        function populateDistricts(stateName, districtElem, selectedCity = "") {
            districtElem.innerHTML = '<option value="">Select City</option>';
            if (stateName && locationData) {
                const stateObj = locationData.find(s => s.state === stateName);
                if (stateObj && stateObj.districts) {
                    stateObj.districts.forEach(d => {
                        const option = document.createElement('option');
                        option.value = d;
                        option.textContent = d;
                        if (d === selectedCity) option.selected = true;
                        districtElem.appendChild(option);
                    });
                    districtElem.disabled = false;
                }
            } else {
                districtElem.disabled = true;
            }
        }

        fetch('https://raw.githubusercontent.com/sab99r/Indian-States-And-Districts/master/states-and-districts.json')
            .then(res => res.json())
            .then(data => {
                locationData = data.states;
                locationData.forEach(item => {
                    const optSidebar = document.createElement('option');
                    optSidebar.value = item.state;
                    optSidebar.textContent = item.state;
                    if (item.state === currentState) optSidebar.selected = true;
                    stateSidebar.appendChild(optSidebar);

                    const optMobile = document.createElement('option');
                    optMobile.value = item.state;
                    optMobile.textContent = item.state;
                    if (item.state === currentState) optMobile.selected = true;
                    stateMobile.appendChild(optMobile);
                });

                if (currentState) {
                    populateDistricts(currentState, districtSidebar, currentCity);
                    populateDistricts(currentState, districtMobile, currentCity);
                }
            });

        stateSidebar.addEventListener('change', function () {
            populateDistricts(this.value, districtSidebar);
        });

        stateMobile.addEventListener('change', function () {
            populateDistricts(this.value, districtMobile);
        });
    });
</script>

<?php require_once 'footer.php'; ?>