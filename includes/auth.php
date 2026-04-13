<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if the current user is logged in as an admin.
 */
function is_admin_logged_in(): bool {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Protect admin pages by checking for admin session.
 */
function protect_admin_page() {
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
/**
 * Check if the current user is logged in as a school.
 */
function is_school_logged_in(): bool {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'school';
}

/**
 * Protect school pages by checking for school session.
 */
function protect_school_page() {
    if (!is_school_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
