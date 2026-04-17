<?php
// school.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'includes/common.php';

$id = intval($_GET['id'] ?? 0);
$school = null;
if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM schools WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $school = $stmt->fetch();
}

if (!$school) {
    http_response_code(404);
    require_once 'header.php';
    echo '<section class="container mx-auto py-16 text-center">
            <div class="bg-white rounded-[40px] p-20 border border-slate-100 shadow-sm">
                <h1 class="text-4xl font-black text-slate-900 mb-6">School not found</h1>
                <a href="search.php" class="text-blue-600 font-black text-lg hover:underline"><i class="fa-solid fa-arrow-left mr-2"></i> Back to search</a>
            </div>
          </section>';
    require_once 'footer.php';
    return;
}

$extra_css = '<link rel="stylesheet" href="assets/css/home.css">';
require_once 'header.php';

$facilities = array_filter(array_map('trim', explode(',', $school['facilities'])));
$photos = !empty($school['photos']) ? json_decode($school['photos'], true) : [];
if (empty($photos))
    $photos = ["assets/images/school1.png", "assets/images/school2.png"];
?>

<div class="bg-white">
    <!-- Breadcrumbs & Header -->
    <div class="container mx-auto px-4 pt-12 pb-8">
        <nav class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">
            <a href="index.php" class="hover:text-blue-600 transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <a href="search.php?location=<?php echo urlencode($school['city']); ?>"
                class="hover:text-blue-600 transition-colors"><?php echo htmlspecialchars($school['city']); ?></a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-slate-900"><?php echo htmlspecialchars($school['name']); ?></span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 flex items-center gap-3">
                    <?php echo htmlspecialchars($school['name']); ?>, <?php echo htmlspecialchars($school['city']); ?>
                    <?php if($school['is_verified']): ?>
                        <i class="fa-solid fa-circle-check text-blue-500 text-3xl" title="Verified School"></i>
                    <?php endif; ?>
                </h1>
                <div class="flex flex-wrap items-center gap-6">
                    <div class="flex items-center gap-2 bg-amber-400 px-2 py-0.5 rounded-md">
                        <span class="text-xs font-black text-white">4.8</span>
                        <i class="fa-solid fa-star text-white text-[8px]"></i>
                    </div>
                    <p class="text-slate-500 font-bold flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-blue-500/50"></i>
                        <?php echo htmlspecialchars($school['address']); ?>,
                        <?php echo htmlspecialchars($school['city']); ?>
                    </p>
                </div>
            </div>
            <div class="flex gap-4">
                <button
                    class="w-12 h-12 rounded-2xl border-2 border-slate-100 flex items-center justify-center text-slate-400 hover:border-blue-600 hover:text-blue-600 transition-all">
                    <i class="fa-solid fa-heart"></i>
                </button>
                <button
                    class="w-12 h-12 rounded-2xl border-2 border-slate-100 flex items-center justify-center text-slate-400 hover:border-blue-600 hover:text-blue-600 transition-all">
                    <i class="fa-solid fa-share-nodes"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Gallery Section -->
    <div class="container mx-auto px-4 mb-20">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 h-auto md:h-[600px]">
            <!-- Main Featured Image -->
            <div
                class="h-[350px] md:h-full md:col-span-8 rounded-[40px] overflow-hidden group shadow-2xl bg-slate-100 border-4 border-white">
                <?php if (!empty($photos)): ?>
                    <img src="<?php echo $photos[0]; ?>"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000"
                        alt="Main School Photo">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                        <i class="fa-solid fa-image text-6xl"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Side Images -->
            <div class="md:col-span-4 flex flex-col gap-4 h-[400px] md:h-full">
                <div class="h-1/2 rounded-[40px] overflow-hidden group shadow-xl bg-slate-100 border-4 border-white">
                    <?php if (isset($photos[1])): ?>
                        <img src="<?php echo htmlspecialchars($photos[1]); ?>"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000"
                            alt="School Photo 2">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <i class="fa-solid fa-image text-4xl"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div
                    class="h-1/2 rounded-[40px] overflow-hidden group relative shadow-xl bg-slate-100 border-4 border-white">
                    <?php if (isset($photos[2])): ?>
                        <img src="<?php echo htmlspecialchars($photos[2]); ?>"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000"
                            alt="School Photo 3">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <i class="fa-solid fa-image text-4xl text-slate-200"></i>
                        </div>
                    <?php endif; ?>

                    <button onclick="openGallery()"
                        class="absolute bottom-6 right-6 bg-white/90 backdrop-blur-md px-8 py-4 rounded-3xl border border-white/50 text-[11px] font-black text-[#1B2559] flex items-center gap-3 shadow-2xl hover:bg-[#1B2559] hover:text-white transition-all transform active:scale-95 group/btn">
                        <i class="fa-solid fa-images text-blue-600 group-hover/btn:text-white transition-colors"></i>
                        VIEW ALL <?php echo count($photos); ?> PHOTOS
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="container mx-auto px-4 pb-16">
        <div class="flex flex-col lg:flex-row gap-12">

            <!-- School Details -->
            <div class="flex-1">
                <!-- About Section -->
                <div class="mb-16">
                    <h2 class="text-3xl font-black text-slate-900 mb-8">About the School</h2>
                    <p class="text-slate-600 text-lg leading-relaxed font-medium mb-8">
                        <?php echo nl2br(htmlspecialchars($school['description'] ?? '')); ?>
                    </p>

                    <!-- Quick Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50/50 p-6 rounded-[32px] border border-blue-100/50 text-center">
                            <i class="fa-solid fa-users text-blue-600 text-xl mb-3 block"></i>
                            <span
                                class="block text-xl font-black text-slate-900"><?php echo htmlspecialchars($school['student_ratio'] ?? '1:15'); ?></span>
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Student
                                Ratio</span>
                        </div>
                        <div class="bg-blue-50/50 p-6 rounded-[32px] border border-blue-100/50 text-center">
                            <i class="fa-solid fa-video text-blue-600 text-xl mb-3 block"></i>
                            <span
                                class="block text-xl font-black text-slate-900"><?php echo htmlspecialchars($school['security_info'] ?? 'CCTV 24/7'); ?></span>
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Security</span>
                        </div>
                        <div class="bg-blue-50/50 p-6 rounded-[32px] border border-blue-100/50 text-center">
                            <i class="fa-solid fa-graduation-cap text-blue-600 text-xl mb-3 block"></i>
                            <span
                                class="block text-xl font-black text-slate-900"><?php echo htmlspecialchars($school['curriculum_info'] ?? $school['board']); ?></span>
                            <span
                                class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Curriculum</span>
                        </div>
                    </div>
                </div>

                <!-- Campus Facilities -->
                <div class="mb-16">
                    <h2 class="text-3xl font-black text-slate-900 mb-8">Campus Facilities</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <?php
                        $all_facs = msd_facility_options();
                        $saved_facs = array_filter(array_map('trim', explode(',', $school['facilities'] ?? '')));

                        // Map facilities to visual keywords for placeholders
                        $fac_images = [
                            'Dance Room' => 'dance-studio',
                            'Auditorium' => 'auditorium',
                            'Swimming Pool' => 'swimming-pool',
                            'Horse Riding' => 'horse-riding',
                            'Smart Classroom' => 'modern-classroom',
                            'Sickbay Room' => 'infirmary',
                            'Canteen' => 'cafeteria',
                            'Comprehensive Counseling' => 'counseling',
                            'Books Provided' => 'books',
                            'Uniform' => 'school-uniform',
                            'Play Area' => 'playground',
                            'Library' => 'library',
                            'Art Studio' => 'art-studio',
                            'Music Room' => 'music-room',
                            'GPS Bus' => 'school-bus',
                            'Healthy Meals' => 'school-lunch',
                            'AC Classrooms' => 'classroom',
                            'Science Lab' => 'science-lab',
                            'Sports Complex' => 'gymnasium'
                        ];

                        if (empty($saved_facs)) {
                            echo '<p class="text-slate-400 font-bold italic col-span-3">No specific facilities listed.</p>';
                        } else {
                            foreach ($saved_facs as $fac_name):
                                $icon = $all_facs[$fac_name] ?? 'fa-circle-check';
                                $facility_custom_images = !empty($school['facility_images']) ? json_decode($school['facility_images'], true) : [];
                                $img_url = !empty($facility_custom_images[$fac_name]) ? $facility_custom_images[$fac_name] : '';

                                if ($img_url): ?>
                                    <!-- Facility with Admin Uploaded Image -->
                                    <div class="group relative h-48 rounded-[32px] overflow-hidden shadow-sm border border-slate-100 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1">
                                        <img src="<?php echo $img_url; ?>" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" 
                                             alt="<?php echo htmlspecialchars($fac_name); ?>">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent"></div>
                                        <div class="absolute bottom-5 left-5 right-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-white/20 backdrop-blur-md text-white rounded-xl flex items-center justify-center text-xs border border-white/30 shadow-lg group-hover:bg-blue-600 group-hover:border-blue-500 transition-all duration-300">
                                                    <i class="fa-solid <?php echo $icon; ?>"></i>
                                                </div>
                                                <span class="font-black text-white text-[10px] tracking-widest uppercase"><?php echo htmlspecialchars($fac_name); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Facility without Image (Clean Minimalist Style) -->
                                    <div class="group h-48 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[32px] flex flex-col items-center justify-center gap-4 hover:border-blue-500 hover:bg-white transition-all duration-500">
                                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-blue-600 shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                                            <i class="fa-solid <?php echo $icon; ?> text-xl"></i>
                                        </div>
                                        <span class="font-black text-slate-400 group-hover:text-slate-900 text-[10px] tracking-widest uppercase transition-colors"><?php echo htmlspecialchars($fac_name); ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php
                            endforeach;
                        }
                        ?>
                    </div>
                </div>

                <!-- Fee Structure -->
                <div class="mb-16">
                    <h2 class="text-3xl font-black text-slate-900 mb-8">Fee Structure (Annual)</h2>
                    <div class="overflow-hidden rounded-[32px] border border-slate-100 shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th
                                        class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Class</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Admission</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Tuition</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Transport</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Others</th>
                                    <th
                                        class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php
                                $fees = json_decode($school['fee_structure'] ?? '[]', true);
                                if (empty($fees)):
                                    ?>
                                    <tr>
                                        <td colspan="6" class="px-8 py-10 text-center text-slate-400 font-bold italic">
                                            Detailed fee structure not provided.</td>
                                    </tr>
                                <?php else:
                                    foreach ($fees as $fee): ?>
                                        <tr>
                                            <td class="px-8 py-5 font-bold text-slate-600 uppercase tracking-tight">
                                                <?php echo htmlspecialchars($fee['class']); ?>
                                            </td>
                                            <td class="px-8 py-5 font-bold text-slate-500">
                                                ₹<?php echo number_format($fee['admission'] ?? 0); ?></td>
                                            <td class="px-8 py-5 font-bold text-slate-500">
                                                ₹<?php echo number_format($fee['tuition'] ?? 0); ?></td>
                                            <td class="px-8 py-5 font-bold text-slate-500">
                                                ₹<?php echo number_format($fee['transport'] ?? 0); ?></td>
                                            <td class="px-8 py-5 font-bold text-slate-500">
                                                ₹<?php echo number_format($fee['others'] ?? 0); ?></td>
                                            <td class="px-8 py-5 font-black text-blue-600">
                                                ₹<?php echo number_format($fee['total'] ?? 0); ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold italic mt-6">* Fees are subject to verification with the school administration.</p>
                </div>

                <!-- School Contact Details -->
                <div class="mb-16">
                    <h2 class="text-3xl font-black text-slate-900 mb-8">Official Contact Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <?php if (!empty($school['contact_p1_name'])): ?>
                            <!-- Contact Card 1 -->
                            <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 flex items-start gap-6 group">
                                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center text-2xl shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <div class="flex-1">
                                    <span class="block text-[8px] font-black text-blue-500 uppercase tracking-[0.2em] mb-1">Primary Representative</span>
                                    <h4 class="text-xl font-black text-slate-900 mb-1 leading-tight"><?php echo htmlspecialchars($school['contact_p1_name']); ?></h4>
                                    <p class="text-slate-500 font-bold text-sm mb-4 uppercase tracking-wide leading-none"><?php echo htmlspecialchars($school['contact_p1_designation']); ?></p>
                                    <a href="tel:<?php echo $school['contact_p1_phone']; ?>" class="inline-flex items-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-black px-4 py-2 rounded-xl text-[10px] transition-colors border border-slate-100 uppercase tracking-widest">
                                        <i class="fa-solid fa-phone-volume text-[10px] text-blue-600"></i>
                                        <?php echo htmlspecialchars($school['contact_p1_phone']); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($school['contact_p2_name'])): ?>
                            <!-- Contact Card 2 -->
                            <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 flex items-start gap-6 group">
                                <div class="w-16 h-16 bg-slate-50 text-slate-900 rounded-3xl flex items-center justify-center text-2xl shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-all duration-300">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                                <div class="flex-1">
                                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Secondary Representative</span>
                                    <h4 class="text-xl font-black text-slate-900 mb-1 leading-tight"><?php echo htmlspecialchars($school['contact_p2_name']); ?></h4>
                                    <p class="text-slate-500 font-bold text-sm mb-4 uppercase tracking-wide leading-none"><?php echo htmlspecialchars($school['contact_p2_designation']); ?></p>
                                    <a href="tel:<?php echo $school['contact_p2_phone']; ?>" class="inline-flex items-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-black px-4 py-2 rounded-xl text-[10px] transition-colors border border-slate-100 uppercase tracking-widest">
                                        <i class="fa-solid fa-phone-volume text-[10px] text-slate-900"></i>
                                        <?php echo htmlspecialchars($school['contact_p2_phone']); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Sticky Sidebar: Form -->
            <aside class="w-full lg:w-[420px] shrink-0">
                <div class="sticky top-24 space-y-8">
                    <div
                        class="bg-white rounded-[40px] p-10 shadow-[0_30px_80px_-20px_rgba(0,0,0,0.1)] border border-slate-100">
                        <div class="mb-8">
                            <h3 class="text-3xl font-black text-slate-900 mb-2">Parent Enquiry</h3>
                            <p class="text-slate-500 font-medium text-sm">Fill the form below and the school counselor
                                will get in touch with you shortly.</p>
                        </div>

                        <form action="enquiry_submit.php" method="POST" class="space-y-4">
                            <input type="hidden" name="school_ids[]" value="<?php echo $school['id']; ?>">

                            <div>
                                <label
                                    class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">PARENT
                                    NAME</label>
                                <input type="text" name="parent_name" placeholder="e.g. Rahul Sharma"
                                    class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20"
                                    required>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">MOBILE</label>
                                    <input type="tel" name="mobile" placeholder="+91 98XXX"
                                        class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20"
                                        required>
                                </div>
                                <div>
                                    <label
                                        class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">EMAIL</label>
                                    <input type="email" name="email" placeholder="rahul@example.com"
                                        class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20"
                                        required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">CHILD
                                        NAME</label>
                                    <input type="text" name="child_name" placeholder="Name"
                                        class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20"
                                        required>
                                </div>
                                <div>
                                    <label
                                        class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">DOB</label>
                                    <input type="date" name="child_dob"
                                        class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20"
                                        required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">CLASS</label>
                                    <select name="target_class"
                                        class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                                        <?php foreach (msd_class_options() as $c): ?>
                                            <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">LOCATION</label>
                                    <input type="text" name="location" placeholder="Area"
                                        class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label
                                        class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">EXISTING
                                        SCHOOL</label>
                                    <input type="text" name="existing_school" placeholder="Current School"
                                        class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20">
                                </div>
                                <div>
                                    <label
                                        class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">PASSING
                                        YEAR</label>
                                    <select name="passing_year"
                                        class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                                        <option value="">Year</option>
                                        <?php for ($y = 2024; $y <= 2027; $y++)
                                            echo "<option value='$y'>$y</option>"; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label
                                        class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">STUDENT
                                        ID</label>
                                    <input type="text" name="student_id" placeholder="ID/Roll"
                                        class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20">
                                </div>
                                <div>
                                    <label
                                        class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">GOVT
                                        ID TYPE</label>
                                    <select name="govt_id_type"
                                        class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                                        <option value="">ID Type</option>
                                        <option value="Aadhaar">Aadhaar</option>
                                        <option value="PAN">PAN</option>
                                        <option value="Birth Certificate">Birth Cert</option>
                                        <option value="APAAR ID">APAAR ID</option>
                                    </select>
                                </div>
                            </div>

                            <label class="flex items-center gap-3 cursor-pointer group mt-4">
                                <input type="checkbox"
                                    class="w-5 h-5 rounded-md border-slate-200 text-blue-600 focus:ring-blue-500/20"
                                    checked>
                                <span
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wide group-hover:text-slate-600 transition-colors">Apply
                                    to Multiple Schools. Share my details with other top-rated preschools in Akota
                                    area.</span>
                            </label>

                            <button type="submit"
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-5 rounded-3xl shadow-xl shadow-orange-500/30 transition-all hover:scale-[1.02] active:scale-95 text-lg mt-6">
                                Apply for Admission
                            </button>

                            <a href="https://wa.me/916351165654?text=<?php echo urlencode('Hi, I am interested in admission for ' . $school['name'] . '. Can you help me?'); ?>"
                                target="_blank"
                                class="flex items-center justify-center gap-3 border-2 border-slate-50 hover:border-green-500 hover:text-green-600 font-bold py-4 rounded-3xl transition-all w-full text-slate-700">
                                <i class="fa-brands fa-whatsapp text-xl text-green-500"></i> WhatsApp Enquiry
                            </a>
                        </form>
                    </div>

                    <!-- Map/Location Widget -->
                    <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm overflow-hidden group">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xs">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                            <h4 class="text-sm font-black text-slate-900">Campus Location</h4>
                        </div>
                        <div
                            class="relative h-64 rounded-3xl overflow-hidden shadow-inner border border-slate-50 bg-slate-50">
                            <?php
                            if (!empty($school['map_location']) && strpos($school['map_location'], '<iframe') !== false) {
                                // If it's an iframe, make it responsive
                                $map_html = preg_replace('/width="\d+"/', 'width="100%"', $school['map_location']);
                                $map_html = preg_replace('/height="\d+"/', 'height="100%"', $map_html);
                                echo $map_html;
                            } elseif (!empty($school['map_location'])) {
                                // If it's just a URL, show a placeholder with a link
                                ?>
                                <img src="assets/images/map_placeholder.png"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center">
                                    <a href="<?php echo htmlspecialchars($school['map_location']); ?>" target="_blank"
                                        class="bg-white text-slate-900 px-6 py-3 rounded-2xl font-black text-xs shadow-2xl hover:scale-105 transition-all">
                                        VIEW ON GOOGLE MAPS
                                    </a>
                                </div>
                            <?php } else {
                                // Dynamic map placeholder based on address
                                $map_query = urlencode($school['name'] . ', ' . $school['address'] . ', ' . $school['city']);
                                ?>
                                <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0"
                                    marginwidth="0"
                                    src="https://maps.google.com/maps?q=<?php echo $map_query; ?>&t=&z=15&ie=UTF8&iwloc=&output=embed">
                                </iframe>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<!-- Photo Gallery Modal -->
<div id="photoGallery" class="fixed inset-0 z-[2000] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/90 backdrop-blur-xl transition-opacity duration-500 opacity-0"
        id="galleryBackdrop" onclick="closeGallery()"></div>

    <!-- Modal Content -->
    <div class="relative h-full w-full flex flex-col items-center justify-center p-4 md:p-12 pointer-events-none">
        <div class="w-full max-w-6xl bg-white rounded-[40px] overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-500 pointer-events-auto flex flex-col max-h-[90vh]"
            id="galleryContent">
            <!-- Modal Header -->
            <div class="p-8 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="text-2xl font-black text-slate-900">Photo Gallery</h3>
                    <p class="text-slate-500 font-bold text-sm"><?php echo htmlspecialchars($school['name']); ?> •
                        <?php echo count($photos); ?> Photos
                    </p>
                </div>
                <button onclick="closeGallery()"
                    class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white transition-all flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Modal Body (Grid) -->
            <div class="p-8 overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <?php foreach ($photos as $index => $photo): ?>
                        <div
                            class="aspect-square rounded-[32px] overflow-hidden group relative cursor-pointer shadow-lg border-2 border-slate-50 hover:border-blue-600 transition-all">
                            <img src="<?php echo htmlspecialchars($photo); ?>" alt="School Photo <?php echo $index + 1; ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                                <span class="text-white text-[10px] font-black uppercase tracking-widest">View Image</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #E2E8F0;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #CBD5E1;
    }

    #photoGallery.active {
        display: block;
    }
</style>

<script>
    function openGallery() {
        const modal = document.getElementById('photoGallery');
        const backdrop = document.getElementById('galleryBackdrop');
        const content = document.getElementById('galleryContent');

        modal.classList.remove('hidden');
        modal.classList.add('active');

        // Prevent scrolling
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeGallery() {
        const modal = document.getElementById('photoGallery');
        const backdrop = document.getElementById('galleryBackdrop');
        const content = document.getElementById('galleryContent');

        backdrop.classList.add('opacity-0');
        backdrop.classList.remove('opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        content.classList.remove('scale-100', 'opacity-100');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }, 500);
    }

    // Close on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeGallery();
    });
</script>

<?php require_once 'footer.php'; ?>