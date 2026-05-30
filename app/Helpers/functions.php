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

if (!function_exists('get_thumbnail_url')) {
    /**
     * Obtiene la URL de miniatura para un post.
     *
     * Prioridad:
     * 1. Si imagen_destacada_url es una imagen real → la devuelve
     * 2. Si es una URL de YouTube → genera thumbnail
     * 3. Si es una URL no-imagen (Calameo, Genially) → la ignora
     * 4. Si es NULL → busca iframes en el contenido del post
     *
     * @param string|null $url            imagen_destacada_url del post
     * @param string|null $contenido      contenido HTML del post (para buscar iframes)
     * @return array{url: string|null, type: string}  url de la miniatura y tipo de contenido
     */
    function get_thumbnail_url(?string $url, ?string $contenido = null): array
    {
        // 1. Si hay URL explícita, analizar qué tipo es
        if (!empty($url)) {
            $url = trim($url);

            // YouTube → thumbnail
            if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{11})/', $url, $m)) {
                return ['url' => 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg', 'type' => 'youtube'];
            }

            // Calameo → no se puede generar thumbnail sin API, usar placeholder
            if (str_contains($url, 'calameo.com')) {
                // Caer al análisis de contenido o devolver placeholder
            }
            // Genially → no es una imagen renderizable
            elseif (str_contains($url, 'genially.com') || str_contains($url, 'genial.ly')) {
                // No devolver como imagen, caer al análisis de contenido
            }
            // Cualquier otra cosa (imagen real, upload local) → devolver tal cual
            else {
                return ['url' => $url, 'type' => 'image'];
            }
        }

        // 2. Sin imagen válida: buscar iframes en el contenido
        if (!empty($contenido)) {
            // YouTube embebido en contenido
            if (preg_match('/(?:youtube\.com\/embed\/|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $contenido, $m)) {
                return ['url' => 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg', 'type' => 'youtube'];
            }

            // Genially embebido
            if (str_contains($contenido, 'genially.com') || str_contains($contenido, 'genial.ly')) {
                return ['url' => null, 'type' => 'genially'];
            }

            // Calameo embebido → placeholder (thumbnail requiere API con clave)
            if (str_contains($contenido, 'calameo.com')) {
                return ['url' => null, 'type' => 'calameo'];
            }

            // Vimeo embebido
            if (preg_match('/player\.vimeo\.com\/video\/(\d+)/', $contenido, $m)) {
                return ['url' => null, 'type' => 'vimeo'];
            }
        }

        // 3. Nada encontrado
        return ['url' => null, 'type' => 'none'];
    }
}

if (!function_exists('slugify')) {
    /**
     * Genera un slug limpio a partir de texto libre.
     * Centraliza la lógica antes duplicada en Page, Category y guardar_post.php.
     *
     * - Transliterar acentos y ñ (á → a, ñ → n) con mapa explícito (determinista)
     * - iconv como red para cualquier otro carácter no-ASCII
     * - Minúsculas, solo a-z 0-9 y guiones, sin guiones repetidos ni en los extremos
     * - Máximo 150 caracteres
     */
    function slugify(string $text): string {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        // 1) Mapa explícito para caracteres del español (no depende del locale)
        $map = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ü' => 'u', 'Ñ' => 'n',
        ];
        $text = strtr($text, $map);

        // 2) iconv como red para el resto (à, ç, etc.)
        $text = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;

        // 3) Normalizar
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? $text;
        $text = trim($text, '-');
        $text = preg_replace('/-+/', '-', $text) ?? $text;

        return substr($text, 0, 150);
    }
}
