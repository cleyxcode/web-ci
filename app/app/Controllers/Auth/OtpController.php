<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class OtpController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
        helper(['form', 'url', 'app']);
    }

    public function verifyForm()
    {
        if (! session()->get('otp_user_id')) {
            return redirect()->to('/forgot-password');
        }

        return view('auth/otp_verify', ['title' => 'Verifikasi OTP']);
    }

    public function verify()
    {
        $userId = session()->get('otp_user_id');

        if (! $userId) {
            return redirect()->to('/forgot-password');
        }

        $otp = implode('', $this->request->getPost('otp') ?? []);

        if (strlen($otp) !== 6) {
            return redirect()->back()->with('error', 'Masukkan 6 digit OTP.');
        }

        $otpModel = model(\App\Models\OtpModel::class);
        $record   = $otpModel->where('user_id', $userId)
            ->where('otp_code', $otp)
            ->where('is_used', 0)
            ->where('expired_at >=', date('Y-m-d H:i:s'))
            ->first();

        if (! $record) {
            return redirect()->back()->with('error', 'OTP tidak valid atau sudah expired.');
        }

        session()->set('otp_verified', true);
        session()->set('otp_code_id', $record['id']);

        return redirect()->to('/reset-password');
    }

    public function resetForm()
    {
        if (! session()->get('otp_verified')) {
            return redirect()->to('/forgot-password');
        }

        return view('auth/reset_password', ['title' => 'Password Baru']);
    }

    public function reset()
    {
        if (! session()->get('otp_verified')) {
            return redirect()->to('/forgot-password');
        }

        $rules = [
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $userId   = session()->get('otp_user_id');
        $password = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);

        $this->userModel->update($userId, ['password' => $password]);

        $otpModel = model(\App\Models\OtpModel::class);
        $otpModel->update(session()->get('otp_code_id'), ['is_used' => 1]);

        session()->remove(['otp_user_id', 'otp_verified', 'otp_code_id']);

        return redirect()->to('/login')->with('success', 'Password berhasil diubah. Silakan login.');
    }
}
