<?php
// app/Helpers/TocGenerator.php
declare(strict_types=1);

namespace App\Helpers;

class TocGenerator
{
    /**
     * Genera un índice de contenidos a partir del HTML
     * Busca H2, H3, H4 y crea estructura jerárquica
     *
     * @param string $html Contenido HTML
     * @return array ['toc' => HTML del índice, 'content' => HTML con IDs añadidos]
     */
    public static function generate(string $html): array
    {
        if (empty(trim($html))) {
            return ['toc' => '', 'content' => $html];
        }

        // Usar DOMDocument para parsear
        $dom = new \DOMDocument();

        // Evitar warnings por HTML5 y preservar encoding
        libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="UTF-8">' .
            '<div id="toc-wrapper">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        // Buscar encabezados H2, H3, H4
        $xpath = new \DOMXPath($dom);
        $headings = $xpath->query('//h2|//h3|//h4');

        if ($headings->length === 0) {
            return ['toc' => '', 'content' => $html];
        }

        $tocItems = [];
        $counters = [2 => 0, 3 => 0, 4 => 0];

        foreach ($headings as $index => $heading) {
            $level = (int) substr($heading->nodeName, 1); // h2 -> 2
            $text = trim($heading->textContent);

            if (empty($text)) {
                continue;
            }

            // Generar ID único
            $id = self::generateSlug($text) . '-' . $index;

            // Añadir ID al encabezado si no tiene
            if (!$heading->hasAttribute('id')) {
                $heading->setAttribute('id', $id);
            } else {
                $id = $heading->getAttribute('id');
            }

            // Actualizar contadores para numeración
            $counters[$level]++;
            // Resetear contadores de niveles inferiores
            for ($i = $level + 1; $i <= 4; $i++) {
                $counters[$i] = 0;
            }

            // Generar número jerárquico
            $number = self::generateNumber($counters, $level);

            $tocItems[] = [
                'level' => $level,
                'text' => $text,
                'id' => $id,
                'number' => $number
            ];
        }

        // Generar HTML del índice
        $tocHtml = self::buildTocHtml($tocItems);

        // Extraer contenido modificado (sin el wrapper)
        $wrapper = $dom->getElementById('toc-wrapper');
        $modifiedContent = '';
        foreach ($wrapper->childNodes as $child) {
            $modifiedContent .= $dom->saveHTML($child);
        }

        return [
            'toc' => $tocHtml,
            'content' => $modifiedContent
        ];
    }

    /**
     * Genera el HTML del índice
     */
    private static function buildTocHtml(array $items): string
    {
        if (empty($items)) {
            return '';
        }

        $html = '<nav class="toc" aria-label="Índice de contenidos">';
        $html .= '<div class="toc__header">';
        $html .= '<h2 class="toc__title">📑 Índice de contenidos</h2>';
        $html .= '<button class="toc__toggle" aria-expanded="true" aria-controls="toc-list">';
        $html .= '<span class="toc__toggle-text">Ocultar</span>';
        $html .= '<span class="toc__toggle-icon">▼</span>';
        $html .= '</button>';
        $html .= '</div>';
        $html .= '<ol class="toc__list" id="toc-list">';

        $currentLevel = 2;

        foreach ($items as $item) {
            $level = $item['level'];

            // Abrir sublistas si bajamos de nivel
            while ($currentLevel < $level) {
                $html .= '<ol class="toc__sublist">';
                $currentLevel++;
            }

            // Cerrar sublistas si subimos de nivel
            while ($currentLevel > $level) {
                $html .= '</li></ol>';
                $currentLevel--;
            }

            // Añadir ítem
            $html .= sprintf(
                '<li class="toc__item toc__item--level-%d">' .
                '<a href="#%s" class="toc__link">' .
                '<span class="toc__number">%s</span>' .
                '<span class="toc__text">%s</span>' .
                '</a>',
                $level,
                htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($item['number'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($item['text'], ENT_QUOTES, 'UTF-8')
            );
        }

        // Cerrar todas las listas abiertas
        while ($currentLevel >= 2) {
            $html .= '</li></ol>';
            $currentLevel--;
        }

        $html .= '</nav>';

        return $html;
    }

    /**
     * Genera número jerárquico (1, 1.1, 1.1.1)
     */
    private static function generateNumber(array $counters, int $level): string
    {
        $parts = [];
        for ($i = 2; $i <= $level; $i++) {
            if ($counters[$i] > 0) {
                $parts[] = $counters[$i];
            }
        }
        return implode('.', $parts);
    }

    /**
     * Genera slug para ID
     */
    private static function generateSlug(string $text): string
    {
        $slug = mb_strtolower($text, 'UTF-8');
        $slug = preg_replace('/[áàäâã]/u', 'a', $slug);
        $slug = preg_replace('/[éèëê]/u', 'e', $slug);
        $slug = preg_replace('/[íìïî]/u', 'i', $slug);
        $slug = preg_replace('/[óòöôõ]/u', 'o', $slug);
        $slug = preg_replace('/[úùüû]/u', 'u', $slug);
        $slug = preg_replace('/[ñ]/u', 'n', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return substr($slug, 0, 50) ?: 'seccion';
    }
}
