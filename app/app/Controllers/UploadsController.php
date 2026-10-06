<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Serve files from writable/uploads (with legacy public/uploads fallback).
 *
 * Catatan routing CI4: path bersarang (logbook/a.jpg) diteruskan sebagai
 * beberapa argumen method — harus digabung lagi dengan implode.
 */
class UploadsController extends BaseController
{
    public function serve(string ...$segments): ResponseInterface
    {
        helper('app');

        if (! session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login untuk melihat file.');
        }

        $path = rawurldecode(implode('/', $segments));
        $filePath = resolve_uploaded_file($path);
        if ($filePath === null) {
            throw PageNotFoundException::forPageNotFound('File upload tidak ditemukan.');
        }

        $mime = mime_content_type($filePath) ?: 'application/octet-stream';
        $size = filesize($filePath);
        $body = file_get_contents($filePath);

        if ($body === false) {
            throw PageNotFoundException::forPageNotFound('File upload tidak dapat dibaca.');
        }

        $safeName = str_replace(['"', "\r", "\n"], '', basename($filePath));

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Length', (string) ($size === false ? strlen($body) : $size))
            ->setHeader('Content-Disposition', 'inline; filename="' . $safeName . '"')
            ->setHeader('Cache-Control', 'private, max-age=3600')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody($body);
    }
}
