<?php
// compare.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'includes/common.php';

$ids_str = trim($_GET['ids'] ?? '');
$school_ids = array_filter(array_map('intval', explode(',', $ids_str)));
$schools = [];

if (!empty($school_ids)) {
    // Limit to 3 schools for readability
    $ids_placeholders = implode(',', array_fill(0, count($school_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM schools WHERE id IN ($ids_placeholders) LIMIT 3");
    $stmt->execute($school_ids);
    $schools = $stmt->fetchAll();
}

$extra_css = '<link rel="stylesheet" href="assets/css/home.css">';
require_once 'header.php';

// Facility mapping for comparison
$facilities_to_compare = [
    'Play Area' => 'fa-child-reaching',
    'Transport' => 'fa-bus',
    'AC Classrooms' => 'fa-snowflake',
    'Science Lab' => 'fa-flask-vial',
    'Library' => 'fa-book-open'
];
?>

<div class="bg-gradient-to-br from-[#F8FAFF] to-[#E0E9FF] min-h-screen py-10 px-4">
    <div class="container mx-auto">
        
        <?php if (empty($schools)): ?>
            <!-- Empty State -->
            <div class="max-w-2xl mx-auto bg-white/70 backdrop-blur-2xl rounded-[48px] p-10 text-center shadow-2xl border border-white/50 animate-fade-in">
                <div class="w-24 h-24 bg-blue-600/10 rounded-[32px] flex items-center justify-center mx-auto mb-8 group">
                    <i class="fa-solid fa-code-compare text-4xl text-blue-600 group-hover:rotate-12 transition-transform duration-500"></i>
                </div>
                <h1 class="text-3xl font-black text-slate-900 mb-4">Compare Schools</h1>
                <p class="text-slate-500 text-base font-medium mb-8 leading-relaxed">Select up to 3 schools from the discovery page to compare their features side-by-side.</p>
                <a href="search.php" class="inline-flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white font-black py-5 px-12 rounded-2xl shadow-xl shadow-blue-500/20 transition-all hover:scale-105 active:scale-95 group">
                    <span class="uppercase tracking-widest text-xs">Browse Schools</span>
                    <i class="fa-solid fa-arrow-right-long group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        <?php else: ?>
            <!-- Comparison Header -->
<div class="flex items-center justify-center gap-6 mb-8 animate-fade-in relative">
                <a href="search.php" class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/50 hover:bg-white text-slate-600 font-black px-6 py-3 rounded-2xl border border-white/50 text-[10px] uppercase tracking-widest transition-all hover:scale-105 active:scale-95 flex items-center gap-2 group">
                    <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                    <span>Back to Search</span>
                </a>

                <div class="text-center">
                    <span class="inline-block bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-lg uppercase tracking-widest mb-6">Side-by-Side</span>
                    <h1 class="text-4xl md:text-5xl font-black text-slate-900 leading-tight">
                        Making the <span class="text-blue-600 italic">Right</span> Choice.
                    </h1>
                </div>

                <?php if (count($schools) < 3): ?>
                <a href="search.php" class="absolute right-0 top-1/2 -translate-y-1/2 bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-3 rounded-2xl shadow-xl shadow-blue-500/20 text-[10px] uppercase tracking-widest transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Compare More</span>
                </a>
                <?php endif; ?>
            </div>

            <!-- Comparison Table -->
            <div class="bg-white/70 backdrop-blur-2xl rounded-[40px] shadow-2xl border border-white/50 overflow-hidden animate-fade-in-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="p-4 px-6 w-64 border-r border-slate-100/50">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Specifications</span>
                                </th>
                                <?php foreach ($schools as $school): ?>
                                    <th class="p-4 px-6 min-w-[260px] border-r border-slate-100/50 last:border-r-0">
                                        <div class="flex flex-col items-center text-center">
                                            <div class="w-16 h-16 rounded-2xl bg-slate-100 overflow-hidden mb-2 shadow-inner">
                                                <?php 
                                                    $photos = json_decode($school['photos'] ?? '[]', true);
                                                    $school_img = !empty($photos) ? $photos[0] : 'assets/images/school1.png';
                                                ?>
                                                <img src="<?php echo htmlspecialchars($school_img); ?>" class="w-full h-full object-cover">
                                            </div>
                                            <h3 class="text-lg font-black text-slate-900 mb-1 line-clamp-1"><?php echo htmlspecialchars($school['name']); ?></h3>
                                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5 justify-center">
                                                <i class="fa-solid fa-location-dot text-blue-500/50"></i> <?php echo htmlspecialchars($school['city']); ?>
                                            </p>
                                        </div>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/50">
                            <!-- Board -->
                            <tr class="group hover:bg-slate-50/30 transition-colors">
                                <td class="p-4 px-6 border-r border-slate-100/50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-sm shadow-sm group-hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-graduation-cap"></i>
                                        </div>
                                        <span class="text-sm font-black text-slate-700">Board</span>
                                    </div>
                                </td>
                                <?php foreach ($schools as $school): ?>
                                    <td class="p-4 px-6 border-r border-slate-100/50 last:border-r-0">
                                        <span class="bg-blue-600/10 text-blue-600 text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-widest"><?php echo htmlspecialchars($school['board']); ?></span>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Classes -->
                            <tr class="group hover:bg-slate-50/30 transition-colors">
                                <td class="p-4 px-6 border-r border-slate-100/50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-sm shadow-sm group-hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-user-graduate"></i>
                                        </div>
                                        <span class="text-sm font-black text-slate-700">Classes</span>
                                    </div>
                                </td>
                                <?php foreach ($schools as $school): ?>
                                    <td class="p-4 px-6 border-r border-slate-100/50 last:border-r-0">
                                        <span class="text-sm font-bold text-slate-600"><?php echo htmlspecialchars($school['classes_offered'] ?: 'Nursery - 12th'); ?></span>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Annual Fees -->
                            <tr class="group hover:bg-slate-50/30 transition-colors">
                                <td class="p-4 px-6 border-r border-slate-100/50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-sm shadow-sm group-hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-wallet"></i>
                                        </div>
                                        <span class="text-sm font-black text-slate-700">Annual Fees</span>
                                    </div>
                                </td>
                                <?php foreach ($schools as $school): ?>
                                    <td class="p-4 px-6 border-r border-slate-100/50 last:border-r-0">
                                        <span class="text-lg font-black text-slate-900"><?php echo msd_format_currency($school['fees_min']); ?> - <?php echo msd_format_currency($school['fees_max']); ?></span>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Parent Rating -->
                            <tr class="group hover:bg-slate-50/30 transition-colors">
                                <td class="p-4 px-6 border-r border-slate-100/50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center text-sm shadow-sm group-hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-star"></i>
                                        </div>
                                        <span class="text-sm font-black text-slate-700">Parent Rating</span>
                                    </div>
                                </td>
                                <?php foreach ($schools as $school): ?>
                                    <td class="p-4 px-6 border-r border-slate-100/50 last:border-r-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-black text-slate-900"><?php echo htmlspecialchars($school['view_rating'] ?? '4.8'); ?></span>
                                            <div class="flex text-[10px] text-amber-400">
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                            </div>
                                        </div>
                                    </td>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Facilities -->
                            <?php foreach ($facilities_to_compare as $fac_name => $fac_icon): ?>
                            <tr class="group hover:bg-slate-50/30 transition-colors">
                                <td class="p-4 px-6 border-r border-slate-100/50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center text-sm shadow-sm group-hover:scale-110 transition-transform">
                                            <i class="fa-solid <?php echo $fac_icon; ?>"></i>
                                        </div>
                                        <span class="text-sm font-black text-slate-700"><?php echo $fac_name; ?></span>
                                    </div>
                                </td>
                                <?php foreach ($schools as $school): 
                                    $has_fac = str_contains(strtolower($school['facilities'] ?? ''), strtolower($fac_name));
                                ?>
                                    <td class="p-4 px-6 border-r border-slate-100/50 last:border-r-0">
                                        <?php if ($has_fac): ?>
                                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs">
                                                <i class="fa-solid fa-check"></i>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center text-[10px]">
                                                <i class="fa-solid fa-xmark"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>

                            <!-- Action Button Row -->
                            <tr>
                                <td class="p-4 px-6 border-r border-slate-100/50"></td>
                                <?php foreach ($schools as $school): ?>
                                    <td class="p-4 px-6 border-r border-slate-100/50 last:border-r-0">
                                        <a href="school.php?id=<?php echo $school['id']; ?>" class="w-max mx-auto bg-slate-900 hover:bg-black text-white font-black py-2.5 px-6 rounded-lg flex items-center justify-center text-[10px] uppercase tracking-widest shadow-lg transition-all hover:scale-[1.02] active:scale-95">
                                            Enquire Now
                                        </a>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once 'footer.php'; ?>
