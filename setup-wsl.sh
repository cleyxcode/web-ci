#!/usr/bin/env bash
set -Eeuo pipefail

project_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
cd "$project_dir"

log() {
    printf '\n==> %s\n' "$1"
}

fail() {
    printf '\nERROR: %s\n' "$1" >&2
    exit 1
}

command -v docker >/dev/null 2>&1 || fail "Docker belum terpasang di WSL. Install Docker Desktop dan aktifkan WSL Integration."
docker info >/dev/null 2>&1 || fail "Docker Desktop belum berjalan atau WSL Integration belum aktif."

if docker compose version >/dev/null 2>&1; then
    compose_command=(docker compose)
elif command -v docker-compose >/dev/null 2>&1; then
    compose_command=(docker-compose)
else
    fail "Docker Compose tidak tersedia. Gunakan Docker Desktop versi terbaru."
fi

required_files=(
    Dockerfile
    docker-compose.yml
    app/composer.json
    app/composer.lock
    sql/init.sql
    sql/alter_evaluasi.sql
    sql/alter_remove_knn.sql
    sql/alter_2026_08.sql
)

for required_file in "${required_files[@]}"; do
    [[ -f "$required_file" ]] || fail "File tidak ditemukan: $required_file"
done

log "Membangun image dan menyalakan semua service"
"${compose_command[@]}" up -d --build

log "Menunggu MySQL siap"
database_ready=false
for attempt in $(seq 1 60); do
    if "${compose_command[@]}" exec -T db sh -c \
        'mysqladmin ping -h localhost -u root -p"$MYSQL_ROOT_PASSWORD" --silent' \
        >/dev/null 2>&1; then
        database_ready=true
        break
    fi

    printf 'Menunggu MySQL (%s/60)\r' "$attempt"
    sleep 2
done
printf '\n'

if [[ "$database_ready" != true ]]; then
    "${compose_command[@]}" logs --tail=80 db >&2 || true
    fail "MySQL tidak siap setelah 120 detik."
fi

table_count="$(${compose_command[@]} exec -T db sh -c \
    'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" -Nse "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()"' \
    | tr -d '[:space:]')"

if [[ "$table_count" == "0" ]]; then
    log "Database kosong, mengimpor schema dan data SQL"
    sql_files=(
        /docker-entrypoint-initdb.d/01-init.sql
        /docker-entrypoint-initdb.d/02-alter-evaluasi.sql
        /docker-entrypoint-initdb.d/03-alter-remove-knn.sql
        /docker-entrypoint-initdb.d/04-alter-2026-08.sql
    )

    for sql_file in "${sql_files[@]}"; do
        "${compose_command[@]}" exec -T db sh -c \
            "mysql -uroot -p\"\$MYSQL_ROOT_PASSWORD\" \"\$MYSQL_DATABASE\" < '$sql_file'"
    done
fi

table_count="$(${compose_command[@]} exec -T db sh -c \
    'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" -Nse "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()"' \
    | tr -d '[:space:]')"

[[ "$table_count" -ge 13 ]] || fail "Database belum lengkap. Hanya ditemukan $table_count tabel."

if ! "${compose_command[@]}" exec -T app sh -c \
    'curl -fsS http://localhost/login >/dev/null'; then
    fail "Aplikasi tidak merespons. Periksa: docker compose logs app"
fi

log "Setup selesai"
printf '%s\n' \
    "Aplikasi   : http://localhost:8083" \
    "phpMyAdmin : http://localhost:8081" \
    "DB host    : db" \
    "DB name    : kkn_tematik" \
    "DB user    : kkn_user" \
    "DB port    : 3307 (dari WSL/host)" \
    "Login awal : admin / admin123"
