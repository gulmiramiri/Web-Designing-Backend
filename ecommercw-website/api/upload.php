<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

requireAdmin();

if (empty($_FILES['image'])) {
    jsonResponse(['success' => false, 'message' => 'No image file provided.'], 422);
}

$filename = handleImageUpload($_FILES['image'], __DIR__ . '/../uploads');

if ($filename === null) {
    jsonResponse(['success' => false, 'message' => 'Invalid image. Allowed types: jpg, png, webp, gif (max 5MB).'], 422);
}

jsonResponse(['success' => true, 'message' => 'Image uploaded.', 'filename' => $filename]);
