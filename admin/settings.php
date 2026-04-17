<?php
// admin/settings.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../includes/common.php';
require_once '../includes/auth.php';

// Protect the page
protect_admin_page();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $admission_year = trim($_POST['admission_year']);
    $primary_color = trim($_POST['primary_color']);
    
    msd_update_setting($pdo, 'admission_year', $admission_year);
    msd_update_setting($pdo, 'primary_color', $primary_color);
    
    $message = "Settings updated successfully!";
}

$current_year = msd_get_setting($pdo, 'admission_year', '2026-27');
$current_color = msd_get_setting($pdo, 'primary_color', '#2563eb');

$color_options = [
    'Blue' => '#2563eb',
    'Indigo' => '#4f46e5',
    'Purple' => '#9333ea',
    'Rose' => '#e11d48',
    'Amber' => '#d97706',
    'Emerald' => '#059669'
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Settings | Admin Console</title>
    <link rel="icon" type="image/png" href="../assets/images/logo_boy.png">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F4F7FE; }
        .color-card { cursor: pointer; border: 4px solid transparent; transition: all 0.3s; }
        .color-card.active { border-color: #1B2559; transform: scale(1.05); }
    </style>
</head>
<body class="flex flex-col lg:flex-row min-h-screen">

    <!-- Simple Sidebar Shortcut -->
    <aside class="w-64 bg-white border-r border-gray-100 p-6 hidden lg:block">
        <div class="mb-10 px-4">
            <h1 class="text-xl font-extrabold text-[#1B2559]">Admin Console</h1>
            <p class="text-[10px] font-bold text-[#A3AED0] uppercase tracking-widest">Management Portal</p>
        </div>
        <nav class="space-y-4">
            <a href="index.php" class="flex items-center gap-4 text-slate-400 font-bold px-4 py-3 hover:bg-gray-50 rounded-lg">
                <i class="fa-solid fa-house-chimney"></i> Dashboard
            </a>
            <a href="settings.php" class="flex items-center gap-4 bg-blue-50 text-blue-600 font-bold px-4 py-3 rounded-lg">
                <i class="fa-solid fa-gear"></i> Settings
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-6 lg:p-12">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-black text-[#1B2559]">Global Settings</h2>
                    <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px] mt-1">Configure your platform aesthetics and sessions</p>
                </div>
                <a href="index.php" class="bg-white border border-slate-200 text-[#1B2559] px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-50 transition-all">Back to Dashboard</a>
            </div>

            <?php if ($message): ?>
                <div class="bg-green-50 text-green-600 p-6 rounded-3xl border border-green-100 mb-8 font-bold flex items-center gap-4 animate-fade-in">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-8">
                <!-- Admission Year Card -->
                <div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100">
                    <h3 class="text-xl font-black text-[#1B2559] mb-8 flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        Admission Session
                    </h3>
                    <div class="max-w-md">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 block">CURRENT ADMISSION YEAR</label>
                        <input type="text" name="admission_year" value="<?php echo htmlspecialchars($current_year); ?>" class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-3xl px-8 py-5 text-lg font-black text-slate-900 outline-none transition-all" placeholder="e.g. 2026-27">
                        <p class="mt-4 text-xs text-slate-400 font-medium italic">This will update the "Admission Open" labels across the public website.</p>
                    </div>
                </div>

                <!-- Theme Color Card -->
                <div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100">
                    <h3 class="text-xl font-black text-[#1B2559] mb-8 flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-palette"></i>
                        </div>
                        Brand Aesthetic
                    </h3>
                    
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 block">SELECT THEME COLOR</label>
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-6 mb-8">
                        <?php foreach($color_options as $name => $hex): ?>
                            <div onclick="selectColor('<?php echo $hex; ?>')" class="color-card rounded-3xl overflow-hidden shadow-lg <?php echo $current_color === $hex ? 'active' : ''; ?>" id="card-<?php echo $hex; ?>">
                                <div class="h-16 w-full" style="background-color: <?php echo $hex; ?>"></div>
                                <div class="bg-white p-3 text-center">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-900"><?php echo $name; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <input type="hidden" name="primary_color" id="primary_color_input" value="<?php echo $current_color; ?>">
                    
                    <div class="flex items-center gap-6 p-6 rounded-3xl bg-slate-50 border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl shadow-xl" id="color-preview" style="background-color: <?php echo $current_color; ?>"></div>
                        <div>
                            <p class="text-xs font-black text-slate-900">Custom HEX Code</p>
                            <input type="text" id="hex_input" value="<?php echo $current_color; ?>" oninput="updateFromHex(this.value)" class="text-xs font-bold text-slate-500 bg-transparent border-none p-0 focus:ring-0">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" name="save_settings" class="bg-blue-600 hover:bg-[#1B2559] text-white font-black px-12 py-5 rounded-3xl shadow-xl shadow-blue-500/20 transition-all hover:scale-105 active:scale-95 text-sm uppercase tracking-widest">
                        Save Global Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function selectColor(hex) {
            document.getElementById('primary_color_input').value = hex;
            document.getElementById('hex_input').value = hex;
            document.getElementById('color-preview').style.backgroundColor = hex;
            
            // Highlight active card
            document.querySelectorAll('.color-card').forEach(c => c.classList.remove('active'));
            const selectedCard = document.getElementById('card-' + hex);
            if(selectedCard) selectedCard.classList.add('active');
        }

        function updateFromHex(hex) {
            if(/^#[0-9A-F]{6}$/i.test(hex)) {
                document.getElementById('primary_color_input').value = hex;
                document.getElementById('color-preview').style.backgroundColor = hex;
                document.querySelectorAll('.color-card').forEach(c => c.classList.remove('active'));
            }
        }
    </script>
</body>
</html>
