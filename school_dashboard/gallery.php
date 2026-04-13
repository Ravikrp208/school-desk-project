<?php
// school_dashboard/gallery.php
require_once '../config.php';
require_once '../includes/common.php';
require_once '../includes/auth.php';

// Protect the page
protect_school_page();

$userId = $_SESSION['user_id'];
$message = '';
$error = '';

// Fetch existing school data
$stmt = $pdo->prepare('SELECT id, name, photos FROM schools WHERE user_id = :user_id LIMIT 1');
$stmt->execute(['user_id' => $userId]);
$school = $stmt->fetch();

if (!$school) {
    die('No school profile associated with your account.');
}

$photos = json_decode($school['photos'] ?? '[]', true) ?: [];

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['school_photo'])) {
    $uploadDir = '../assets/uploads/schools/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $file = $_FILES['school_photo'];
    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    $dbPath = 'assets/uploads/schools/' . $fileName;

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (in_array($file['type'], $allowedTypes)) {
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $photos[] = $dbPath;
            $updateStmt = $pdo->prepare('UPDATE schools SET photos = :photos WHERE id = :id');
            $updateStmt->execute(['photos' => json_encode($photos), 'id' => $school['id']]);
            $message = "Photo uploaded successfully!";
        } else {
            $error = "Failed to move uploaded file.";
        }
    } else {
        $error = "Invalid file type. Only JPG, PNG, and WEBP are allowed.";
    }
}

// Handle Delete
if (isset($_GET['delete_idx'])) {
    $idx = (int)$_GET['delete_idx'];
    if (isset($photos[$idx])) {
        // Optional: Delete physical file
        $filePath = '../' . $photos[$idx];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        unset($photos[$idx]);
        $photos = array_values($photos); // Re-index
        $updateStmt = $pdo->prepare('UPDATE schools SET photos = :photos WHERE id = :id');
        $updateStmt->execute(['photos' => json_encode($photos), 'id' => $school['id']]);
        $message = "Photo deleted successfully!";
        header('Location: gallery.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>School Gallery - <?php echo htmlspecialchars($school['name']); ?></title>
    <link rel="icon" type="image/png" href="../assets/images/logo_boy.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
    </style>
</head>
<body class="flex min-h-screen">
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-12 overflow-y-auto">
        <header class="flex items-center justify-between mb-12">
            <div>
                <h1 class="text-3xl font-black text-slate-900">School Gallery</h1>
                <p class="text-slate-500 font-medium tracking-tight">Upload high-quality photos of your campus and facilities</p>
            </div>
            <label class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-[1.02] transition-all cursor-pointer">
                <i class="fa-solid fa-plus mr-2"></i> Upload New Photo
                <form method="POST" enctype="multipart/form-data" class="hidden">
                    <input type="file" name="school_photo" onchange="this.form.submit()" accept="image/*">
                </form>
            </label>
        </header>

        <?php if ($message): ?>
            <div class="bg-green-50 border border-green-100 text-green-600 p-4 rounded-2xl mb-8 flex items-center gap-3 font-bold">
                <i class="fa-solid fa-circle-check"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-100 text-red-500 p-4 rounded-2xl mb-8 flex items-center gap-3 font-bold">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Gallery Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php foreach($photos as $idx => $path): ?>
                <div class="group relative aspect-square bg-slate-200 rounded-[32px] overflow-hidden border border-slate-100 shadow-sm transition-all hover:shadow-xl">
                    <img src="../<?php echo htmlspecialchars($path); ?>" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-4">
                        <a href="../<?php echo htmlspecialchars($path); ?>" target="_blank" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-900 hover:scale-110 transition-transform">
                            <i class="fa-solid fa-expand"></i>
                        </a>
                        <a href="?delete_idx=<?php echo $idx; ?>" onclick="return confirm('Are you sure you want to delete this photo?')" class="w-12 h-12 bg-red-500 rounded-2xl flex items-center justify-center text-white hover:scale-110 transition-transform">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if(empty($photos)): ?>
                <div class="col-span-full py-32 text-center flex flex-col items-center justify-center">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 text-4xl mb-6">
                        <i class="fa-solid fa-images"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-400 italic">No photos uploaded yet.</h3>
                    <p class="text-slate-400 font-medium">Click the upload button to get started.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
