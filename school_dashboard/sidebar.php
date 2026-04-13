<?php
// school_dashboard/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="w-64 bg-[#0C1E3C] text-white p-6 hidden lg:flex flex-col h-screen sticky top-0">
    <div class="flex items-center gap-3 mb-12">
        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center text-xl shadow-lg shadow-green-500/20 text-white">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <span class="font-black text-xl tracking-tight">MSD Partner</span>
    </div>
    <nav class="space-y-2 flex-1">
        <a href="index.php" class="flex items-center gap-4 p-4 rounded-2xl font-bold transition-all <?php echo $current_page == 'index.php' ? 'bg-green-600 text-white' : 'hover:bg-white/5 text-blue-100'; ?>">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
        <a href="profile.php" class="flex items-center gap-4 p-4 rounded-2xl font-bold transition-all <?php echo $current_page == 'profile.php' ? 'bg-green-600 text-white' : 'hover:bg-white/5 text-blue-100'; ?>">
            <i class="fa-solid fa-building"></i> Profile
        </a>
        <a href="gallery.php" class="flex items-center gap-4 p-4 rounded-2xl font-bold transition-all <?php echo $current_page == 'gallery.php' ? 'bg-green-600 text-white' : 'hover:bg-white/5 text-blue-100'; ?>">
            <i class="fa-solid fa-images"></i> Gallery
        </a>
        <a href="leads.php" class="flex items-center gap-4 p-4 rounded-2xl font-bold transition-all <?php echo $current_page == 'leads.php' ? 'bg-green-600 text-white' : 'hover:bg-white/5 text-blue-100'; ?>">
            <i class="fa-solid fa-inbox"></i> Leads
        </a>
    </nav>
    <div class="mt-auto">
        <a href="logout.php" class="flex items-center gap-4 hover:bg-white/5 p-4 rounded-2xl font-bold text-blue-100 transition-all">
            <i class="fa-solid fa-sign-out"></i> Logout
        </a>
    </div>
</aside>
