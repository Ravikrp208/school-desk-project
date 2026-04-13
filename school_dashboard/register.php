<?php
// school_dashboard/register.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $school_name = trim($_POST['school_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $state = $_POST['state'] ?? '';
    $district = $_POST['district'] ?? '';

    // Basic Validation
    if (empty($school_name) || empty($email) || empty($phone) || empty($password) || empty($state) || empty($district)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "This email is already registered.";
            } else {
                // Start Transaction
                $pdo->beginTransaction();

                // 1. Create User Account
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $username = strtolower(str_replace(' ', '', $school_name)) . rand(100, 999); // Simple unique username
                
                $stmtUser = $pdo->prepare("INSERT INTO users (name, username, email, password, phone, role) VALUES (?, ?, ?, ?, ?, 'school')");
                $stmtUser->execute([$school_name, $username, $email, $hashed_password, $phone]);
                $user_id = $pdo->lastInsertId();

                // 2. Create School Listing
                // Generate Slug
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $school_name)));
                $slug .= '-' . strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $district)));
                
                // Ensure slug uniqueness
                $stmtCheckSlug = $pdo->prepare("SELECT id FROM schools WHERE slug = ?");
                $stmtCheckSlug->execute([$slug]);
                if ($stmtCheckSlug->fetch()) {
                    $slug .= '-' . rand(100, 999);
                }

                $stmtSchool = $pdo->prepare("INSERT INTO schools (user_id, name, slug, state, city, contact_email, contact_phone, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
                $stmtSchool->execute([$user_id, $school_name, $slug, $state, $district, $email, $phone]);

                // Commit Transaction
                $pdo->commit();
                $success = "Registration successful! You can now login.";
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Registration failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Registration | MySchoolDesk</title>
    <link rel="icon" type="image/png" href="../assets/images/logo_boy.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0C1E3C; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .form-input { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; transition: all 0.3s; width: 100%; }
        .form-input:focus { background: rgba(255, 255, 255, 0.1); border-color: #3B82F6; box-shadow: 0 0 20px rgba(59, 130, 246, 0.2); }
        select option { background: #0C1E3C; color: white; }
    </style>
</head>
<body class="min-h-screen flex justify-center items-start py-10 md:py-20 px-6 relative bg-[#0C1E3C]">
    <!-- Background Decor -->
    <div class="absolute inset-0 opacity-20 pointer-events-none">
        <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-blue-500 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-indigo-500 rounded-full blur-[140px] translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="max-w-[600px] w-full glass-card rounded-[40px] p-8 md:p-14 relative z-10 mx-auto">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-xl shadow-blue-500/20 text-white text-3xl">
                <i class="fa-solid fa-school"></i>
            </div>
            <h1 class="text-3xl font-black text-white mb-3">Register School</h1>
            <p class="text-blue-200/60 font-medium text-sm">Join our network of elite educational institutions.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-8 flex items-center gap-3 text-sm font-bold">
                <i class="fa-solid fa-circle-exclamation"></i>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-500/10 border border-green-500/20 text-green-400 p-4 rounded-2xl mb-8 flex items-center gap-3 text-sm font-bold">
                <i class="fa-solid fa-circle-check"></i>
                <div>
                    <p><?php echo $success; ?></p>
                    <a href="login.php" class="text-white underline mt-2 block">Go to Login</a>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2 space-y-2">
                    <label class="text-[10px] font-black text-blue-200/40 uppercase tracking-widest ml-1">School Name</label>
                    <input type="text" name="school_name" required placeholder="e.g. St. Xavier's High School" class="form-input rounded-2xl px-6 py-4 font-bold outline-none">
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-200/40 uppercase tracking-widest ml-1">Official Email</label>
                    <input type="email" name="email" required placeholder="admin@school.com" class="form-input rounded-2xl px-6 py-4 font-bold outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-200/40 uppercase tracking-widest ml-1">Contact Phone</label>
                    <input type="tel" name="phone" required placeholder="9876543210" class="form-input rounded-2xl px-6 py-4 font-bold outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-200/40 uppercase tracking-widest ml-1">State</label>
                    <select id="state" name="state" required class="form-input rounded-2xl px-6 py-4 font-bold outline-none appearance-none">
                        <option value="">Select State</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-200/40 uppercase tracking-widest ml-1">District</label>
                    <select id="district" name="district" required disabled class="form-input rounded-2xl px-6 py-4 font-bold outline-none appearance-none disabled:opacity-50">
                        <option value="">Select District</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-200/40 uppercase tracking-widest ml-1">Create Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="form-input rounded-2xl px-6 py-4 font-bold outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-200/40 uppercase tracking-widest ml-1">Confirm Password</label>
                    <input type="password" name="confirm_password" required placeholder="••••••••" class="form-input rounded-2xl px-6 py-4 font-bold outline-none">
                </div>
            </div>

            <button type="submit" name="register" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] text-lg uppercase tracking-widest">
                Register Now
            </button>
            
            <p class="text-[10px] text-blue-200/30 text-center uppercase tracking-widest font-black mt-8">
                Already registered? <a href="login.php" class="text-blue-400 hover:underline">Sign In Instead</a>
            </p>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stateSelect = document.getElementById('state');
            const districtSelect = document.getElementById('district');
            let locationData = null;

            fetch('https://raw.githubusercontent.com/sab99r/Indian-States-And-Districts/master/states-and-districts.json')
                .then(response => response.json())
                .then(data => {
                    locationData = data.states;
                    locationData.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.state;
                        option.textContent = item.state;
                        stateSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching states:', error));

            stateSelect.addEventListener('change', function() {
                districtSelect.innerHTML = '<option value="">Select District</option>';
                if (this.value && locationData) {
                    const state = locationData.find(s => s.state === this.value);
                    if (state && state.districts) {
                        state.districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district;
                            option.textContent = district;
                            districtSelect.appendChild(option);
                        });
                        districtSelect.disabled = false;
                    }
                } else {
                    districtSelect.disabled = true;
                }
            });
        });
    </script>
</body>
</html>