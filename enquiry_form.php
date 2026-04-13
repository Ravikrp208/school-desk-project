<?php
// enquiry_form.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'includes/common.php';

$selected_school_ids = $_GET['school_id'] ?? [];
if (!is_array($selected_school_ids)) {
    $selected_school_ids = [$selected_school_ids];
}

$schools = [];
if (!empty($selected_school_ids)) {
    $placeholders = implode(',', array_fill(0, count($selected_school_ids), '?'));
    $stmt = $pdo->prepare("SELECT id, name, city FROM schools WHERE id IN ($placeholders)");
    $stmt->execute($selected_school_ids);
    $schools = $stmt->fetchAll();
}

$extra_css = '<link rel="stylesheet" href="assets/css/home.css">';
require_once 'header.php';
?>

<section class="bg-[#0C1E3C] py-20 relative overflow-hidden">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-3xl md:text-5xl font-black text-white mb-4">Admission Enquiry Form</h1>
        <p class="text-lg text-blue-100/70 max-w-2xl mx-auto">One form, multiple schools. Get priority response from the best institutions.</p>
    </div>
</section>

<section class="container mx-auto -mt-10 mb-24 px-4 relative z-20">
    <div class="max-w-4xl mx-auto bg-white rounded-[40px] shadow-2xl border border-slate-100 overflow-hidden">
        <div class="flex flex-col md:flex-row">
            <!-- Left: Sidebar info -->
            <div class="md:w-1/3 bg-slate-50 p-10 border-r border-slate-100">
                <div class="mb-10">
                    <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-4">Selected Schools</h4>
                    <ul class="space-y-4">
                        <?php foreach($schools as $s): ?>
                            <li class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-3">
                                <i class="fa-solid fa-school text-blue-500 mt-1"></i>
                                <div>
                                    <span class="block font-bold text-slate-800 leading-tight"><?php echo htmlspecialchars($s['name']); ?></span>
                                    <span class="block text-[10px] text-slate-400 font-bold uppercase mt-1"><?php echo htmlspecialchars($s['city']); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <?php if(empty($schools)): ?>
                            <li class="text-slate-400 italic text-sm">No schools selected.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="p-6 bg-blue-600 rounded-3xl text-white shadow-lg">
                    <i class="fa-solid fa-shield-halved text-2xl mb-4"></i>
                    <h5 class="font-black mb-2">Safe & Secure</h5>
                    <p class="text-xs text-blue-100 leading-relaxed font-medium">Your data is only shared with the schools you select and our verified admission experts.</p>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="md:w-2/3 p-10 md:p-14">
                <form action="enquiry_submit.php" method="POST" class="space-y-8">
                    <?php foreach($selected_school_ids as $sid): ?>
                        <input type="hidden" name="school_ids[]" value="<?php echo (int)$sid; ?>">
                    <?php endforeach; ?>

                    <!-- Parent Details -->
                    <div>
                        <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">1</span>
                            Parent Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Parent Name</label>
                                <input type="text" name="parent_name" required placeholder="Full Name" class="w-full bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl px-5 py-4 font-bold text-slate-800 placeholder:text-slate-300">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mobile Number</label>
                                <input type="tel" name="mobile" required pattern="[0-9]{10}" placeholder="10 Digit Number" class="w-full bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl px-5 py-4 font-bold text-slate-800 placeholder:text-slate-300">
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email ID</label>
                                <input type="email" name="email" required placeholder="example@mail.com" class="w-full bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl px-5 py-4 font-bold text-slate-800 placeholder:text-slate-300">
                            </div>
                        </div>
                    </div>

                    <!-- Child Details -->
                    <div class="pt-8 border-t border-slate-50">
                        <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">2</span>
                            Child Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Child Name</label>
                                <input type="text" name="child_name" required placeholder="Name" class="w-full bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl px-5 py-4 font-bold text-slate-800 placeholder:text-slate-300">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Date of Birth</label>
                                <input type="date" name="child_dob" required class="w-full bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl px-5 py-4 font-bold text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Class Applying For</label>
                                <select name="child_class" required class="w-full bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl px-5 py-4 font-bold text-slate-800 appearance-none">
                                    <?php foreach(msd_class_options() as $opt): ?>
                                        <option value="<?php echo $opt; ?>"><?php echo $opt; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Preferred Location</label>
                                <input type="text" name="location" required placeholder="e.g. Akota" class="w-full bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl px-5 py-4 font-bold text-slate-800 placeholder:text-slate-300">
                            </div>
                        </div>
                    </div>

                    <!-- Preferences -->
                    <div class="pt-8 border-t border-slate-50">
                        <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">3</span>
                            Preferences
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Annual Budget Range</label>
                                <select name="budget_range" required class="w-full bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl px-5 py-4 font-bold text-slate-800 appearance-none">
                                    <?php foreach(msd_budget_options() as $opt): ?>
                                        <option value="<?php echo $opt; ?>"><?php echo $opt; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Preferred Board</label>
                                <select name="board_preference" required class="w-full bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl px-5 py-4 font-bold text-slate-800 appearance-none">
                                    <?php foreach(msd_board_options() as $opt): ?>
                                        <option value="<?php echo $opt; ?>"><?php echo $opt; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-500/30 transition-all hover:scale-[1.01] active:scale-[0.98] text-lg">
                        APPLY TO SELECTED SCHOOLS
                    </button>
                    
                    <p class="text-[10px] text-slate-400 text-center uppercase tracking-widest font-black">
                        By applying, you agree to our <a href="#" class="text-blue-500">Terms of Service</a> & <a href="#" class="text-blue-500">Privacy Policy</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>
