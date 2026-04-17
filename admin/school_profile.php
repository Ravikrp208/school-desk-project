<?php
// admin/school_profile.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../includes/common.php';
require_once '../includes/auth.php';

// Protect the page
protect_admin_page();

// Self-healing database migration for school stats
try {
    $pdo->query("SELECT view_distance FROM schools LIMIT 1");
} catch (PDOException $e) {
    if ($e->getCode() == '42S22') {
        try {
            $pdo->exec("ALTER TABLE schools ADD COLUMN view_distance VARCHAR(50) DEFAULT '7.3 km away' AFTER medical_aid");
            $pdo->exec("ALTER TABLE schools ADD COLUMN view_rating DECIMAL(3,1) DEFAULT 4.8 AFTER view_distance");
            $pdo->exec("ALTER TABLE schools ADD COLUMN view_reviews_count INT DEFAULT 120 AFTER view_rating");
        } catch (PDOException $ex) {
        }
    }
}

// Ensure district column exists
try {
    $pdo->query("SELECT district FROM schools LIMIT 1");
} catch (PDOException $e) {
    if ($e->getCode() == '42S22') {
        try {
            $pdo->exec("ALTER TABLE schools ADD COLUMN district VARCHAR(100) AFTER city");
        } catch (PDOException $ex) {
        }
    }
}

// Ensure is_active column exists
try {
    $pdo->query("SELECT is_active FROM schools LIMIT 1");
} catch (PDOException $e) {
    if ($e->getCode() == '42S22') {
        try {
            $pdo->exec("ALTER TABLE schools ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER status");
        } catch (PDOException $ex) {
        }
    }
}

// Ensure is_featured column exists
try {
    $pdo->query("SELECT is_featured FROM schools LIMIT 1");
} catch (PDOException $e) {
    if ($e->getCode() == '42S22') {
        try {
            $pdo->exec("ALTER TABLE schools ADD COLUMN is_featured TINYINT(1) DEFAULT 0 AFTER is_active");
        } catch (PDOException $ex) {
        }
    }
}

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
$showForm = false;


// Delete Handler
if (isset($_GET['delete_id'])) {
    $deleteId = (int) $_GET['delete_id'];
    try {
        // Find user_id associated with this school to potentially delete it too
        $findStmt = $pdo->prepare("SELECT user_id FROM schools WHERE id = ?");
        $findStmt->execute([$deleteId]);
        $schoolRow = $findStmt->fetch();

        if ($schoolRow) {
            $stmt = $pdo->prepare("DELETE FROM schools WHERE id = ?");
            if ($stmt->execute([$deleteId])) {
                $message = "School profile deleted successfully.";
            }
        }
    } catch (PDOException $e) {
        $error = "Deletion Error: " . $e->getMessage();
    }
}

