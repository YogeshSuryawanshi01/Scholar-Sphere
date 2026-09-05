<?php
/**
 * Session bootstrapper.
 * Keeps admin and public sessions isolated so both areas can be used side by side.
 */

function getRequestedSessionArea() {
    $script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    if (strpos($script_name, '/admin/') !== false || preg_match('#/dashboard\.php$#', $script_name)) {
        return 'admin';
    }

    return 'public';
}

function bootSession($area = null) {
    $area = $area ?: getRequestedSessionArea();
    $session_name = $area === 'admin' ? 'SCHOLAR_ADMIN' : 'SCHOLAR_PUBLIC';
    $cookie_path = '/';

    if (session_status() === PHP_SESSION_ACTIVE) {
        if (session_name() === $session_name) {
            return $area;
        }

        session_write_close();
    }

    session_name($session_name);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => $cookie_path,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();

    return $area;
}
?>
