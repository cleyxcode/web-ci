#!/usr/bin/env bash
set -Eeuo pipefail

project_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
cd "$project_dir"

# Ambil perubahan terbaru dari branch utama sebelum setup dijalankan.
git pull origin main

setup_script="$project_dir/setup-wsl.sh"

if [[ ! -x "$setup_script" ]]; then
    chmod +x "$setup_script"
fi

bash "$setup_script"

# Jalankan seluruh migration CodeIgniter setelah service dan database siap.
# Ini membuat database lokal selalu mengikuti migration terbaru yang ada di repo,
# termasuk tabel evaluasi_kriteria dan cakupan kriterianya.
if docker compose version >/dev/null 2>&1; then
    compose_command=(docker compose)
elif command -v docker-compose >/dev/null 2>&1; then
    compose_command=(docker-compose)
else
    printf '\nERROR: Docker Compose tidak tersedia untuk menjalankan migration.\n' >&2
    exit 1
fi

printf '\n==> Menjalankan migration database terbaru\n'
"${compose_command[@]}" exec -T app php spark migrate --all

printf '\nURL yang dapat digunakan:\n'
printf '  Domain      : https://kkntematikukim.site\n'
printf '  Lokal       : http://localhost:8083\n'
printf '  phpMyAdmin  : http://localhost:8081\n'
printf '  Database    : mysql://kkn_user:kkn_pass@localhost:3307/kkn_tematik\n'
printf '  DB internal : mysql://kkn_user:kkn_pass@db:3306/kkn_tematik\n'

wsl_ip_list="$(hostname -I 2>/dev/null || true)"
for ip_address in $wsl_ip_list; do
    case "$ip_address" in
        127.*|169.254.*)
            continue
            ;;
    esac

    printf '  WSL/client  : http://%s:8083\n' "$ip_address"
    printf '  DB client   : mysql://kkn_user:kkn_pass@%s:3307/kkn_tematik\n' "$ip_address"
done

if command -v powershell.exe >/dev/null 2>&1; then
    windows_ip_list="$(powershell.exe -NoProfile -Command \
        '[Console]::Write((Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.IPAddress -notlike "127.*" -and $_.IPAddress -notlike "169.254.*" -and $_.InterfaceAlias -notmatch "WSL|vEthernet|Docker|Loopback" } | Select-Object -ExpandProperty IPAddress) -join " ")' \
        2>/dev/null | tr -d '\r')"

    for ip_address in $windows_ip_list; do
        printf '  LAN client  : http://%s:8083\n' "$ip_address"
        printf '  DB LAN      : mysql://kkn_user:kkn_pass@%s:3307/kkn_tematik\n' "$ip_address"
    done
fi

printf '\nLogin awal aplikasi: admin / admin123\n'
printf 'Jika URL LAN tidak bisa dibuka, izinkan port 8083 pada Windows Firewall.\n'
