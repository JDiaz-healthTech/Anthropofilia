<?php
// upload_image.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$security->requireLogin();                      // sólo usuarios autenticados
$security->checkRateLimit('upload_image', 20, 3600); // 20 subidas/h por IP

// Forzar POST con archivo 'file' (TinyMCE usa ese nombre por defecto)
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

// (Opcional) Si añades el token en el formData o cabecera, lo validamos.
// En tu tinymce.init puedes pasar X-CSRF-Token. Aquí lo aceptamos si llega.
$csrf = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
if ($csrf) {
    $security->csrfValidate($csrf);
}

// Validación básica del archivo + reglas de tu SecurityManager
if (!isset($_FILES['file']) || ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Archivo inválido']);
    exit();
}
try {
    $security->validateUpload($_FILES['file']); // tamaño, MIME, dimensiones >0
} catch (Throwable $e) {
    http_response_code(415);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

// MIME real y dimensiones
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = (string)$finfo->file($_FILES['file']['tmp_name']);
$info  = getimagesize($_FILES['file']['tmp_name']);
if ($info === false) {
    http_response_code(415);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Imagen inválida']);
    exit();
}
[$w, $h] = $info;

// Elegir loader según MIME
$create = match ($mime) {
    'image/jpeg' => 'imagecreatefromjpeg',
    'image/png'  => 'imagecreatefrompng',
    'image/webp' => 'imagecreatefromwebp',
    'image/gif'  => 'imagecreatefromgif', // ojo: GIF animado perderá animación
    default      => null,
};
if (!$create || !function_exists($create)) {
    http_response_code(415);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Formato no soportado']);
    exit();
}

// Cargar y preparar lienzo destino (preservando alpha en PNG/WEBP/GIF)
$src = @$create($_FILES['file']['tmp_name']);
if (!$src) {
    http_response_code(415);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No se pudo decodificar la imagen']);
    exit();
}
$maxSide = 1600;
$ratio   = max($w, $h) > 0 ? min(1.0, $maxSide / max($w, $h)) : 1.0;
$nw = max(1, (int)round($w * $ratio));
$nh = max(1, (int)round($h * $ratio));

$dst = imagecreatetruecolor($nw, $nh);

// Preservar transparencia
if (in_array($mime, ['image/png', 'image/webp', 'image/gif'], true)) {
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    // Para GIF con transparencia indexada:
    $transparentIndex = imagecolortransparent($src);
    if ($transparentIndex >= 0 && $transparentIndex < imagecolorstotal($src)) {
        $transparentColor = imagecolorsforindex($src, $transparentIndex);
        $transIndex = imagecolorallocatealpha($dst, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue'], 127);
        imagefill($dst, 0, 0, $transIndex);
        imagecolortransparent($dst, $transIndex);
    } else {
        $alpha = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $alpha);
    }
}

imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

// Directorio destino (por año/mes)
$fsDir  = __DIR__ . '/uploads/' . date('Y/m/');
$urlDir = '/uploads/' . date('Y/m/');
if (!is_dir($fsDir) && !mkdir($fsDir, 0755, true) && !is_dir($fsDir)) {
    imagedestroy($src); imagedestroy($dst);
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No se pudo crear el directorio de subida']);
    exit();
}

// Nombre opaco + preferir WebP si está disponible
$nameBase = 'tinymce_' . bin2hex(random_bytes(16));
$location = null;

if (function_exists('imagewebp')) {
    $fsPath = $fsDir . $nameBase . '.webp';
    $ok = imagewebp($dst, $fsPath, 80);
    if ($ok) {
        $location = $urlDir . $nameBase . '.webp';
    }
}

if (!$location) {
    // Fallback al tipo original
    $ext = match ($mime) {
        'image/jpeg' => '.jpg',
        'image/png'  => '.png',
        'image/gif'  => '.gif',
        default      => '.bin',
    };
    $fsPath = $fsDir . $nameBase . $ext;
    $ok = match ($ext) {
        '.jpg' => imagejpeg($dst, $fsPath, 85),
        '.png' => imagepng($dst, $fsPath, 6),
        '.gif' => imagegif($dst, $fsPath),
        default => false,
    };
    if ($ok) {
        $location = $urlDir . $nameBase . $ext;
    }
}

imagedestroy($src);
imagedestroy($dst);

if (!$location) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No se pudo guardar la imagen']);
    exit();
}

// Respuesta para TinyMCE
header('Content-Type: application/json');
echo json_encode(['location' => $location]);
