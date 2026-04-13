<?php
$extra_css = '<link rel="stylesheet" href="/myschooldesk/assets/css/home.css">';
require_once 'includes/common.php';

$slug = trim($_GET['slug'] ?? '');
$school = null;
if ($slug !== '') {
    $stmt = $pdo->prepare('SELECT * FROM schools WHERE slug = :slug LIMIT 1');
    $stmt->execute(['slug' => $slug]);
    $school = $stmt->fetch();
}

if (!$school) {
    http_response_code(404);
    require_once 'header.php';
    echo '<section class="container" style="padding-top:100px;padding-bottom:80px;"><h1>School not found</h1><a href="/myschooldesk/search">Back to search</a></section>';
    require_once 'footer.php';
    return;
}

$relatedSchoolsStmt = $pdo->prepare(
    'SELECT id, name FROM schools WHERE status = "approved" AND city = :city ORDER BY is_verified DESC, sort_order ASC, created_at DESC LIMIT 20'
);
$relatedSchoolsStmt->execute(['city' => $school['city']]);
$relatedSchools = $relatedSchoolsStmt->fetchAll();

$photos = [];
if (!empty($school['photos'])) {
    $decoded = json_decode($school['photos'], true);
    if (is_array($decoded)) {
        $photos = $decoded;
    }
}
require_once 'header.php';
?>

<!-- School Header Section -->
<section class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white py-20 text-center relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('/MySchoolDesk/assets/images/hero_comparison.jpg')] bg-cover bg-center mix-blend-overlay"></div>
    <div class="container mx-auto px-4 relative z-10">
        <?php if ((int)$school['is_verified'] === 1): ?>
            <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4 inline-block shadow-sm">
                Verified Listing
            </span>
        <?php endif; ?>
        <h1 class="text-3xl md:text-5xl font-bold mb-4"><?php echo htmlspecialchars($school['name']); ?></h1>
        <p class="text-lg text-blue-100 max-w-2xl mx-auto">
            <i class="fa-solid fa-map-marker-alt mr-2 text-amber-400"></i>
            <?php echo htmlspecialchars(trim(($school['address'] ?? '') . ', ' . ($school['city'] ?? ''))); ?>
        </p>
    </div>
</section>

<!-- Detailed Mock Content Section -->
<section class="container mx-auto -mt-10 mb-20 px-4 relative z-10">
    <!-- Two Column Layout -->
    <div class="flex flex-col lg:flex-row gap-6 md:gap-8">
        
        <!-- Left Column: School Details (About, Facilities, Gallery) -->
        <div class="lg:w-2/3">
            <div class="bg-white rounded-2xl p-6 md:p-8 border border-gray-100 shadow-[0_15px_40px_-15px_rgba(0,0,0,0.1)] mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">About <?php echo htmlspecialchars($school['name']); ?></h2>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    <?php echo htmlspecialchars($school['description'] ?: 'School details will be updated soon.'); ?>
                </p>

                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">School Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-gray-700">
                    <div><strong>Board:</strong> <?php echo htmlspecialchars($school['board'] ?: '-'); ?></div>
                    <div><strong>Classes:</strong> <?php echo htmlspecialchars($school['classes_offered'] ?: '-'); ?></div>
                    <div><strong>Fees:</strong> Rs. <?php echo htmlspecialchars($school['fees_min'] ?: '0'); ?> - Rs. <?php echo htmlspecialchars($school['fees_max'] ?: '0'); ?></div>
                    <div><strong>Teachers:</strong> <?php echo (int)($school['teachers_strength'] ?? 0); ?></div>
                    <div><strong>Min Qualification:</strong> <?php echo htmlspecialchars($school['teacher_min_qual'] ?: '-'); ?></div>
                    <div><strong>Max Qualification:</strong> <?php echo htmlspecialchars($school['teacher_max_qual'] ?: '-'); ?></div>
                    <div><strong>Email:</strong> <?php echo htmlspecialchars($school['contact_email'] ?: '-'); ?></div>
                    <div><strong>Phone:</strong> <?php echo htmlspecialchars($school['contact_phone'] ?: '-'); ?></div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Key Facilities</h3>
                <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($school['facilities'] ?: 'Facilities will be updated soon.'); ?></p>

                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Photo Gallery</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 md:gap-4">
                    <?php if (!empty($photos)): ?>
                        <?php foreach ($photos as $photo): ?>
                            <div class="h-32 bg-gray-200 rounded-xl overflow-hidden">
                                <img src="<?php echo htmlspecialchars($photo); ?>" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300" alt="School photo">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="h-32 bg-gray-200 rounded-xl overflow-hidden">
                            <img src="/myschooldesk/assets/images/school1.png" class="w-full h-full object-cover" alt="School">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Enquiry Form (Sticky) -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-2xl p-6 md:p-8 border border-gray-100 shadow-[0_15px_40px_-15px_rgba(0,0,0,0.1)] lg:sticky top-[100px]">
                <h3 class="text-xl font-bold text-gray-800 mb-6 text-center">Apply for Admission</h3>
                <form action="/myschooldesk/enquiry_submit.php" method="POST" class="flex flex-col gap-3 md:gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-600 block mb-1">Parent Name</label>
                        <input type="text" name="parent_name" required placeholder="Enter full name" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600 block mb-1">Mobile Number</label>
                        <input type="tel" name="mobile" required pattern="[0-9]{10}" placeholder="10-digit mobile number" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600 block mb-1">Email ID</label>
                        <input type="email" name="email" placeholder="Parent email address" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600 block mb-1">Child Name</label>
                        <input type="text" name="child_name" required placeholder="Child's full name" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600 block mb-1">Date of Birth</label>
                        <input type="date" name="child_dob" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600 block mb-1">Class Applying For</label>
                        <select name="child_class" required class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                            <?php foreach (msd_class_options() as $className): ?>
                                <option value="<?php echo htmlspecialchars($className); ?>"><?php echo htmlspecialchars($className); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600 block mb-1">Preferred Location</label>
                        <input type="text" name="location" value="<?php echo htmlspecialchars((string)$school['city']); ?>" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600 block mb-1">Budget Range</label>
                        <select name="budget_range" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                            <option value="">Select budget</option>
                            <?php foreach (msd_budget_options() as $budget): ?>
                                <option value="<?php echo htmlspecialchars($budget); ?>"><?php echo htmlspecialchars($budget); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600 block mb-1">Preferred Board</label>
                        <select name="board_preference" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                            <option value="">Any Board</option>
                            <?php foreach (msd_board_options() as $board): ?>
                                <option value="<?php echo htmlspecialchars($board); ?>"><?php echo htmlspecialchars($board); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600 block mb-1">Select Schools (Max 5)</label>
                        <select name="school_ids[]" multiple required size="5" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                            <?php foreach ($relatedSchools as $relatedSchool): ?>
                                <option value="<?php echo (int)$relatedSchool['id']; ?>" <?php echo ((int)$relatedSchool['id'] === (int)$school['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($relatedSchool['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600 block mb-1">Additional Message (Optional)</label>
                        <textarea name="message" rows="3" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                    </div>
                    <button type="submit" class="mt-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all">Apply to Selected Schools</button>
                    <p class="text-xs text-gray-400 text-center mt-2">By submitting, you agree to be contacted by selected schools.</p>
                </form>
            </div>
        </div>

    </div>
</section>

<?php require_once 'footer.php'; ?>
