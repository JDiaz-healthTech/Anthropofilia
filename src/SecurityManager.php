<?php
/**
 * security_manager.php
 * Gestor de seguridad para Anthropofilia
 *
 * Requisitos:
 * - PHP 8.0+
 * - PDO configurado (MySQL/MariaDB)
 * - Composer autoload (opcional: HTMLPurifier)
 */

declare(strict_types=1);

namespace App;

use PDO;                // ✅ evitar App\PDO
use PDOException;
use RuntimeException;

final class SecurityManager
{
    
    private ?PDO $pdo = null;

    /** @var array<string,mixed> */
    private array $config = [
        
        'env' => 'prod',

        // Sesión
        'session_name'        => 'anth_session',
        'session_idle_timeout'=> 1800, // 30 min
        'session_rotate_every'=> 600,  // 10 min
        'trust_proxy'         => false, // si estás detrás de proxy, ponlo a true
        // 'cookie_domain'     => null, // puedes fijarlo si lo necesitas

        // Rate limit
        'rate_limit' => [
            'enabled' => true,
            'general' => ['max' => 120, 'window' => 3600], // 120 req/h
            'post'    => ['max' => 30,  'window' => 3600], // 30 POST/h
        ],

        // CSP
        'csp' => [
            'tinymce_cdn'       => 'https://cdn.tiny.cloud',
            'extra_script_src'  => ['https://cdn.jsdelivr.net'],
            'allow_unsafe_inline' => false, // si lo pones a false, se usará nonce
        ],

        // Uploads
        'uploads' => [
            'max_size_bytes' => 2 * 1024 * 1024, // 2MB
            'allowed_mime'   => ['image/jpeg','image/png','image/gif','image/webp'],
            'max_pixels'     => 1600,
        ],
    ];

    private ?string $cspNonce = null;

    public function __construct(array $config = [], ?PDO $pdo = null)
    {
        $this->config = array_replace_recursive($this->config, $config);
        $this->pdo    = $pdo;
    }

    /** Llamar una vez por request */
    public function boot(): void
    {
        $this->startSecureSession();
        $this->applySecurityHeaders();
        $this->enforceRateLimits();
    }

    /* ======================
       Autenticación / Roles
       ====================== */

    public function requireLogin(): void
    {
        $this->startSecureSession();
        if (empty($_SESSION['id_usuario'])) {
            header('Location: login.php');
            exit();
        }
    }

    public function userId(): ?int
    {
        $this->startSecureSession();
        return isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;
    }

    public function roles(): array
    {
        $this->startSecureSession();
        if (isset($_SESSION['roles']) && is_array($_SESSION['roles'])) return $_SESSION['roles'];
        if (isset($_SESSION['rol'])) return [$_SESSION['rol']];
        return [];
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles(), true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /** Permite dueño o roles dados; si no, 403 */
    public function requireOwnershipOrRole(?int $ownerId, array $allowedRoles = ['admin']): void
    {
        $uid = $this->userId();
        if ($ownerId !== null && $uid === $ownerId) return;
        foreach ($allowedRoles as $r) if ($this->hasRole($r)) return;
        $this->abort(403, 'Acceso denegado.');
    }

    /* ==============
       Sesión segura
       ============== */

    private function startSecureSession(): void
    {
        if (session_status() !== PHP_SESSION_NONE) return;

        $secure = $this->isHttps();
        $params = [
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ];
        if (!empty($this->config['cookie_domain'])) {
            $params['domain'] = (string)$this->config['cookie_domain'];
        }

        session_name((string)$this->config['session_name']);
        session_set_cookie_params($params);
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
            $_SESSION['__created_at'] = time();
        }
        $_SESSION['__last_seen'] = time();
    }

