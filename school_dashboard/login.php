<?php
// school_dashboard/login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../includes/auth.php';

// Redirect if already logged in
if (is_school_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both credentials.";
    } else {
        try {
            // Schools login using email as username/email
             $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = :user1 OR email = :user2) AND role = 'school' LIMIT 1");
             $stmt->execute(['user1' => $username, 'user2' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['last_login'] = time();
                
                header('Location: index.php');
                exit;
            } else {
                $error = "Invalid credentials. Please use the login sent to your email.";
            }
        } catch (PDOException $e) {
            $error = "System error. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Login | MySchoolDesk</title>
    <link rel="icon" type="image/png" href="../assets/images/logo_boy.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0C1E3C; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .form-input { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; transition: all 0.3s; }
        .form-input:focus { background: rgba(255, 255, 255, 0.1); border-color: #3B82F6; box-shadow: 0 0 20px rgba(59, 130, 246, 0.2); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-10 px-6 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute inset-0 opacity-20 pointer-events-none">
        <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-blue-500 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-indigo-500 rounded-full blur-[140px] translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="max-w-[450px] w-full glass-card rounded-[40px] p-8 md:p-14 relative z-10 mx-auto">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-xl shadow-blue-500/20 text-white text-3xl">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h1 class="text-3xl font-black text-white mb-3">School Login</h1>
            <p class="text-blue-200/60 font-medium text-sm">Manage your listings and leads.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-8 flex items-center gap-3 text-sm font-bold">
                <i class="fa-solid fa-circle-exclamation"></i>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-blue-200/40 uppercase tracking-widest ml-1">Official Email</label>
                <input type="email" name="username" required placeholder="admin@yourschool.com" class="w-full form-input rounded-2xl px-6 py-4 font-bold outline-none">
            </div>
            
            <div class="space-y-2">
                <label class="text-[10px] font-black text-blue-200/40 uppercase tracking-widest ml-1">Initial Password (Phone)</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full form-input rounded-2xl px-6 py-4 font-bold outline-none">
            </div>

            <button type="submit" name="login" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] text-lg uppercase tracking-widest">
                Login
            </button>
            
            <div class="flex flex-col gap-4 mt-8 pt-6 border-t border-white/5">
                <p class="text-[10px] text-blue-200/30 text-center uppercase tracking-widest font-black">
                    New Partner? <a href="register.php" class="text-blue-400 hover:underline">Apply for listing</a>
                </p>
                <p class="text-[10px] text-blue-200/30 text-center uppercase tracking-widest font-black">
                    Administrator? <a href="../admin/login.php" class="text-indigo-400 hover:underline">Staff Portal</a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>
