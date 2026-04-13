<?php
// admin/contact_messages.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../includes/common.php';
require_once '../includes/auth.php';

protect_admin_page();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$notif_id = isset($_GET['notif_id']) ? (int)$_GET['notif_id'] : 0;

// Mark notification as read if notif_id is provided
if ($notif_id > 0) {
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?")->execute([$notif_id]);
}

// Fetch the message
$stmt = $pdo->prepare("SELECT * FROM support_messages WHERE id = ?");
$stmt->execute([$id]);
$msg = $stmt->fetch();

if (!$msg) {
    die("Message not found.");
}

// Also mark message as read if we just arrived here
$pdo->prepare("UPDATE support_messages SET status = 'read' WHERE id = ?")->execute([$id]);

// Fetch for header
$unreadNotifsCount = (int)$pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")->fetchColumn();
$recentNotifs = $pdo->query("SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Details | Admin Console</title>
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
    </style>
</head>
<body class="flex flex-col lg:flex-row min-h-screen">
    <aside id="sidebar" class="w-64 sidebar flex flex-col fixed inset-y-0 left-0 z-[50] p-6 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out h-full overflow-y-auto">
        <div class="mb-10 px-4">
            <h1 class="text-xl font-extrabold text-[#1B2559]">Admin Console</h1>
            <p class="text-[10px] font-bold text-[#A3AED0] uppercase tracking-widest">Management Portal</p>
        </div>
        <nav class="flex-1 space-y-2">
            <a href="index.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-house-chimney text-lg"></i> Dashboard
            </a>
            <a href="enquiries.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-comment-dots text-lg"></i> Enquiries
            </a>
            <a href="school_profile.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-user-graduate text-lg"></i> School Profile
            </a>
            <a href="logout.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-red-50 hover:text-red-500 rounded-lg transition-all">
                <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i> Logout
            </a>
        </nav>
    </aside>

    <main class="flex-1 lg:ml-64 p-4 lg:p-10 transition-all duration-300">
        <header class="flex items-center gap-8 mb-12">
            <a href="notifications.php" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-[#1B2559] shadow-sm hover:shadow-md transition-all active:scale-90">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-2xl font-black text-[#1B2559]">Message Inquiry</h2>
                <p class="text-xs font-bold text-[#A3AED0]">Reviewing support request #<?php echo $msg['id']; ?></p>
            </div>
        </header>

        <div class="max-w-4xl">
            <div class="bg-white rounded-[48px] overflow-hidden shadow-2xl shadow-gray-200/50 border border-gray-100">
                <div class="p-8 lg:p-12 border-b border-gray-50 bg-[#F4F7FE]/30">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 bg-[#4318FF] text-white rounded-[20px] flex items-center justify-center text-2xl font-black shadow-lg shadow-blue-500/30">
                                <?php echo strtoupper(substr($msg['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-[#1B2559]"><?php echo htmlspecialchars($msg['name']); ?></h3>
                                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mt-0.5"><?php echo htmlspecialchars($msg['email']); ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] font-black text-[#A3AED0] uppercase tracking-widest mb-1">Received On</span>
                            <span class="text-sm font-black text-[#1B2559]"><?php echo date('d M Y, h:i A', strtotime($msg['created_at'])); ?></span>
                        </div>
                    </div>
                </div>

                <div class="p-8 lg:p-12 space-y-10">
                    <div>
                        <span class="block text-[10px] font-black text-blue-600 uppercase tracking-widest mb-4">Subject</span>
                        <h4 class="text-2xl font-black text-[#1B2559]"><?php echo htmlspecialchars($msg['subject']); ?></h4>
                    </div>

                    <div class="bg-gray-50/50 rounded-[32px] p-8 md:p-10 border border-gray-100">
                        <span class="block text-[10px] font-black text-[#A3AED0] uppercase tracking-widest mb-6">Message Content</span>
                        <p class="text-slate-700 font-medium leading-[1.8] text-lg italic"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <a href="mailto:<?php echo $msg['email']; ?>?subject=Re: <?php echo urlencode($msg['subject']); ?>" class="flex-1 bg-[#4318FF] text-white font-black py-4 px-8 rounded-2xl shadow-xl shadow-blue-500/20 text-center uppercase tracking-widest text-xs hover:bg-blue-700 transition-all flex items-center justify-center gap-3 active:scale-95">
                            <i class="fa-solid fa-reply"></i> Reply via Email
                        </a>
                        <button onclick="window.print()" class="bg-white border border-gray-200 text-[#1B2559] font-black py-4 px-8 rounded-2xl text-center uppercase tracking-widest text-xs hover:bg-gray-50 transition-all flex items-center justify-center gap-3 active:scale-95">
                            <i class="fa-solid fa-print"></i> Print Details
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mt-10 flex items-center gap-4 bg-orange-500/10 p-6 rounded-[28px] border border-orange-500/20">
                <div class="w-12 h-12 bg-orange-500 text-white rounded-xl flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <p class="text-xs font-bold text-orange-700 leading-relaxed">Always verify the identity of the sender before sharing any sensitive platform data or credentials via email.</p>
            </div>
        </div>
    </main>
</body>
</html>