    private function destroySession(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'] ?? '',
                (bool)$params['secure'], (bool)$params['httponly']
            );
        }
        session_destroy();
    }

    private function isHttps(): bool
    {
        if (!empty($this->config['trust_proxy'])) {
            $proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
            if (strtolower($proto) === 'https') return true;
        }
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (string)$_SERVER['SERVER_PORT'] === '443');
    }

    /* =========
       Cabeceras / CSP
       ========= */

    public function cspNonce(): string
    {
        if ($this->cspNonce === null) {
            $this->cspNonce = base64_encode(random_bytes(16));
        }
        return $this->cspNonce;
    }

    public function applySecurityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('X-Frame-Options: SAMEORIGIN');

        if (($this->config['env'] ?? 'prod') === 'prod' && $this->isHttps()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        $scriptSrc  = ["'self'"];
        $styleSrc   = ["'self'"];
        $imgSrc     = ["'self'", "data:", "blob:", "https:"];
        $fontSrc    = ["'self'", "data:", "https:"];
        $connectSrc = ["'self'"];
        $frameAnc   = ["'self'"];

        if (!empty($this->config['csp']['tinymce_cdn'])) {
            $cdn = (string)$this->config['csp']['tinymce_cdn'];
            $scriptSrc[]  = $cdn;
            $connectSrc[] = $cdn;
        }

        if (!empty($this->config['csp']['extra_script_src'])) {
            foreach ($this->config['csp']['extra_script_src'] as $u) {
                $scriptSrc[] = (string)$u;
            }
        }

        if (!empty($this->config['csp']['allow_unsafe_inline'])) {
            $scriptSrc[] = "'unsafe-inline'";
            $styleSrc[]  = "'unsafe-inline'";
        } else {
            // si desactivas inline, añade nonce
            $scriptSrc[] = "'nonce-".$this->cspNonce()."'";
        }

        $csp = sprintf(
            "default-src 'self'; script-src %s; style-src %s; img-src %s; font-src %s; connect-src %s; frame-ancestors %s;",
            implode(' ', array_unique($scriptSrc)),
            implode(' ', array_unique($styleSrc)),
            implode(' ', array_unique($imgSrc)),
            implode(' ', array_unique($fontSrc)),
            implode(' ', array_unique($connectSrc)),
            implode(' ', array_unique($frameAnc))
        );
        header("Content-Security-Policy: $csp");
    }

    /* =========
       CSRF
       ========= */

    public function csrfToken(): string
    {
        $this->startSecureSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_time']  = time();
        }
        return $_SESSION['csrf_token'];
    }

    /** Valida y rota token; TTL por defecto 2h */
    public function csrfValidate(?string $token, int $ttlSeconds = 7200): void
    {
        $this->startSecureSession();
        $stored = $_SESSION['csrf_token'] ?? null;
        $t      = (int)($_SESSION['csrf_time'] ?? 0);

        $ok = $token && $stored && hash_equals((string)$stored, (string)$token)
            && ($ttlSeconds <= 0 || (time() - $t) <= $ttlSeconds);

        if (!$ok) {
            $this->logEvent('security', 'csrf_invalid', ['uri' => $_SERVER['REQUEST_URI'] ?? '']);
            $this->abort(419, 'CSRF inválido o caducado.');
        }

        unset($_SESSION['csrf_token'], $_SESSION['csrf_time']); // rotación
    }

    /** Helper para vistas */
    public function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' .
               htmlspecialchars($this->csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /* =========
       Rate limiting
       ========= */

    private function enforceRateLimits(): void
    {
        if (empty($this->config['rate_limit']['enabled'])) return;

        $this->checkRateLimit(
            'general',
            (int)$this->config['rate_limit']['general']['max'],
            (int)$this->config['rate_limit']['general']['window']
        );

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->checkRateLimit(
                'post_form',
                (int)$this->config['rate_limit']['post']['max'],
                (int)$this->config['rate_limit']['post']['window']
            );
        }
    }

