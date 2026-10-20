<?php

class CaptchaController extends Controller
{
    public function index()
    {
        $alphabet = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';
        $length = strlen($alphabet) - 2;
        $chars = [];

        for ($i = 0; $i < 5; $i++) {
            $chars[] = $alphabet[rand(0, $length)];
        }

        $code = implode($chars);
        $_SESSION['captcha_code'] = $code;

        $image = imagecreatetruecolor(150, 50);

        $bgColor = imagecolorallocate($image, 22, 86, 165);
        $textColor = imagecolorallocate($image, 223, 230, 233);

        imagefill($image, 0, 0, $bgColor);
        imagestring($image, 5, 40, 17, $code, $textColor);

        header('Content-Type: image/jpeg');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');

        imagejpeg($image);
        imagedestroy($image);
        exit;
    }
}
