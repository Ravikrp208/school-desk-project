<?php
// admin/login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../includes/auth.php';

// Redirect if already logged in
if (is_admin_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = :u1 OR email = :u2) AND role = 'admin' LIMIT 1");
            $stmt->execute(['u1' => $username, 'u2' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['last_login'] = time();
                
                header('Location: index.php');
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '42S22') { // Column not found
                $error = "Database needs setup. Please visit <a href='../setup_admin.php' class='underline'>setup_admin.php</a> to fix.";
            } else {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | MySchoolDesk</title>
    <link rel="icon" type="image/png" href="../assets/images/logo_boy.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F4F7FE; }
        .login-card { 
            background: #FFFFFF; 
            border-radius: 40px; 
            box-shadow: 0 20px 80px rgba(0,0,0,0.05);
            max-width: 450px;
            width: 100%;
        }
        .form-input { 
            background: #F4F7FE; 
            border: 2px solid transparent; 
            border-radius: 20px; 
            padding: 16px 24px; 
            font-size: 15px; 
            font-weight: 600; 
            color: #1B2559; 
            transition: all 0.2s;
        }
        .form-input:focus { 
            border-color: #4318FF; 
            outline: none; 
            background: white; 
            box-shadow: 0 10px 20px rgba(67, 24, 255, 0.05);
        }
        .login-btn {
            background: #4318FF;
            color: white;
            padding: 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(67, 24, 255, 0.2);
        }
        .login-btn:hover {
            background: #3311CC;
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(67, 24, 255, 0.3);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="login-card p-8 md:p-12">
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-[#F4F7FE] rounded-3xl flex items-center justify-center mx-auto mb-6 border border-gray-100">
                <i class="fa-solid fa-lock text-[#4318FF] text-2xl"></i>
            </div>
            <h1 class="text-3xl font-black text-[#1B2559]">Admin Login</h1>
            <p class="text-sm font-bold text-[#A3AED0] mt-2">Enter credentials to open Portal</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-8 flex items-center gap-3 border border-red-100">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
                <p class="text-xs font-black"><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-[10px] font-black text-[#1B2559] uppercase tracking-widest mb-2 pl-2">Username or Email</label>
                <input type="text" name="username" class="w-full form-input" placeholder="admin" required autofocus>
            </div>
            <div>
                <label class="block text-[10px] font-black text-[#1B2559] uppercase tracking-widest mb-2 pl-2">Password</label>
                <input type="password" name="password" class="w-full form-input" placeholder="••••••••" required>
            </div>
            <div class="flex items-center justify-between px-2 pt-2 pb-4">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" class="w-4 h-4 accent-[#4318FF] rounded-lg">
                    <span class="text-xs font-bold text-[#A3AED0] group-hover:text-[#1B2559] transition-all">Remember me</span>
                </label>
            </div>
            <button type="submit" name="login" class="w-full login-btn flex items-center justify-center gap-3">
                <span>Login</span>
                <i class="fa-solid fa-arrow-right-to-bracket text-lg"></i>
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="../school_dashboard/login.php" class="text-[10px] font-black text-[#4318FF] uppercase tracking-widest hover:underline">
                <i class="fa-solid fa-graduation-cap mr-1"></i> School Partner Login
            </a>
        </div>

        <div class="mt-10 text-center border-t border-gray-50 pt-8">
            <p class="text-xs font-bold text-[#A3AED0]">MySchoolDesk Discovery Platform</p>
            <p class="text-[10px] font-black text-gray-300 mt-2 uppercase tracking-widest">Digital Atelier v1.0</p>
        </div>
    </div>

</body>
</html>
