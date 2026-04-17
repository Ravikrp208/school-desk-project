<?php
// enquiry_submit.php
require_once 'config.php';
require_once 'includes/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$parent_name = trim($_POST['parent_name'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$email = trim($_POST['email'] ?? '');
$child_name = trim($_POST['child_name'] ?? '');
$child_dob = $_POST['child_dob'] ?? null;
$child_class = trim($_POST['child_class'] ?? $_POST['target_class'] ?? '');
$location = trim($_POST['location'] ?? '');
$existing_school = trim($_POST['existing_school'] ?? '');
$passing_year = trim($_POST['passing_year'] ?? '');
$student_id = trim($_POST['student_id'] ?? '');
$govt_id_type = trim($_POST['govt_id_type'] ?? '');
$budget_range = trim($_POST['budget_range'] ?? '');
$board_preference = trim($_POST['board_preference'] ?? '');
$school_ids = $_POST['school_ids'] ?? [];

if ($parent_name === '' || $mobile === '' || empty($school_ids)) {
    require_once 'header.php';
    echo '<div class="container mx-auto py-32 text-center">
            <div class="bg-white rounded-[40px] p-20 border border-slate-100 shadow-sm">
                <h1 class="text-3xl font-black text-red-500 mb-6">Error: Missing Required Fields</h1>
                <p class="text-slate-500 mb-8">Please provide your name, mobile number, and at least one school.</p>
                <a href="javascript:history.back()" class="text-blue-600 font-black text-lg hover:underline"><i class="fa-solid fa-arrow-left mr-2"></i> Go Back</a>
            </div>
          </div>';
    require_once 'footer.php';
    exit;
}

