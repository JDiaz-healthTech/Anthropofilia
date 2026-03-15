<?php
// app/Services/ImageService.php
declare(strict_types=1);

namespace App\Services;

class ImageService
{
    private const WEBP_QUALITY     = 80;
    private const JPEG_QUALITY     = 85;
    private const PNG_COMPRESSION  = 6;
    private const DEFAULT_MAX_SIDE = 1600;
    private const ALLOWED_MIME     = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MIME_EXT_MAP     = [
        'image/jpeg' => '.jpg',
        'image/png'  => '.png',
        'image/gif'  => '.gif',
        'image/webp' => '.webp',
    ];
    private const MIME_LOADER_MAP = [
        'image/jpeg' => 'imagecreatefromjpeg',
        'image/png'  => 'imagecreatefrompng',
        'image/gif'  => 'imagecreatefromgif',
        'image/webp' => 'imagecreatefromwebp',
    ];

    /**
     * Procesa y almacena una imagen subida.
     *
     * @param array $file    Elemento de $_FILES
     * @param array $options Opciones:
     *   'max_side' => int|null  (null = no resize)
     *   'webp'     => bool      (convertir a WebP, default false)
     *   'prefix'   => string    (prefijo del nombre, default '')
     *   'subdir'   => string    (subcarpeta en uploads/, default 'YYYY/MM')
     * @return string Ruta relativa servible (e.g. 'uploads/2026/03/abc.webp')
     * @throws \RuntimeException
     */
    public static function store(array $file, array $options = []): string
    {
        $maxSide     = $options['max_side'] ?? null;
        $convertWebp = $options['webp'] ?? false;
        $prefix      = $options['prefix'] ?? '';
        $subdir      = $options['subdir'] ?? date('Y/m');

        $tmpPath = $file['tmp_name'];
        $mime    = self::detectMime($tmpPath);
        self::assertAllowedMime($mime);

        $fsDir   = self::ensureDir($subdir);
        $urlDir  = 'uploads/' . $subdir . '/';
        $name    = self::generateName($prefix);

        if ($maxSide !== null || $convertWebp) {
            return self::processWithGd($tmpPath, $mime, $fsDir, $urlDir, $name, $maxSide, $convertWebp);
        }

        // Sin procesamiento GD: mover directamente
        $ext  = self::MIME_EXT_MAP[$mime] ?? '.bin';
        $dest = $fsDir . $name . $ext;

        if (!move_uploaded_file($tmpPath, $dest)) {
            throw new \RuntimeException('Error al mover el archivo subido.');
        }

        return $urlDir . $name . $ext;
    }

    private static function detectMime(string $tmpPath): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = (string) $finfo->file($tmpPath);

        return $mime;
    }

    private static function assertAllowedMime(string $mime): void
    {
        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            throw new \RuntimeException('Formato de imagen no soportado: ' . $mime);
        }
    }

    private static function ensureDir(string $subdir): string
    {
        $fsDir = PUBLIC_PATH . '/uploads/' . $subdir . '/';

        if (!is_dir($fsDir) && !mkdir($fsDir, 0755, true) && !is_dir($fsDir)) {
            throw new \RuntimeException('No se pudo crear el directorio de subida.');
        }

        return $fsDir;
    }

    private static function generateName(string $prefix): string
    {
        return $prefix . bin2hex(random_bytes(16));
    }

    private static function processWithGd(
        string $tmpPath,
        string $mime,
        string $fsDir,
        string $urlDir,
        string $nameBase,
        ?int   $maxSide,
        bool   $convertWebp
    ): string {
        $loader = self::MIME_LOADER_MAP[$mime] ?? null;
        if (!$loader || !function_exists($loader)) {
            throw new \RuntimeException('No se pudo decodificar la imagen.');
        }

        $src = @$loader($tmpPath);
        if (!$src) {
            throw new \RuntimeException('No se pudo decodificar la imagen.');
        }

        $w = imagesx($src);
        $h = imagesy($src);

        // Calcular dimensiones de destino
        if ($maxSide !== null && max($w, $h) > $maxSide) {
            $ratio = $maxSide / max($w, $h);
            $nw = max(1, (int) round($w * $ratio));
            $nh = max(1, (int) round($h * $ratio));
        } else {
            $nw = $w;
            $nh = $h;
        }

        $dst = imagecreatetruecolor($nw, $nh);
        self::preserveAlpha($dst, $src, $mime);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($src);

        $result = self::saveImage($dst, $fsDir, $urlDir, $nameBase, $mime, $convertWebp);
        imagedestroy($dst);

        return $result;
    }

    private static function preserveAlpha(\GdImage $dst, \GdImage $src, string $mime): void
    {
        if (!in_array($mime, ['image/png', 'image/webp', 'image/gif'], true)) {
            return;
        }

        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        // GIF con transparencia indexada
        $transparentIndex = imagecolortransparent($src);
        if ($transparentIndex >= 0 && $transparentIndex < imagecolorstotal($src)) {
            $tc = imagecolorsforindex($src, $transparentIndex);
            $transIdx = imagecolorallocatealpha($dst, $tc['red'], $tc['green'], $tc['blue'], 127);
            imagefill($dst, 0, 0, $transIdx);
            imagecolortransparent($dst, $transIdx);
        } else {
            $alpha = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefill($dst, 0, 0, $alpha);
        }
    }

    private static function saveImage(
        \GdImage $image,
        string   $fsDir,
        string   $urlDir,
        string   $nameBase,
        string   $mime,
        bool     $preferWebp
    ): string {
        // Intentar WebP
        if ($preferWebp && function_exists('imagewebp')) {
            $path = $fsDir . $nameBase . '.webp';
            if (imagewebp($image, $path, self::WEBP_QUALITY)) {
                return $urlDir . $nameBase . '.webp';
            }
        }

        // Fallback al formato original
        $ext = self::MIME_EXT_MAP[$mime] ?? '.bin';
        $path = $fsDir . $nameBase . $ext;

        $ok = match ($ext) {
            '.jpg'  => imagejpeg($image, $path, self::JPEG_QUALITY),
            '.png'  => imagepng($image, $path, self::PNG_COMPRESSION),
            '.gif'  => imagegif($image, $path),
            '.webp' => imagewebp($image, $path, self::WEBP_QUALITY),
            default => false,
        };

        if (!$ok) {
            throw new \RuntimeException('No se pudo guardar la imagen.');
        }

        return $urlDir . $nameBase . $ext;
    }
}
