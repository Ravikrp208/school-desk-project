<?php
// admin/index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../includes/common.php';
require_once '../includes/auth.php';

// Protect the page
protect_admin_page();

// Fetch stats
$totalEnquiries = (int)$pdo->query('SELECT COUNT(*) FROM enquiries')->fetchColumn();
$pendingSchools = (int)$pdo->query("SELECT COUNT(*) FROM schools WHERE status = 'pending'")->fetchColumn();
$activeSchools = (int)$pdo->query("SELECT COUNT(*) FROM schools WHERE status = 'approved'")->fetchColumn();
$convertedLeads = (int)$pdo->query("SELECT COUNT(*) FROM enquiry_school_mapping WHERE admission_status = 'admission_done'")->fetchColumn();
$platformRevenue = "$14.2k"; // Mock value for UI matching

// Fetch Notifications
$unreadNotifsCount = (int)$pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")->fetchColumn();
$recentNotifs = $pdo->query("SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Fetch recent leads
$recentEnquiries = $pdo->query(
    "SELECT e.*, GROUP_CONCAT(CONCAT(s.name, ' (', esm.admission_status, ')') SEPARATOR ' | ') as school_details 
     FROM enquiries e 
     LEFT JOIN enquiry_school_mapping esm ON e.id = esm.enquiry_id 
     LEFT JOIN schools s ON esm.school_id = s.id 
     GROUP BY e.id 
     ORDER BY e.created_at DESC LIMIT 6"
)->fetchAll();

// Fetch verification requests
$pendingSchoolRows = $pdo->query("SELECT * FROM schools WHERE status='pending' ORDER BY created_at DESC LIMIT 2")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['school_id'])) {
    $schoolId = (int)$_POST['school_id'];
    if ($_POST['action'] === 'approve') {
        // Fetch school details
        $schoolStmt = $pdo->prepare("SELECT name, contact_email, contact_phone FROM schools WHERE id = ?");
        $schoolStmt->execute([$schoolId]);
        $school = $schoolStmt->fetch();

        if ($school) {
            $email = $school['contact_email'];
            $phone = $school['contact_phone'];
            $name = $school['name'];

            // Check if user already exists
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

            // Update school status and associate with the user
            $updateStmt = $pdo->prepare("UPDATE schools SET status='approved', user_id=:user_id WHERE id=:id");
            $updateStmt->execute(['user_id' => $userId, 'id' => $schoolId]);

            // Send Credentials via PHPMailer
            require_once '../includes/mailer.php';
            $mailStatus = msd_send_credentials($email, $phone, $name);
            
            if ($mailStatus['success']) {
                $_SESSION['message'] = "School approved successfully and credentials sent to $email!";
            } else {
                $_SESSION['error'] = "School approved, but mail failed: " . $mailStatus['message'];
            }
        }
    }
    header('Location: index.php');
    exit;
}

