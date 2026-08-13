<?php

namespace App\Controllers;

class NotifikasiController extends PanelController
{
    public function index()
    {
        $user = current_user();

        return $this->render('notifikasi/index', [
            'title'      => 'Notifikasi',
            'items'      => $this->notifikasiModel->getAllForUser($user['id'], 80),
            'unreadCount'=> $this->notifikasiModel->countUnread($user['id']),
        ]);
    }

    public function markRead(int $id)
    {
        $user = current_user();
        $this->notifikasiModel->markAsRead($id, $user['id']);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'          => true,
                'unreadCount' => $this->notifikasiModel->countUnread($user['id']),
            ]);
        }

        return redirect()->back();
    }

    public function markAllRead()
    {
        $user = current_user();
        $this->notifikasiModel->markAllRead($user['id']);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true, 'unreadCount' => 0]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }

    public function apiList()
    {
        $user  = current_user();
        $items = $this->notifikasiModel->getAllForUser($user['id'], 15);

        return $this->response->setJSON([
            'items'       => array_map(static fn ($n) => [
                'id'         => (int) $n['id'],
                'judul'      => $n['judul'],
                'pesan'      => $n['pesan'],
                'type'       => $n['type'],
                'is_read'    => (bool) $n['is_read'],
                'created_at' => format_tanggal($n['created_at'] ?? null),
            ], $items),
            'unreadCount' => $this->notifikasiModel->countUnread($user['id']),
        ]);
    }
}