public function checkRateLimit(string $action, int $maxAttempts, int $windowSeconds): void
{
    $ip = $this->clientIP();

    if ($this->pdo) {
        // Limpieza: fuera de la ventana
        $threshold = date('Y-m-d H:i:s', time() - $windowSeconds);
        $del = $this->pdo->prepare("DELETE FROM rate_limits WHERE ts < :threshold");
        $del->execute([':threshold' => $threshold]);

        // Bucket al minuto (ajusta 60-> tu ventana si quieres)
        $bucketSql = "FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(NOW())/60)*60)";

        // Inserta/acumula
        $sql = "INSERT INTO rate_limits (action, ip, bucket_start, hits)
                VALUES (:a, :ip, $bucketSql, 1)
                ON DUPLICATE KEY UPDATE hits = hits + 1, ts = NOW()";
        $ins = $this->pdo->prepare($sql);
        $ins->execute([':a' => $action, ':ip' => $ip]);

        // Cuenta dentro de la ventana (suma de hits)
        $sel = $this->pdo->prepare(
            "SELECT COALESCE(SUM(hits),0)
             FROM rate_limits
             WHERE action = :a AND ip = :ip AND ts >= :threshold"
        );
        $sel->execute([':a' => $action, ':ip' => $ip, ':threshold' => $threshold]);
        $count = (int)$sel->fetchColumn();

        if ($count >= $maxAttempts) {
            http_response_code(429);
            exit('Demasiadas solicitudes. Intenta más tarde.');
        }
        return;
    }

    // Fallback en sesión (tu bloque actual está bien)
    $key    = sprintf('rl_%s_%s', $action, bin2hex($ip));
    $bucket = $_SESSION[$key] ?? [];
    $now    = time();
    $bucket = array_filter($bucket, fn($ts) => ($now - (int)$ts) < $windowSeconds);
    if (count($bucket) >= $maxAttempts) {
        http_response_code(429);
        exit('Demasiadas solicitudes. Intenta más tarde.');
    }
    $bucket[]       = $now;
    $_SESSION[$key] = $bucket;
}

    /* =========
       Sanitización HTML
       ========= */

    public function sanitizeHTML(string $html): string
    {
        if (class_exists(\HTMLPurifier::class)) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', __DIR__ . '/cache');
            $config->set('URI.AllowedSchemes', ['http'=>true,'https'=>true,'mailto'=>true,'data'=>true]);
            $config->set('HTML.Allowed',
                'p,br,strong,em,ul,ol,li,blockquote,a[href|title|target|rel],img[src|alt|title|width|height],h2,h3,code,pre,table,thead,tbody,tr,th,td'
            );
            $config->set('Attr.AllowedFrameTargets', ['_blank']);
            $purifier = new \HTMLPurifier($config);
            return $purifier->purify($html);
        }

        // Fallback sencillo
        $allowed = '<p><br><strong><em><ul><ol><li><blockquote><a><img><h2><h3><code><pre>';
        $clean = strip_tags($html, $allowed);

        $clean = (string)preg_replace_callback('/<(a|img)\s+[^>]*>/i', function ($m) {
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

        return $clean;
    }

    /* =========
       Uploads
       ========= */

    /** @param array $file uno de $_FILES[...] */
    public function validateUpload(array $file): void
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Parámetros de subida inválidos.');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Error en la subida: ' . $file['error']);
        }

        $maxSize = (int)$this->config['uploads']['max_size_bytes'];
        if ((int)$file['size'] > $maxSize) {
            throw new RuntimeException('Archivo demasiado grande (máx ' . round($maxSize/1024/1024,2) . 'MB).');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        $allowed = $this->config['uploads']['allowed_mime'];
        if (!in_array($mime, $allowed, true)) {
            throw new RuntimeException('Tipo no permitido: ' . $mime);
        }

        if (str_starts_with((string)$mime, 'image/')) {
            $info = getimagesize($file['tmp_name']);
            if ($info === false) throw new RuntimeException('Imagen inválida.');
            [$w,$h] = $info;
            if ($w < 1 || $h < 1) throw new RuntimeException('Dimensiones inválidas.');
        }
    }

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
        return hash('sha256', $originalName . microtime(true) . random_bytes(16))
            . $this->extensionFromMime($mime);
        }

    /* =========
       Logging y helpers
       ========= */

    /** @param 'info'|'warning'|'error'|'security' $level */
    public function logEvent(string $level, string $event, array $details = []): void
    {
        $ip = $this->clientIP();
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $uid = $this->userId();

        if ($this->pdo) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO app_logs (ip,user_id,level,event,details,user_agent)
                 VALUES (:ip,:uid,:lvl,:ev,:det,:ua)"
            );
            $stmt->execute([
                ':ip'  => $ip,
                ':uid' => $uid,
                ':lvl' => $level,
                ':ev'  => $event,
                ':det' => json_encode($details, JSON_UNESCAPED_UNICODE),
                ':ua'  => $ua,
            ]);
        } else {
            error_log('APP_LOG ' . json_encode([
                'ts'=>date('c'),'ip'=>bin2hex($ip),'user_id'=>$uid,'level'=>$level,
                'event'=>$event,'details'=>$details,'ua'=>$ua
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    public function abort(int $statusCode, string $message = 'Error'): void
    {
        http_response_code($statusCode);
        echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        exit();
    }

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
        if (!empty($this->config['trust_proxy'])) {
            $xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
            if ($xff) {
                $parts = array_map('trim', explode(',', $xff));
                if ($parts[0] !== '') return $parts[0];
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }


}
