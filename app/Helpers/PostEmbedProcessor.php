<?php
/**
 * PostEmbedProcessor.php
 * 
 * Procesa marcadores [POST id="X" tipo="card|embebido"] en el contenido
 * y los reemplaza por el HTML del post correspondiente.
 * 
 * Ubicación: app/Helpers/PostEmbedProcessor.php
 */
declare(strict_types=1);

namespace App\Helpers;

use PDO;

class PostEmbedProcessor
{
    private PDO $pdo;
    private object $security;
    
    // Patrón para detectar marcadores: [POST id="123" tipo="card"]
    // Soporta: [POST id="5"], [POST id="5" tipo="embebido"], [POST slug="mi-post" tipo="card"]
    private const PATTERN = '/\[POST\s+(?:id="(\d+)"|slug="([a-z0-9-]+)")(?:\s+tipo="(card|embebido)")?\s*\]/i';
    
    public function __construct(PDO $pdo, object $security)
    {
        $this->pdo = $pdo;
        $this->security = $security;
    }
    
    /**
     * Procesa el contenido y reemplaza los marcadores por HTML de posts
     */
    public function process(string $contenido): string
    {
        return preg_replace_callback(self::PATTERN, function($matches) {
            $idPost = !empty($matches[1]) ? (int)$matches[1] : null;
            $slug = !empty($matches[2]) ? $matches[2] : null;
            $tipo = !empty($matches[3]) ? strtolower($matches[3]) : 'card';
            
            // Cargar el post
            $post = $this->getPost($idPost, $slug);
            
            if (!$post) {
                // Si no existe, devolver comentario HTML (no visible)
                return '<!-- Post no encontrado -->';
            }
            
            // Renderizar según tipo
            return $tipo === 'embebido' 
                ? $this->renderEmbebido($post) 
                : $this->renderCard($post);
                
        }, $contenido);
    }
    
    /**
     * Obtiene un post por ID o slug
     */
    private function getPost(?int $id, ?string $slug): ?array
    {
        if ($id) {
            $sql = 'SELECT p.*, c.nombre_categoria 
                    FROM posts p 
                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                    WHERE p.id_post = ? 
                    LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
        } elseif ($slug) {
            $sql = 'SELECT p.*, c.nombre_categoria 
                    FROM posts p 
                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                    WHERE p.slug = ? 
                    LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$slug]);
        } else {
            return null;
        }
        
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        return $post ?: null;
    }
    
    /**
     * Renderiza un post como contenido embebido (completo)
     */
    private function renderEmbebido(array $post): string
    {
        $titulo = htmlspecialchars($post['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
        $slug = urlencode($post['slug'] ?? '');
        $categoria = htmlspecialchars($post['nombre_categoria'] ?? '', ENT_QUOTES, 'UTF-8');
        $fecha = date('d/m/Y', strtotime($post['fecha_publicacion']));
        $fechaISO = date('Y-m-d', strtotime($post['fecha_publicacion']));
        $imagen = htmlspecialchars($post['imagen_destacada_url'] ?? '', ENT_QUOTES, 'UTF-8');
        $contenido = $this->security->sanitizeHTML($post['contenido'] ?? '');
        $url = url('post.php?slug=' . $slug);
        
        $imagenHTML = '';
        if ($imagen) {
            $imagenHTML = <<<HTML
            <div class="post-embebido__imagen">
                <img src="{$imagen}" alt="{$titulo}" loading="lazy">
            </div>
HTML;
        }
        
        $categoriaHTML = '';
        if ($categoria) {
            $categoriaHTML = '<span class="categoria">📁 ' . $categoria . '</span>';
        }
        
        return <<<HTML
<article class="post-embebido">
    <header class="post-embebido__header">
        <h3><a href="{$url}">{$titulo}</a></h3>
        <div class="post-embebido__meta">
            {$categoriaHTML}
            <time datetime="{$fechaISO}">{$fecha}</time>
        </div>
    </header>
    {$imagenHTML}
    <div class="post-embebido__contenido">
        {$contenido}
    </div>
    <footer class="post-embebido__footer">
        <a href="{$url}" class="btn-link">Ver post original →</a>
    </footer>
</article>
HTML;
    }
    
    /**
     * Renderiza un post como card (preview)
     */
    private function renderCard(array $post): string
    {
        $titulo = htmlspecialchars($post['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
        $slug = urlencode($post['slug'] ?? '');
        $categoria = htmlspecialchars($post['nombre_categoria'] ?? '', ENT_QUOTES, 'UTF-8');
        $fecha = date('d/m/Y', strtotime($post['fecha_publicacion']));
        $fechaISO = date('Y-m-d', strtotime($post['fecha_publicacion']));
        $imagen = htmlspecialchars($post['imagen_destacada_url'] ?? '', ENT_QUOTES, 'UTF-8');
        $url = url('post.php?slug=' . $slug);
        
        // Excerpt
        $excerpt = strip_tags($post['contenido'] ?? '');
        $excerpt = mb_substr($excerpt, 0, 200, 'UTF-8');
        $excerpt = htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') . '...';
        
        $imagenHTML = '';
        if ($imagen) {
            $imagenHTML = <<<HTML
            <div class="post-card-pagina__imagen">
                <a href="{$url}">
                    <img src="{$imagen}" alt="{$titulo}" loading="lazy">
                </a>
            </div>
HTML;
        }
        
        $categoriaHTML = '';
        if ($categoria) {
            $categoriaHTML = '<span class="categoria">📁 ' . $categoria . '</span>';
        }
        
        return <<<HTML
<article class="post-card-pagina">
    {$imagenHTML}
    <div class="post-card-pagina__contenido">
        <h3><a href="{$url}">{$titulo}</a></h3>
        <div class="post-card-pagina__meta">
            {$categoriaHTML}
            <time datetime="{$fechaISO}">{$fecha}</time>
        </div>
        <div class="post-card-pagina__excerpt">{$excerpt}</div>
        <a href="{$url}" class="btn btn-sm">Leer más →</a>
    </div>
</article>
HTML;
    }
}
