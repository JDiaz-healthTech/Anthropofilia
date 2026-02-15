<?php
// public/guardar_personalizacion.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$security->requireLogin();

// Verificar método POST o reset
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['reset'])) {
    header('Location: personalizar.php');
    exit();
}

// RESET: Restaurar valores predeterminados
if (isset($_GET['reset'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM settings WHERE k IN ('theme_primary_color', 'theme_bg_color', 'header_bg_url')");
        $stmt->execute();

        header('Location: personalizar.php?status=success');
        exit();
    } catch (Exception $e) {
        error_log("Error al restablecer configuración: " . $e->getMessage());
        header('Location: personalizar.php?status=error');
        exit();
    }
}

// CSRF
$security->csrfValidate($_POST['csrf_token'] ?? null);

$current_header = get_setting($pdo, 'header_bg_url', '');

// 0. RESTAURAR DESDE HISTORIAL
    if (isset($_POST['restore_from_history'])) {
        $restorePath = trim($_POST['restore_from_history']);
        // Validar que existe en el historial
        $historyJson = get_setting($pdo, 'header_bg_history', '[]');
        $history = json_decode($historyJson, true) ?: [];

        if (in_array($restorePath, $history, true) && file_exists(__DIR__ . '/' . $restorePath)) {
            // Mover actual al historial
            if (!empty($current_header) && !in_array($current_header, $history, true)) {
                array_unshift($history, $current_header);
            }
            // Quitar la restaurada del historial
            $history = array_values(array_filter($history, fn($h) => $h !== $restorePath));
            $history = array_slice($history, 0, 5);

            set_setting($pdo, 'header_bg_history', json_encode($history));
            set_setting($pdo, 'header_bg_url', $restorePath);

            header('Location: personalizar.php?status=success');
            exit();
        }
    }



try {

    // 0b. OVERLAY OPACITY
    $overlayOpacity = (int)($_POST['header_overlay_opacity'] ?? 50);
    $overlayOpacity = max(0, min(100, $overlayOpacity));
    set_setting($pdo, 'header_overlay_opacity', (string)$overlayOpacity);

    // 1. COLORES
    $primary_color = trim($_POST['primary_color'] ?? '#0645ad');
    $bg_color = trim($_POST['bg_color'] ?? '#ffffff');

    // Validar formato hex
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $primary_color)) {
        $primary_color = '#0645ad';
    }
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $bg_color)) {
        $bg_color = '#ffffff';
    }

    // Guardar colores
    set_setting($pdo, 'theme_primary_color', $primary_color);
    set_setting($pdo, 'theme_bg_color', $bg_color);

// ¿Eliminar imagen actual?
    if (isset($_POST['remove_header_image']) && $_POST['remove_header_image'] === '1') {
        // Mover al historial en vez de borrar
        if (!empty($current_header)) {
            $historyJson = get_setting($pdo, 'header_bg_history', '[]');
            $history = json_decode($historyJson, true) ?: [];
            if (!in_array($current_header, $history, true)) {
                array_unshift($history, $current_header);
                $history = array_slice($history, 0, 5);
            }
            set_setting($pdo, 'header_bg_history', json_encode($history));
        }
        set_setting($pdo, 'header_bg_url', '');
        $current_header = '';
    }

    // ¿Subir nueva imagen?
    if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['header_image'];

        // Validar tipo
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            throw new Exception('Tipo de archivo no permitido');
        }

        // Validar tamaño (2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new Exception('Archivo demasiado grande (máximo 2MB)');
        }

        // Directorio de destino
        $uploadDir = __DIR__ . '/uploads/theme/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Nombre único
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'header_' . time() . '.' . $ext;
        $filepath = $uploadDir . $filename;

        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Error al subir el archivo');
        }

// Añadir imagen anterior al historial (máximo 5)
        if (!empty($current_header)) {
            $historyJson = get_setting($pdo, 'header_bg_history', '[]');
            $history = json_decode($historyJson, true) ?: [];

            // Añadir al inicio si no está ya
            if (!in_array($current_header, $history, true)) {
                array_unshift($history, $current_header);
            }

            // Mantener solo las últimas 5
            if (count($history) > 5) {
                $removed = array_splice($history, 5);
                // Borrar archivos que salen del historial
                foreach ($removed as $old) {
                    $oldPath = __DIR__ . '/' . $old;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            }

            set_setting($pdo, 'header_bg_history', json_encode($history));
        }

        // Guardar nueva imagen como activa
        $relativePath = 'uploads/theme/' . $filename;
        set_setting($pdo, 'header_bg_url', $relativePath);
    }



    header('Location: personalizar.php?status=success');
    exit();

} catch (Exception $e) {
    error_log("Error guardando personalización: " . $e->getMessage());
    header('Location: personalizar.php?status=error');
    exit();
}

