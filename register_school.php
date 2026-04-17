<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'includes/common.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['school_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $board = trim($_POST['board']);
    $p1_name = trim($_POST['contact_p1_name']);
    $p1_designation = trim($_POST['contact_p1_designation']);
    $p1_phone = trim($_POST['contact_p1_phone']);
    
    // Simple validation
    if (empty($name) || empty($email) || empty($phone)) {
        $error = "Please fill in all mandatory fields.";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM schools WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "A school with this email is already registered.";
            } else {
                // Insert into schools table with status='pending'
                $sql = "INSERT INTO schools (name, email, phone, city, state, board, contact_p1_name, contact_p1_designation, contact_p1_phone, status, is_featured) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $email, $phone, $city, $state, $board, $p1_name, $p1_designation, $p1_phone]);
                
                $message = "Your school registration request has been submitted successfully! Our admin team will review it and contact you shortly.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

require_once 'header.php';
?>

<div class="min-h-screen bg-[#F4F7FE] py-12 md:py-20 px-4">
    <div class="container mx-auto max-w-4xl">
        <div class="bg-white rounded-[40px] shadow-2xl overflow-hidden animate-fade-in-up">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <!-- Illustration Side -->
                <div class="bg-blue-600 p-12 text-white flex flex-col justify-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 blur-[80px] rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative z-10">
                        <span class="inline-block bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full text-[10px] font-black tracking-widest uppercase mb-6">Partner with Us</span>
                        <h2 class="text-4xl font-black mb-6 leading-tight">Grow Your School's Digital Presence.</h2>
                        <p class="text-blue-100 font-medium opacity-90 leading-relaxed mb-8">
                            Join India's most trusted school discovery platform and reach thousands of parents looking for the perfect education for their children.
                        </p>
                        
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <i class="fa-solid fa-chart-line"></i>
                                </div>
                                <span class="text-sm font-bold">Increase Admissons</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <span class="text-sm font-bold">Premium Branding</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <i class="fa-solid fa-headset"></i>
                                </div>
                                <span class="text-sm font-bold">Dedicated Support</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Side -->
                <div class="p-8 md:p-12">
                    <?php if ($message): ?>
                        <div class="bg-green-50 border border-green-100 p-8 rounded-3xl text-center">
                            <div class="w-16 h-16 bg-green-500 text-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-green-500/20">
                                <i class="fa-solid fa-check text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-4">Request Sent!</h3>
                            <p class="text-slate-500 font-medium mb-8 leading-relaxed"><?php echo $message; ?></p>
                            <a href="index.php" class="inline-block bg-blue-600 text-white font-black py-4 px-10 rounded-xl shadow-lg hover:scale-105 transition-all uppercase tracking-widest text-xs">Back to Home</a>
                        </div>
                    <?php else: ?>
                        <div class="mb-10">
                            <h3 class="text-2xl font-black text-slate-900 mb-2">School Registration</h3>
                            <p class="text-slate-400 font-bold text-sm uppercase tracking-widest">Onboarding Process</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="bg-red-50 text-red-500 p-4 rounded-xl text-xs font-bold mb-6 border border-red-100 flex items-center gap-3">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="space-y-6">
                            <div class="space-y-5">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Institutional Details</h4>
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[9px] font-black text-blue-600 uppercase tracking-widest">Official School Name</label>
                                    <input type="text" name="school_name" required 
                                        class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-2xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all"
                                        placeholder="e.g. St. Xavier's International School">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="relative group">
                                        <label class="absolute left-6 top-3 text-[9px] font-black text-blue-600 uppercase tracking-widest">Email Address</label>
                                        <input type="email" name="email" required 
                                            class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-2xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all"
                                            placeholder="admin@school.com">
                                    </div>
                                    <div class="relative group">
                                        <label class="absolute left-6 top-3 text-[9px] font-black text-blue-600 uppercase tracking-widest">Phone Number</label>
                                        <input type="tel" name="phone" required 
                                            class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-2xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all"
                                            placeholder="90XXXXXXXX">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="relative group">
                                        <label class="absolute left-6 top-3 text-[9px] font-black text-blue-600 uppercase tracking-widest">State</label>
                                        <input type="text" name="state" required 
                                            class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-2xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all"
                                            placeholder="Gujarat">
                                    </div>
                                    <div class="relative group">
                                        <label class="absolute left-6 top-3 text-[9px] font-black text-blue-600 uppercase tracking-widest">City</label>
                                        <input type="text" name="city" required 
                                            class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-2xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all"
                                            placeholder="Vadodara">
                                    </div>
                                </div>

                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[9px] font-black text-blue-600 uppercase tracking-widest">Board Affiliation</label>
                                    <select name="board" required class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-2xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all appearance-none cursor-pointer">
                                        <option value="">Select Board</option>
                                        <?php foreach (msd_board_options() as $b): ?>
                                            <option value="<?php echo $b; ?>"><?php echo $b; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 translate-y-2 text-slate-300 pointer-events-none"></i>
                                </div>
                            </div>

                            <div class="space-y-5 pt-4">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Primary Contact Person</h4>
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[9px] font-black text-blue-600 uppercase tracking-widest">Full Name</label>
                                    <input type="text" name="contact_p1_name" required 
                                        class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-2xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all"
                                        placeholder="Principal / Admin Name">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="relative group">
                                        <label class="absolute left-6 top-3 text-[9px] font-black text-blue-600 uppercase tracking-widest">Designation</label>
                                        <input type="text" name="contact_p1_designation" required 
                                            class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-2xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all"
                                            placeholder="e.g. Principal">
                                    </div>
                                    <div class="relative group">
                                        <label class="absolute left-6 top-3 text-[9px] font-black text-blue-600 uppercase tracking-widest">Contact Number</label>
                                        <input type="tel" name="contact_p1_phone" required 
                                            class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-2xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all"
                                            placeholder="Direct Phone">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white font-black py-5 rounded-2xl shadow-xl hover:bg-blue-700 hover:scale-[1.02] active:scale-95 transition-all text-sm uppercase tracking-[0.2em] mt-8">
                                Submit Registration Request
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <p class="text-[10px] text-center text-slate-400 font-bold uppercase tracking-widest mt-12">
            Professional School Discovery Platform &copy; <?php echo date('Y'); ?>
        </p>
    </div>
</div>

<?php require_once 'footer.php'; ?>
