<?php

namespace App\Helpers;

use App\Models\Language;
use App\Models\User;
use Aws\Ses\Exception\SesException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Mailgun\Mailgun;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

trait Helper
{

    public function uploadImage($file, $type, $folder)
{
    try {
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $this->getExt($type))) {
            return [false, 'allow_extention_error'];
        }

        // Upload to Cloudinary
        $uploadedFile = cloudinary()->upload($file->getRealPath(), [
            'folder' => 'uploads/' . $folder,
            'transformation' => [
                'width' => 300,
                'height' => 200,
                'crop' => 'fill'
            ]
        ]);

        // Return the secure URL
        return [true, $uploadedFile->getSecurePath()];

    } catch (\Exception $exception) {
        return [false, 'unable_upload_file'];
    }
}

    public function sendEmail($user)
    {
        try {
            $send = \Mail::send('mail-verification-code', ['user' => $user], function ($m) use ($user) {
            $m->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $m->to($user->email, $user->username)->subject('Your verification code');
            });

            return [true];
        } catch (\Swift_TransportException$ex) {
            return [false, $ex->getMessage()];
        } catch (SesException $ex) {
            return [false, $ex->getMessage()];
        } catch (ClientException $ex) {
            return [false, $ex->getMessage()];
        } catch (ServerException $ex) {
            return [false, $ex->getMessage()];
        }
    }

    public function getExt($type)
    {
        if ($type == 'image') {
            return ['gif', 'jpg', 'jpeg', 'png'];
        } elseif ($type == 'file') {
            return ['txt', 'doc', 'docx', 'xls', 'xlsx', 'pdf'];
        } else {
            return [];
        }
    }

    public function statusCodes($status = false)
    {
        $array = [
            'success' => 200,
            'validation' => 201,
            'not-found-data' => 202,
            'error-update' => 203,
            'error-insert' => 204,
            'exception' => 205,
            'error-upload' => 206,
            'error-old-password' => 207,
            'row-isset' => 208,
            'user_not_verify' => 209,
            'faild_send_email' => 210,
            'error-delete' => 211,
            'user-registerd' => 212,
            'token-invalid' => -1,
            'token-absent' => -2,
            'token-expired' => -3,
            'user-not-found' => -4,
            'JWT_Exception' => -5,
            'Invalid_Claim_Exception' => -6,
            'Payload_Exception' => -7,
            'Blacklisted_token' => -8,
        ];
        if ($status) {
            return $array[$status];
        }
        return $array;
    }

    public function outApiJson($statusCode, $messages, $data = null, $responseStatus = 200)
    {

        $outData = [];
        $outData['code'] = $this->statusCodes($statusCode);
        $outData['messages'] = $messages;
        if ($data) {
            $outData['data'] = $data;
        }
        return response()->json($outData, $responseStatus);
    }

    public function generateCodeNumber()
    {
        $number = mt_rand(1000, 9999); // better than rand()
        if ($this->codeNumberExists($number)) {
            return generateCodeNumber();
        }
        return $number;
    }

    public function codeNumberExists($number)
    {
        return User::where('verification_code', $number)->exists();
    }

    public function systemLanguages()
    {
        $languages = Language::where('status', 'active')->get();
        return $languages;

    }

}
