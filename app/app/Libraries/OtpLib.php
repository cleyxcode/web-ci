<?php

namespace App\Libraries;

use App\Models\OtpModel;
use App\Models\UserModel;

class OtpLib
{
    protected OtpModel $otpModel;

    protected UserModel $userModel;

    public function __construct()
    {
        $this->otpModel  = model(OtpModel::class);
        $this->userModel = model(UserModel::class);
    }

    public function generate(int $userId, string $email, string $type = 'reset_password'): string
    {
        $code = (string) random_int(100000, 999999);

        $this->otpModel->where('user_id', $userId)
            ->where('type', $type)
            ->where('is_used', 0)
            ->set(['is_used' => 1])
            ->update();

        $this->otpModel->insert([
            'user_id'    => $userId,
            'email'      => $email,
            'otp_code'   => $code,
            'type'       => $type,
            'expired_at' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
        ]);

        return $code;
    }

    public function verify(int $userId, string $code, string $type = 'reset_password'): bool
    {
        $otp = $this->otpModel->where('user_id', $userId)
            ->where('otp_code', $code)
            ->where('type', $type)
            ->where('is_used', 0)
            ->where('expired_at >=', date('Y-m-d H:i:s'))
            ->first();

        if (! $otp) {
            return false;
        }

        $this->otpModel->update($otp['id'], ['is_used' => 1]);

        return true;
    }

    public function sendResetEmail(string $email, string $nama, string $code): bool
    {
        $emailService = \Config\Services::email();

        $message = view('emails/otp', [
            'nama'     => $nama,
            'otp_code' => $code,
        ]);

        return $emailService->setTo($email)
            ->setSubject('Kode OTP - Sistem KKN UKIM')
            ->setMessage($message)
            ->send();
    }
}
