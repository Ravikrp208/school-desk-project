<?php
// expert_callback.php
require_once 'config.php';
require_once 'includes/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$parent_name = trim($_POST['parent_name'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$email = trim($_POST['email'] ?? '');
$location = trim($_POST['location'] ?? '');
$child_class = trim($_POST['child_class'] ?? '');

if ($parent_name === '' || $mobile === '') {
    die("Name and Mobile are required.");
}

try {
    $lead_id = msd_generate_lead_id($pdo);

    $stmt = $pdo->prepare('INSERT INTO enquiries (lead_id, parent_name, mobile, email, child_name, child_class, location, message) VALUES (:lead_id, :parent_name, :mobile, :email, "", :child_class, :location, "Request for Expert Counseling Callback")');

    $stmt->execute([
        'lead_id' => $lead_id,
        'parent_name' => $parent_name,
        'mobile' => $mobile,
        'email' => $email,
        'child_class' => $child_class,
        'location' => $location
    ]);

    require_once 'header.php';
    ?>
    <section class="bg-[#1D4ED8] py-24 relative overflow-hidden flex items-center min-h-[70vh]">
        <div class="absolute inset-0 bg-blue-900/10 pointer-events-none"></div>
        <div class="container mx-auto px-4 text-center relative z-10">
            <div
                class="max-w-2xl mx-auto bg-white/10 backdrop-blur-3xl p-12 md:p-20 rounded-[48px] border border-white/20 shadow-2xl">
                <div
                    class="w-24 h-24 bg-green-500 text-white rounded-3xl flex items-center justify-center mx-auto mb-10 text-5xl shadow-2xl shadow-green-500/40 animate-bounce">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white mb-6 leading-tight">Request Received!</h1>
                <p class="text-xl text-blue-100 font-medium mb-12 opacity-90">
                    Our Senior Admission Expert <span class="text-white font-black">will call you back</span> on your
                    provided number <span class="bg-white/20 px-2 rounded"><?php echo htmlspecialchars($mobile); ?></span>
                    soon.
                </p>

                <div class="flex flex-col md:flex-row gap-4">
                    <a href="index.php"
                        class="flex-1 bg-white text-blue-700 font-black py-5 rounded-2xl shadow-xl hover:scale-105 transition-all text-sm uppercase tracking-widest">Done,
                        Take me Home</a>
                </div>

                <p class="mt-10 text-blue-200 text-xs font-bold uppercase tracking-[0.2em] opacity-50">Reference ID:
                    <?php echo $lead_id; ?></p>
            </div>
        </div>
    </section>
    <?php
    require_once 'footer.php';
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
