<?php
// contact.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'includes/common.php';

$title = "Contact Support - MySchoolDesk";
require_once 'header.php';

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? 'General Enquiry');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        try {
            // Save to support_messages
            $stmt = $pdo->prepare("INSERT INTO support_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $messageId = $pdo->lastInsertId();

            // Create Notification
            $notifStmt = $pdo->prepare("INSERT INTO notifications (type, reference_id, message) VALUES ('contact_message', ?, ?)");
            $notifMsg = "New support enquiry from $name";
            $notifStmt->execute([$messageId, $notifMsg]);

            $success = true;
        } catch (PDOException $e) {
            error_log("Contact Form Error: " . $e->getMessage());
        }
    }
}
?>

<div class="bg-gradient-to-br from-[#F8FAFF] to-[#E0E9FF] min-h-screen py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <span class="inline-block bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-lg uppercase tracking-widest mb-4">GET IN TOUCH</span>
                <h1 class="text-4xl md:text-6xl font-black text-slate-900 mb-6">How Can We <span class="text-blue-600 italic">Help?</span></h1>
                <?php if ($success): ?>
                    <div class="bg-green-500/10 border border-green-500/20 text-green-600 px-6 py-4 rounded-2xl mb-8 font-black animate-fade-in flex items-center justify-center gap-3">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Thank you! Your message has been sent to our support team.</span>
                    </div>
                <?php endif; ?>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto font-medium">Have questions about admissions or need assistance? Our expert team is here to support you in every step.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Contact Info Cards -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white/70 backdrop-blur-xl p-8 rounded-[32px] border border-white/50 shadow-xl hover:-translate-y-2 transition-all duration-500">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 mb-2">Our Office</h3>
                        <p class="text-slate-500 font-medium leading-relaxed">Horizon Tower, Race Course Road,<br>Vadodara, Gujarat 390007</p>
                    </div>

                    <div class="bg-white/70 backdrop-blur-xl p-8 rounded-[32px] border border-white/50 shadow-xl hover:-translate-y-2 transition-all duration-500">
                        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 mb-2">Connect via Email</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest leading-none block mb-1">Founder / Admissions</p>
                                <p class="text-slate-500 font-bold text-sm">hershil@myschooldesk.co.in</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest leading-none block mb-1">General Enquiries</p>
                                <p class="text-slate-500 font-bold text-sm">help@myschooldesk.co.in</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest leading-none block mb-1">CFO Desk</p>
                                    <p class="text-slate-500 font-bold text-[11px]">cfo@myschooldesk.co.in</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest leading-none block mb-1">HR & Careers</p>
                                    <p class="text-slate-500 font-bold text-[11px]">hr@myschooldesk.co.in</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/70 backdrop-blur-xl p-8 rounded-[32px] border border-white/50 shadow-xl hover:-translate-y-2 transition-all duration-500">
                        <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-xl mb-6">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 mb-2">Emergency Support</h3>
                        <p class="text-slate-500 text-sm font-black tracking-widest">+91 63511 65654</p>
                        <p class="text-slate-400 text-[10px] mt-2 font-medium leading-relaxed">Available 24/7 for urgent admission support.</p>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-[48px] p-8 md:p-12 shadow-2xl border-8 border-white/50 relative overflow-hidden">
                        <!-- Decorative glow -->
                        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-400/10 blur-[100px] rounded-full -translate-y-1/2 translate-x-1/2"></div>
                        
                        <form action="#" method="POST" class="relative z-10 space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[8px] font-black text-blue-600/40 uppercase tracking-widest transition-all group-focus-within:text-blue-600">Full Name</label>
                                    <input type="text" name="name" required class="w-full bg-slate-50/50 border-none rounded-2xl px-6 pt-7 pb-3 text-sm font-black text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20" placeholder="e.g. John Doe">
                                </div>
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[8px] font-black text-blue-600/40 uppercase tracking-widest transition-all group-focus-within:text-blue-600">Email Address</label>
                                    <input type="email" name="email" required class="w-full bg-slate-50/50 border-none rounded-2xl px-6 pt-7 pb-3 text-sm font-black text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20" placeholder="e.g. john@example.com">
                                </div>
                            </div>

                            <div class="relative group">
                                <label class="absolute left-6 top-3 text-[8px] font-black text-blue-600/40 uppercase tracking-widest transition-all group-focus-within:text-blue-600">Subject</label>
                                <select name="subject" class="w-full bg-slate-50/50 border-none rounded-2xl px-6 pt-7 pb-3 text-sm font-black text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 appearance-none cursor-pointer">
                                    <option value="general">General Enquiry</option>
                                    <option value="admission">Admission Support</option>
                                    <option value="school">School Partnership</option>
                                    <option value="technical">Technical Issue</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
                            </div>

                            <div class="relative group">
                                <label class="absolute left-6 top-3 text-[8px] font-black text-blue-600/40 uppercase tracking-widest transition-all group-focus-within:text-blue-600">Message</label>
                                <textarea name="message" rows="5" required class="w-full bg-slate-50/50 border-none rounded-2xl px-6 pt-7 pb-3 text-sm font-black text-slate-700 outline-none focus:ring-2 focus:ring-blue-500/20 resize-none" placeholder="Describe how we can help you..."></textarea>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3 group">
                                <span class="uppercase tracking-widest text-xs">Send Message</span>
                                <i class="fa-solid fa-paper-plane text-sm group-hover:rotate-12 transition-transform"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
