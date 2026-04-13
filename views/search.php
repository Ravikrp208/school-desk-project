<?php
$extra_css = '<link rel="stylesheet" href="/myschooldesk/assets/css/home.css">';
require_once 'header.php';

$location = trim($_GET['location'] ?? '');
$class = trim($_GET['class'] ?? '');
$board = trim($_GET['board'] ?? '');

$sql = 'SELECT * FROM schools WHERE status = "approved"';
$params = [];
if ($location !== '') {
    $sql .= ' AND (city LIKE :location OR address LIKE :location)';
    $params['location'] = '%' . $location . '%';
}
if ($class !== '') {
    $sql .= ' AND classes_offered LIKE :class';
    $params['class'] = '%' . $class . '%';
}
if ($board !== '') {
    $sql .= ' AND board LIKE :board';
    $params['board'] = '%' . $board . '%';
}
$sql .= ' ORDER BY is_verified DESC, sort_order ASC, created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$schools = $stmt->fetchAll();
?>

<!-- Search Header Section -->
<section class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white py-16 text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl md:text-5xl font-bold mb-4">Find Schools</h1>
        <p class="text-lg text-blue-100 max-w-2xl mx-auto">Explore the best schools matching your criteria and apply with ease.</p>
    </div>
</section>

<!-- Search Filters Container -->
<section class="container mx-auto -mt-8 mb-12 px-4 relative z-10">
    <div class="bg-white rounded-2xl shadow-xl p-5 md:p-8 border border-gray-100">
        <form action="/MySchoolDesk/search" method="GET" class="w-full flex flex-col md:flex-row items-end gap-4">
            <div class="w-full md:flex-1 flex flex-col">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Location</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="e.g. Vadodara" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-base md:text-lg rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>
            <div class="w-full md:flex-1 flex flex-col">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Class</label>
                <select name="class" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-base md:text-lg rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all cursor-pointer">
                    <option value="">Any Class</option>
                    <option value="playgroup" <?php echo $class === 'playgroup' ? 'selected' : ''; ?>>Playgroup</option>
                    <option value="nursery" <?php echo $class === 'nursery' ? 'selected' : ''; ?>>Nursery</option>
                    <option value="1" <?php echo $class === '1' ? 'selected' : ''; ?>>Class 1</option>
                    <option value="10" <?php echo $class === '10' ? 'selected' : ''; ?>>Class 10</option>
                </select>
            </div>
            <div class="w-full md:flex-1 flex flex-col">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Board</label>
                <select name="board" class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-base md:text-lg rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all cursor-pointer">
                    <option value="">Any Board</option>
                    <option value="cbse" <?php echo $board === 'cbse' ? 'selected' : ''; ?>>CBSE</option>
                    <option value="icse" <?php echo $board === 'icse' ? 'selected' : ''; ?>>ICSE</option>
                    <option value="state" <?php echo $board === 'state' ? 'selected' : ''; ?>>State Board</option>
                </select>
            </div>
            <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold h-[52px] md:h-[54px] px-8 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center whitespace-nowrap">
                <i class="fa-solid fa-search mr-2"></i> Search
            </button>
        </form>
    </div>
</section>

<section class="container mx-auto mb-24 px-4">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Schools Found: <?php echo count($schools); ?></h2>
    <?php if (empty($schools)): ?>
        <div class="bg-gray-50 rounded-2xl p-10 border border-gray-200 text-center">
            <p class="text-gray-600">No schools matched your filters. Try changing location, class, or board.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($schools as $school): ?>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($school['name']); ?></h3>
                        <?php if ((int)$school['is_verified'] === 1): ?>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Verified</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-sm text-gray-500 mt-2"><?php echo htmlspecialchars($school['address'] . ', ' . $school['city']); ?></p>
                    <div class="mt-3 text-sm text-gray-700 space-y-1">
                        <div><strong>Board:</strong> <?php echo htmlspecialchars($school['board'] ?: '-'); ?></div>
                        <div><strong>Classes:</strong> <?php echo htmlspecialchars($school['classes_offered'] ?: '-'); ?></div>
                        <div><strong>Fees:</strong> Rs. <?php echo htmlspecialchars($school['fees_min'] ?: '0'); ?> - Rs. <?php echo htmlspecialchars($school['fees_max'] ?: '0'); ?></div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="/myschooldesk/school/<?php echo urlencode($school['slug']); ?>" class="flex-1 text-center py-2 border border-blue-600 text-blue-700 rounded-lg font-semibold">View</a>
                        <a href="/myschooldesk/school/<?php echo urlencode($school['slug']); ?>" class="flex-1 text-center py-2 bg-blue-600 text-white rounded-lg font-semibold">Send Enquiry</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'footer.php'; ?>
