<?php
namespace App\Models;

use PDO;

class User {
    
    /**
     * Buscar usuario por nombre de usuario
     * Se usa para el Login.
     */
    public static function findByUsername(string $username) {
        $db = Database::getConnection();
        
        // Seleccionamos explícitamente las columnas necesarias
        $stmt = $db->prepare(
            "SELECT id_usuario, nombre_usuario, email, contrasena_hash, 
                    rol, fecha_registro
             FROM usuarios 
             WHERE nombre_usuario = :user 
             LIMIT 1"
        );
        $stmt->execute([':user' => $username]);
        
        return $stmt->fetch();
    }

    /**
     * Verificar contraseña
     * Compara la contraseña en texto plano con el hash de la BD
     */
    public static function verifyPassword(string $inputPassword, string $storedHash): bool {
        return password_verify($inputPassword, $storedHash);
    }
    
    /**
     * Actualizar último login (Opcional, pero recomendado)
     */
    public static function updateLastLogin(int $userId): void {
        $db = Database::getConnection();
        // Asumiendo que tienes una columna para esto, si no, puedes omitirlo
        // En tu SQL no vi columna 'ultimo_login', así que dejaremos esto comentado
        // $stmt = $db->prepare("UPDATE usuarios SET ultimo_access = NOW() WHERE id_usuario = ?");
        // $stmt->execute([$userId]);
    }
}