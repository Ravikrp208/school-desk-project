<?php
// school_dashboard/profile.php
require_once '../config.php';
require_once '../includes/common.php';
require_once '../includes/auth.php';

// Protect the page
protect_school_page();

$userId = $_SESSION['user_id'];
$message = '';
$error = '';

// Fetch existing school data
$stmt = $pdo->prepare('SELECT * FROM schools WHERE user_id = :user_id LIMIT 1');
$stmt->execute(['user_id' => $userId]);
$school = $stmt->fetch();

if (!$school) {
    die('No school profile associated with your account. Please contact admin.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);
    $state = trim($_POST['state']);
    $contact_email = trim($_POST['contact_email']);
    $contact_phone = trim($_POST['contact_phone']);

    $contact_p1_name = trim($_POST['contact_p1_name'] ?? '');
    $contact_p1_designation = trim($_POST['contact_p1_designation'] ?? '');
    $contact_p1_phone = trim($_POST['contact_p1_phone'] ?? '');

    $contact_p2_name = trim($_POST['contact_p2_name'] ?? '');
    $contact_p2_designation = trim($_POST['contact_p2_designation'] ?? '');
    $contact_p2_phone = trim($_POST['contact_p2_phone'] ?? '');
    $description = trim($_POST['description']);
    $fees_min = (float) $_POST['fees_min'];
    $fees_max = (float) $_POST['fees_max'];
    $classes_offered = trim($_POST['classes_offered']);
    $teachers_strength = (int) $_POST['teachers_strength'];
    $teacher_min_qual = trim($_POST['teacher_min_qual']);
    $teacher_max_qual = trim($_POST['teacher_max_qual']);
    $boards = isset($_POST['boards']) ? implode(',', $_POST['boards']) : '';
    $facilities_arr = isset($_POST['facilities']) && is_array($_POST['facilities']) ? $_POST['facilities'] : [];
    if (!empty($_POST['custom_facilities'])) {
        $custom_facs = array_filter(array_map('trim', explode(',', $_POST['custom_facilities'])));
        $facilities_arr = array_unique(array_merge($facilities_arr, $custom_facs));
    }
    $facilities_str = implode(',', $facilities_arr);
    $map_location = trim($_POST['map_location'] ?? '');

    // New stats fields
    $student_ratio = trim($_POST['student_ratio'] ?? '1:15');
    $security_info = trim($_POST['security_info'] ?? 'CCTV 24/7 Monitoring');
    $curriculum_info = trim($_POST['curriculum_info'] ?? 'CBSE, ICSE, IB, State Board');
    $view_distance = trim($_POST['view_distance'] ?? '7.3 km away');
    $view_rating = (float) ($_POST['view_rating'] ?? 4.8);
    $view_reviews_count = (int) ($_POST['view_reviews_count'] ?? 120);

    // Photo Handling
    $photos = [];
    $stmt_p = $pdo->prepare("SELECT photos FROM schools WHERE id = ?");
    $stmt_p->execute([$school['id']]);
    $row_p = $stmt_p->fetch();
    if ($row_p)
        $photos = json_decode($row_p['photos'] ?? '[]', true) ?: [];

    // Handle removals
    if (isset($_POST['remove_photos'])) {
        foreach ($_POST['remove_photos'] as $pToRemove) {
            if (($key = array_search($pToRemove, $photos)) !== false) {
                unset($photos[$key]);
                if (file_exists('../' . $pToRemove))
                    unlink('../' . $pToRemove);
            }
        }
        $photos = array_values($photos);
    }

    // Handle new uploads
    if (!empty($_FILES['school_photos']['name'][0])) {
        $uploadDir = '../uploads/schools/';
        if (!is_dir($uploadDir))
            mkdir($uploadDir, 0777, true);

        foreach ($_FILES['school_photos']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['school_photos']['error'][$key] === 0) {
                $ext = pathinfo($_FILES['school_photos']['name'][$key], PATHINFO_EXTENSION);
                $fileName = uniqid('school_') . '.' . $ext;
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $photos[] = 'uploads/schools/' . $fileName;
                }
            }
        }
    }
    $photos_json = json_encode($photos);

    $fee_structure = [];
    if (isset($_POST['fee_class'])) {
        foreach ($_POST['fee_class'] as $key => $val) {
            if (!empty($val)) {
                $adm = (float)($_POST['fee_admission'][$key] ?? 0);
                $tui = (float)($_POST['fee_tuition'][$key] ?? 0);
                $trp = (float)($_POST['fee_transport'][$key] ?? 0);
                $oth = (float)($_POST['fee_others'][$key] ?? 0);
                $fee_structure[] = [
                    'class' => $val,
                    'admission' => $adm,
                    'tuition' => $tui,
                    'transport' => $trp,
                    'others' => $oth,
                    'total' => ($adm + $tui + $trp + $oth)
                ];
            }
        }
    }
    $fee_structure_json = json_encode($fee_structure);

    try {
        $updateStmt = $pdo->prepare('
            UPDATE schools 
            SET name = :name, address = :address, city = :city, district = :district, state = :state, 
                board = :board, contact_email = :contact_email, contact_phone = :contact_phone, 
                description = :description, fees_min = :fees_min, fees_max = :fees_max, 
                facilities = :facilities, facility_images = :facility_images, fee_structure = :fee_structure, photos = :photos, classes_offered = :classes_offered, 
                contact_p1_name = :contact_p1_name, contact_p1_designation = :contact_p1_designation, contact_p1_phone = :contact_p1_phone,
                contact_p2_name = :contact_p2_name, contact_p2_designation = :contact_p2_designation, contact_p2_phone = :contact_p2_phone,
                teachers_strength = :teachers_strength, teacher_min_qual = :teacher_min_qual, 
                teacher_max_qual = :teacher_max_qual, map_location = :map_location,
                student_ratio = :student_ratio, security_info = :security_info, 
                curriculum_info = :curriculum_info,
                view_distance = :view_distance, view_rating = :view_rating, view_reviews_count = :view_reviews_count
            WHERE id = :id
        ');

        $updateStmt->execute([
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'district' => $district,
            'state' => $state,
            'board' => $boards,
            'contact_email' => $contact_email,
            'contact_phone' => $contact_phone,
            'description' => $description,
            'fees_min' => $fees_min,
            'fees_max' => $fees_max,
            'facilities' => $facilities_str,
            'facility_images' => $facility_images_json,
            'fee_structure' => $fee_structure_json,
            'contact_p1_name' => $contact_p1_name,
            'contact_p1_designation' => $contact_p1_designation,
            'contact_p1_phone' => $contact_p1_phone,
            'contact_p2_name' => $contact_p2_name,
            'contact_p2_designation' => $contact_p2_designation,
            'contact_p2_phone' => $contact_p2_phone,
            'photos' => $photos_json,
            'classes_offered' => $classes_offered,
            'teachers_strength' => $teachers_strength,
            'teacher_min_qual' => $teacher_min_qual,
            'teacher_max_qual' => $teacher_max_qual,
            'map_location' => $map_location,
            'student_ratio' => $student_ratio,
            'security_info' => $security_info,
            'curriculum_info' => $curriculum_info,
            'view_distance' => $view_distance,
            'view_rating' => $view_rating,
            'view_reviews_count' => $view_reviews_count,
            'id' => $school['id']
        ]);

        $message = "Profile updated successfully!";
        // Refresh school data
        $stmt->execute(['user_id' => $userId]);
        $school = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Error updating profile: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>School Profile - <?php echo htmlspecialchars($school['name']); ?></title>
    <link rel="icon" type="image/png" href="../assets/images/logo_boy.png">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style type="text/tailwindcss">
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .form-input { 
            @apply w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3 text-slate-700 font-semibold focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none border;
        }
        label { @apply block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1; }
    </style>
</head>

<body class="flex min-h-screen">
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-12 overflow-y-auto">
        <header class="flex items-center justify-between mb-12">
            <div>
                <h1 class="text-3xl font-black text-slate-900">School Profile</h1>
                <p class="text-slate-500 font-medium tracking-tight">Complete your profile to attract more parents</p>
            </div>
        </header>

        <?php if ($message): ?>
            <div
                class="bg-green-50 border border-green-100 text-green-600 p-4 rounded-2xl mb-8 flex items-center gap-3 font-bold">
                <i class="fa-solid fa-circle-check"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div
                class="bg-red-50 border border-red-100 text-red-500 p-4 rounded-2xl mb-8 flex items-center gap-3 font-bold">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-8 max-w-5xl">
            <!-- Basic Information -->
            <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm">
                <h2 class="text-xl font-black text-slate-900 mb-8 flex items-center gap-3 text-blue-600">
                    <i class="fa-solid fa-info-circle"></i> Basic Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="col-span-2">
                        <label>School Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($school['name']); ?>"
                            class="form-input" required>
                    </div>
                    <div>
                        <label>Contact Email</label>
                        <input type="email" name="contact_email"
                            value="<?php echo htmlspecialchars($school['contact_email']); ?>" class="form-input"
                            required>
                    </div>
                    <div>
                        <label>Reception/Office Number</label>
                        <input type="tel" name="contact_phone"
                            value="<?php echo htmlspecialchars($school['contact_phone']); ?>" class="form-input"
                            required>
                    </div>

                    <!-- Detailed Contact Persons -->
                    <div class="col-span-2 bg-slate-50/50 p-8 rounded-[40px] border border-slate-100 mt-4">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-address-book text-blue-600"></i> Contact Persons details (Shown on Profile)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Contact Person 1 -->
                            <div class="bg-white p-6 rounded-3xl border border-blue-100 shadow-sm">
                                <span class="bg-blue-600 text-white text-[8px] font-black px-3 py-1 rounded-full uppercase tracking-widest mb-4 inline-block">Primary Person</span>
                                <div class="space-y-4">
                                    <div>
                                        <label>Full Name</label>
                                        <input type="text" name="contact_p1_name" value="<?php echo htmlspecialchars($school['contact_p1_name'] ?? ''); ?>" class="form-input" placeholder="e.g. Mr. Sharma">
                                    </div>
                                    <div>
                                        <label>Designation</label>
                                        <input type="text" name="contact_p1_designation" value="<?php echo htmlspecialchars($school['contact_p1_designation'] ?? ''); ?>" class="form-input" placeholder="e.g. Principal">
                                    </div>
                                    <div>
                                        <label>Direct Phone</label>
                                        <input type="text" name="contact_p1_phone" value="<?php echo htmlspecialchars($school['contact_p1_phone'] ?? ''); ?>" class="form-input" placeholder="+91 91XXX">
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Person 2 -->
                            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                                <span class="bg-slate-700 text-white text-[8px] font-black px-3 py-1 rounded-full uppercase tracking-widest mb-4 inline-block">Secondary Person</span>
                                <div class="space-y-4">
                                    <div>
                                        <label>Full Name</label>
                                        <input type="text" name="contact_p2_name" value="<?php echo htmlspecialchars($school['contact_p2_name'] ?? ''); ?>" class="form-input" placeholder="e.g. Mrs. Gupta">
                                    </div>
                                    <div>
                                        <label>Designation</label>
                                        <input type="text" name="contact_p2_designation" value="<?php echo htmlspecialchars($school['contact_p2_designation'] ?? ''); ?>" class="form-input" placeholder="e.g. Admission Head">
                                    </div>
                                    <div>
                                        <label>Direct Phone</label>
                                        <input type="text" name="contact_p2_phone" value="<?php echo htmlspecialchars($school['contact_p2_phone'] ?? ''); ?>" class="form-input" placeholder="+91 91XXX">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label>Short Description</label>
                        <textarea name="description" rows="4"
                            class="form-input"><?php echo htmlspecialchars($school['description']); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Location & Academic -->
            <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm">
                <h2 class="text-xl font-black text-slate-900 mb-8 flex items-center gap-3 text-indigo-600">
                    <i class="fa-solid fa-graduation-cap"></i> Academic & Location
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="col-span-2">
                        <label>Full Address</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($school['address']); ?>"
                            class="form-input">
                    </div>
                    <!-- State, District, City in one line -->
                    <div class="col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label>State</label>
                            <select id="state" name="state" class="form-input w-full"
                                data-selected="<?php echo htmlspecialchars($school['state'] ?? ''); ?>" required>
                                <option value="">Select State</option>
                            </select>
                        </div>
                        <div>
                            <label>District</label>
                            <select id="district" name="district" class="form-input w-full"
                                data-selected="<?php echo htmlspecialchars($school['district'] ?? ''); ?>" required
                                disabled>
                                <option value="">Select District</option>
                            </select>
                        </div>
                        <div>
                            <label>City/Locality (Optional)</label>
                            <input type="text" name="city"
                                value="<?php echo htmlspecialchars($school['city'] ?? ''); ?>" class="form-input"
                                placeholder="e.g. Bandra West">
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label>Live Map Location (Google Maps URL or Embed Code)</label>
                        <textarea name="map_location" rows="2" class="form-input"
                            placeholder="Paste Google Maps URL or <iframe> here..."><?php echo htmlspecialchars($school['map_location'] ?? ''); ?></textarea>
                        <p class="text-[10px] text-slate-400 mt-2 font-black uppercase tracking-widest px-1">Tip: Open
                            Google Maps -> Share -> Embed a Map -> Copy HTML and paste it here.</p>
                    </div>
                    <div class="col-span-2">
                        <label>Educational Boards</label>
                        <div class="flex flex-wrap gap-4 mt-2">
                            <?php
                            $selectedBoards = explode(',', $school['board'] ?? '');
                            foreach (msd_board_options() as $board):
                                ?>
                                <label
                                    class="group flex items-center gap-3 bg-slate-50 p-4 rounded-2xl cursor-pointer hover:bg-blue-50 transition-all border-2 border-transparent has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50">
                                    <input type="checkbox" name="boards[]" value="<?php echo $board; ?>" class="hidden"
                                        <?php echo in_array($board, $selectedBoards) ? 'checked' : ''; ?>>
                                    <div
                                        class="w-5 h-5 rounded-md border-2 border-slate-300 flex items-center justify-center group-has-[:checked]:bg-blue-500 group-has-[:checked]:border-blue-500 transition-all">
                                        <i
                                            class="fa-solid fa-check text-white text-[10px] hidden group-has-[:checked]:block"></i>
                                    </div>
                                    <span
                                        class="text-xs font-black text-slate-600 uppercase tracking-wider"><?php echo $board; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Stats -->
            <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm">
                <h2 class="text-xl font-black text-slate-900 mb-8 flex items-center gap-3 text-green-600">
                    <i class="fa-solid fa-chart-line"></i> Statistics & Facilities
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <label>Min Annual Fees (₹)</label>
                        <input type="number" name="fees_min"
                            value="<?php echo htmlspecialchars($school['fees_min']); ?>" class="form-input">
                    </div>
                    <div>
                        <label>Max Annual Fees (₹)</label>
                        <input type="number" name="fees_max"
                            value="<?php echo htmlspecialchars($school['fees_max']); ?>" class="form-input">
                    </div>
                    <div>
                        <label>Teachers Strength</label>
                        <input type="number" name="teachers_strength"
                            value="<?php echo htmlspecialchars($school['teachers_strength']); ?>" class="form-input">
                    </div>
                    <div>
                        <label>Min Teacher Qual.</label>
                        <input type="text" name="teacher_min_qual"
                            value="<?php echo htmlspecialchars($school['teacher_min_qual']); ?>" class="form-input"
                            placeholder="e.g. B.Ed">
                    </div>
                    <div>
                        <label>Max Teacher Qual.</label>
                        <input type="text" name="teacher_max_qual"
                            value="<?php echo htmlspecialchars($school['teacher_max_qual']); ?>" class="form-input"
                            placeholder="e.g. M.Ed / Ph.D">
                    </div>
                    <div>
                        <label>Classes Offered</label>
                        <input type="text" name="classes_offered"
                            value="<?php echo htmlspecialchars($school['classes_offered']); ?>" class="form-input"
                            placeholder="e.g. Playgroup to 12th">
                    </div>

                    <!-- Quick Stats -->
                    <div
                        class="col-span-3 grid grid-cols-1 md:grid-cols-3 gap-8 py-8 bg-blue-50/50 rounded-[32px] px-8 border border-blue-100/50 mt-4">
                        <div class="col-span-3">
                            <h4
                                class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                                <i class="fa-solid fa-chart-simple text-blue-600"></i> Quick Statistics (Visible on
                                profile)
                            </h4>
                        </div>
                        <div>
                            <label>Student Ratio</label>
                            <input type="text" name="student_ratio"
                                value="<?php echo htmlspecialchars($school['student_ratio'] ?? '1:15'); ?>"
                                class="form-input" placeholder="e.g. 1:12">
                        </div>
                        <div>
                            <label>Security</label>
                            <input type="text" name="security_info"
                                value="<?php echo htmlspecialchars($school['security_info'] ?? 'CCTV 24/7 Monitoring'); ?>"
                                class="form-input" placeholder="e.g. CCTV 24/7">
                        </div>
                        <div>
                            <label>Curriculum</label>
                            <input type="text" name="curriculum_info"
                                value="<?php echo htmlspecialchars($school['curriculum_info'] ?? 'CBSE, ICSE, IB, State Board'); ?>"
                                class="form-input" placeholder="e.g. CBSE, ICSE">
                        </div>
                    </div>

                    <!-- Display Statistics -->
                    <div
                        class="col-span-3 grid grid-cols-1 md:grid-cols-3 gap-8 py-8 bg-slate-50/50 rounded-[32px] px-8 border border-slate-100/50 mt-4">
                        <div class="col-span-3">
                            <h4
                                class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                                <i class="fa-solid fa-display text-blue-600"></i> Search Results Display Stats
                            </h4>
                        </div>
                        <div>
                            <label>Display Rating</label>
                            <input type="number" name="view_rating" step="0.1" max="5"
                                value="<?php echo htmlspecialchars($school['view_rating'] ?? 4.8); ?>"
                                class="form-input" placeholder="e.g. 4.8">
                        </div>
                        <div>
                            <label>Reviews Count</label>
                            <input type="number" name="view_reviews_count"
                                value="<?php echo htmlspecialchars($school['view_reviews_count'] ?? 120); ?>"
                                class="form-input" placeholder="e.g. 120">
                        </div>
                    </div>

                        <div class="col-span-3 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-4">
                            <?php
                            $all_facs = msd_facility_options();
                            $selectedFacilities = explode(',', $school['facilities'] ?? '');
                            $facilityImages = !empty($school['facility_images']) ? json_decode($school['facility_images'], true) : [];
                            
                            foreach ($all_facs as $facility => $icon):
                                $currentImg = $facilityImages[$facility] ?? '';
                                ?>
                                <div class="bg-white p-6 rounded-[32px] border-2 border-[#F4F7FE] hover:border-blue-200 hover:shadow-2xl hover:shadow-blue-500/5 transition-all group overflow-hidden">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-[#F4F7FE] rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm flex-shrink-0">
                                                <i class="fa-solid <?php echo $icon; ?> text-lg"></i>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Facility Name</span>
                                                <span class="text-[13px] font-black text-[#1B2559] uppercase leading-none block break-words max-w-[120px]"><?php echo $facility; ?></span>
                                            </div>
                                        </div>
                                        <input type="checkbox" name="facilities[]" value="<?php echo $facility; ?>"
                                            class="w-7 h-7 rounded-xl accent-blue-600 border-slate-200 cursor-pointer" <?php echo in_array($facility, $selectedFacilities) ? 'checked' : ''; ?>>
                                    </div>
                                    
                                    <!-- Upload Area -->
                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 group-hover:bg-white transition-colors">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-3">Facility Photo</span>
                                        <div class="flex items-center gap-4">
                                            <div class="relative w-16 h-16 rounded-xl bg-white border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center text-slate-200 shadow-inner text-xl">
                                                <?php if ($currentImg): ?>
                                                    <img src="../<?php echo $currentImg; ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <i class="fa-solid fa-camera"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1 space-y-2">
                                                <div class="relative overflow-hidden group/file">
                                                    <input type="file" name="facility_img[<?php echo $facility; ?>]" 
                                                           class="absolute inset-0 opacity-0 cursor-pointer z-20" accept="image/*">
                                                    <div class="bg-white text-blue-600 border border-blue-100 rounded-xl py-3 px-4 flex items-center justify-center gap-2 group-hover/file:bg-blue-600 group-hover/file:text-white transition-all shadow-sm">
                                                        <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                                                        <span class="text-[9px] font-black uppercase tracking-widest">Upload Photo</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="col-span-3 mt-8 bg-slate-50/50 p-8 rounded-[40px] border border-slate-100">
                            <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles text-blue-600"></i> Other Facilities
                            </h4>
                            <label class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-3 block px-1">Add any other facilities (Comma separated)</label>
                            <?php
                            $all_predefined = array_keys(msd_facility_options());
                            $custom_selected = array_diff($selectedFacilities, $all_predefined);
                            $custom_str = implode(', ', $custom_selected);
                            ?>
                            <input type="text" name="custom_facilities" class="form-input bg-white"
                                placeholder="e.g. Robotics Lab, Horse Riding..."
                                value="<?php echo htmlspecialchars($custom_str); ?>">
                                
                            <?php if (!empty($custom_selected)): ?>
                                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <p class="col-span-full text-[9px] font-black text-blue-600 uppercase tracking-widest mb-2 px-1">Custom Facility Photos</p>
                                    <?php foreach ($custom_selected as $cfac): ?>
                                        <div class="bg-white p-4 rounded-3xl border border-slate-100 flex items-center gap-4">
                                            <span class="text-[10px] font-black text-slate-700 uppercase truncate flex-1"><?php echo $cfac; ?></span>
                                            <div class="relative w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 overflow-hidden flex-shrink-0">
                                                <?php if (!empty($facilityImages[$cfac])): ?>
                                                    <img src="../<?php echo $facilityImages[$cfac]; ?>" class="w-full h-full object-cover">
                                                <?php endif; ?>
                                                <input type="file" name="facility_img[<?php echo $cfac; ?>]" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 hover:opacity-100 transition-opacity">
                                                    <i class="fa-solid fa-camera text-white text-xs"></i>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                    <!-- Fee Structure -->
                    <div class="col-span-3">
                        <label>Annual Fee Structure Breakdown</label>
                        <div class="mt-2 bg-slate-50 rounded-3XL p-6 border border-slate-100">
                            <div id="fee-rows" class="space-y-4">
                                <?php
                                $fees = json_decode($school['fee_structure'] ?? '[]', true);
                                if (empty($fees))
                                    $fees = [['class' => '', 'admission' => '', 'tuition' => '']];
                                foreach ($fees as $idx => $fee):
                                    ?>
                                    <div class="fee-row grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                                        <div>
                                            <label class="text-[8px]">Class Name</label>
                                            <input type="text" name="fee_class[]"
                                                value="<?php echo htmlspecialchars($fee['class']); ?>" class="form-input"
                                                placeholder="e.g. Nursery">
                                        </div>
                                        <div>
                                            <label class="text-[8px]">Admission (₹)</label>
                                            <input type="number" name="fee_admission[]"
                                                value="<?php echo htmlspecialchars($fee['admission'] ?? 0); ?>"
                                                class="form-input" placeholder="0">
                                        </div>
                                        <div>
                                            <label class="text-[8px]">Tuition (₹)</label>
                                            <input type="number" name="fee_tuition[]"
                                                value="<?php echo htmlspecialchars($fee['tuition'] ?? 0); ?>" class="form-input"
                                                placeholder="0">
                                        </div>
                                        <div>
                                            <label class="text-[8px]">Transport (₹)</label>
                                            <input type="number" name="fee_transport[]"
                                                value="<?php echo htmlspecialchars($fee['transport'] ?? 0); ?>" class="form-input"
                                                placeholder="0">
                                        </div>
                                        <div>
                                            <label class="text-[8px]">Others (₹)</label>
                                            <input type="number" name="fee_others[]"
                                                value="<?php echo htmlspecialchars($fee['others'] ?? 0); ?>" class="form-input"
                                                placeholder="0">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="removeFeeRow(this)"
                                                class="w-12 h-12 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all border border-red-100">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" onclick="addFeeRow()"
                                class="mt-6 flex items-center gap-2 text-xs font-black text-blue-600 hover:underline uppercase tracking-widest pl-1">
                                <i class="fa-solid fa-plus-circle"></i> Add Another Class
                            </button>
                        </div>
                    </div>

                    <!-- Gallery Photos -->
                    <div class="col-span-3">
                        <label>School Gallery Photos</label>
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php
                            $currentPhotos = json_decode($school['photos'] ?? '[]', true) ?: [];
                            if ($currentPhotos):
                                foreach ($currentPhotos as $photo): ?>
                                    <div
                                        class="relative group aspect-square rounded-3xl overflow-hidden shadow-sm border border-slate-100 bg-slate-50">
                                        <img src="../<?php echo $photo; ?>"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        <div
                                            class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2">
                                            <label
                                                class="flex items-center gap-2 text-white bg-red-500 hover:bg-red-600 px-4 py-2 rounded-2xl text-[10px] font-black cursor-pointer transition-all scale-90 group-hover:scale-100 shadow-xl">
                                                <input type="checkbox" name="remove_photos[]" value="<?php echo $photo; ?>"
                                                    class="hidden">
                                                <i class="fa-solid fa-trash-can"></i> REMOVE
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; endif; ?>

                            <!-- Add Photo Placeholder -->
                            <label
                                class="flex flex-col items-center justify-center aspect-square rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50/50 hover:bg-white hover:border-blue-500 transition-all cursor-pointer group shadow-inner">
                                <input type="file" name="school_photos[]" multiple class="hidden"
                                    onchange="previewImages(this)">
                                <div
                                    class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-3 group-hover:scale-110 transition-transform text-blue-600">
                                    <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                                </div>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Add
                                    Photos</span>
                                <span class="text-[7px] font-bold text-blue-400 mt-1 uppercase">Max 5MB per file</span>
                            </label>
                        </div>
                        <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
                        <p
                            class="text-[8px] font-black text-slate-400 mt-6 italic uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-blue-500"></i>
                            Select photos to remove. Changes apply on save.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-6">
                <button type="submit"
                    class="bg-blue-600 text-white px-12 py-5 rounded-[24px] font-black text-sm uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-[1.02] transition-all active:scale-[0.98]">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </main>
    <script>
        // JavaScript for dependent State & District dropdowns
        document.addEventListener('DOMContentLoaded', function () {
            const stateSelect = document.getElementById('state');
            const districtSelect = document.getElementById('district');
            const selectedState = stateSelect.getAttribute('data-selected');
            const selectedDistrict = districtSelect.getAttribute('data-selected');

            let locationData = null;

            // Fetch state and district data
            fetch('https://raw.githubusercontent.com/sab99r/Indian-States-And-Districts/master/states-and-districts.json')
                .then(response => response.json())
                .then(data => {
                    locationData = data.states;
                    populateStates();

                    if (selectedState) {
                        stateSelect.value = selectedState;
                        updateDistricts(selectedState, selectedDistrict);
                    }
                })
                .catch(error => console.error('Error fetching location data:', error));

            function populateStates() {
                if (!locationData) return;
                locationData.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.state;
                    option.textContent = item.state;
                    stateSelect.appendChild(option);
                });
            }

            stateSelect.addEventListener('change', function () {
                updateDistricts(this.value);
            });

            function updateDistricts(stateName, defaultDistrict = '') {
                districtSelect.innerHTML = '<option value="">Select District</option>';

                if (!stateName || !locationData) {
                    districtSelect.disabled = true;
                    return;
                }

                const state = locationData.find(s => s.state === stateName);
                if (state && state.districts) {
                    state.districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district;
                        option.textContent = district;
                        districtSelect.appendChild(option);
                    });
                    districtSelect.disabled = false;
                    if (defaultDistrict) {
                        districtSelect.value = defaultDistrict;
                    }
                }
            }
        });

        function addFeeRow() {
            const container = document.getElementById('fee-rows');
            const newRow = document.createElement('div');
            newRow.className = 'fee-row grid grid-cols-1 md:grid-cols-6 gap-4 items-end';
            newRow.innerHTML = `
                <div>
                    <label class="text-[8px]">Class Name</label>
                    <input type="text" name="fee_class[]" class="form-input" placeholder="e.g. Nursery">
                </div>
                <div>
                    <label class="text-[8px]">Admission (₹)</label>
                    <input type="number" name="fee_admission[]" class="form-input" placeholder="0">
                </div>
                <div>
                    <label class="text-[8px]">Tuition (₹)</label>
                    <input type="number" name="fee_tuition[]" class="form-input" placeholder="0">
                </div>
                <div>
                    <label class="text-[8px]">Transport (₹)</label>
                    <input type="number" name="fee_transport[]" class="form-input" placeholder="0">
                </div>
                <div>
                    <label class="text-[8px]">Others (₹)</label>
                    <input type="number" name="fee_others[]" class="form-input" placeholder="0">
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="removeFeeRow(this)" class="w-12 h-12 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all border border-red-100">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
        }

        function removeFeeRow(btn) {
            const rows = document.querySelectorAll('.fee-row');
            if (rows.length > 1) {
                btn.closest('.fee-row').remove();
            } else {
                alert('At least one row is required.');
            }
        }

        function previewImages(input) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const div = document.createElement('div');
                        div.className = 'aspect-square rounded-3xl overflow-hidden shadow-lg border-2 border-blue-400 relative bg-slate-50';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-blue-600/10"></div>
                            <div class="absolute top-3 right-3 bg-blue-600 text-white px-2 py-1 rounded-lg text-[8px] font-black shadow-xl ring-2 ring-white">NEW</div>
                        `;
                        preview.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
</body>

</html>