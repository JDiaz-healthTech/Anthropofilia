<?php
// Helpers de settings (reutilizables)
function get_setting(PDO $pdo, string $k, ?string $default = null): ?string {
  $stmt = $pdo->prepare('SELECT v FROM settings WHERE k = ? LIMIT 1');
  $stmt->execute([$k]);
  $val = $stmt->fetchColumn();
  return ($val === false) ? $default : (string)$val;
}

function set_setting(PDO $pdo, string $k, string $v): void {
  $stmt = $pdo->prepare('INSERT INTO settings (k, v) VALUES (?, ?)
                         ON DUPLICATE KEY UPDATE v = VALUES(v)');
  $stmt->execute([$k, $v]);
}

function is_hex_color(string $c): bool {
  return (bool)preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i', $c);
}

