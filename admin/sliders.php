<?php
// admin/sliders.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../includes/common.php';
require_once '../includes/auth.php';

// Protect the page
protect_admin_page();

$message = '';
$error = '';

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $link = $_POST['link'] ?? '#';
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $status = isset($_POST['status']) ? 1 : 0;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/sliders/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('slider_', true) . '.' . $file_ext;
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $stmt = $pdo->prepare("INSERT INTO home_sliders (image_path, link, sort_order, status) VALUES (?, ?, ?, ?)");
                $stmt->execute(['uploads/sliders/' . $file_name, $link, $sort_order, $status]);
                $message = "Slider image added successfully!";
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Please select a valid image.";
        }
    } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        
        // Get image path to delete file
        $stmt = $pdo->prepare("SELECT image_path FROM home_sliders WHERE id = ?");
        $stmt->execute([$id]);
        $slider = $stmt->fetch();
        
        if ($slider) {
            if (file_exists('../' . $slider['image_path'])) {
                unlink('../' . $slider['image_path']);
            }
            $stmt = $pdo->prepare("DELETE FROM home_sliders WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Slider deleted successfully!";
        }
    } elseif ($_POST['action'] === 'toggle' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("UPDATE home_sliders SET status = 1 - status WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Status updated successfully!";
    }
}

// Fetch sliders
$sliders = $pdo->query("SELECT * FROM home_sliders ORDER BY sort_order ASC, created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sliders | MySchoolDesk Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/logo_boy.png">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F4F7FE; }
        .sidebar { background: #FFFFFF; border-right: 1px solid #E2E8F0; }
        .nav-item-active { background: #F4F7FE; color: #4318FF; font-weight: 800; }
        .nav-item { color: #A3AED0; font-weight: 600; }
        .nav-item:hover { color: #1B2559; }
        .metric-card { border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); transition: all 0.3s ease; }
    </style>
</head>
<body class="flex flex-col lg:flex-row min-h-screen">

    <!-- Sidebar (Simplified for now, matching index.php structure) -->
    <aside id="sidebar" class="w-64 sidebar flex flex-col fixed inset-y-0 left-0 z-[50] p-6 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out h-full overflow-y-auto">
        <div class="mb-10 px-4">
            <h1 class="text-xl font-extrabold text-[#1B2559]">Admin Console</h1>
            <p class="text-[10px] font-bold text-[#A3AED0] uppercase tracking-widest">Management Portal</p>
        </div>

        <nav class="flex-1 space-y-2">
            <a href="index.php" class="flex items-center gap-4 nav-item px-4 py-3 rounded-lg transition-all">
                <i class="fa-solid fa-house-chimney text-lg"></i> Dashboard
            </a>
            <a href="enquiries.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-comment-dots text-lg"></i> Enquiries
            </a>
            <a href="sliders.php" class="flex items-center gap-4 nav-item-active px-4 py-3 rounded-lg transition-all">
                <i class="fa-solid fa-images text-lg"></i> Homepage Slider
            </a>
            <a href="school_profile.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-gray-50 rounded-lg transition-all">
                <i class="fa-solid fa-user-graduate text-lg"></i> School Profile
            </a>
            <a href="logout.php" class="flex items-center gap-4 nav-item px-4 py-3 hover:bg-red-50 hover:text-red-500 rounded-lg transition-all">
                <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i> Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-64 p-4 lg:p-10 transition-all duration-300">
        <div class="mb-8">
            <h2 class="text-3xl font-black text-[#1B2559]">Homepage Slider</h2>
            <p class="text-sm font-bold text-[#A3AED0]">Manage top banners for the main website.</p>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                <p class="font-bold"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                <p class="font-bold"><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Add Slider Form -->
            <div class="xl:col-span-1">
                <div class="bg-white p-8 rounded-[32px] shadow-sm metric-card">
                    <h3 class="text-xl font-black text-[#1B2559] mb-6">Add New Slide</h3>
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="action" value="add">
                        
                        <div>
                            <label class="text-xs font-black text-[#A3AED0] uppercase tracking-widest block mb-2">Slider Image</label>
                            <div class="relative">
                                <input type="file" name="image" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                <p class="text-[10px] text-gray-400 mt-2 italic font-medium">* Recommended size: 1920x600px</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-black text-[#A3AED0] uppercase tracking-widest block mb-2">Target Link (Optional)</label>
                            <input type="text" name="link" placeholder="https://..." class="w-full bg-[#F4F7FE] border-none rounded-2xl py-3 px-6 text-sm font-bold text-[#1B2559] focus:ring-4 focus:ring-blue-500/10">
                        </div>

                        <div>
                            <label class="text-xs font-black text-[#A3AED0] uppercase tracking-widest block mb-2">Sort Order</label>
                            <input type="number" name="sort_order" value="0" class="w-full bg-[#F4F7FE] border-none rounded-2xl py-3 px-6 text-sm font-bold text-[#1B2559] focus:ring-4 focus:ring-blue-500/10">
                        </div>

                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="status" checked id="status" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="status" class="text-sm font-bold text-[#1B2559]">Active Immediately</label>
                        </div>

                        <button type="submit" class="w-full bg-[#4318FF] text-white py-4 rounded-2xl font-black shadow-lg shadow-blue-500/20 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-xs">
                            Upload Slide
                        </button>
                    </form>
                </div>
            </div>

            <!-- Existing Sliders List -->
            <div class="xl:col-span-2">
                <div class="bg-white p-8 rounded-[32px] shadow-sm metric-card">
                    <h3 class="text-xl font-black text-[#1B2559] mb-6">Active Sliders</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($sliders as $s): ?>
                            <div class="bg-[#F4F7FE] rounded-3xl overflow-hidden group border border-transparent hover:border-blue-200 transition-all flex flex-col">
                                <div class="relative h-40">
                                    <img src="../<?php echo htmlspecialchars($s['image_path']); ?>" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-4">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                            <button type="submit" onclick="return confirm('Are you sure?')" class="w-10 h-10 bg-red-500 text-white rounded-xl flex items-center justify-center hover:scale-110 transition-transform">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                            <button type="submit" class="w-10 h-10 bg-white text-blue-600 rounded-xl flex items-center justify-center hover:scale-110 transition-transform">
                                                <i class="fa-solid <?php echo $s['status'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <?php if ($s['status']): ?>
                                        <span class="absolute top-4 left-4 bg-green-500 text-white text-[8px] font-black px-2 py-1 rounded-lg uppercase">Active</span>
                                    <?php else: ?>
                                        <span class="absolute top-4 left-4 bg-gray-500 text-white text-[8px] font-black px-2 py-1 rounded-lg uppercase">Inactive</span>
                                    <?php endif; ?>
                                </div>
                                <div class="p-4 flex-1 flex flex-col justify-between">
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Link</p>
                                        <p class="text-xs font-bold text-[#1B2559] truncate max-w-full italic"><?php echo htmlspecialchars($s['link']); ?></p>
                                    </div>
                                    <div class="flex items-center justify-between mt-4">
                                        <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">Order: <?php echo $s['sort_order']; ?></span>
                                        <span class="text-[10px] font-bold text-gray-400"><?php echo date('M d, Y', strtotime($s['created_at'])); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($sliders)): ?>
                            <div class="col-span-full py-20 text-center opacity-50">
                                <i class="fa-solid fa-images text-6xl text-gray-200 mb-6"></i>
                                <p class="text-sm font-bold text-gray-400 italic">No slider images uploaded yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
