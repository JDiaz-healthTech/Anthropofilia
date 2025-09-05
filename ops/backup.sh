#!/usr/bin/env bash
set -euo pipefail

# ==== CONFIG ====
DB_HOST="127.0.0.1"
DB_USER="usuario"
DB_PASS="password"
DB_NAME="blogdb"

# Carpeta donde guardas tus "imágenes de salvamento" (snapshots)
BACKUP_DIR="/var/backups/blogdb"
RETENTION_MAX_FILES=100
MIN_DAYS_BETWEEN_SNAPSHOTS=10

# ==== PREP ====
mkdir -p "$BACKUP_DIR"
STAMP="$(date +'%Y%m%d-%H%M%S')"
OUT="$BACKUP_DIR/${DB_NAME}_${STAMP}.sql.gz"
SUM="$OUT.sha256"
LAST_FILE_META="$BACKUP_DIR/.last_snapshot"

# ==== ¿Debemos crear snapshot hoy? ====
need_snapshot=true
if [[ -f "$LAST_FILE_META" ]]; then
  last_ts="$(cat "$LAST_FILE_META" || true)"
  if [[ -n "$last_ts" ]]; then
    # segundos transcurridos / 86400
    now="$(date +%s)"
    (( (now - last_ts) / 86400 >= MIN_DAYS_BETWEEN_SNAPSHOTS )) || need_snapshot=false
  fi
fi

if [[ "$need_snapshot" != true ]]; then
  echo "No han pasado ${MIN_DAYS_BETWEEN_SNAPSHOTS} días desde el último snapshot. Saliendo."
  exit 0
fi

# ==== DUMP consistente (InnoDB) ====
mysqldump \
  --user="$DB_USER" --password="$DB_PASS" --host="$DB_HOST" \
  --single-transaction --routines --triggers --events --hex-blob \
  --default-character-set=utf8mb4\
  "$DB_NAME" | gzip -9 > "$OUT"

# Test de integridad del gzip
gunzip -t "$OUT"
# Checksum
sha256sum "$OUT" > "$SUM"

# Actualiza marca temporal del último snapshot
date +%s > "$LAST_FILE_META"

echo "Backup OK: $OUT"

# ==== ROTACIÓN por número (máximo 100) ====
# Ordena por fecha (más antiguo primero) y elimina lo que sobrepase RETENTION_MAX_FILES
mapfile -t files < <(ls -1t "$BACKUP_DIR"/${DB_NAME}_*.sql.gz 2>/dev/null || true)
if (( ${#files[@]} > RETENTION_MAX_FILES )); then
  to_delete=( "${files[@]:RETENTION_MAX_FILES}" )
  for f in "${to_delete[@]}"; do
    echo "Eliminando antiguo: $f"
    rm -f "$f" "$f.sha256" || true
  done
fi
