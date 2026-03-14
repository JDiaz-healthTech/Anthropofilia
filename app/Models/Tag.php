  <?php
  // app/Models/Tag.php
  declare(strict_types=1);

  namespace App\Models;

  use PDO;

  class Tag
  {
      /**
       * Contar total de etiquetas
       */
      public static function countAll(): int
      {
          $db = Database::getConnection();
          return (int) $db->query("SELECT COUNT(*) FROM etiquetas")->fetchColumn();
      }

      /**
       * Obtener todas las etiquetas ordenadas alfabéticamente
       */
      public static function getAll(): array
      {
          $db = Database::getConnection();
          $stmt = $db->query(
              "SELECT id_etiqueta, nombre_etiqueta FROM etiquetas ORDER BY nombre_etiqueta ASC"
          );
          return $stmt->fetchAll(PDO::FETCH_ASSOC);
      }

      /**
       * Buscar etiqueta por ID
       */
      public static function findById(int $id): array|false
      {
          $db   = Database::getConnection();
          $stmt = $db->prepare("SELECT id_etiqueta, nombre_etiqueta FROM etiquetas WHERE id_etiqueta =
  ?");
          $stmt->execute([$id]);
          return $stmt->fetch(PDO::FETCH_ASSOC);
      }

      /**
       * Buscar etiqueta por nombre exacto (case-insensitive por collation utf8mb4_unicode_ci)
       */
      public static function findByName(string $nombre): array|false
      {
          $db   = Database::getConnection();
          $stmt = $db->prepare("SELECT id_etiqueta, nombre_etiqueta FROM etiquetas WHERE
  nombre_etiqueta = ?");
          $stmt->execute([$nombre]);
          return $stmt->fetch(PDO::FETCH_ASSOC);
      }

      /**
       * Crear etiqueta nueva. Devuelve el ID insertado.
       */
      public static function create(string $nombre): int
      {
          $db   = Database::getConnection();
          $stmt = $db->prepare("INSERT INTO etiquetas (nombre_etiqueta) VALUES (?)");
          $stmt->execute([mb_strtolower(trim($nombre), 'UTF-8')]);
          return (int) $db->lastInsertId();
      }

      /**
       * Insertar etiqueta si no existe; devuelve su ID en cualquier caso.
       * Patrón: INSERT ... ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)
       */
      public static function upsert(string $nombre): int
      {
          $db   = Database::getConnection();
          $stmt = $db->prepare(
              "INSERT INTO etiquetas (nombre_etiqueta)
               VALUES (?)
               ON DUPLICATE KEY UPDATE id_etiqueta = LAST_INSERT_ID(id_etiqueta)"
          );
          $stmt->execute([mb_strtolower(trim($nombre), 'UTF-8')]);
          return (int) $db->lastInsertId();
      }

      /**
       * Eliminar etiqueta por ID (la FK CASCADE limpia post_etiquetas automáticamente)
       */
      public static function delete(int $id): bool
      {
          $db   = Database::getConnection();
          $stmt = $db->prepare("DELETE FROM etiquetas WHERE id_etiqueta = ?");
          return $stmt->execute([$id]);
      }

      /**
       * Obtener etiquetas de un post concreto (array de nombres)
       */
      public static function getByPost(int $id_post): array
      {
          $db   = Database::getConnection();
          $stmt = $db->prepare(
              "SELECT e.nombre_etiqueta
               FROM post_etiquetas pe
               INNER JOIN etiquetas e ON e.id_etiqueta = pe.id_etiqueta
               WHERE pe.id_post = ?
               ORDER BY e.nombre_etiqueta ASC"
          );
          $stmt->execute([$id_post]);
          return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
      }

      /**
       * Sincronizar las etiquetas de un post: elimina las actuales e inserta las nuevas.
       * Acepta array de nombres (strings). Normaliza a minúsculas y elimina duplicados.
       * Resuelve C-03 (actualizar_post.php no actualizaba etiquetas).
       *
       * Se llama dentro de una transacción activa del script padre.
       */
      public static function syncForPost(int $id_post, array $nombres): void
      {
          $db = Database::getConnection();

          // Normalizar
          $tags = array_values(array_unique(array_filter(
              array_map(fn(string $t) => trim(mb_strtolower($t, 'UTF-8')), $nombres),
              fn(string $t) => $t !== ''
          )));

          // Limpiar vínculos actuales
          $db->prepare("DELETE FROM post_etiquetas WHERE id_post = ?")->execute([$id_post]);

          if ($tags === []) {
              return;
          }

          $stmtLink = $db->prepare(
              "INSERT IGNORE INTO post_etiquetas (id_post, id_etiqueta) VALUES (?, ?)"
          );

          foreach ($tags as $nombre) {
              $id_tag = self::upsert($nombre);
              $stmtLink->execute([$id_post, $id_tag]);
          }
      }

      /**
       * Etiquetas más usadas, con conteo de posts.
       * Devuelve array de ['nombre_etiqueta' => string, 'total' => int]
       */
      public static function getMostUsed(int $limit = 10): array
      {
          $db   = Database::getConnection();
          $stmt = $db->prepare(
              "SELECT e.nombre_etiqueta, COUNT(pe.id_post) AS total
               FROM etiquetas e
               INNER JOIN post_etiquetas pe ON pe.id_etiqueta = e.id_etiqueta
               GROUP BY e.id_etiqueta, e.nombre_etiqueta
               ORDER BY total DESC, e.nombre_etiqueta ASC
               LIMIT ?"
          );
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
          return $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
  }
