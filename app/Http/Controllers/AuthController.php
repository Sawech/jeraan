<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\UserMobile;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    use Helper;

    public function __construct()
    {
        if (app('request')->header('lang')) {
            app()->setLocale(app('request')->header('lang'));
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = $this->validateRegister($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            /*$checkMobile = UserMobile::where('mobile', $request->mobile)->first();
            if (!$checkMobile) {
                return $this->outApiJson('error-insert', trans('main.mobile_not_registerd'));
            }*/

            $checkUser = User::where('mobile', $request->mobile)->where('email_verified_at',NULL)->first();
            
            if($checkUser){
                return $this->outApiJson('user-registerd', trans('main.mobile_registerd'),$checkUser);
            }
            
            $checkUser2 = User::where('mobile', $request->mobile)->first();
            
            if($checkUser2){
              return $this->outApiJson('error-insert', trans('main.error_insert'));
            }
            
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'role_id' => 1,
                'password' => bcrypt($request->password),
                //'email_verified_at' => Carbon::now()->toDateTimeString()
            ]);
            if (!$user) {
                return $this->outApiJson('error-insert', trans('main.error_insert'));
            }
            return $this->outApiJson('success', trans('main.user_created_successfully'), $user);

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateRegister($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|email|unique:users',
            'mobile' => 'required',
            'password' => 'required|string|confirmed|min:6',
        ]);

        return $validator;
    }

    public function login(Request $request)
{
    try {
        error_log('========== LOGIN ATTEMPT ==========');
        error_log('Mobile/Email: ' . $request->input('mobile_or_email'));
        error_log('Request Data: ' . json_encode($request->all()));
        
        $validator = $this->validateLogin($request);
        if ($validator->fails()) {
            error_log('Validation Failed: ' . json_encode($validator->errors()));
            return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
        }

        error_log('Validation passed, looking up user...');

        error_log('Step 2: Validation passed');
        
        // Check if user exists
        $user = User::where('email', $request->input('mobile_or_email'))->first();
        error_log('Step 3: User lookup', ['found' => $user ? 'yes' : 'no', 'email' => $request->input('mobile_or_email')]);
        
        if (!$user) {
            $user = User::where('mobile', $request->input('mobile_or_email'))->first();
            error_log('Step 4: Mobile lookup', ['found' => $user ? 'yes' : 'no']);
        }
        
        if (!$user) {
            return $this->outApiJson('user-not-found', trans('main.user_not_found'));
        }
        error_log('Step 5: About to attempt auth');
        
        // Try authentication WITHOUT status check
        $token = auth('api')->attempt([
            'email' => $request->input('mobile_or_email'), 
            'password' => $request->input('password')
        ]);
        
        error_log('Step 6: After email attempt', ['token' => $token ? 'success' : 'failed']);
        
        if (!$token) {
            $token = auth('api')->attempt([
                'mobile' => $request->input('mobile_or_email'), 
                'password' => $request->input('password')
            ]);
            error_log('Step 7: After mobile attempt', ['token' => $token ? 'success' : 'failed']);
        }

        if (!$token) {
            return $this->outApiJson('user-not-found', trans('main.user_not_found'));
        }

        error_log('Step 8: Login successful');
        return $this->createNewToken($token);

    } catch (\Exception $th) {
        error_log('Exception', ['message' => $th->getMessage()]);
        return $this->outApiJson('exception', trans('main.exception'));
    }
}

    public function validateLogin($request)
    {

        $validator = Validator::make($request->all(), [
            'mobile_or_email' => 'required',
            'password' => 'required|string',
        ]);

        return $validator;
    }

    public function createNewToken($token)
    {
        \Log::info('createNewToken', [
            'token' => $token
        ]);

        $user = [
            'access_token' => $token,
            //'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ];

        return $this->outApiJson('success', trans('main.success'), $user);
    }

    public function profile()
    {
        try {
            return $this->outApiJson('success', trans('main.success'), auth()->user());
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return $this->outApiJson('success', trans('main.logout'));
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function editProfile(Request $request)
    {
        try {
            $validator = $this->validateEditProfile($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $password = Auth::user()->password;
            if ($request->password != null) {
                $password = bcrypt($request->password);
            } else {
                $password = $password;
            }

            $user = User::where('id', Auth::id())->update([
                'email' => $request->email,
                'password' => $password,
            ]);

            if (!$user) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }
            $newUser = User::find(Auth::id());
            if (!$newUser) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }
            return $this->outApiJson('success', trans('main.success'), $newUser);

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateEditProfile($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'confirmed',
        ]);

        return $validator;
    }

    public function sendVerificationCode(Request $request)
    {
        try {
            $validator = $this->validateSendVerificationCode($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $checkUser = User::where('email', $request->email)->first();

            if (!$checkUser) {
                return $this->outApiJson('user-not-found', trans('main.user_not_found'));
            }

            $generateCodeNumber = $this->generateCodeNumber();

            $updateUserCode = User::where('email', $request->email)->update(['verification_code' => $generateCodeNumber]);
            if (!$updateUserCode) {
                return $this->outApiJson('error-update', trans('main.faild_update_code'));
            }
            //dd(env('MAIL_FROM_ADDRESS'));
            $checkUser = User::where('email', $request->email)->first();
            $this->sendEmail($checkUser);
            return $this->outApiJson('success', trans('main.code_sent_successfully'));
        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.faild_send_email'));
        }
    }

    public function validateSendVerificationCode($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);
        return $validator;
    }

    public function checkVerificationCode(Request $request)
    {
        try {
            $validator = $this->validateCheckVerificationCode($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $checkUser = User::where('email', $request->email)->where('verification_code', $request->verification_code)->first();

            if (!$checkUser) {
                return $this->outApiJson('user-not-found', trans('main.error_verification_code'));
            }
            $verifyEmail = User::where('email', $request->email)->update(['email_verified_at' => Carbon::now()->toDateTimeString()]);
            if (!$verifyEmail) {
                return $this->outApiJson('error-update', trans('main.faild_update_verify_email'));
            }
            return $this->outApiJson('success', trans('main.success_code'));
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateCheckVerificationCode($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'verification_code' => 'required',
        ]);
        return $validator;
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = $this->validateChangePassword($request);
            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $user = User::where('email', $request->email)->where('verification_code', $request->verification_code)->update([
                'password' => bcrypt($request->password),
            ]);
            if (!$user) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }
            return $this->outApiJson('success', trans('main.password_changed_successfully'));

        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateChangePassword($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'verification_code' => 'required',
            'password' => 'required|string|confirmed|min:6',
        ]);

        return $validator;
    }
}
