<?php
// ===================================================================
// upload_image.php
// - Endpoint para subida de imágenes desde TinyMCE
// - Valida MIME, tamaño, reescala a 1600px y guarda (WebP si es posible)
// - Devuelve JSON con { location: 'ruta' } que TinyMCE inserta
// ===================================================================

require_once __DIR__ . '/init.php';

// Solo POST con archivo 'file'
if (!isset($_FILES['file']) || ($_FILES['file']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
    http_response_code(400); exit('Archivo inválido');
}

// Validación básica (reuse SecurityManager validator si quieres)
try {
    SecurityManager::instance()->validateUpload($_FILES['file']);
} catch (Throwable $e) {
    http_response_code(415); echo $e->getMessage(); exit;
}

// MIME real
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($_FILES['file']['tmp_name']);

// Dimensiones
[$w,$h] = getimagesize($_FILES['file']['tmp_name']);
$max = 1600; $ratio = min(1, $max / max($w,$h));
$nw = (int)($w * $ratio); $nh = (int)($h * $ratio);

// Creador según MIME
$create = match ($mime) {
  'image/jpeg' => 'imagecreatefromjpeg',
  'image/png'  => 'imagecreatefrompng',
  'image/webp' => 'imagecreatefromwebp',
  'image/gif'  => 'imagecreatefromgif',
  default      => null,
};
if (!$create || !function_exists($create)) { http_response_code(415); exit('Formato no soportado'); }

$src = $create($_FILES['file']['tmp_name']);
$dst = imagecreatetruecolor($nw, $nh);
imagecopyresampled($dst, $src, 0,0,0,0, $nw,$nh, $w,$h);

// Nombre seguro
$name = 'tinymce_' . bin2hex(random_bytes(8));
$dir = __DIR__ . '/uploads';
if (!is_dir($dir)) { mkdir($dir, 0755, true); }

if (function_exists('imagewebp')) {
    $path = $dir . "/{$name}.webp";
    imagewebp($dst, $path, 80);
    $url = "uploads/{$name}.webp";
} else {
    // Fallback al tipo original
    $ext = match ($mime) {
      'image/jpeg' => '.jpg',
      'image/png'  => '.png',
      'image/gif'  => '.gif',
      default      => '.bin'
    };
    $path = $dir . "/{$name}{$ext}";
    $url  = "uploads/{$name}{$ext}";
    if ($mime==='image/jpeg') imagejpeg($dst,$path,85);
    elseif ($mime==='image/png') imagepng($dst,$path,6);
    elseif ($mime==='image/gif') imagegif($dst,$path);
}

imagedestroy($src); imagedestroy($dst);

header('Content-Type: application/json');
echo json_encode(['location' => $url]);