// Edit Fetch Handler
$editData = null;
if (isset($_GET['edit_id'])) {
    $editId = (int) $_GET['edit_id'];
    $editStmt = $pdo->prepare("SELECT * FROM schools WHERE id = ?");
    $editStmt->execute([$editId]);
    $editData = $editStmt->fetch(PDO::FETCH_ASSOC);
    if ($editData) {
        $showForm = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_school']) || isset($_POST['update_school']))) {
    $schoolId = isset($_POST['school_id']) ? (int) $_POST['school_id'] : 0;
    $name = trim($_POST['school_name']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);
    $state = trim($_POST['state']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $email = trim($_POST['contact_email']);
    $phone = trim($_POST['contact_phone']);

    $contact_p1_name = trim($_POST['contact_p1_name'] ?? '');
    $contact_p1_designation = trim($_POST['contact_p1_designation'] ?? '');
    $contact_p1_phone = trim($_POST['contact_p1_phone'] ?? '');

    $contact_p2_name = trim($_POST['contact_p2_name'] ?? '');
    $contact_p2_designation = trim($_POST['contact_p2_designation'] ?? '');
    $contact_p2_phone = trim($_POST['contact_p2_phone'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $fees_min = (float) ($_POST['fees_min'] ?? 0);
    $fees_max = (float) ($_POST['fees_max'] ?? 0);
    $teachers_strength = (int) ($_POST['teachers_strength'] ?? 0);
    $teacher_min_qual = trim($_POST['teacher_min_qual'] ?? '');
    $teacher_max_qual = trim($_POST['teacher_max_qual'] ?? '');
    $classes_offered = trim($_POST['classes_offered'] ?? '');
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

    // Photo Handling
    $photos = [];
    if ($schoolId > 0) {
        $stmt_p = $pdo->prepare("SELECT photos FROM schools WHERE id = ?");
        $stmt_p->execute([$schoolId]);
        $row_p = $stmt_p->fetch();
        if ($row_p)
            $photos = json_decode($row_p['photos'] ?? '[]', true) ?: [];
    }

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
    // Handle Registration Certificate
    $reg_certificate = $_POST['current_reg_certificate'] ?? '';
    if (!empty($_FILES['reg_certificate']['name'])) {
        $certDir = '../uploads/certificates/';
        if (!is_dir($certDir))
            mkdir($certDir, 0777, true);

        $ext = pathinfo($_FILES['reg_certificate']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('cert_') . '.' . $ext;
        $targetPath = $certDir . $fileName;
        if (move_uploaded_file($_FILES['reg_certificate']['tmp_name'], $targetPath)) {
            // Delete old if exists
            if (!empty($reg_certificate) && file_exists('../' . $reg_certificate)) {
                unlink('../' . $reg_certificate);
            }
            $reg_certificate = 'uploads/certificates/' . $fileName;
        }
    }

    $photos_json = json_encode($photos);

    // Facility Image Handling
    $facility_images = [];
    if ($schoolId > 0) {
        $stmt_fi = $pdo->prepare("SELECT facility_images FROM schools WHERE id = ?");
        $stmt_fi->execute([$schoolId]);
        $row_fi = $stmt_fi->fetch();
        if ($row_fi) {
            $facility_images = json_decode($row_fi['facility_images'] ?? '{}', true) ?: [];
        }
    }

    if (!empty($_FILES['facility_img'])) {
        $facDir = '../uploads/facilities/';
        if (!is_dir($facDir)) mkdir($facDir, 0777, true);

        foreach ($_FILES['facility_img']['name'] as $fac_name => $fileName) {
            if ($_FILES['facility_img']['error'][$fac_name] === 0) {
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $newName = uniqid('fac_') . '.' . $ext;
                if (move_uploaded_file($_FILES['facility_img']['tmp_name'][$fac_name], $facDir . $newName)) {
                    if (!empty($facility_images[$fac_name]) && file_exists('../' . $facility_images[$fac_name])) {
                        unlink('../' . $facility_images[$fac_name]);
                    }
                    $facility_images[$fac_name] = 'uploads/facilities/' . $newName;
                }
            }
        }
    }
    $facility_images_json = json_encode($facility_images);

    $status = trim($_POST['status'] ?? 'pending');

    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;

    // Generate slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    // Basic validation
    if (empty($name) || empty($email) || empty($phone)) {
        $error = "Please fill in all required fields.";
        $showForm = true;
    } else {
        try {
            if ($schoolId > 0) {
                // UPDATE Logic
                $updateStmt = $pdo->prepare("UPDATE schools SET name = ?, slug = ?, address = ?, city = ?, district = ?, state = ?, board = ?, contact_email = ?, contact_phone = ?, contact_p1_name = ?, contact_p1_designation = ?, contact_p1_phone = ?, contact_p2_name = ?, contact_p2_designation = ?, contact_p2_phone = ?, status = ?, facilities = ?, facility_images = ?, fee_structure = ?, photos = ?, description = ?, fees_min = ?, fees_max = ?, classes_offered = ?, teachers_strength = ?, teacher_min_qual = ?, teacher_max_qual = ?, map_location = ?, reg_certificate = ?, student_ratio = ?, security_info = ?, curriculum_info = ?, view_distance = ?, view_rating = ?, view_reviews_count = ?, is_active = ?, is_featured = ?, is_verified = ? WHERE id = ?");
                $updateStmt->execute([$name, $slug, $address, $city, $district, $state, $boards, $email, $phone, $contact_p1_name, $contact_p1_designation, $contact_p1_phone, $contact_p2_name, $contact_p2_designation, $contact_p2_phone, $status, $facilities_str, $facility_images_json, $fee_structure_json, $photos_json, $description, $fees_min, $fees_max, $classes_offered, $teachers_strength, $teacher_min_qual, $teacher_max_qual, $map_location, $reg_certificate, $student_ratio, $security_info, $curriculum_info, $view_distance, $view_rating, $view_reviews_count, $is_active, $is_featured, $is_verified, $schoolId]);
                $_SESSION['message'] = "School profile updated successfully!";
                header("Location: school_profile.php?edit_id=" . $schoolId);
                exit;
            } else {
                // INSERT Logic
                // Handle user creation/verification for the school
                $userStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $userStmt->execute([$email]);
                $user = $userStmt->fetch();

                if (!$user) {
                    // Create new user for the school
                    $hashedPassword = password_hash($phone, PASSWORD_BCRYPT);
                    $createUserStmt = $pdo->prepare("INSERT INTO users (name, username, email, password, phone, role) VALUES (?, ?, ?, ?, ?, 'school')");
                    $createUserStmt->execute([$name, $email, $email, $hashedPassword, $phone]);
                    $userId = $pdo->lastInsertId();
                } else {
                    $userId = $user['id'];
                }

                $stmt = $pdo->prepare("INSERT INTO schools (user_id, name, slug, address, city, district, state, board, contact_email, contact_phone, contact_p1_name, contact_p1_designation, contact_p1_phone, contact_p2_name, contact_p2_designation, contact_p2_phone, status, facilities, facility_images, fee_structure, photos, description, fees_min, fees_max, classes_offered, teachers_strength, teacher_min_qual, teacher_max_qual, map_location, reg_certificate, student_ratio, security_info, curriculum_info, view_distance, view_rating, view_reviews_count, is_active, is_featured, is_verified) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $name, $slug, $address, $city, $district, $state, $boards, $email, $phone, $contact_p1_name, $contact_p1_designation, $contact_p1_phone, $contact_p2_name, $contact_p2_designation, $contact_p2_phone, $status, $facilities_str, $facility_images_json, $fee_structure_json, $photos_json, $description, $fees_min, $fees_max, $classes_offered, $teachers_strength, $teacher_min_qual, $teacher_max_qual, $map_location, $reg_certificate, $student_ratio, $security_info, $curriculum_info, $view_distance, $view_rating, $view_reviews_count, $is_active, $is_featured, $is_verified]);

                // Send Credentials via PHPMailer
                require_once '../includes/mailer.php';
                $mailStatus = msd_send_credentials($email, $phone, $name);

                if ($mailStatus['success']) {
                    $_SESSION['message'] = "School profile created successfully and credentials sent to $email!";
                } else {
                    $_SESSION['message'] = "School profile created successfully, but there was an issue sending credentials.";
                }
                header("Location: school_profile.php");
                exit;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate slug
                $error = "A school with this name already exists.";
            } else {
                $error = "Error: " . $e->getMessage();
            }
            $showForm = true;
        }
    }
}


// Fetch all schools for the list display
$schoolsStmt = $pdo->query("SELECT * FROM schools ORDER BY id DESC");
$schools = $schoolsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch stats for sidebar (optional, reused from index.php if needed)
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Profile | Admin Console</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style type="text/tailwindcss">
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F4F7FE; }
        .sidebar { background: #FFFFFF; border-right: 1px solid #E2E8F0; }
        .nav-item-active { background: #F4F7FE; color: #1B2559; font-weight: 800; border-left: 4px solid #1B2559; }
        .nav-item { color: #A3AED0; font-weight: 600; }
        .nav-item:hover { color: #1B2559; }
        .card { border-radius: 30px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
        .form-input { 
            background: #F4F7FE; 
            border: 2px solid transparent; 
            border-radius: 16px; 
            padding: 12px 20px; 
            font-size: 14px; 
            font-weight: 600; 
            color: #1B2559; 
            transition: all 0.2s;
        }
        .form-input:focus { 
            border-color: #4318FF; 
            outline: none; 
            background: white; 
            box-shadow: 0 10px 20px rgba(67, 24, 255, 0.05);
        }
        label { 
            font-size: 12px; 
            font-weight: 800; 
            color: #1B2559; 
            margin-bottom: 8px; 
            display: block; 
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
    </style>
</head>

<body class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 sidebar flex flex-col fixed h-full z-50 p-6">
        <div class="mb-10 px-4">
            <h1 class="text-xl font-extrabold text-[#1B2559]">Admin Console</h1>
            <p class="text-[10px] font-bold text-[#A3AED0] uppercase tracking-widest">Management Portal</p>
        </div>

        <nav class="flex-1 space-y-2">
            <a href="index.php"
                class="flex items-center gap-4 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'nav-item-active' : 'nav-item'; ?> px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-house-chimney text-lg"></i> Dashboard
            </a>
            <a href="enquiries.php"
                class="flex items-center gap-4 <?php echo basename($_SERVER['PHP_SELF']) == 'enquiries.php' ? 'nav-item-active' : 'nav-item'; ?> px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-comment-dots text-lg"></i> Enquiries
            </a>
            <a href="sliders.php"
                class="flex items-center gap-4 <?php echo basename($_SERVER['PHP_SELF']) == 'sliders.php' ? 'nav-item-active' : 'nav-item'; ?> px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-images text-lg"></i> Homepage Slider
            </a>
            <a href="school_profile.php"
                class="flex items-center gap-4 nav-item-active px-4 py-3 rounded-lg transition-all">
                <i class="fa-solid fa-user-graduate text-lg"></i> School Profile
            </a>
            <a href="logout.php"
                class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-red-50 hover:text-red-500 rounded-lg transition-all">
                <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i> Logout
            </a>
        </nav>

        <!-- Sidebar Widget -->
        <div class="mt-auto bg-[#F4F7FE] p-4 rounded-2xl border border-gray-100 mb-4">
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="w-10 h-10 bg-white rounded-full border border-gray-200 flex items-center justify-center p-1">
                    <img src="../assets/images/logo_boy.png" class="w-full h-full object-cover">
                </div>
                <div>
                    <h4 class="text-xs font-black text-[#1B2559]">School Logo</h4>
                    <span class="text-[10px] font-bold text-gray-400">Active Admin</span>
                </div>
            </div>
            <button
                class="w-full bg-[#1B2559] text-white py-2.5 rounded-xl text-xs font-extrabold hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
                WhatsApp Support
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-10">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-[#1B2559]">School Management</h2>
                <p class="text-xs font-semibold text-[#A3AED0]">Create and customize school profiles</p>
            </div>
            <div class="flex items-center gap-4">
                <button id="toggleViewBtn" onclick="toggleView()"
                    class="text-white px-6 py-3 rounded-2xl text-sm font-bold shadow-md flex items-center gap-2 transition-all <?php echo $showForm ? 'bg-[#1B2559] hover:bg-[#11193d]' : 'bg-[#4318FF] hover:bg-blue-700'; ?>">
                    <?php if ($showForm): ?>
                        <i class="fa-solid fa-list"></i> View Schools
                    <?php else: ?>
                        <i class="fa-solid fa-plus"></i> Add School
                    <?php endif; ?>
                </button>
                <a href="index.php"
                    class="bg-white text-[#1B2559] px-6 py-3 rounded-2xl text-sm font-bold shadow-sm flex items-center gap-2 hover:bg-gray-50 transition-all">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded-xl shadow-sm flex items-center gap-3"
                role="alert">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <p class="font-bold"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded-xl shadow-sm flex items-center gap-3"
                role="alert">
                <i class="fa-solid fa-circle-exclamation text-xl"></i>
                <p class="font-bold"><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <!-- School List Card -->
        <div id="school-list-container"
            class="bg-white rounded-[30px] shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden <?php echo $showForm ? 'hidden' : ''; ?>">
            <div class="p-8 border-b border-gray-100 grid grid-cols-1 md:grid-cols-3 items-center gap-4">
                <h3 class="text-lg font-bold text-[#1B2559] flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xs">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    Registered Schools
                </h3>

                <!-- Centered Search Box: Same to Same Design -->
                <div class="relative w-full max-w-sm mx-auto group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-[#A3AED0] text-sm"></i>
                    </div>
                    <input type="text" id="schoolSearch" onkeyup="filterSchools()"
                        class="block w-full pl-12 pr-6 py-3 border-2 border-blue-100 bg-white text-[#1B2559] text-[13px] font-semibold rounded-full focus:border-[#4318FF]/40 focus:ring-4 focus:ring-[#4318FF]/5 transition-all placeholder:text-[#94A3B8]"
                        placeholder="Search leads or schools...">
                </div>

                <div class="flex justify-end order-last md:order-none">
                    <span
                        class="text-[10px] font-bold text-[#A3AED0] uppercase tracking-widest leading-none block">School
                        Records</span>
                </div>
            </div>

            <?php if (empty($schools)): ?>
                <div class="p-12 text-center flex flex-col items-center justify-center">
                    <div
                        class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center text-blue-500 text-4xl mb-4">
                        <i class="fa-solid fa-school-flag"></i>
                    </div>
                    <h4 class="text-xl font-bold text-[#1B2559] mb-2">No schools added yet</h4>
                    <p class="text-[#A3AED0] mb-6 max-w-sm">Get started by adding your first school profile to the platform.
                    </p>
                    <button type="button" onclick="toggleView()"
                        class="text-[#4318FF] font-bold text-sm tracking-wide hover:underline inline-flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle"></i> Add your first school
                    </button>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto p-4">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr
                                class="text-[11px] font-extrabold text-[#A3AED0] uppercase tracking-widest border-b border-gray-50">
                                <th class="py-3 px-6">School Details</th>
                                <th class="py-3 px-6">Contact Info</th>
                                <th class="py-3 px-6">Location</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="school-table-body">
                            <?php foreach ($schools as $school): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="py-4 px-6 border-b border-gray-50 flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center text-[#4318FF] font-bold text-lg shadow-sm border border-blue-100/50">
                                            <?php echo strtoupper(substr($school['name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <p class="text-[15px] font-bold text-[#1B2559] mb-0.5">
                                                <?php echo htmlspecialchars($school['name']); ?>
                                            </p>
                                            <p class="text-[11px] font-semibold text-[#A3AED0] line-clamp-1 max-w-[200px]">
                                                <?php echo htmlspecialchars($school['board']); ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 border-b border-gray-50">
                                        <p class="text-xs font-bold text-[#1B2559] flex items-center gap-2 mb-1"><i
                                                class="fa-solid fa-envelope text-[#A3AED0] text-[10px]"></i>
                                            <?php echo htmlspecialchars($school['contact_email']); ?></p>
                                        <p class="text-[10px] font-bold text-[#A3AED0] flex items-center gap-2"><i
                                                class="fa-solid fa-phone text-[#A3AED0] text-[10px]"></i>
                                            <?php echo htmlspecialchars($school['contact_phone']); ?></p>
                                    </td>
                                    <td class="py-4 px-6 border-b border-gray-50">
                                        <p class="text-xs font-bold text-[#1B2559]">
                                            <?php echo htmlspecialchars($school['city'] ? $school['city'] : 'N/A'); ?>
                                        </p>
                                        <p class="text-[10px] font-bold text-[#A3AED0]">
                                            <?php echo htmlspecialchars($school['state'] ? $school['state'] : 'N/A'); ?>
                                        </p>
                                    </td>
                                    <td class="py-4 px-6 border-b border-gray-50 text-center">
                                        <?php if ($school['status'] == 'active'): ?>
                                            <span
                                                class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-green-100 text-green-700 text-[10px] font-extrabold uppercase tracking-wide">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                            </span>
                                        <?php elseif ($school['status'] == 'inactive'): ?>
                                            <span
                                                class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-gray-100 text-gray-600 text-[10px] font-extrabold uppercase tracking-wide">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-yellow-100 text-yellow-700 text-[10px] font-extrabold uppercase tracking-wide">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-6 border-b border-gray-50 text-right">
                                        <div class="flex items-center justify-end gap-2 transition-opacity">
                                            <a href="?edit_id=<?php echo $school['id']; ?>"
                                                class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors shadow-sm"
                                                title="Edit">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </a>
                                            <a href="?delete_id=<?php echo $school['id']; ?>"
                                                onclick="return confirm('Are you sure you want to delete this school?')"
                                                class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-colors shadow-sm"
                                                title="Delete">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Form Card -->
        <div id="school-form-container" class="bg-white p-10 card <?php echo !$showForm ? 'hidden' : ''; ?>">
            <h3 class="text-xl font-black text-[#1B2559] mb-8 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-school"></i>
                </div>
                <?php echo $editData ? 'Update School Profile' : 'New School Profile'; ?>
            </h3>

            <form method="POST" enctype="multipart/form-data" class="space-y-8">
                <!-- Status Toggles -->
                <div class="flex items-center gap-6 p-6 rounded-3xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_featured" class="sr-only peer" <?php echo ($editData['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                        <span class="text-xs font-black text-slate-700 uppercase tracking-widest">Featured School</span>
                    </div>

                    <div class="flex items-center gap-3 border-l border-slate-200 pl-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_verified" class="sr-only peer" <?php echo ($editData['is_verified'] ?? 0) ? 'checked' : ''; ?>>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                        <span class="text-xs font-black text-slate-700 uppercase tracking-widest">Verified Badge</span>
                    </div>

                    <div class="flex items-center gap-3 border-l border-slate-200 pl-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" class="sr-only peer" <?php echo ($editData['is_active'] ?? 1) ? 'checked' : ''; ?>>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#05CD99]"></div>
                        </label>
                        <span class="text-xs font-black text-slate-700 uppercase tracking-widest">Active Profile</span>
                    </div>
                </div>

                <?php if ($editData): ?>
                    <input type="hidden" name="school_id" value="<?php echo $editData['id']; ?>">
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- School Name -->
                    <div class="col-span-2">
                        <label for="school_name">School Name <span class="text-red-500">*</span></label>
                        <input type="text" id="school_name" name="school_name" class="w-full form-input"
                            placeholder="e.g. Greenwood High International"
                            value="<?php echo htmlspecialchars($editData['name'] ?? ''); ?>" required>
                    </div>

                    <!-- Contact Details -->
                    <!-- Detailed Contact Persons -->
                    <div class="col-span-2 bg-blue-50/30 p-8 rounded-[32px] border border-blue-100/50">
                        <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-address-book text-blue-600"></i> Contact Persons Data
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Contact Person 1 -->
                            <div class="bg-white p-6 rounded-2xl border border-blue-100 shadow-sm">
                                <span class="bg-blue-600 text-white text-[8px] font-black px-3 py-1 rounded-full uppercase tracking-widest mb-4 inline-block">Primary Contact</span>
                                <div class="space-y-4">
                                    <div>
                                        <label>Full Name</label>
                                        <input type="text" name="contact_p1_name" value="<?php echo htmlspecialchars($editData['contact_p1_name'] ?? ''); ?>" class="w-full form-input" placeholder="e.g. Mr. Sharma">
                                    </div>
                                    <div>
                                        <label>Designation</label>
                                        <input type="text" name="contact_p1_designation" value="<?php echo htmlspecialchars($editData['contact_p1_designation'] ?? ''); ?>" class="w-full form-input" placeholder="e.g. Principal">
                                    </div>
                                    <div>
                                        <label>Direct Phone</label>
                                        <input type="text" name="contact_p1_phone" value="<?php echo htmlspecialchars($editData['contact_p1_phone'] ?? ''); ?>" class="w-full form-input" placeholder="+91 91XXX">
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Person 2 -->
                            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                                <span class="bg-slate-700 text-white text-[8px] font-black px-3 py-1 rounded-full uppercase tracking-widest mb-4 inline-block">Secondary Contact</span>
                                <div class="space-y-4">
                                    <div>
                                        <label>Full Name</label>
                                        <input type="text" name="contact_p2_name" value="<?php echo htmlspecialchars($editData['contact_p2_name'] ?? ''); ?>" class="w-full form-input" placeholder="e.g. Mrs. Gupta">
                                    </div>
                                    <div>
                                        <label>Designation</label>
                                        <input type="text" name="contact_p2_designation" value="<?php echo htmlspecialchars($editData['contact_p2_designation'] ?? ''); ?>" class="w-full form-input" placeholder="e.g. Admission Head">
                                    </div>
                                    <div>
                                        <label>Direct Phone</label>
                                        <input type="text" name="contact_p2_phone" value="<?php echo htmlspecialchars($editData['contact_p2_phone'] ?? ''); ?>" class="w-full form-input" placeholder="+91 91XXX">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div>
                        <label for="contact_email">General Contact Email <span class="text-red-500">*</span></label>
                        <input type="email" id="contact_email" name="contact_email" class="w-full form-input"
                            placeholder="info@school.com"
                            value="<?php echo htmlspecialchars($editData['contact_email'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="contact_phone">Official Office Number <span class="text-red-500">*</span></label>
                        <input type="tel" id="contact_phone" name="contact_phone" class="w-full form-input"
                            placeholder="+91 999 999 9999"
                            value="<?php echo htmlspecialchars($editData['contact_phone'] ?? ''); ?>" required>
                    </div>

                    <!-- Location Details -->
                    <div class="col-span-2">
                        <label for="address">Full Address</label>
                        <textarea id="address" name="address" rows="3" class="w-full form-input py-4"
                            placeholder="Street name, landmark..."><?php echo htmlspecialchars($editData['address'] ?? ''); ?></textarea>
                    </div>
                    <!-- State, District, City in one line -->
                    <div class="col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="state">State</label>
                            <select id="state" name="state" class="w-full form-input"
                                data-selected="<?php echo htmlspecialchars($editData['state'] ?? ''); ?>" required>
                                <option value="">Select State</option>
                            </select>
                        </div>
                        <div>
                            <label for="district">District</label>
                            <select id="district" name="district" class="w-full form-input"
                                data-selected="<?php echo htmlspecialchars($editData['district'] ?? ''); ?>" required
                                disabled>
                                <option value="">Select District</option>
                            </select>
                        </div>
                        <div>
                            <label for="city">City/Locality (Optional)</label>
                            <input type="text" id="city" name="city" class="w-full form-input"
                                placeholder="e.g. Bandra West"
                                value="<?php echo htmlspecialchars($editData['city'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label for="map_location">Live Map Location (Google Maps URL or Embed Code)</label>
                        <textarea id="address" name="map_location" rows="2" class="w-full form-input py-4"
                            placeholder="Paste Google Maps URL or <iframe> here..."><?php echo htmlspecialchars($editData['map_location'] ?? ''); ?></textarea>
                        <p class="text-[10px] text-gray-400 mt-2 font-bold px-2 uppercase tracking-wide">Tip: Open
                            Google Maps -> Share -> Embed a Map -> Copy HTML and paste it here.</p>
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label for="description">Short Description</label>
                        <textarea id="description" name="description" rows="4" class="w-full form-input py-4"
                            placeholder="Briefly describe the school..."><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea>
                    </div>

                    <!-- Fees & Class Stats -->
                    <div>
                        <label for="fees_min">Min Annual Fees (₹)</label>
                        <input type="number" id="fees_min" name="fees_min" class="w-full form-input"
                            placeholder="e.g. 50000"
                            value="<?php echo htmlspecialchars($editData['fees_min'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="fees_max">Max Annual Fees (₹)</label>
                        <input type="number" id="fees_max" name="fees_max" class="w-full form-input"
                            placeholder="e.g. 150000"
                            value="<?php echo htmlspecialchars($editData['fees_max'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="classes_offered">Classes Offered</label>
                        <input type="text" id="classes_offered" name="classes_offered" class="w-full form-input"
                            placeholder="e.g. Playgroup to 10th"
                            value="<?php echo htmlspecialchars($editData['classes_offered'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="reg_certificate" class="flex items-center gap-2">
                            School Registration Certificate
                            <?php if (!empty($editData['reg_certificate'])): ?>
                                <a href="../<?php echo $editData['reg_certificate']; ?>" target="_blank"
                                    class="text-blue-600 hover:underline text-[10px] font-black uppercase tracking-widest">[View
                                    Current]</a>
                            <?php endif; ?>
                        </label>
                        <input type="file" id="reg_certificate" name="reg_certificate" class="w-full form-input"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <input type="hidden" name="current_reg_certificate"
                            value="<?php echo htmlspecialchars($editData['reg_certificate'] ?? ''); ?>">
                        <p class="text-[8px] text-slate-400 font-bold mt-1 uppercase tracking-widest">Upload PDF or
                            Image of registration certificate</p>
                    </div>
                    <div>
                        <label for="teachers_strength">Teachers Strength</label>
                        <input type="number" id="teachers_strength" name="teachers_strength" class="w-full form-input"
                            placeholder="e.g. 25"
                            value="<?php echo htmlspecialchars($editData['teachers_strength'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="teacher_min_qual">Min Teacher Qual.</label>
                        <input type="text" id="teacher_min_qual" name="teacher_min_qual" class="w-full form-input"
                            placeholder="e.g. B.Ed"
                            value="<?php echo htmlspecialchars($editData['teacher_min_qual'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="teacher_max_qual">Max Teacher Qual.</label>
                        <input type="text" id="teacher_max_qual" name="teacher_max_qual" class="w-full form-input"
                            placeholder="e.g. M.A, M.Ed"
                            value="<?php echo htmlspecialchars($editData['teacher_max_qual'] ?? ''); ?>">
                    </div>

                    <!-- Quick Stats -->
                    <div
                        class="col-span-2 grid grid-cols-1 md:grid-cols-3 gap-8 py-6 bg-blue-50/30 rounded-[30px] px-8 border border-blue-100/50 mt-4">
                        <div class="col-span-3">
                            <h4
                                class="text-sm font-black text-[#1B2559] uppercase tracking-widest flex items-center gap-2">
                                <i class="fa-solid fa-chart-simple text-blue-600"></i> Quick Statistics (Visible on
                                profile)
                            </h4>
                        </div>
                        <div>
                            <label for="student_ratio">Student Ratio</label>
                            <input type="text" id="student_ratio" name="student_ratio" class="w-full form-input"
                                placeholder="e.g. 1:12"
                                value="<?php echo htmlspecialchars($editData['student_ratio'] ?? '1:15'); ?>">
                        </div>
                        <div>
                            <label for="security_info">Security</label>
                            <input type="text" id="security_info" name="security_info" class="w-full form-input"
                                placeholder="e.g. CCTV 24/7"
                                value="<?php echo htmlspecialchars($editData['security_info'] ?? 'CCTV 24/7 Monitoring'); ?>">
                        </div>
                        <div>
                            <label for="curriculum_info">Curriculum</label>
                            <input type="text" id="curriculum_info" name="curriculum_info" class="w-full form-input"
                                placeholder="e.g. CBSE, ICSE"
                                value="<?php echo htmlspecialchars($editData['curriculum_info'] ?? 'CBSE, ICSE, IB, State Board'); ?>">
                        </div>
                    </div>

                    <!-- Display Statistics -->
                    <div
                        class="col-span-2 grid grid-cols-1 md:grid-cols-3 gap-8 py-6 bg-slate-50/50 rounded-[30px] px-8 border border-slate-100/50 mt-4">
                        <div class="col-span-3">
                            <h4
                                class="text-sm font-black text-[#1B2559] uppercase tracking-widest flex items-center gap-2">
                                <i class="fa-solid fa-display text-blue-600"></i> Search Results Display Stats
                            </h4>
                        </div>
                        <div>
                            <label for="view_rating">Display Rating</label>
                            <input type="number" id="view_rating" name="view_rating" step="0.1" max="5"
                                class="w-full form-input" placeholder="e.g. 4.8"
                                value="<?php echo htmlspecialchars($editData['view_rating'] ?? 4.8); ?>">
                        </div>
                        <div>
                            <label for="view_reviews_count">Reviews Count</label>
                            <input type="number" id="view_reviews_count" name="view_reviews_count"
                                class="w-full form-input" placeholder="e.g. 120"
                                value="<?php echo htmlspecialchars($editData['view_reviews_count'] ?? 120); ?>">
                        </div>
                    </div>

                    <!-- Status Selection -->
                    <div class="col-span-2">
                        <label for="status">Account Status</label>
                        <select id="status" name="status"
                            class="w-full form-input appearance-none bg-no-repeat bg-[right_1.5rem_center] bg-[length:1em_1em]"
                            style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%231B2559%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');">
                            <option value="active" <?php echo ($editData['status'] ?? 'active') == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($editData['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="pending" <?php echo ($editData['status'] ?? '') == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>

                    <!-- Boards -->
                    <div class="col-span-2">
                        <label>Educational Boards</label>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4">
                            <?php
                            $selectedBoards = isset($editData['board']) ? explode(',', $editData['board']) : [];
                            foreach (msd_board_options() as $board):
                                ?>
                                <label
                                    class="flex items-center gap-3 bg-[#F4F7FE] p-4 rounded-2xl cursor-pointer hover:bg-blue-50 transition-all border-2 border-transparent hover:border-blue-200">
                                    <input type="checkbox" name="boards[]" value="<?php echo $board; ?>"
                                        class="w-5 h-5 accent-[#4318FF]" <?php echo in_array($board, $selectedBoards) ? 'checked' : ''; ?>>
                                    <span class="text-sm font-bold text-[#1B2559] uppercase"><?php echo $board; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-span-2">
                        <h2 class="text-xl font-black text-[#1B2559] mb-8 flex items-center gap-3">
                            <i class="fa-solid fa-building-circle-check text-blue-600"></i> Campus Facilities & Photos
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-6">
                            <?php
                            $selectedFacilities = isset($editData['facilities']) ? explode(',', $editData['facilities']) : [];
                            $facilityImages = !empty($editData['facility_images']) ? json_decode($editData['facility_images'], true) : [];
                            
                            foreach (msd_facility_options() as $facility => $icon):
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
                                            class="w-7 h-7 rounded-xl accent-[#4318FF] border-slate-200 cursor-pointer" <?php echo in_array($facility, $selectedFacilities) ? 'checked' : ''; ?>>
                                    </div>
                                    
                                    <!-- Upload Area -->
                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 group-hover:bg-white transition-colors">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-3">Facility Photo</span>
                                        <div class="flex items-center gap-4">
                                            <div class="relative w-16 h-16 rounded-xl bg-white border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center text-slate-200 shadow-inner">
                                                <?php if ($currentImg): ?>
                                                    <img src="../<?php echo $currentImg; ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <i class="fa-solid fa-camera text-xl"></i>
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

                        <!-- Other Facilities -->
                        <div class="mt-10 bg-blue-50/30 p-8 rounded-[40px] border border-blue-100/50">
                            <h4 class="text-[11px] font-black text-[#1B2559] uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles text-blue-600"></i> Other Facilities
                            </h4>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-4 px-1">Add any facility not listed above (Comma separated)</p>
                            <?php
                            $all_predefined = array_keys(msd_facility_options());
                            $custom_selected = array_diff($selectedFacilities, $all_predefined);
                            $custom_str = implode(', ', $custom_selected);
                            ?>
                            <input type="text" id="custom_facilities" name="custom_facilities" 
                                class="w-full form-input bg-white border-transparent focus:border-blue-300" 
                                placeholder="e.g. Robotics Lab, Horse Riding..."
                                value="<?php echo htmlspecialchars($custom_str); ?>">
                                
                            <?php if (!empty($custom_selected)): ?>
                                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <p class="col-span-full text-[9px] font-black text-blue-600 uppercase tracking-widest mb-2 bg-blue-100/50 py-2 px-4 rounded-lg inline-block w-fit">Custom Facility Photo Uploads</p>
                                    <?php foreach ($custom_selected as $cfac): ?>
                                        <div class="bg-white p-5 rounded-3xl border border-slate-100 flex items-center gap-4 hover:border-blue-200 transition-all group/custom">
                                            <div class="relative w-16 h-16 rounded-xl bg-slate-50 border border-slate-100 overflow-hidden flex-shrink-0 flex items-center justify-center text-slate-200 group-hover/custom:bg-white transition-colors">
                                                <?php if (!empty($facilityImages[$cfac])): ?>
                                                    <img src="../<?php echo $facilityImages[$cfac]; ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <i class="fa-solid fa-camera text-lg"></i>
                                                <?php endif; ?>
                                                <input type="file" name="facility_img[<?php echo $cfac; ?>]" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover/custom:opacity-100 transition-opacity">
                                                    <i class="fa-solid fa-plus text-white text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 overflow-hidden">
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Custom Facility</p>
                                                <p class="text-[11px] font-black text-slate-700 uppercase truncate"><?php echo $cfac; ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Fee Structure -->
                    <div class="col-span-2">
                        <label>Annual Fee Structure</label>
                        <div class="mt-4 bg-[#F4F7FE] rounded-3xl p-8 border border-[#E2E8F0]">
                            <div id="fee-rows" class="space-y-4">
                                <?php
                                $fees = isset($editData['fee_structure']) ? json_decode($editData['fee_structure'], true) : [];
                                if (empty($fees))
                                    $fees = [['class' => '', 'admission' => '', 'tuition' => '']];
                                foreach ($fees as $idx => $fee):
                                    ?>
                                    <div class="fee-row grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                                        <div>
                                            <label class="text-[10px] text-gray-400">Class</label>
                                            <input type="text" name="fee_class[]"
                                                value="<?php echo htmlspecialchars($fee['class']); ?>"
                                                class="w-full form-input" placeholder="e.g. Nursery">
                                        </div>
                                        <div>
                                            <label class="text-[10px] text-gray-400">Admission Fee</label>
                                            <input type="number" name="fee_admission[]"
                                                value="<?php echo htmlspecialchars($fee['admission'] ?? 0); ?>"
                                                class="w-full form-input" placeholder="0">
                                        </div>
                                        <div>
                                            <label class="text-[10px] text-gray-400">Tuition Fee</label>
                                            <input type="number" name="fee_tuition[]"
                                                value="<?php echo htmlspecialchars($fee['tuition'] ?? 0); ?>"
                                                class="w-full form-input" placeholder="0">
                                        </div>
                                        <div>
                                            <label class="text-[10px] text-gray-400">Transport Fee</label>
                                            <input type="number" name="fee_transport[]"
                                                value="<?php echo htmlspecialchars($fee['transport'] ?? 0); ?>"
                                                class="w-full form-input" placeholder="0">
                                        </div>
                                        <div>
                                            <label class="text-[10px] text-gray-400">Other Fees</label>
                                            <input type="number" name="fee_others[]"
                                                value="<?php echo htmlspecialchars($fee['others'] ?? 0); ?>"
                                                class="w-full form-input" placeholder="0">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="removeFeeRow(this)"
                                                class="w-12 h-12 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" onclick="addFeeRow()"
                                class="mt-6 flex items-center gap-2 text-sm font-bold text-[#4318FF] hover:underline">
                                <i class="fa-solid fa-plus-circle"></i> Add Another Class
                            </button>
                        </div>
                    </div>

                    <!-- Gallery Photos -->
                    <div class="col-span-2">
                        <label>School Gallery Photos</label>
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-4">
                            <?php
                            $currentPhotos = isset($editData['photos']) ? json_decode($editData['photos'], true) : [];
                            if ($currentPhotos):
                                foreach ($currentPhotos as $photo): ?>
                                    <div
                                        class="relative group aspect-square rounded-2xl overflow-hidden shadow-sm border border-gray-100 bg-gray-50 photo-wrapper">
                                        <img src="../<?php echo $photo; ?>"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-all duration-500 photo-img">
                                        <div
                                            class="delete-overlay absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col items-center justify-center gap-2">
                                            <label
                                                class="flex items-center gap-2 text-white bg-red-500 hover:bg-red-600 px-4 py-2 rounded-xl text-[10px] font-black cursor-pointer transition-all scale-90 group-hover:scale-100 delete-btn">
                                                <input type="checkbox" name="remove_photos[]"
                                                    value="<?php echo htmlspecialchars($photo); ?>" class="hidden"
                                                    onchange="togglePhotoRemoval(this)">
                                                <i class="fa-solid fa-trash-can"></i> <span class="btn-text">REMOVE</span>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; endif; ?>

                            <!-- Add Photo Placeholder -->
                            <label
                                class="flex flex-col items-center justify-center aspect-square rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50/30 hover:bg-blue-50 transition-all cursor-pointer group hover:border-blue-400">
                                <input type="file" name="school_photos[]" multiple class="hidden"
                                    onchange="previewImages(this)">
                                <div
                                    class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-3 group-hover:scale-110 transition-transform text-blue-600">
                                    <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                                </div>
                                <span class="text-[10px] font-black text-[#1B2559] uppercase tracking-widest">Add
                                    Photos</span>
                                <span class="text-[8px] font-bold text-blue-400 mt-1 uppercase">Max 5MB each</span>
                            </label>
                        </div>
                        <div id="image-preview" class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4"></div>
                        <p class="text-[10px] font-bold text-gray-400 mt-3 italic uppercase tracking-wider">Note:
                            Selected photos for removal will be deleted on save.</p>
                    </div>

                </div>

                <div class="pt-6 flex items-center gap-4">
                    <button type="submit" name="<?php echo $editData ? 'update_school' : 'add_school'; ?>"
                        class="w-full md:w-auto bg-[#4318FF] text-white px-12 py-4 rounded-2xl text-sm font-extrabold shadow-xl shadow-blue-500/30 hover:bg-blue-700 transition-all flex items-center justify-center gap-3">
                        <i class="fa-solid <?php echo $editData ? 'fa-save' : 'fa-plus-circle'; ?>"></i>
                        <?php echo $editData ? 'Update School Profile' : 'Create School Profile'; ?>
                    </button>
                    <?php if ($editData): ?>
                        <a href="school_profile.php"
                            class="bg-gray-100 text-[#1B2559] px-8 py-4 rounded-2xl text-sm font-bold hover:bg-gray-200 transition-all">
                            Cancel
                        </a>
                    <?php endif; ?>
                </div>

            </form>
        </div>
    </main>

    <script>
        // Search functionality for schools
        function filterSchools() {
            const input = document.getElementById('schoolSearch');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('#school-table-body tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

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

        function toggleView() {
            const listContainer = document.getElementById('school-list-container');
            const formContainer = document.getElementById('school-form-container');
            const toggleBtn = document.getElementById('toggleViewBtn');

            if (formContainer.classList.contains('hidden')) {
                // Show Form, Hide List
                formContainer.classList.remove('hidden');
                listContainer.classList.add('hidden');
                toggleBtn.innerHTML = '<i class="fa-solid fa-list"></i> View Schools';

                toggleBtn.classList.remove('bg-[#4318FF]');
                toggleBtn.classList.remove('hover:bg-blue-700');
                toggleBtn.classList.add('bg-[#1B2559]');
                toggleBtn.classList.add('hover:bg-[#11193d]');
            } else {
                // Show List, Hide Form
                formContainer.classList.add('hidden');
                listContainer.classList.remove('hidden');
                toggleBtn.innerHTML = '<i class="fa-solid fa-plus"></i> Add School';

                toggleBtn.classList.add('bg-[#4318FF]');
                toggleBtn.classList.add('hover:bg-blue-700');
                toggleBtn.classList.remove('bg-[#1B2559]');
                toggleBtn.classList.remove('hover:bg-[#11193d]');
            }
        }

        function addFeeRow() {
            const container = document.getElementById('fee-rows');
            const newRow = document.createElement('div');
            newRow.className = 'fee-row grid grid-cols-1 md:grid-cols-6 gap-4 items-end';
            newRow.innerHTML = `
                <div>
                    <label class="text-[10px] text-gray-400">Class</label>
                    <input type="text" name="fee_class[]" class="w-full form-input" placeholder="e.g. Nursery">
                </div>
                <div>
                    <label class="text-[10px] text-gray-400">Admission Fee</label>
                    <input type="number" name="fee_admission[]" class="w-full form-input" placeholder="0">
                </div>
                <div>
                    <label class="text-[10px] text-gray-400">Tuition Fee</label>
                    <input type="number" name="fee_tuition[]" class="w-full form-input" placeholder="0">
                </div>
                <div>
                    <label class="text-[10px] text-gray-400">Transport Fee</label>
                    <input type="number" name="fee_transport[]" class="w-full form-input" placeholder="0">
                </div>
                <div>
                    <label class="text-[10px] text-gray-400">Other Fees</label>
                    <input type="number" name="fee_others[]" class="w-full form-input" placeholder="0">
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="removeFeeRow(this)" class="w-12 h-12 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all">
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
                        div.className = 'aspect-square rounded-2xl overflow-hidden shadow-lg border-2 border-blue-400 relative group';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-blue-600/20"></div>
                            <div class="absolute top-3 right-3 bg-blue-600 text-white px-2 py-1 rounded-lg flex items-center justify-center text-[8px] font-black shadow-xl ring-2 ring-white">NEW</div>
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