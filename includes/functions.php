<?php
// includes/functions.php
function generate_request_code($length = 8) {
    return strtoupper(substr(bin2hex(random_bytes(16)), 0, $length));
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}

function require_admin() {
    if (!is_admin_logged_in()) {
        header("Location: /admin/login.php");
        exit;
    }
}
