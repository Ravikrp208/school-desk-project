<?php
function msd_get_setting(PDO $pdo, string $key, string $default = ''): string
{
    try {
        $stmt = $pdo->prepare('SELECT value FROM settings WHERE key_name = ?');
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : $default;
    } catch (Exception $e) {
        return $default;
    }
}

function msd_update_setting(PDO $pdo, string $key, string $value): bool
{
    $stmt = $pdo->prepare('INSERT INTO settings (key_name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?');
    return $stmt->execute([$key, $value, $value]);
}

function msd_class_options(): array
{
    return [
        'Playgroup',
        'Nursery',
        'Jr KG',
        'Sr KG',
        'Class 1',
        'Class 2',
        'Class 3',
        'Class 4',
        'Class 5',
        'Class 6',
        'Class 7',
        'Class 8',
        'Class 9',
        'Class 10',
        'Class 11 Science',
        'Class 11 Commerce',
        'Class 11 Humanities',
        'Class 12 Science',
        'Class 12 Commerce',
        'Class 12 Humanities',
    ];
}

function msd_facility_options(): array
{
    return [
        'Dance Room' => 'fa-music',
        'Auditorium' => 'fa-landmark',
        'Swimming Pool' => 'fa-person-swimming',
        'Horse Riding' => 'fa-horse',
        'Smart Classroom' => 'fa-chalkboard-user',
        'Sickbay Room' => 'fa-heart-pulse',
        'Canteen' => 'fa-utensils',
        'Comprehensive Counseling' => 'fa-headset',
        'Books Provided' => 'fa-book',
        'Uniform' => 'fa-shirt',
        'Play Area' => 'fa-football',
        'Library' => 'fa-book-open',
        'Art Studio' => 'fa-palette',
        'Music Room' => 'fa-music',
        'GPS Bus' => 'fa-bus',
        'Healthy Meals' => 'fa-apple-whole',
        'AC Classrooms' => 'fa-snowflake',
        'Science Lab' => 'fa-flask',
        'Sports Complex' => 'fa-volleyball'
    ];
}

function msd_budget_options(): array
{
    return [
        '20K-50K',
        '50K-1L',
        '1L-2L',
        '2L-3L',
        '3L+',
    ];
}

function msd_board_options(): array
{
    return ['CBSE', 'ICSE', 'State', 'IB', 'IGCSE'];
}

function msd_generate_lead_id(PDO $pdo): string
{
    do {
        $leadId = 'MSD' . random_int(10000, 99999);
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM enquiries WHERE lead_id = :lead_id');
        $stmt->execute(['lead_id' => $leadId]);
    } while ((int) $stmt->fetchColumn() > 0);

    return $leadId;
}

function msd_format_currency($amount): string
{
    if (!$amount)
        return 'N/A';
    return '₹' . number_format($amount / 1000, 1) . 'K';
}

function msd_get_status_badge($status): string
{
    $badges = [
        'pending' => '<span class="badge badge-orange">Pending</span>',
        'admission_done' => '<span class="badge badge-green">Confirmed </span>',
        'not_converted' => '<span class="badge badge-red">Not Converted </span>'
    ];
    return $badges[$status] ?? $status;
}

function time_elapsed_string($datetime, $full = false) {
    if (!$datetime) return 'N/A';
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff_days = $diff->d;
    $weeks = floor($diff_days / 7);
    $days = $diff_days % 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    // Manual week handling if needed, but standard diff is usually fine for d, h, i, s.
    // Let's just use the standard ones for simplicity and reliability.
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
