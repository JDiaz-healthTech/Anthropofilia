<?php
/**
 * app/Helpers/functions.php
 *
 * Funciones auxiliares reutilizables en todo el proyecto.
 */
declare(strict_types=1);

if (!function_exists('format_date_es')) {
    /**
     * Formatear fecha ISO a formato español (dd/mm/YYYY)
     */
    function format_date_es(?string $isoDate, string $format = 'd/m/Y'): string {
        if ($isoDate === null || $isoDate === '') {
            return 'Fecha no disponible';
        }

        $timestamp = strtotime($isoDate);
        return $timestamp ? date($format, $timestamp) : 'Fecha no disponible';
    }
}

if (!function_exists('excerpt')) {
    /**
     * Generar extracto de texto HTML
     */
    function excerpt(?string $html, int $maxWords = 30, string $suffix = '…'): string {
        if ($html === null || $html === '') {
            return '';
        }

        // Eliminar tags HTML y normalizar espacios
        $plain = strip_tags($html);
        $plain = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = preg_replace('/\s+/u', ' ', $plain);
        $plain = trim($plain);

        if ($plain === '') {
            return '';
        }

        // Dividir en palabras
        $words = preg_split('/\s+/u', $plain);

        if (count($words) <= $maxWords) {
            return $plain;
        }

        return implode(' ', array_slice($words, 0, $maxWords)) . $suffix;
    }
}

if (!function_exists('post_url')) {
    /**
     * Generar URL de un post (prefiere slug sobre id)
     */
    function post_url(array $post): string {
        if (!empty($post['slug'])) {
            return 'post.php?slug=' . urlencode($post['slug']);
        }

        if (!empty($post['id_post'])) {
            return 'post.php?id=' . (int) $post['id_post'];
        }

        return '#';
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML corto (alias de htmlspecialchars)
     */
    function e(?string $value): string {
        return $value === null ? '' : htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('pluralize')) {
    /**
     * Pluralizar palabra según cantidad
     */
    function pluralize(int $count, string $singular, ?string $plural = null): string {
        if ($plural === null) {
            $plural = $singular . 's';
        }

        return $count === 1 ? $singular : $plural;
    }
}
