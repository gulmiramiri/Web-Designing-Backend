<?php
/**
 * Shared helper functions used across the API and pages.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Make sure API responses are ALWAYS valid JSON, even if a PHP warning,
 * notice, or fatal error occurs. Without this, any stray warning gets
 * printed as HTML before our json_encode() output, which breaks
 * response.json() on the frontend with "Unexpected server response."
 *
 * DEBUG_MODE = true shows the real PHP error message in the JSON response
 * (very useful while setting the project up locally). Set it to false
 * once the site is live, so internal error details are never exposed to
 * visitors — the log_errors line below still writes everything to the
 * normal PHP error log either way.
 */
define('DEBUG_MODE', true);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

function isApiRequest(): bool
{
    return strpos($_SERVER['SCRIPT_NAME'] ?? '', '/api/') !== false;
}

function sendServerErrorJson(string $debugMessage): void
{
    if (!isApiRequest() || headers_sent()) {
        return;
    }
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => DEBUG_MODE ? $debugMessage : 'A server error occurred. Check the PHP error log for details.',
    ]);
}

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false; // respect @-suppressed errors
    }
    error_log("PHP warning/notice: $message in $file on line $line");
    return true; // prevent default HTML output of the warning
});

set_exception_handler(function (Throwable $e) {
    // This is the most common source of "server error" during setup —
    // e.g. a PDOException because a table doesn't exist yet, or the
    // database credentials are wrong.
    error_log('Uncaught exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    sendServerErrorJson('Server error: ' . $e->getMessage());
    exit;
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        error_log('PHP fatal error: ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line']);
        sendServerErrorJson('Server error: ' . $error['message'] . ' in ' . basename($error['file']) . ' on line ' . $error['line']);
    }
});

require_once __DIR__ . '/../config/database.php';

/** Send a JSON response and stop execution. */
function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/** Read and decode the JSON request body. */
function getJsonInput(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/** Escape output for safe HTML display (XSS protection). */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/** Basic input sanitization helper. */
function clean(?string $value): string
{
    return trim(strip_tags((string)$value));
}

/** Generate and store a CSRF token, returning it. */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Validate a CSRF token sent by the client. */
function validateCsrfToken(?string $token): bool
{
    return !empty($_SESSION['csrf_token']) && !empty($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

/** Check whether the current user is logged in. */
function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

/** Check whether the current user is an admin. */
function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

/** Require login for an API endpoint, otherwise respond 401. */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
    }
}

/** Require admin role for an API endpoint, otherwise respond 403. */
function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        jsonResponse(['success' => false, 'message' => 'Admin access required.'], 403);
    }
}

/** Require login for a normal (non-API) page, redirecting if not authenticated. */
function requirePageLogin(string $redirect = 'login.php'): void
{
    if (!isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}

/** Require admin for a normal (non-API) page. */
function requirePageAdmin(string $redirect = '../login.php'): void
{
    if (!isAdmin()) {
        header('Location: ' . $redirect);
        exit;
    }
}

/** Validate an uploaded image and move it into uploads/, returning the filename. */
function handleImageUpload(array $file, string $uploadDir): ?string
{
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $allowedExts  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $maxSize      = 5 * 1024 * 1024; // 5MB

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if ($file['size'] > $maxSize) {
        return null;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes, true)) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts, true)) {
        return null;
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destination = rtrim($uploadDir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return null;
    }

    return $filename;
}
