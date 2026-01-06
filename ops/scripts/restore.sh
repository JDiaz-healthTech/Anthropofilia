#!/usr/bin/env bash
set -euo pipefail

# Uso: ./restore.sh /var/backups/blogdb/blogdb_YYYYmmdd-HHMMSS.sql.gz

DB_HOST="127.0.0.1"
DB_USER="usuario"
DB_PASS="password"
PROD_DB="blogdb"

INPUT="${1:-}"
if [[ -z "$INPUT" || ! -f "$INPUT" ]]; then
  echo "Uso: $0 /ruta/al/backup.sql.gz"
  exit 1
fi

TMP_DB="blogdb_restore_$(date +'%Y%m%d%H%M%S')"

mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE \`$TMP_DB\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo "Importando a DB temporal: $TMP_DB ..."
gunzip -c "$INPUT" | mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$TMP_DB"
echo "Ensayo OK en $TMP_DB."

echo "¿Restaurar SOBRE PRODUCCIÓN ($PROD_DB)? Escribe: SI"
read -r CONFIRM
if [[ "$CONFIRM" == "SI" ]]; then
  echo "Haciendo backup previo por seguridad..."
  "$(dirname "$0")/backup.sh" || true
  echo "Recreando $PROD_DB y restaurando..."
mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "DROP DATABASE \`$PROD_DB\`; CREATE DATABASE \`$PROD_DB\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  gunzip -c "$INPUT" | mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$PROD_DB"
  echo "Restauración completada."
else
  echo "Restauración cancelada. Puedes inspeccionar $TMP_DB."
fi