// Get messages from session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['message'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Console | MySchoolDesk</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F4F7FE; }
        .sidebar { background: #FFFFFF; border-right: 1px solid #E2E8F0; }
        .nav-item-active { background: #F4F7FE; color: #4318FF; font-weight: 800; }
        .nav-item { color: #A3AED0; font-weight: 600; }
        .nav-item:hover { color: #1B2559; }
        .metric-card { border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); transition: all 0.3s ease; }
        .metric-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .lead-id { color: #4318FF; font-weight: 800; }
        /* Sidebar transition */
        #sidebar.open { transform: translateX(0); }
        #sidebar-overlay.open { display: block; opacity: 1; }
    </style>
</head>
<body class="flex flex-col lg:flex-row min-h-screen">

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-[45] hidden opacity-0 transition-opacity duration-300 lg:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 sidebar flex flex-col fixed inset-y-0 left-0 z-[50] p-6 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out h-full overflow-y-auto">
        <div class="mb-10 px-4">
            <h1 class="text-xl font-extrabold text-[#1B2559]">Admin Console</h1>
            <p class="text-[10px] font-bold text-[#A3AED0] uppercase tracking-widest">Management Portal</p>
        </div>

        <nav class="flex-1 space-y-2">
            <a href="index.php" class="flex items-center gap-4 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'nav-item-active' : 'nav-item'; ?> px-4 py-3 rounded-lg transition-all">
                <i class="fa-solid fa-house-chimney text-lg"></i> Dashboard
            </a>
            <a href="enquiries.php" class="flex items-center gap-4 <?php echo basename($_SERVER['PHP_SELF']) == 'enquiries.php' ? 'nav-item-active' : 'nav-item'; ?> px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-comment-dots text-lg"></i> Enquiries
            </a>
            <a href="school_profile.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-user-graduate text-lg"></i> School Profile
            </a>
            <a href="logout.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-red-50 hover:text-red-500 rounded-lg transition-all">
                <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i> Logout
            </a>
        </nav>

        <!-- Sidebar Widget -->
        <div class="mt-auto bg-[#F4F7FE] p-4 rounded-2xl border border-gray-100 mb-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-white rounded-full border border-gray-200 flex items-center justify-center p-1">
                    <img src="../assets/images/logo_boy.png" class="w-full h-full object-cover">
                </div>
                <div>
                    <h4 class="text-xs font-black text-[#1B2559]">School Logo</h4>
                    <span class="text-[10px] font-bold text-gray-400">Active Admin</span>
                </div>
            </div>
            <button class="w-full bg-[#1B2559] text-white py-2.5 rounded-xl text-xs font-extrabold hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
                WhatsApp Support
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-64 p-4 lg:p-10 transition-all duration-300">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl lg:text-3xl font-black text-[#1B2559]">The Digital Atelier</h2>
                    <p class="text-xs lg:text-sm font-bold text-[#A3AED0]">Welcome back, Platform Overseer.</p>
                </div>
                <!-- Hamburger Menu Button -->
                <button id="sidebarToggle" class="lg:hidden w-10 h-10 bg-white rounded-xl flex items-center justify-center text-[#1B2559] shadow-sm">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
            </div>
            <div class="flex flex-col md:flex-row items-center gap-4 lg:gap-6 w-full lg:w-auto">
                <div class="relative w-full md:w-80 group">
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                    <input type="text" id="dashboardSearch" placeholder="Search leads or schools..." 
                           class="w-full bg-white border-none rounded-2xl lg:rounded-full py-3 px-14 text-sm font-bold text-[#1B2559] placeholder:text-gray-400 focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm">
                </div>
                <div class="flex gap-4 w-full md:w-auto justify-end">
                    <div class="relative group/notif">
                        <button class="w-10 h-10 lg:w-12 lg:h-12 bg-white rounded-xl lg:rounded-2xl flex items-center justify-center text-gray-500 relative shadow-sm hover:shadow-md transition-all active:scale-95 group">
                            <i class="fa-solid fa-bell group-hover:rotate-12 transition-transform"></i>
                            <?php if ($unreadNotifsCount > 0): ?>
                                <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                            <?php endif; ?>
                        </button>
                        
                        <!-- Notifications Dropdown -->
                        <div class="absolute right-0 mt-4 w-80 bg-white rounded-[24px] shadow-2xl border border-gray-100 py-6 px-4 hidden group-hover/notif:block z-[100] animate-fade-in-up">
                            <div class="flex items-center justify-between mb-4 px-2">
                                <h4 class="text-sm font-black text-[#1B2559]">Notifications</h4>
                                <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-lg"><?php echo $unreadNotifsCount; ?> New</span>
                            </div>
                            <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                                <?php if (empty($recentNotifs)): ?>
                                    <div class="py-10 text-center">
                                        <i class="fa-solid fa-bell-slash text-gray-200 text-3xl mb-3"></i>
                                        <p class="text-xs font-bold text-gray-400 italic">No new notifications</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($recentNotifs as $notif): ?>
                                        <a href="contact_messages.php?id=<?php echo $notif['reference_id']; ?>" class="flex items-start gap-4 p-3 rounded-2xl hover:bg-gray-50 transition-all group/item">
                                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                                                <i class="fa-solid fa-envelope-open-text"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-xs font-bold text-[#1B2559] leading-tight mb-1"><?php echo htmlspecialchars($notif['message']); ?></p>
                                                <span class="text-[10px] font-bold text-gray-400 italic"><?php echo time_elapsed_string($notif['created_at']); ?></span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($unreadNotifsCount > 0): ?>
                                <div class="mt-6 pt-4 border-t border-gray-50 text-center">
                                    <a href="notifications.php" class="text-[11px] font-black text-blue-600 hover:text-blue-700 uppercase tracking-widest">View All Notifications</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button class="w-10 h-10 lg:w-12 lg:h-12 bg-white rounded-xl lg:rounded-2xl flex items-center justify-center text-gray-500 shadow-sm hover:shadow-md transition-all active:scale-95">
                        <i class="fa-solid fa-circle-info"></i>
                    </button>
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-[#05CD99]/10 border-l-4 border-[#05CD99] text-[#05CD99] p-5 mb-10 rounded-2xl shadow-sm flex items-center gap-4 animate-fade-in" role="alert">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <p class="font-extrabold text-sm"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <!-- Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 mb-10">
            <!-- Card 1 -->
            <div class="bg-white p-6 metric-card flex items-center gap-5 border border-transparent hover:border-blue-100 relative overflow-hidden group">
                <div class="w-14 h-14 rounded-2xl bg-[#F4F7FE] flex items-center justify-center text-[#4318FF] text-xl transform group-hover:rotate-6 transition-transform">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>
                <div class="flex-1">
                    <span class="block text-[10px] font-black text-[#A3AED0] uppercase tracking-widest mb-1">Total Enquiries</span>
                    <div class="flex items-end gap-2">
                        <span class="text-2xl font-black text-[#1B2559] leading-none"><?php echo number_format($totalEnquiries); ?></span>
                        <span class="text-[10px] font-bold text-[#05CD99] mb-0.5">+12% this month</span>
                    </div>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="bg-white p-6 metric-card flex items-center gap-5 border-l-4 border-orange-500 relative group">
                <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-500 text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <span class="block text-[10px] font-black text-[#A3AED0] uppercase tracking-widest mb-1">Pending Verification</span>
                    <span class="text-2xl font-black text-[#1B2559] block leading-none"><?php echo $pendingSchools; ?></span>
                    <span class="text-[10px] font-bold text-gray-400 mt-1 block">Schools awaiting approval</span>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="bg-white p-6 metric-card flex items-center gap-5 border border-transparent hover:border-green-100 group">
                <div class="w-14 h-14 rounded-2xl bg-[#05CD99]/10 flex items-center justify-center text-[#05CD99] text-xl transition-transform group-hover:rotate-12">
                    <i class="fa-solid fa-school-flag"></i>
                </div>
                <div>
                    <span class="block text-[10px] font-black text-[#A3AED0] uppercase tracking-widest mb-1">Active Schools</span>
                    <span class="text-2xl font-black text-[#1B2559] block leading-none"><?php echo $activeSchools; ?></span>
                    <span class="text-[10px] font-bold text-gray-400 mt-1 block">Across 18 cities</span>
                </div>
            </div>
            <!-- Card 4 -->
            <div class="bg-[#4318FF] p-6 metric-card flex items-center gap-5 text-white shadow-xl shadow-blue-500/20 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
                <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-white text-xl">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="relative z-10">
                    <span class="block text-[10px] font-black text-white/60 uppercase tracking-widest mb-1">Platform Revenue</span>
                    <span class="text-2xl font-black block leading-none"><?php echo $platformRevenue; ?></span>
                    <span class="text-[10px] font-bold text-white/60 mt-1 block">Subscription MRR</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-8">
            <!-- Left Grid: Table -->
            <div class="col-span-12 xl:col-span-8 space-y-8">


                <!-- Enquiry Lead Tracker -->
                <div class="bg-white rounded-[32px] lg:rounded-[40px] p-6 lg:p-10 metric-card">
                    <div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4">
                        <h3 class="text-xl font-black text-[#1B2559]">Enquiry Lead Tracker</h3>
                        <div class="flex gap-2 lg:gap-3 w-full sm:w-auto">
                            <button class="flex-1 sm:flex-none bg-[#F4F7FE] text-[#1B2559] font-black px-4 lg:px-6 py-2.5 rounded-xl text-[10px] lg:text-xs uppercase tracking-widest">Export CSV</button>
                            <button class="flex-1 sm:flex-none bg-orange-500 text-white font-black px-4 lg:px-6 py-2.5 rounded-xl text-[10px] lg:text-xs uppercase tracking-widest shadow-lg shadow-orange-500/30">Filter View</button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="text-[10px] font-black text-[#A3AED0] uppercase tracking-widest">
                                <tr class="border-b border-gray-100">
                                    <th class="pb-5 px-4">Lead ID</th>
                                    <th class="pb-5 px-4 whitespace-nowrap">Parent Name</th>
                                    <th class="pb-5 px-4 whitespace-nowrap">Applied Schools</th>
                                    <th class="pb-5 px-4">Status</th>
                                    <th class="pb-5 px-4 text-right">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody id="dashboardLeadsTable" class="divide-y divide-gray-50/50">
                                <?php foreach($recentEnquiries as $enquiry): 
                                    $initials = strtoupper(substr($enquiry['parent_name'], 0, 2));
                                    $bgColors = ['bg-blue-100 text-[#4318FF]', 'bg-orange-100 text-orange-600', 'bg-purple-100 text-purple-600', 'bg-green-100 text-green-600'];
                                    $color = $bgColors[rand(0, 3)];
                                ?>
                                <tr class="group hover:bg-[#F4F7FE]/50 transition-colors cursor-pointer">
                                    <td class="py-6 px-4">
                                        <span class="text-sm font-black text-[#4318FF]">#LD-<?php echo substr($enquiry['lead_id'], -4); ?></span>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl <?php echo $color; ?> flex items-center justify-center text-[11px] font-black shadow-sm"><?php echo $initials; ?></div>
                                            <span class="text-sm font-black text-[#1B2559]"><?php echo htmlspecialchars($enquiry['parent_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <span class="text-sm font-bold text-gray-400"><?php echo htmlspecialchars(explode(' | ', $enquiry['school_details'])[0]); ?></span>
                                    </td>
                                    <td class="py-6 px-4">
                                        <?php if(str_contains($enquiry['school_details'], 'admission_done')): ?>
                                            <div class="flex items-center gap-2 bg-[#05CD99]/10 px-3 py-1.5 rounded-lg w-fit">
                                                <div class="w-1.5 h-1.5 rounded-full bg-[#05CD99]"></div>
                                                <span class="text-[10px] font-black uppercase text-[#05CD99] tracking-widest">Converted</span>
                                            </div>
                                        <?php elseif(rand(0,1)): ?>
                                            <div class="flex items-center gap-2 bg-[#4318FF] px-3 py-1.5 rounded-lg w-fit">
                                                <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                                <span class="text-[10px] font-black uppercase text-white tracking-widest">New Lead</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex items-center gap-2 bg-orange-500 px-3 py-1.5 rounded-lg w-fit">
                                                <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                                                <span class="text-[10px] font-black uppercase text-white tracking-widest">Follow-up</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-6 px-4 text-right">
                                        <span class="text-xs font-bold text-gray-400"><?php echo rand(2, 59); ?>m ago</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-8 text-center pt-8 border-t border-gray-50">
                        <a href="enquiries.php" class="inline-flex items-center gap-2 text-sm font-black text-[#4318FF] hover:gap-3 transition-all">
                            View All Leads <i class="fa-solid fa-arrow-right-long mt-0.5"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Grid: Widgets -->
            <div class="col-span-12 xl:col-span-4 space-y-8">

                <!-- Verification Requests -->
                <div class="bg-white rounded-[32px] p-6 lg:p-8 metric-card">
                    <h3 class="text-lg font-black text-[#1B2559] mb-6">Verification Requests</h3>
                    <div class="space-y-6">
                        <?php foreach($pendingSchoolRows as $school): ?>
                        <div class="flex items-start gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl overflow-hidden shrink-0 shadow-sm">
                                <?php 
                                    $photos = json_decode($school['photos'] ?? '[]', true);
                                    $school_img = !empty($photos) ? '../' . $photos[0] : '../assets/images/school1.png';
                                ?>
                                <img src="<?php echo htmlspecialchars($school_img); ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <h4 class="text-sm font-black text-[#1B2559] truncate"><?php echo htmlspecialchars($school['name']); ?></h4>
                                <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase"><?php echo htmlspecialchars($school['city']); ?> • <?php echo rand(5, 12); ?> Documents</p>
                                <div class="flex gap-2 mt-4">
                                    <form method="POST">
                                        <input type="hidden" name="school_id" value="<?php echo $school['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button class="bg-[#4318FF] text-white px-4 py-2 rounded-xl text-[10px] font-black transition-all hover:bg-blue-700 shadow-lg shadow-blue-500/20">Verify</button>
                                    </form>
                                    <button class="bg-gray-100 text-gray-500 px-4 py-2 rounded-xl text-[10px] font-black transition-all hover:bg-gray-200">Review</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if(empty($pendingSchoolRows)): ?>
                            <div class="flex flex-col items-center justify-center py-10 opacity-50">
                                <i class="fa-solid fa-folder-open text-3xl text-gray-200 mb-4"></i>
                                <p class="text-xs font-bold text-gray-400 italic">No new requests</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>



                <!-- Floating Action Button Idea -->
                <div class="fixed bottom-10 right-10 z-[100]">
                    <button class="w-16 h-16 bg-orange-500 text-white rounded-[20px] shadow-2xl shadow-orange-500/40 flex items-center justify-center text-2xl hover:scale-110 active:scale-95 transition-all group">
                        <i class="fa-solid fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-20 pt-10 border-t border-gray-100 flex flex-col md:flex-row gap-10 opacity-70">
            <div class="flex-1">
                <h4 class="text-lg font-black text-[#1B2559] mb-4">MySchoolDesk</h4>
                <p class="text-sm font-medium text-gray-500 leading-relaxed max-w-sm">The Digital Atelier of Discovery. Precision management for modern education.</p>
            </div>
            <div class="flex-1">
                <h5 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-6">SYSTEM LINKS</h5>
                <ul class="space-y-3">
                    <li><a href="#" class="text-sm font-bold text-gray-500 hover:text-[#1B2559]">About Us</a></li>
                    <li><a href="#" class="text-sm font-bold text-gray-500 hover:text-[#1B2559]">Terms of Service</a></li>
                    <li><a href="#" class="text-sm font-bold text-gray-500 hover:text-[#1B2559]">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="flex-1">
                <h5 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-6">SUPPORT HUB</h5>
                <ul class="space-y-3">
                    <li><a href="#" class="text-sm font-bold text-gray-500 hover:text-[#1B2559]">Contact Support</a></li>
                    <li><a href="#" class="text-sm font-bold text-gray-500 hover:text-[#1B2559]">Sitemap</a></li>
                    <li><a href="#" class="text-sm font-bold text-gray-500 hover:text-[#1B2559]">API Docs</a></li>
                </ul>
            </div>
            <div class="flex-1">
                <h5 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-6">COMPLIANCE</h5>
                <div class="bg-gray-100 p-4 rounded-xl">
                    <p class="text-[10px] font-bold text-gray-500 leading-relaxed">© 2024 MySchoolDesk. Registered trade entity. All portal activity is logged for security audits.</p>
                </div>
            </div>
        </footer>
    <script>
        // Sidebar Toggle Logic
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            setTimeout(() => {
                overlay.classList.toggle('opacity-0');
            }, 10);
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        // Search Logic
        document.getElementById('dashboardSearch').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#dashboardLeadsTable tr');
            
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
