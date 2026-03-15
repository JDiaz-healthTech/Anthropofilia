<?php
// public/guardar_personalizacion.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$security->requireLogin();

// Solo POST permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: personalizar.php');
    exit();
}

// CSRF — valida siempre, para reset y para guardar
$security->requireValidCsrf();

// RESET: Restaurar valores predeterminados
if (($_POST['action'] ?? '') === 'reset') {
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
        $relativePath = \App\Services\ImageService::store($_FILES['header_image'], [
            'prefix' => 'header_',
            'subdir' => 'theme',
        ]);

        // Añadir imagen anterior al historial (máximo 5)
        if (!empty($current_header)) {
            $historyJson = get_setting($pdo, 'header_bg_history', '[]');
            $history = json_decode($historyJson, true) ?: [];

            if (!in_array($current_header, $history, true)) {
                array_unshift($history, $current_header);
            }

            if (count($history) > 5) {
                $removed = array_splice($history, 5);
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
        set_setting($pdo, 'header_bg_url', $relativePath);
    }

    header('Location: personalizar.php?status=success');
    exit();

} catch (Exception $e) {
    error_log("Error guardando personalización: " . $e->getMessage());
    header('Location: personalizar.php?status=error');
    exit();
}

