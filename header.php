<?php
// header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
$base_url = "/myschooldesk";
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySchoolDesk - School Discovery & Admission</title>
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>/assets/images/logo_boy.png">
    <!-- Google Fonts (Stitching in a premium UI font) -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <!-- Tailwind CSS CDN (Development Only - Use Build Tool for Production) -->
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
    <!-- Custom Vanilla CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <?php if (isset($extra_css))
        echo $extra_css; ?>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar border-b border-gray-100 bg-white sticky top-0 z-[1000] shadow-sm">
        <div class="container h-full flex items-center justify-between px-4 sm:px-6">
            <a href="<?php echo $base_url; ?>/" class="flex items-center group py-2">
                <div
                    class="h-16 w-16 bg-white rounded-2xl flex items-center justify-center shadow-2xl border-4 border-white overflow-hidden transform group-hover:scale-110 transition-transform duration-500">
                    <img src="<?php echo $base_url; ?>/assets/images/logo_boy.png" alt="Icon"
                        class="w-full h-full object-contain">
                </div>
            </a>

            <div class="nav-links hidden md:flex items-center gap-8">
                <a href="<?php echo $base_url; ?>/"
                    class="text-gray-600 font-bold hover:text-[#1D4ED8] transition-all relative py-2 <?php echo ($current_page == 'index.php' || $current_page == '') ? 'text-[#1D4ED8] active-link' : ''; ?>">Home</a>
                <a href="<?php echo $base_url; ?>/search.php"
                    class="text-gray-600 font-bold hover:text-[#1D4ED8] transition-all relative py-2 <?php echo ($current_page == 'search.php') ? 'text-[#1D4ED8] active-link' : ''; ?>">Find Schools</a>
                <a href="<?php echo $base_url; ?>/compare.php"
                    class="text-gray-600 font-bold hover:text-[#1D4ED8] transition-all relative py-2 <?php echo ($current_page == 'compare.php') ? 'text-[#1D4ED8] active-link' : ''; ?>">Compare</a>
                
                <!-- Mobile Only Login Buttons (shown in dropdown) -->
                <div class="flex flex-col gap-4 mt-6 pt-6 border-t border-gray-100 md:hidden w-full items-center">
                    <a href="<?php echo $base_url; ?>/school_dashboard"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-center font-black py-4 rounded-full shadow-lg shadow-blue-500/20 text-xs uppercase tracking-widest active:scale-95 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-graduation-cap"></i>
                        School Login
                    </a>
                    <a href="<?php echo $base_url; ?>/admin"
                        class="w-full bg-gradient-to-r from-orange-500 to-amber-600 text-white text-center font-black py-4 rounded-full shadow-lg shadow-orange-500/20 text-xs uppercase tracking-widest active:scale-95 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-user-shield"></i>
                        Admin Login
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <!-- Desktop Login Buttons -->
                <a href="<?php echo $base_url; ?>/school_dashboard"
                    class="hidden md:block bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-extrabold px-6 py-2.5 rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 hover:scale-105 transition-all active:scale-95 text-sm uppercase tracking-wide">School Login</a>
                <a href="<?php echo $base_url; ?>/admin"
                    class="hidden md:block bg-gradient-to-r from-[#FF8008] to-[#FFC837] text-white font-extrabold px-6 py-2.5 rounded-xl shadow-lg shadow-orange-500/20 hover:shadow-orange-500/40 hover:scale-105 transition-all active:scale-95 text-sm uppercase tracking-wide">Admin Login</a>
                
                <button class="menu-btn block md:hidden text-3xl text-gray-900 border-none bg-transparent cursor-pointer z-[2100]">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

    </nav>
    
    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="fixed inset-0 bg-white z-[2000] translate-x-full transition-transform duration-300 md:hidden flex flex-col p-8 pt-24 overflow-y-auto">
        <div class="flex flex-col gap-6 items-center text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-2">Discovery Portal</p>
            
            <a href="<?php echo $base_url; ?>/" class="w-full flex flex-col items-center gap-2 group transition-all <?php echo ($current_page == 'index.php' || $current_page == '') ? 'active-mobile-link' : ''; ?>">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center shadow-sm text-blue-600 group-hover:bg-blue-50 group-hover:scale-110 transition-all <?php echo ($current_page == 'index.php' || $current_page == '') ? 'bg-blue-50 scale-110 text-[#1D4ED8]' : ''; ?>">
                    <i class="fa-solid fa-house text-xl"></i>
                </div>
                <span class="text-sm font-black text-[#1B2559]">Home</span>
            </a>

            <a href="<?php echo $base_url; ?>/search.php" class="w-full flex flex-col items-center gap-2 group transition-all <?php echo ($current_page == 'search.php') ? 'active-mobile-link' : ''; ?>">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center shadow-sm text-blue-600 group-hover:bg-blue-50 group-hover:scale-110 transition-all <?php echo ($current_page == 'search.php') ? 'bg-blue-50 scale-110 text-[#1D4ED8]' : ''; ?>">
                    <i class="fa-solid fa-magnifying-glass text-xl"></i>
                </div>
                <span class="text-sm font-black text-[#1B2559]">Find Schools</span>
            </a>

            <a href="<?php echo $base_url; ?>/compare.php" class="w-full flex flex-col items-center gap-2 group transition-all <?php echo ($current_page == 'compare.php') ? 'active-mobile-link' : ''; ?>">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center shadow-sm text-blue-600 group-hover:bg-blue-50 group-hover:scale-110 transition-all <?php echo ($current_page == 'compare.php') ? 'bg-blue-50 scale-110 text-[#1D4ED8]' : ''; ?>">
                    <i class="fa-solid fa-code-compare text-xl"></i>
                </div>
                <span class="text-sm font-black text-[#1B2559]">Compare</span>
            </a>
            
            <div class="w-full h-px bg-gray-100 my-4"></div>
            
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-2">Partner Portals</p>
            
            <a href="<?php echo $base_url; ?>/school_dashboard" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 p-5 rounded-full flex items-center justify-center gap-3 shadow-xl shadow-blue-500/20 active:scale-95 transition-all text-white group">
                <i class="fa-solid fa-graduation-cap text-lg"></i>
                <span class="text-sm font-black uppercase tracking-wider">School Partner Login</span>
            </a>

            <a href="<?php echo $base_url; ?>/admin" class="w-full bg-gradient-to-r from-orange-500 to-amber-600 p-5 rounded-full flex items-center justify-center gap-3 shadow-xl shadow-orange-500/20 active:scale-95 transition-all text-white group">
                <i class="fa-solid fa-user-shield text-lg"></i>
                <span class="text-sm font-black uppercase tracking-wider">Admin Staff Login</span>
            </a>
        </div>
        
        <div class="mt-16 text-center pb-12 opacity-30 mt-auto">
            <p class="text-[8px] font-black text-gray-500 uppercase tracking-[0.4em]">Stitched with Precision</p>
        </div>
    </div>
    <main class="main-content">