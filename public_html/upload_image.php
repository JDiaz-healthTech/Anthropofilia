<?php
// upload_image.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Services\ImageService;

$security->requireLogin();
$security->checkRateLimit('upload_image', 20, 3600);

// Forzar POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

// CSRF opcional (TinyMCE puede enviar X-CSRF-Token)
$csrf = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
if ($csrf) {
    $security->csrfValidate($csrf);
}

// Validar archivo presente
if (!isset($_FILES['file']) || ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Archivo inválido']);
    exit();
}

try {
    $security->validateUpload($_FILES['file']);

    $location = ImageService::store($_FILES['file'], [
        'max_side' => 1600,
        'webp'     => true,
        'prefix'   => 'tinymce_',
    ]);
} catch (\Throwable $e) {
    http_response_code(415);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

// Respuesta para TinyMCE (necesita ruta absoluta con /)
header('Content-Type: application/json');
echo json_encode(['location' => '/' . $location]);
