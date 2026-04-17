<?php
require_once '../config.php';
require_once '../includes/auth.php';
protect_admin_page();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=enquiries_export_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Lead ID', 'Parent Name', 'Mobile', 'Email', 'Child Name', 'Class', 'Applied Schools', 'Status', 'Date']);

$query = "SELECT e.*, GROUP_CONCAT(CONCAT(s.name, ' (', esm.admission_status, ')') SEPARATOR ', ') as school_list 
          FROM enquiries e 
          LEFT JOIN enquiry_school_mapping esm ON e.id = esm.enquiry_id 
          LEFT JOIN schools s ON esm.school_id = s.id 
          GROUP BY e.id 
          ORDER BY e.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Count schools for the summary
    $schoolsArr = $row['school_list'] ? explode(', ', $row['school_list']) : [];
    $schoolCount = count($schoolsArr);
    $statusSummary = ($schoolCount > 1) ? "Applied to $schoolCount Schools" : "Single Application";

    fputcsv($output, [
        $row['lead_id'],
        $row['parent_name'],
        $row['mobile'],
        $row['email'],
        $row['child_name'],
        $row['child_class'],
        $row['school_list'],
        $statusSummary,
        $row['created_at']
    ]);
}

fclose($output);
exit;
?>
