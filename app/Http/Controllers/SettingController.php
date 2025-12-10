<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Message;
use App\Models\Setting;
use App\Models\SiteInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    use Helper;

    public function __construct()
    {
        if (app('request')->header('lang')) {
            app()->setLocale(app('request')->header('lang'));
        }
    }

    public function siteInfo()
    {
        try {
            /*\Mail::send('mail-verification-code',[], function ($m) {
                $m->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $m->to('eng.mohammedhefny@gmail.com', 'Mohammed')->subject('Your verification code');
            });
            dd(env('MAIL_USERNAME'));*/
            $siteInfo = SiteInfo::get();
            if ($siteInfo->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $siteInfo);
        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function settings()
    {
        try {
            $settings = Setting::get();
            if ($settings->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $settings);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $validator = $this->validateSendMessage($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $message = Message::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'message' => $request->message,
            ]);

            if (!$message) {
                return $this->outApiJson('error-insert', trans('main.faild_insert'));
            }
            return $this->outApiJson('success', trans('main.success'));

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateSendMessage($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'message' => 'required',
        ]);

        return $validator;
    }

}
