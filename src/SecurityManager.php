<?php
/**
 * security_manager.php
 * Gestor de seguridad para Anthropofilia
 *
 * Requisitos recomendados:
 * - PHP 8.0+
 * - PDO configurado (MySQL/MariaDB)
 * - Composer autoload (para HTMLPurifier si lo instalas)
 *
 * Tablas sugeridas:
 *
 * CREATE TABLE IF NOT EXISTS rate_limits (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   action VARCHAR(50) NOT NULL,
 *   ip VARBINARY(16) NOT NULL,
 *   ts TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *   INDEX(action, ip, ts)
 * ) ENGINE=InnoDB;
 *
 * CREATE TABLE IF NOT EXISTS app_logs (
 *   id BIGINT AUTO_INCREMENT PRIMARY KEY,
 *   ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   ip VARBINARY(16) NULL,
 *   user_id INT NULL,
 *   level ENUM('info','warning','error','security') NOT NULL DEFAULT 'info',
 *   event VARCHAR(100) NOT NULL,
 *   details JSON NULL,
 *   user_agent VARCHAR(255) NULL,
 *   INDEX(level, ts),
 *   INDEX(event, ts)
 * ) ENGINE=InnoDB;
 */

declare(strict_types=1);

namespace App;

final class SecurityManager
{
   // private static ?self $instance = null;

    /** @var PDO|null */
    private ?PDO $pdo = null;

    /** @var array<string,mixed> */
    private array $config = [
        // 'dev'|'prod'
        'env' => 'prod',

        // Session
        'session_name' => 'anth_session',
        'session_idle_timeout' => 1800, // 30 min
        'session_rotate_every' => 600,  // 10 min

        // Rate limit (general y formularios POST)
        'rate_limit' => [
            'enabled' => true,
            'general' => ['max' => 120, 'window' => 3600],  // 120 req/hora
            'post'    => ['max' => 30,  'window' => 3600],  // 30 POST/hora
        ],

        // CSP y cabeceras; ajustado para TinyMCE CDN
        'csp' => [
            // Si usas TinyMCE via CDN
            'tinymce_cdn' => 'https://cdn.tiny.cloud',
            // Si usas jsdelivr/otros, puedes añadirlos aquí
            'extra_script_src' => ['https://cdn.jsdelivr.net'],
            // Permite inline por practicidad inicial; idealmente usar nonces/hashes
            'allow_unsafe_inline' => true,
        ],

        // Uploads
        'uploads' => [
            'max_size_bytes' => 2 * 1024 * 1024, // 2MB
            'allowed_mime' => ['image/jpeg','image/png','image/gif','image/webp'],
            'max_pixels' => 1600, // redimensionado recomendado en tu uploader
        ],
    ];

    /** Rate limit fallback en sesión */
    private array $rateLimitSession = [];

    /**
     * Constructor para la Inyección de Dependencias
     *
     * @param array<string,mixed> $config
     * @param PDO|null $pdo
     */
    public function __construct(array $config = [], ?PDO $pdo = null)
    {
        $this->config = array_replace_recursive($this->config, $config);
        $this->pdo = $pdo;
    }

    /** Versátil: llama una vez al inicio de cada request */
    public function boot(): void
    {
        $this->startSecureSession();
        $this->applySecurityHeaders();
        $this->enforceRateLimits();
    }

