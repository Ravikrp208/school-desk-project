<?php
// admin/enquiries.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../includes/common.php';
require_once '../includes/auth.php';

// Protect the page
protect_admin_page();

// Fetch stats for enquiries
$totalEnquiries = (int)$pdo->query('SELECT COUNT(*) FROM enquiries')->fetchColumn();
$convertedLeads = (int)$pdo->query("SELECT COUNT(*) FROM enquiry_school_mapping WHERE admission_status = 'admission_done'")->fetchColumn();
$pendingLeads = (int)$pdo->query("SELECT COUNT(*) FROM enquiry_school_mapping WHERE admission_status = 'pending'")->fetchColumn();

// Fetch Notifications
$unreadNotifsCount = (int)$pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")->fetchColumn();
$recentNotifs = $pdo->query("SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Fetch all enquiries with their mapped schools and multi-school count
$enquiries = $pdo->query(
    "SELECT e.*, 
            GROUP_CONCAT(CONCAT(s.name, ' (', esm.admission_status, ')') SEPARATOR ', ') as school_list,
            GROUP_CONCAT(CONCAT(s.id, '::', s.name, '::', esm.admission_status, '::', IFNULL(esm.follow_up_notes, '')) SEPARATOR '|||') as mapping_details,
            COUNT(esm.school_id) as school_count
     FROM enquiries e 
     LEFT JOIN enquiry_school_mapping esm ON e.id = esm.enquiry_id 
     LEFT JOIN schools s ON esm.school_id = s.id 
     GROUP BY e.id 
     ORDER BY e.created_at DESC"
)->fetchAll();

// Handle Follow-up Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_followup'])) {
    $enqId = (int)$_POST['enquiry_id'];
    $schoolId = (int)$_POST['school_id'];
    $status = $_POST['admission_status'];
    $notes = trim($_POST['follow_up_notes']);

    $stmt = $pdo->prepare("UPDATE enquiry_school_mapping SET admission_status = ?, follow_up_notes = ? WHERE enquiry_id = ? AND school_id = ?");
    $stmt->execute([$status, $notes, $enqId, $schoolId]);
    header("Location: enquiries.php?msg=updated");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enquiries Management | Admin Console</title>
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
    </style>
</head>
<body class="flex flex-col lg:flex-row min-h-screen overflow-x-hidden">

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-[45] hidden opacity-0 transition-opacity duration-300 lg:hidden focus-within:z-[50]"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 sidebar flex flex-col fixed inset-y-0 left-0 z-[55] p-6 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out h-full overflow-y-auto">
        <div class="mb-10 px-4">
            <h1 class="text-xl font-extrabold text-[#1B2559]">Admin Console</h1>
            <p class="text-[10px] font-bold text-[#A3AED0] uppercase tracking-widest">Management Portal</p>
        </div>

        <nav class="flex-1 space-y-2">
            <a href="index.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-house-chimney text-lg"></i> Dashboard
            </a>
            <a href="enquiries.php" class="flex items-center gap-4 nav-item-active px-4 py-3 rounded-lg transition-all">
                <i class="fa-solid fa-comment-dots text-lg"></i> Enquiries
            </a>
            <a href="school_profile.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-user-graduate text-lg"></i> School Profile
            </a>
            <a href="settings.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-gear text-lg"></i> Global Settings
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
                <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp Support
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-64 p-4 lg:p-8 transition-all duration-300">
        <!-- Header -->
        <header class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl lg:text-3xl font-black text-[#1B2559]">Parent Enquiries</h2>
                    <p class="text-xs lg:text-sm font-bold text-[#A3AED0]">Monitor and manage all admission leads across the platform.</p>
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
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="metric-card bg-white p-6 lg:p-8 flex items-center gap-6 group hover:border-blue-100 border border-transparent">
                <div class="w-14 lg:w-16 h-14 lg:h-16 bg-[#F4F7FE] rounded-2xl flex items-center justify-center text-[#4318FF] text-2xl group-hover:rotate-6 transition-transform">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-[#A3AED0] uppercase tracking-widest leading-none block mb-1">TOTAL ENQUIRIES</span>
                    <h3 class="text-xl lg:text-2xl font-bold text-[#1B2559]"><?php echo $totalEnquiries; ?></h3>
                </div>
            </div>
            <div class="metric-card bg-white p-6 lg:p-8 flex items-center gap-6 group hover:border-green-100 border border-transparent">
                <div class="w-14 lg:w-16 h-14 lg:h-16 bg-[#05CD99]/10 rounded-2xl flex items-center justify-center text-[#05CD99] text-2xl group-hover:-rotate-6 transition-transform">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-[#A3AED0] uppercase tracking-widest leading-none block mb-1">CONVERTED</span>
                    <h3 class="text-xl lg:text-2xl font-black text-[#1B2559]"><?php echo $convertedLeads; ?></h3>
                </div>
            </div>
            <div class="metric-card bg-white p-6 lg:p-8 flex items-center gap-6 group hover:border-orange-100 border border-transparent">
                <div class="w-14 lg:w-16 h-14 lg:h-16 bg-[#FFB547]/10 rounded-2xl flex items-center justify-center text-[#FFB547] text-2xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-[#A3AED0] uppercase tracking-widest leading-none block mb-1">PENDING</span>
                    <h3 class="text-xl lg:text-2xl font-black text-[#1B2559]"><?php echo $pendingLeads; ?></h3>
                </div>
            </div>
        </div>

        <!-- Enquiries List -->
        <div class="bg-white rounded-[32px] p-6 lg:p-8 metric-card border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 items-center mb-8 gap-4">
                <h3 class="text-lg font-bold text-[#1B2559] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    Enquiry Tracker
                </h3>
                
                <!-- Centered Search Box: Same to Same Design -->
                <div class="relative w-full max-w-sm mx-auto group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-[#A3AED0] text-sm"></i>
                    </div>
                    <input type="text" id="enquirySearch" onkeyup="filterEnquiries()" 
                           class="block w-full pl-12 pr-6 py-3 border-2 border-blue-100 bg-white text-[#1B2559] text-[13px] font-semibold rounded-full focus:border-[#4318FF]/40 focus:ring-4 focus:ring-[#4318FF]/5 transition-all placeholder:text-[#94A3B8]" 
                           placeholder="Search leads or schools...">
                </div>

                <div class="flex flex-wrap md:flex-nowrap justify-end gap-3 order-last md:order-none">
                    <select id="statusFilter" onchange="filterEnquiries()" class="bg-[#F4F7FE] border-none rounded-xl text-[10px] font-extrabold px-4 py-2.5 uppercase tracking-widest text-[#4318FF] cursor-pointer">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="admission_done">Admission Done</option>
                        <option value="not_converted">Not Converted</option>
                        <option value="multi">Multiple Schools</option>
                    </select>
                    <a href="export_enquiries.php" class="bg-white border border-[#4318FF] text-[#4318FF] text-[9px] font-extrabold px-5 py-2.5 rounded-xl uppercase tracking-widest hover:bg-[#4318FF] hover:text-white transition-all shadow-sm">Export Report</a>
                </div>
            </div>

            <div class="overflow-x-auto -mx-6 px-6">
                <table class="w-full text-left min-w-[800px]">
                    <thead>
                        <tr class="text-[11px] font-extrabold text-[#A3AED0] uppercase tracking-widest border-b border-gray-50 pb-4">
                            <th class="pb-3 px-4">LEAD ID</th>
                            <th class="pb-3 px-4">PARENT & CHILD</th>
                            <th class="pb-3 px-4">CONTACT INFO</th>
                            <th class="pb-3 px-4">APPLIED SCHOOLS</th>
                            <th class="pb-3 px-4 text-right pr-6">GENERATED AT</th>
                        </tr>
                    </thead>
                    <tbody id="enquiryTableBody" class="divide-y divide-gray-50">
                        <?php foreach($enquiries as $enq): ?>
                        <tr class="group hover:bg-gray-50/50 transition-all cursor-pointer" onclick="openLeadDetails(<?php echo htmlspecialchars(json_encode($enq)); ?>)">
                            <td class="py-5 px-4">
                                <span class="bg-[#F4F7FE] text-[#4318FF] font-extrabold px-3 py-1.5 rounded-lg text-xs tracking-tight group-hover:bg-[#4318FF] group-hover:text-white transition-colors">#LD-<?php echo substr($enq['lead_id'], -4); ?></span>
                            </td>
                            <td class="py-5 px-4">
                                <h4 class="text-[15px] font-bold text-[#1B2559]"><?php echo htmlspecialchars($enq['parent_name']); ?></h4>
                                <p class="text-[11px] font-semibold text-gray-400 uppercase mt-1 tracking-tight">CHILD: <?php echo htmlspecialchars($enq['child_name']); ?> (<?php echo htmlspecialchars($enq['child_class']); ?>)</p>
                            </td>
                            <td class="py-6 px-4">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <div class="w-6 h-6 bg-slate-50 rounded-lg flex items-center justify-center text-[10px] text-slate-400 border border-slate-100">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <span class="text-xs font-black text-slate-700"><?php echo htmlspecialchars($enq['mobile']); ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-slate-50 rounded-lg flex items-center justify-center text-[10px] text-slate-400 border border-slate-100">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight"><?php echo htmlspecialchars($enq['email']); ?></span>
                                </div>
                            </td>
                            <td class="py-6 px-4">
                                <div class="flex flex-col gap-1.5">
                                    <p class="text-[11px] font-bold text-slate-600 leading-relaxed uppercase tracking-tight flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        <?php echo htmlspecialchars($enq['school_list'] ?: 'N/A'); ?>
                                    </p>
                                    <?php if($enq['school_count'] > 1): ?>
                                        <span class="bg-orange-50 text-orange-600 text-[8px] font-black px-2 py-0.5 rounded uppercase tracking-widest w-fit">Applied to <?php echo $enq['school_count']; ?> Schools</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="py-6 px-4 text-right pr-8">
                                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest"><?php echo date('d M Y', strtotime($enq['created_at'])); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; if(empty($enquiries)): ?>
                        <tr><td colspan="5" class="py-20 text-center text-gray-400 font-bold italic">No enquiries received yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    </main>

    <!-- Lead Details Modal -->
    <div id="leadModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-[40px] shadow-2xl overflow-hidden animate-fade-in-up">
            <div class="bg-blue-600 p-8 flex items-center justify-between text-white">
                <div>
                    <span id="modalLeadId" class="text-[10px] font-black uppercase tracking-[0.3em] opacity-80 mb-2 block"></span>
                    <h3 id="modalParentName" class="text-2xl font-black"></h3>
                </div>
                <button onclick="closeLeadModal()" class="w-12 h-12 bg-white/20 hover:bg-white/40 rounded-2xl flex items-center justify-center transition-all">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <div class="p-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-2 gap-8 mb-10">
                    <div class="space-y-6">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">General Information</h4>
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase mb-1">Child Details</p>
                            <p id="modalChildInfo" class="text-sm font-bold text-slate-700"></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase mb-1">Previous School</p>
                            <p id="modalExistingSchool" class="text-sm font-bold text-slate-700"></p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Contact Details</h4>
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase mb-1">Phone / WhatsApp</p>
                            <p id="modalPhone" class="text-sm font-black text-slate-900"></p>
                            <a id="modalWhatsappSendList" href="#" target="_blank" class="mt-2 inline-flex items-center gap-2 bg-[#25D366] text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-green-500/20">
                                <i class="fa-brands fa-whatsapp text-lg"></i> Send List
                            </a>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase mb-1">Email Address</p>
                            <p id="modalEmail" class="text-sm font-bold text-slate-700"></p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-8">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Applied Schools & Follow-ups</h4>
                    <div id="modalSchoolsList" class="space-y-4">
                        <!-- Schools will be injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebar-overlay');
        const leadModal = document.getElementById('leadModal');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            setTimeout(() => { overlay.classList.toggle('opacity-0'); }, 10);
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        function openLeadDetails(lead) {
            document.getElementById('modalLeadId').innerText = 'Lead ID ' + lead.lead_id;
            document.getElementById('modalParentName').innerText = lead.parent_name;
            document.getElementById('modalChildInfo').innerText = lead.child_name + ' (Class: ' + lead.child_class + ')';
            document.getElementById('modalExistingSchool').innerText = lead.existing_school || 'N/A';
            document.getElementById('modalPhone').innerText = lead.mobile;
            document.getElementById('modalEmail').innerText = lead.email;

            const container = document.getElementById('modalSchoolsList');
            container.innerHTML = '';
            
            const mappings = lead.mapping_details ? lead.mapping_details.split('|||') : [];
            const schoolNames = [];
            
            mappings.forEach(m => {
                const [schoolId, schoolName, status, notes] = m.split('::');
                schoolNames.push(schoolName);
                const div = document.createElement('div');
                div.className = 'bg-slate-50 p-6 rounded-3xl border border-slate-100 group';
                div.innerHTML = `
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="enquiry_id" value="${lead.id}">
                        <input type="hidden" name="school_id" value="${schoolId}">
                        <input type="hidden" name="update_followup" value="1">
                        
                <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-black text-slate-900 mb-1">${schoolName}</p>
                                <span class="text-[9px] font-black uppercase tracking-widest ${status === 'admission_done' ? 'text-green-600' : (status === 'doc_required' ? 'text-blue-600' : (status === 'rejected' ? 'text-red-500' : 'text-orange-500'))}">${status.replace('_', ' ')}</span>
                            </div>
                             <div class="flex gap-2">
                                  <a href="https://wa.me/${lead.mobile.replace(/\D/g,'')}?text=${encodeURIComponent(`Hi ${lead.parent_name}, this is regarding your application for ${lead.child_name} at ${schoolName}. The school requires certain documents (MarkSheet, Birth Certificate, Aadhaar) to move forward. Please share them here.`)}" target="_blank" class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Request Documents"><i class="fa-solid fa-file-invoice text-lg"></i></a>
                                  <a href="https://wa.me/${lead.mobile.replace(/\D/g,'')}" target="_blank" class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center hover:bg-green-600 hover:text-white transition-all shadow-sm" title="Chat with Parent"><i class="fa-brands fa-whatsapp text-lg"></i></a>
                             </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 block">STATUS</label>
                                <select name="admission_status" class="w-full bg-white border-slate-200 rounded-xl text-xs font-bold text-slate-700">
                                    <option value="pending" ${status === 'pending' ? 'selected' : ''}>Pending / Called</option>
                                    <option value="doc_required" ${status === 'doc_required' ? 'selected' : ''}>Documentation Required</option>
                                    <option value="admission_done" ${status === 'admission_done' ? 'selected' : ''}>Admission Confirmed</option>
                                    <option value="rejected" ${status === 'rejected' ? 'selected' : ''}>Rejected / Not Interested</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 block">FOLLOW-UP NOTES</label>
                                <input type="text" name="follow_up_notes" value="${notes || ''}" placeholder="Enter notes..." class="w-full bg-white border-slate-200 rounded-xl text-xs font-bold text-slate-700">
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-[#1B2559] text-white text-[10px] font-black py-2 rounded-xl uppercase tracking-widest hover:bg-blue-600 transition-all opacity-0 group-hover:opacity-100 shadow-lg shadow-blue-500/10">Update Follow-up</button>
                    </form>
                `;
                container.appendChild(div);
            });
 
            // Update WhatsApp Link for sending applied list
            const schoolsList = schoolNames.join(', ');
            const waMessage = `Hi ${lead.parent_name},\n\nYou have successfully applied to the following schools through MySchoolDesk:\n\n${schoolsList}\n\nLead ID: ${lead.lead_id}\n\nOur team will contact you shortly to schedule visits. Contact us if you have any questions!`;
            const waUrl = `https://wa.me/${lead.mobile.replace(/\D/g,'')}?text=${encodeURIComponent(waMessage)}`;
            document.getElementById('modalWhatsappSendList').setAttribute('href', waUrl);

            leadModal.classList.remove('hidden');
            leadModal.classList.add('flex');
            overlay.classList.remove('hidden');
        }

        function closeLeadModal() {
            leadModal.classList.add('hidden');
            leadModal.classList.remove('flex');
            if(window.innerWidth < 1024) toggleSidebar(); 
            else overlay.classList.add('hidden');
        }

        function filterEnquiries() {
            const query = document.getElementById('enquirySearch').value.toLowerCase();
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#enquiryTableBody tr');
            
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const matchesSearch = text.includes(query);
                
                let matchesStatus = true;
                if(status === 'multi') {
                    matchesStatus = text.includes('applied to') && text.includes('schools');
                } else if(status) {
                    matchesStatus = text.includes(status.replace('_', ' '));
                }

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>

</body>
</html>
