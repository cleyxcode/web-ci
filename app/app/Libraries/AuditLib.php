<?php

namespace App\Libraries;

use App\Models\AuditTrailModel;

class AuditLib
{
    public static function log(
        string $aksi,
        string $entitas,
        string $deskripsi,
        ?int $entitasId = null,
        ?array $dataLama = null,
        ?array $dataBaru = null
    ): void {
        $user = function_exists('current_user') ? current_user() : null;

        try {
            // Kolom deskripsi di DB varchar(255) — potong aman agar insert tidak gagal.
            if (mb_strlen($deskripsi) > 255) {
                $deskripsi = mb_substr($deskripsi, 0, 252) . '…';
            }

            model(AuditTrailModel::class)->insert([
                'user_id'    => $user['id'] ?? null,
                'user_nama'  => mb_substr((string) ($user['nama'] ?? 'Sistem'), 0, 100),
                'user_role'  => $user['role'] ?? null,
                'aksi'       => mb_substr($aksi, 0, 50),
                'entitas'    => mb_substr($entitas, 0, 50),
                'entitas_id' => $entitasId,
                'deskripsi'  => $deskripsi,
                'data_lama'  => $dataLama ? json_encode($dataLama, JSON_UNESCAPED_UNICODE) : null,
                'data_baru'  => $dataBaru ? json_encode($dataBaru, JSON_UNESCAPED_UNICODE) : null,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Audit trail gagal: ' . $e->getMessage());
        }
    }
}