try {
    $pdo->beginTransaction();

    $lead_id = msd_generate_lead_id($pdo);

    $stmt = $pdo->prepare('INSERT INTO enquiries (lead_id, parent_name, mobile, email, child_name, child_dob, child_class, location, budget_range, board_preference, existing_school, passing_year, student_id, govt_id_type) VALUES (:lead_id, :parent_name, :mobile, :email, :child_name, :child_dob, :child_class, :location, :budget_range, :board_preference, :existing_school, :passing_year, :student_id, :govt_id_type)');
    $stmt->execute([
        'lead_id' => $lead_id,
        'parent_name' => $parent_name,
        'mobile' => $mobile,
        'email' => $email,
        'child_name' => $child_name,
        'child_dob' => $child_dob,
        'child_class' => $child_class,
        'location' => $location,
        'budget_range' => $budget_range,
        'board_preference' => $board_preference,
        'existing_school' => $existing_school,
        'passing_year' => $passing_year,
        'student_id' => $student_id,
        'govt_id_type' => $govt_id_type
    ]);

    $enquiry_id = (int)$pdo->lastInsertId();

    $mappingStmt = $pdo->prepare('INSERT INTO enquiry_school_mapping (enquiry_id, school_id) VALUES (:enquiry_id, :school_id)');
    foreach ($school_ids as $sid) {
        $mappingStmt->execute([
            'enquiry_id' => $enquiry_id,
            'school_id' => (int)$sid
        ]);
    }

    $pdo->commit();

    // Fetch school names for WhatsApp message
    $placeholders = str_repeat('?,', count($school_ids) - 1) . '?';
    $schoolStmt = $pdo->prepare("SELECT name FROM schools WHERE id IN ($placeholders)");
    $schoolStmt->execute($school_ids);
    $applied_schools = $schoolStmt->fetchAll(PDO::FETCH_COLUMN);
    $schools_list = implode(", ", $applied_schools);

    require_once 'header.php';
    ?>
    <section class="bg-[#0C1E3C] py-20 relative overflow-hidden">
        <div class="container mx-auto px-4 text-center relative z-10">
            <h1 class="text-3xl md:text-5xl font-black text-white mb-4">Application Success</h1>
            <p class="text-lg text-blue-100/70 max-w-2xl mx-auto">Your enquiry has been successfully broadcasted to the selected schools.</p>
        </div>
    </section>

    <section class="container mx-auto -mt-10 mb-24 px-4 relative z-20">
        <div class="max-w-3xl mx-auto bg-white rounded-[40px] shadow-2xl p-10 md:p-20 text-center border border-slate-100">
            <div class="w-32 h-32 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-10 text-6xl shadow-inner border border-green-100 animate-bounce">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h2 class="text-4xl font-black text-slate-900 mb-4">You're All Set!</h2>
            <p class="text-xl text-slate-500 font-medium mb-10">Application Reference ID: <span class="text-blue-600 font-black"><?php echo $lead_id; ?></span></p>
            
            <div class="bg-slate-50 rounded-[32px] p-8 mb-12 text-left">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">APPLIED SCHOOLS</h4>
                <div class="flex flex-wrap gap-2 mb-6">
                    <?php foreach($applied_schools as $sName): ?>
                        <span class="bg-white px-4 py-2 rounded-xl text-xs font-black text-slate-700 border border-slate-100 shadow-sm"><?php echo htmlspecialchars($sName); ?></span>
                    <?php endforeach; ?>
                </div>

                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">WHAT HAPPENS NEXT?</h4>
                <ul class="space-y-4">
                    <li class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-blue-600 shadow-sm border border-slate-100 flex-shrink-0">1</div>
                        <p class="text-slate-600 text-sm font-bold">Schools will review your child's profile details.</p>
                    </li>
                    <li class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-blue-600 shadow-sm border border-slate-100 flex-shrink-0">2</div>
                        <p class="text-slate-600 text-sm font-bold">You will receive a call/WhatsApp from the admission desk within 24-48 hours.</p>
                    </li>
                </ul>
            </div>

            <div class="flex flex-col md:flex-row gap-4">
                <a href="index.php" class="flex-1 bg-slate-100 text-slate-400 font-black py-5 rounded-2xl transition-all hover:bg-slate-200">BACK TO HOME</a>
                <a id="whatsapp-btn" href="https://wa.me/916351165654?text=<?php echo urlencode("Hi MySchoolDesk,\n\nI have just applied for school admission through your portal.\n\nParent: $parent_name\nChild: $child_name\nSchools Applied: $schools_list\nLead ID: $lead_id\n\nPlease assist me with the priority verification and process."); ?>" class="flex-1 bg-[#25D366] text-white font-black py-5 rounded-2xl shadow-xl transition-all hover:scale-105 flex items-center justify-center gap-2 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-black/10 translate-x-[-100%] group-hover:translate-x-0 transition-transform duration-[3000ms] ease-linear" id="timer-bar"></div>
                    <i class="fa-brands fa-whatsapp text-2xl relative z-10"></i> 
                    <span class="relative z-10">WHATSAPP SUPPORT (<span id="countdown">3</span>s)</span>
                </a>
            </div>
            <p class="mt-6 text-slate-400 text-[10px] font-black uppercase tracking-widest animate-pulse">Redirecting to WhatsApp support automatically...</p>
        </div>
    </section>

    <script>
        let seconds = 3;
        const countdownEl = document.getElementById('countdown');
        const timerBar = document.getElementById('timer-bar');
        
        // Start progress bar animation
        setTimeout(() => {
            timerBar.style.transform = 'translateX(0)';
        }, 100);

        const interval = setInterval(() => {
            seconds--;
            countdownEl.innerText = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = document.getElementById('whatsapp-btn').getAttribute('href');
            }
        }, 1000);
    </script>
    <?php
    require_once 'footer.php';
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    require_once 'header.php';
    echo '<div class="container mx-auto py-32 text-center">
            <div class="bg-white rounded-[40px] p-20 border border-slate-100 shadow-sm">
                <h1 class="text-3xl font-black text-red-500 mb-6">Submission Failed</h1>
                <p class="text-slate-500 mb-8">' . htmlspecialchars($e->getMessage()) . '</p>
                <a href="javascript:history.back()" class="text-blue-600 font-black text-lg hover:underline"><i class="fa-solid fa-arrow-left mr-2"></i> Go Back and Try Again</a>
            </div>
          </div>';
    require_once 'footer.php';
}
