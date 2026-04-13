<?php
// migrate_status.php
require_once 'config.php';

try {
    echo "Starting migration...<br>";

    // 1. Update ENUM to include 'active' and 'inactive'
    // First, check existing column type to be safe
    $pdo->exec("ALTER TABLE schools MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'active', 'inactive') DEFAULT 'pending'");
    echo "Updated ENUM to include new statuses.<br>";

    // 2. Map 'approved' to 'active'
    $pdo->exec("UPDATE schools SET status = 'active' WHERE status = 'approved'");
    $count = $pdo->exec("SELECT ROW_COUNT()");
    echo "Mapped existing 'approved' records to 'active'.<br>";

    // 3. Optional: Map 'rejected' to 'inactive' if desired, but we'll leave it for now
    // Actually, let's just make sure 'active' and 'inactive' are the main ones used.
    
    // 4. Finalize ENUM (remove 'approved' if no longer needed, but let's keep it for compatibility if any other script uses it, or just clean it up)
    $pdo->exec("ALTER TABLE schools MODIFY COLUMN status ENUM('pending', 'active', 'inactive', 'rejected') DEFAULT 'pending'");
    echo "Cleaned up ENUM to ('pending', 'active', 'inactive', 'rejected').<br>";

    echo "Migration completed successfully!";

} catch (PDOException $e) {
    echo "Migration Error: " . $e->getMessage();
}
?>
