<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\OtpLib;
use App\Models\UserModel;

class ForgotPasswordController extends BaseController
{
    protected UserModel $userModel;
    protected OtpLib $otpLib;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
        $this->otpLib    = new OtpLib();
        helper(['form', 'url', 'app']);
    }

    public function index()
    {
        return view('auth/forgot_password', ['title' => 'Lupa Password']);
    }

    public function send()
    {
        $rules = [
            'email' => 'required|valid_email|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user  = $this->userModel->where('email', $email)->where('is_active', 1)->first();

        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'Email tidak terdaftar.');
        }

        $code = $this->otpLib->generate($user['id'], $user['email']);
        $sent = $this->otpLib->sendResetEmail($user['email'], $user['nama'], $code);

        session()->set('otp_user_id', $user['id']);

        if (! $sent) {
            session()->setFlashdata('warning', 'OTP dibuat tetapi email gagal dikirim. Mode dev — OTP: ' . $code);
        }

        return redirect()->to('/otp-verify')->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }
}
