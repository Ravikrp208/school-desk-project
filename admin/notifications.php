<?php
// admin/notifications.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../includes/common.php';
require_once '../includes/auth.php';

protect_admin_page();

// Mark all as read if requested
if (isset($_GET['action']) && $_GET['action'] === 'mark_all_read') {
    $pdo->exec("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
    header('Location: notifications.php');
    exit;
}

// Fetch all notifications
$notifications = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC")->fetchAll();

// Fetch unread count for header (re-fetch after potential update)
$unreadNotifsCount = (int)$pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")->fetchColumn();
$recentNotifs = $pdo->query("SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | Admin Console</title>
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
    </style>
</head>
<body class="flex flex-col lg:flex-row min-h-screen">
    <!-- Include Sidebar logic here or use a shared partial. For now, matching index.php -->
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
        <header class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-6">
            <div>
                <h2 class="text-2xl lg:text-3xl font-black text-[#1B2559]">System Notifications</h2>
                <p class="text-xs lg:text-sm font-bold text-[#A3AED0]">Track automated alerts and user enquiries.</p>
            </div>
            <div class="flex gap-4">
                <a href="?action=mark_all_read" class="bg-white text-[#1B2559] font-black px-6 py-3 rounded-2xl text-xs uppercase tracking-widest shadow-sm hover:shadow-md transition-all">Mark All as Read</a>
            </div>
        </header>

        <div class="bg-white rounded-[40px] p-6 lg:p-10 shadow-xl shadow-gray-200/50 border border-gray-100">
            <div class="space-y-6">
                <?php foreach ($notifications as $notif): ?>
                    <div class="flex items-center justify-between p-6 rounded-3xl <?php echo $notif['is_read'] ? 'bg-gray-50/50 grayscale' : 'bg-blue-50/30 border-l-4 border-blue-600'; ?> transition-all hover:translate-x-1 duration-300">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 <?php echo $notif['is_read'] ? 'bg-gray-200 text-gray-400' : 'bg-blue-100 text-blue-600'; ?> rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-sm">
                                <i class="fa-solid <?php echo $notif['type'] == 'contact_message' ? 'fa-envelope-open-text' : 'fa-bell'; ?>"></i>
                            </div>
                            <div>
                                <h4 class="text-base font-black text-[#1B2559] mb-1"><?php echo htmlspecialchars($notif['message']); ?></h4>
                                <div class="flex items-center gap-4">
                                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest"><i class="fa-regular fa-clock mr-1.5"></i><?php echo time_elapsed_string($notif['created_at']); ?></span>
                                    <?php if (!$notif['is_read']): ?>
                                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <a href="contact_messages.php?id=<?php echo $notif['reference_id']; ?>&notif_id=<?php echo $notif['id']; ?>" class="bg-white text-[#1B2559] font-black px-6 py-3 rounded-xl text-[10px] uppercase tracking-widest shadow-sm hover:shadow-md transition-all">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($notifications)): ?>
                    <div class="flex flex-col items-center justify-center py-24 grayscale opacity-50">
                        <i class="fa-solid fa-bell-slash text-6xl text-gray-200 mb-6"></i>
                        <p class="text-xl font-black text-gray-400 italic">Silence is golden. No notifications yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
