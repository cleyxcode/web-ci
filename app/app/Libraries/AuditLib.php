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
            model(AuditTrailModel::class)->insert([
                'user_id'    => $user['id'] ?? null,
                'user_nama'  => $user['nama'] ?? 'Sistem',
                'user_role'  => $user['role'] ?? null,
                'aksi'       => $aksi,
                'entitas'    => $entitas,
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
