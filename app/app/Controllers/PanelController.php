<?php

namespace App\Controllers;

use App\Libraries\PusherLib;
use App\Models\NotifikasiModel;

abstract class PanelController extends BaseController
{
    protected NotifikasiModel $notifikasiModel;
    protected PusherLib $pusher;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        helper(['form', 'url', 'app']);
        $this->notifikasiModel = model(NotifikasiModel::class);
        $this->pusher          = new PusherLib();
    }

    protected function render(string $view, array $data = []): string
    {
        $user = current_user();

        $data['user']         = $user;
        $data['menus']        = panel_menus($user['role'] ?? '');
        $data['notifikasi']      = $this->notifikasiModel->getUnread($user['id'] ?? 0);
        $data['notifikasiAll']   = $this->notifikasiModel->getAllForUser($user['id'] ?? 0, 12);
        $data['unreadCount']     = $this->notifikasiModel->countUnread($user['id'] ?? 0);
        $data['pusherKey']    = $this->pusher->getKey();
        $data['pusherCluster'] = $this->pusher->getCluster();
        $data['pusherEnabled'] = $this->pusher->isEnabled();

        return view('layouts/panel', [
            'content' => view($view, $data),
            ...$data,
        ]);
    }

    protected function notify(int $userId, string $judul, string $pesan, string $type = 'info', ?string $event = 'notifikasi.new', ?array $payload = null): void
    {
        $this->notifikasiModel->createNotif($userId, $judul, $pesan, $type);

        if ($event) {
            $this->pusher->trigger('user-' . $userId, $event, $payload ?? [
                'judul' => $judul,
                'pesan' => $pesan,
                'type'  => $type,
            ]);
        }
    }

    protected function notifyAdmins(string $judul, string $pesan, string $type = 'info'): void
    {
        $admins = model(\App\Models\UserModel::class)->where('role', 'admin')->findAll();

        foreach ($admins as $admin) {
            $this->notify((int) $admin['id'], $judul, $pesan, $type);
        }
    }

    protected function notifyDplOfMahasiswa(?array $mhs, string $judul, string $pesan, string $type = 'info'): void
    {
        $kelompokId = (int) ($mhs['kelompok_id'] ?? 0);

        if ($kelompokId < 1) {
            return;
        }

        $kelompok = model(\App\Models\KelompokKknModel::class)->find($kelompokId);

        if (! $kelompok || empty($kelompok['dpl_id'])) {
            return;
        }

        $dpl = model(\App\Models\DplModel::class)->find((int) $kelompok['dpl_id']);

        if ($dpl && ! empty($dpl['user_id'])) {
            $this->notify((int) $dpl['user_id'], $judul, $pesan, $type);
        }
    }

    /**
     * @param list<array<string, mixed>> $users
     */
    protected function notifyMany(array $users, string $judul, string $pesan, string $type = 'info', string $idKey = 'id'): void
    {
        foreach ($users as $row) {
            $id = (int) ($row[$idKey] ?? 0);

            if ($id > 0) {
                $this->notify($id, $judul, $pesan, $type);
            }
        }
    }
}
