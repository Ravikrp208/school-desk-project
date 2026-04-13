<?php
// school_dashboard/index.php
require_once '../config.php';
require_once '../includes/common.php';
require_once '../includes/auth.php';

// Protect the page
protect_school_page();

// Fetch the school associated with the logged-in user
$userId = $_SESSION['user_id'];
$schoolStmt = $pdo->prepare('SELECT * FROM schools WHERE user_id = :user_id LIMIT 1');
$schoolStmt->execute(['user_id' => $userId]);
$school = $schoolStmt->fetch();

if (!$school) {
    die('No school profile associated with your account. Please contact admin.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enquiry_id'], $_POST['status'])) {
    $enquiryId = (int)$_POST['enquiry_id'];
    $status = $_POST['status'];
    if (in_array($status, ['pending', 'admission_done', 'not_converted'], true)) {
        $updateStmt = $pdo->prepare(
            'UPDATE enquiry_school_mapping SET admission_status = :status WHERE enquiry_id = :enquiry_id AND school_id = :school_id'
        );
        $updateStmt->execute([
            'status' => $status,
            'enquiry_id' => $enquiryId,
            'school_id' => $school['id'],
        ]);
    }
    
    header('Location: index.php?school_id=' . $school['id']);
    exit;
}

// Stats
$statsStmt = $pdo->prepare(
    "SELECT 
      COUNT(*) AS total,
      SUM(CASE WHEN DATE(e.created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS new_week,
      SUM(CASE WHEN esm.admission_status='admission_done' THEN 1 ELSE 0 END) AS admission_done
     FROM enquiry_school_mapping esm
     JOIN enquiries e ON e.id = esm.enquiry_id
     WHERE esm.school_id = :school_id"
);
$statsStmt->execute(['school_id' => $school['id']]);
$stats = $statsStmt->fetch() ?: ['total' => 0, 'new_week' => 0, 'admission_done' => 0];

// Enquiries
$enquiries = $pdo->prepare(
    "SELECT e.*, esm.admission_status, esm.follow_up_notes 
     FROM enquiries e 
     JOIN enquiry_school_mapping esm ON e.id = esm.enquiry_id 
     WHERE esm.school_id = :school_id 
     ORDER BY e.created_at DESC"
);
$enquiries->execute(['school_id' => $school['id']]);
$enquiryRows = $enquiries->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($school['name']); ?> Dash - MySchoolDesk</title>
    <link rel="icon" type="image/png" href="../assets/images/logo_boy.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
    </style>
</head>
<body class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-12 overflow-y-auto">
        <header class="flex items-center justify-between mb-12">
            <div class="flex items-center gap-6">
                <div>
                    <h1 class="text-3xl font-black text-slate-900"><?php echo htmlspecialchars($school['name']); ?></h1>
                    <p class="text-slate-500 font-medium"><?php echo htmlspecialchars($school['city']); ?> • Partner Dashboard</p>
                </div>
                <?php if($school['is_verified']): ?>
                <div class="bg-amber-50 text-amber-600 p-2 px-4 rounded-2xl border border-amber-100 text-xs font-black flex items-center gap-2">
                    <i class="fa-solid fa-certificate"></i> VERIFIED
                </div>
                <?php endif; ?>
            </div>
            <div class="hidden md:block">
                <span class="text-xs font-black text-slate-400 uppercase tracking-widest block mb-1">CURRENT STATUS</span>
                <span class="text-green-600 font-black text-lg flex items-center gap-2"><div class="w-3 h-3 bg-green-500 rounded-full"></div> Online & Accepting Leads</span>
            </div>
        </header>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-sm">
                <span class="block text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2">TOTAL LEADS</span>
                <span class="text-3xl font-black text-slate-900"><?php echo (int)$stats['total']; ?></span>
            </div>
            <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-sm">
                <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-2">NEW THIS WEEK</span>
                <span class="text-3xl font-black text-slate-900"><?php echo (int)$stats['new_week']; ?></span>
            </div>
            <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-sm">
                <span class="block text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">CONVERSIONS</span>
                <span class="text-3xl font-black text-slate-900"><?php echo (int)$stats['admission_done']; ?></span>
            </div>
            <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-sm">
                <span class="block text-[10px] font-black text-amber-500 uppercase tracking-widest mb-2">AVG RATING</span>
                <span class="text-3xl font-black text-slate-900">4.8 <i class="fa-solid fa-star text-sm"></i></span>
            </div>
        </div>

        <!-- Enquiries Table -->
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <h2 class="text-xl font-black text-slate-900">Active Enquiry Inbox</h2>
                <div class="flex gap-2">
                    <span class="bg-green-50 text-green-600 text-[10px] font-black px-3 py-1.5 rounded-lg border border-green-100">REAL-TIME SYNC</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <tr>
                            <th class="p-6">Lead / Applicant</th>
                            <th class="p-6">Class Info</th>
                            <th class="p-6">Contact & Notes</th>
                            <th class="p-6" width="200">Processing Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach($enquiryRows as $item): ?>
                        <tr class="hover:bg-slate-50/50 transition-all">
                            <td class="p-6">
                                <span class="bg-blue-50 text-blue-700 font-black px-2 py-1 rounded-md text-[10px] mb-2 inline-block"><?php echo $item['lead_id']; ?></span>
                                <span class="block font-bold text-slate-800 text-lg"><?php echo htmlspecialchars($item['parent_name']); ?></span>
                                <span class="block text-xs text-slate-500 font-medium">Child: <?php echo htmlspecialchars($item['child_name']); ?></span>
                            </td>
                            <td class="p-6">
                                <div class="space-y-1">
                                    <span class="block text-xs font-black text-slate-400 uppercase">GRADE</span>
                                    <span class="text-slate-900 font-black"><?php echo htmlspecialchars($item['child_class']); ?></span>
                                </div>
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-3 mb-3">
                                    <a href="tel:<?php echo $item['mobile']; ?>" class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center text-xs"><i class="fa-solid fa-phone"></i></a>
                                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $item['mobile']); ?>" class="w-8 h-8 bg-[#25D366]/10 text-[#25D366] rounded-lg flex items-center justify-center text-xs"><i class="fa-brands fa-whatsapp"></i></a>
                                    <span class="font-bold text-slate-800 text-sm"><?php echo $item['mobile']; ?></span>
                                </div>
                                <form method="POST" class="relative group">
                                    <input type="hidden" name="enquiry_id" value="<?php echo $item['id']; ?>">
                                    <input type="text" name="notes" value="<?php echo htmlspecialchars($item['follow_up_notes'] ?? ''); ?>" placeholder="Add follow-up notes..." 
                                           class="w-full bg-slate-50 border-none focus:ring-1 focus:ring-blue-500 rounded-xl px-4 py-2 text-xs font-medium text-slate-600 placeholder:text-slate-300 transition-all hover:bg-white" 
                                           onblur="if(this.value != '<?php echo addslashes($item['follow_up_notes'] ?? ''); ?>') this.form.submit()">
                                </form>
                            </td>
                            <td class="p-6">
                                <form method="POST">
                                    <input type="hidden" name="enquiry_id" value="<?php echo $item['id']; ?>">
                                    <select name="status" onchange="this.form.submit()" class="w-full bg-slate-100 border-none text-slate-800 font-black text-[10px] p-3 rounded-xl uppercase tracking-widest outline-none focus:ring-2 focus:ring-green-500 cursor-pointer">
                                        <option value="pending" <?php echo $item['admission_status'] === 'pending' ? 'selected' : ''; ?>>Pending Intake</option>
                                        <option value="admission_done" <?php echo $item['admission_status'] === 'admission_done' ? 'selected' : ''; ?>>Admission Successful ✨</option>
                                        <option value="not_converted" <?php echo $item['admission_status'] === 'not_converted' ? 'selected' : ''; ?>>Dropped / Other ❌</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($enquiryRows)): ?>
                            <tr><td colspan="4" class="p-20 text-center text-slate-400 italic font-bold">Waiting for new leads...</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