    /**
     * Obtén instancia única
     * @param array<string,mixed> $config
     */
    public static function instance(array $config = [], ?PDO $pdo = null): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        // Permite configurar en caliente (primera llamada)
        if ($pdo && !self::$instance->pdo) {
            self::$instance->pdo = $pdo;
        }
        if ($config) {
            self::$instance->config = array_replace_recursive(self::$instance->config, $config);
        }
        return self::$instance;
    }

  // ========== CONSTRUCTOR ==========
  //  private function __construct() {}

    // ========== SESIÓN SEGURA ==========

    private function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = $this->isHttps();
            session_name($this->config['session_name']);
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();

            $_SESSION['__created_at'] = $_SESSION['__created_at'] ?? time();
            $_SESSION['__last_seen']  = $_SESSION['__last_seen']  ?? time();

            // Rotación de ID
            if (time() - (int)$_SESSION['__created_at'] > (int)$this->config['session_rotate_every']) {
                session_regenerate_id(true);
                $_SESSION['__created_at'] = time();
            }
            // Expiración por inactividad
            if (time() - (int)$_SESSION['__last_seen'] > (int)$this->config['session_idle_timeout']) {
                $this->destroySession();
                session_start();
            }
            $_SESSION['__last_seen'] = time();
        }
    }

    private function destroySession(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'],
                $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    private function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443');
    }

    // ========== CABECERAS / CSP ==========

    public function applySecurityHeaders(): void
    {
        // Importante: no tengas salida previa (echo) antes de estas cabeceras.

        // Tipos y XSS legacy: X-XSS-Protection está obsoleto, no lo usamos.
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Enclickjacking (o usa frame-ancestors en CSP)
        header('X-Frame-Options: SAMEORIGIN');

        // HSTS en producción y bajo HTTPS
        if ($this->config['env'] === 'prod' && $this->isHttps()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        // CSP coherente con TinyMCE + imágenes data/blob
        $scriptSrc = ["'self'"];
        if (!empty($this->config['csp']['tinymce_cdn'])) {
            $scriptSrc[] = $this->config['csp']['tinymce_cdn'];
            // TinyMCE usa XHR/fetch a su CDN
            $connectSrc[] = $this->config['csp']['tinymce_cdn'];
        }
        if (!empty($this->config['csp']['extra_script_src'])) {
            foreach ($this->config['csp']['extra_script_src'] as $u) { $scriptSrc[] = $u; }
        }
        if (!empty($this->config['csp']['allow_unsafe_inline'])) {
            $scriptSrc[] = "'unsafe-inline'";
            $styleSrcInline = "'unsafe-inline'";
        } else {
            $styleSrcInline = '';
        }

        $styleSrc   = array_filter(["'self'", $styleSrcInline]);
        $imgSrc     = ["'self'", "data:", "blob:", "https:"];
        $fontSrc    = ["'self'", "data:", "https:"];
        $connectSrc = $connectSrc ?? ["'self'"];
        $frameAnc   = ["'self'"];

        $csp = sprintf(
            "default-src 'self'; script-src %s; style-src %s; img-src %s; font-src %s; connect-src %s; frame-ancestors %s;",
            implode(' ', $scriptSrc),
            implode(' ', $styleSrc),
            implode(' ', $imgSrc),
            implode(' ', $fontSrc),
            implode(' ', $connectSrc),
            implode(' ', $frameAnc)
        );
        header("Content-Security-Policy: $csp");
    }

    // ========== CSRF ==========

    public function csrfToken(): string
    {
        $this->startSecureSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function csrfValidate(string $token): void
    {
        $this->startSecureSession();
        if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            $this->logEvent('security', 'csrf_invalid', ['uri' => $_SERVER['REQUEST_URI'] ?? '']);
            exit('CSRF inválido');
        }
    }

    // ========== RATE LIMITING ==========

    private function enforceRateLimits(): void
    {
        if (empty($this->config['rate_limit']['enabled'])) return;

        $this->checkRateLimit('general',
            (int)$this->config['rate_limit']['general']['max'],
            (int)$this->config['rate_limit']['general']['window']
        );

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->checkRateLimit('post_form',
                (int)$this->config['rate_limit']['post']['max'],
                (int)$this->config['rate_limit']['post']['window']
            );
        }
    }

    public function checkRateLimit(string $action, int $maxAttempts, int $windowSeconds): void
    {
        $ip = $this->clientIPBinary();

        if ($this->pdo) {
            // Limpieza de ventana
            $del = $this->pdo->prepare("DELETE FROM rate_limits WHERE ts < (NOW() - INTERVAL :w SECOND)");
            $del->execute([':w' => $windowSeconds]);

            $sel = $this->pdo->prepare("SELECT COUNT(*) FROM rate_limits WHERE action = :a AND ip = :ip");
            $sel->execute([':a' => $action, ':ip' => $ip]);
            $count = (int)$sel->fetchColumn();

            if ($count >= $maxAttempts) {
                http_response_code(429);
                $this->logEvent('security', 'rate_limited', ['action'=>$action, 'max'=>$maxAttempts, 'window'=>$windowSeconds]);
                exit('Demasiadas solicitudes. Intenta más tarde.');
            }

            $ins = $this->pdo->prepare("INSERT INTO rate_limits(action, ip) VALUES (:a, :ip)");
            $ins->execute([':a' => $action, ':ip' => $ip]);
        } else {
            // Fallback en sesión
            $key = sprintf('rl_%s_%s', $action, bin2hex($ip));
            $bucket = $_SESSION[$key] ?? [];
            $now = time();
            $bucket = array_filter($bucket, fn($ts) => ($now - (int)$ts) < $windowSeconds);
            if (count($bucket) >= $maxAttempts) {
                http_response_code(429);
                exit('Demasiadas solicitudes. Intenta más tarde.');
            }
            $bucket[] = $now;
            $_SESSION[$key] = $bucket;
        }
    }

    // ========== SANITIZACIÓN HTML ==========

    public function sanitizeHTML(string $html): string
    {
        // Preferir HTMLPurifier si está disponible
        if (class_exists(\HTMLPurifier::class)) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', __DIR__.'/cache'); // asegúrate de crear /cache con permisos
            $config->set('URI.AllowedSchemes', ['http'=>true,'https'=>true,'mailto'=>true,'data'=>true]);
            $config->set('HTML.Allowed',
                'p,br,strong,em,ul,ol,li,blockquote,a[href|title|target|rel],img[src|alt|title|width|height],h2,h3,code,pre,table,thead,tbody,tr,th,td'
            );
            $config->set('Attr.AllowedFrameTargets', ['_blank']);
            $purifier = new \HTMLPurifier($config);
            return $purifier->purify($html);
        }

        // Fallback (más restrictivo): permitir solo un subconjunto mínimo
        $allowed = '<p><br><strong><em><ul><ol><li><blockquote><a><img><h2><h3><code><pre>';
        $clean = strip_tags($html, $allowed);

        // Limpia atributos peligrosos básicos en <a> y <img>
        $clean = preg_replace_callback('/<(a|img)\s+[^>]*>/i', function ($m) {
            $tag = strtolower($m[1]);
            if ($tag === 'a') {
                if (preg_match('/href\s*=\s*([\'"])(.*?)\1/i', $m[0], $href)) {
                    $safe = htmlspecialchars($href[2], ENT_QUOTES, 'UTF-8');
                    return '<a href="'.$safe.'" target="_blank" rel="noopener noreferrer">';
                }
                return '<a>';
            }
            if ($tag === 'img') {
                $src = ''; $alt = '';
                if (preg_match('/src\s*=\s*([\'"])(.*?)\1/i', $m[0], $m1)) $src = htmlspecialchars($m1[2], ENT_QUOTES, 'UTF-8');
                if (preg_match('/alt\s*=\s*([\'"])(.*?)\1/i', $m[0], $m2)) $alt = htmlspecialchars($m2[2], ENT_QUOTES, 'UTF-8');
                if ($src) return '<img src="'.$src.'" alt="'.$alt.'" style="max-width:100%;height:auto;">';
                return '';
            }
            return $m[0];
        }, $clean);

        return (string)$clean;
    }

    // ========== UPLOADS ==========

    /**
     * @param array $file Uno de $_FILES[...]
     * @throws RuntimeException
     */
    public function validateUpload(array $file): void
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Parámetros de subida inválidos.');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Error en la subida: '.$file['error']);
        }

        $maxSize = (int)$this->config['uploads']['max_size_bytes'];
        if ((int)$file['size'] > $maxSize) {
            throw new RuntimeException('Archivo demasiado grande (máx '.round($maxSize/1024/1024,2).'MB).');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        $allowed = $this->config['uploads']['allowed_mime'];
        if (!in_array($mime, $allowed, true)) {
            throw new RuntimeException('Tipo no permitido: '.$mime);
        }

        // Validar imagen real y dimensiones
        if (str_starts_with($mime, 'image/')) {
            $info = getimagesize($file['tmp_name']);
            if ($info === false) throw new RuntimeException('Imagen inválida.');
            [$w,$h] = $info;
            if ($w < 1 || $h < 1) throw new RuntimeException('Dimensiones inválidas.');
        }
    }

    /**
     * Extensión según MIME
     */
    public function extensionFromMime(string $mime): string
    {
        return match ($mime) {
            'image/jpeg' => '.jpg',
            'image/png'  => '.png',
            'image/gif'  => '.gif',
            'image/webp' => '.webp',
            default      => '.bin',
        };
    }

    public function secureFilename(string $originalName, string $mime): string
    {
        return hash('sha256', $originalName.microtime(true).random_bytes(16))
            . $this->extensionFromMime($mime);
    }

    // ========== LOGGING ==========

    /**
     * @param 'info'|'warning'|'error'|'security' $level
     * @param array<string,mixed> $details
     */
    public function logEvent(string $level, string $event, array $details = []): void
    {
        $ip = $this->clientIPBinary();
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $uid = $_SESSION['user_id'] ?? null;

        if ($this->pdo) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO app_logs (ip,user_id,level,event,details,user_agent)
                 VALUES (:ip,:uid,:lvl,:ev,:det,:ua)"
            );
            $stmt->execute([
                ':ip' => $ip,
                ':uid'=> $uid,
                ':lvl'=> $level,
                ':ev' => $event,
                ':det'=> json_encode($details, JSON_UNESCAPED_UNICODE),
                ':ua' => $ua,
            ]);
        } else {
            error_log('APP_LOG '.json_encode([
                'ts'=>date('c'),'ip'=>bin2hex($ip),'user_id'=>$uid,'level'=>$level,
                'event'=>$event,'details'=>$details,'ua'=>$ua
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    // ========== HELPERS ==========

    public function cleanInput(mixed $input, string $type = 'string'): string
    {
        if (is_array($input)) $input = implode(',', $input);
        $input = trim((string)$input);
        return match ($type) {
            'email' => filter_var($input, FILTER_SANITIZE_EMAIL) ?: '',
            'int'   => (string)filter_var($input, FILTER_SANITIZE_NUMBER_INT),
            'url'   => filter_var($input, FILTER_SANITIZE_URL) ?: '',
            default => htmlspecialchars($input, ENT_QUOTES, 'UTF-8'),
        };
    }

    public function clientIP(): string
    {
        // No confiar en X-Forwarded-For salvo que estés detrás de proxy conocido.
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    private function clientIPBinary(): string
    {
        return @inet_pton($this->clientIP()) ?: inet_pton('127.0.0.1');
    }
}
