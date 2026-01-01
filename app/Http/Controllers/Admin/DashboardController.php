<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Order;
use App\Models\Setting;
use App\Models\SiteInfo;
use App\Models\User;
use App\Models\Category;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use Helper;

    private $allLanguages;

    public function __construct()
    {
        if (app('request')->header('lang')) {
            app()->setLocale(app('request')->header('lang'));
        }

        $this->allLanguages = $this->systemLanguages();
    }

    public function dashboardList()
    {
        try {
        error_log('========== Dashboard Begin ==========');
            $data['allLanguages'] = $this->systemLanguages();
        error_log('========== allLanguages Done ==========');
            $data['newOrdersCount'] = Order::where('status', 'new')->count();
        error_log('========== newOrdersCount Done ==========');
            $data['deliveredOrdersCount'] = Order::where('status', 'delivered')->count();
            $data['waitingOrdersCount'] = Order::where('status', 'waiting_payment')->count();
        error_log('========== waitingOrdersCount Done ==========');
            $data['usersCount'] = User::where('role_id', 1)->count();
        error_log('========== usersCount Done ==========');
            $data['siteInfo'] = SiteInfo::get();
        error_log('========== siteInfo Done ==========');
            return $this->outApiJson('success', trans('main.success'), $data);

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function fetchSiteSettings()
    {
        try {
            $siteSettings = Setting::get();
            $siteSettings = $siteSettings->makeVisible(['translations']);
            return $this->outApiJson('success', trans('main.success'), $siteSettings);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function editSiteInfo(Request $request)
    {
        try {

            $validator = $this->validateEditSiteInfo($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $updateSiteInfo = SiteInfo::where('attribute','email')->update([
                'value' => $request->email,
            ]);
            $updateSiteInfo = SiteInfo::where('attribute','phone')->update([
                'value' => $request->phone,
            ]);
            $updateSiteInfo = SiteInfo::where('attribute','whatsapp')->update([
                'value' => $request->whatsapp,
            ]);
            if (!$updateSiteInfo) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }
            $siteInfo = SiteInfo::get();
            return $this->outApiJson('success', trans('main.success'), $siteInfo);

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateEditSiteInfo($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'phone' => 'required',
            'whatsapp' => 'required',
        ]);
        return $validator;
    }

    public function messages()
    {
        try {
            $messages = Message::orderBy('id', 'DESC')->paginate(10);
            if ($messages->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $messages);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function fetchMessage(Request $request)
    {
        try {

            $validator = $this->validateFetchMessage($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $message = Message::find($request->id);
            if (!$message) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $message);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateFetchMessage($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
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
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
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
            'name' => 'required',
            'email' => 'required|string|email|unique:users,email,' . Auth::id(),
            'mobile' => 'required|unique:users,mobile,' . Auth::id(),
            'password' => 'confirmed',
        ]);

        return $validator;
    }

    public function editSetting(Request $request)
    {
        
        try {
            $validator = $this->validateEditSetting($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            foreach ($this->allLanguages as $language) {
                $aboutUs = 'about_us_' . $language->language_universal;
                $terms = 'terms_conditions_' . $language->language_universal;
                $about_us = Setting::where('attribute','about_us')->first();
                $term = Setting::where('attribute','terms_conditions')->first();
                $about_us->translateOrNew($language->language_universal)->value = $request->input($aboutUs);
                $term->translateOrNew($language->language_universal)->value = $request->input($terms);
                $about_us->save();
                $term->save();
            }
            return $this->outApiJson('success', trans('main.settings_updated_successfully'));

        } catch (\Exception $th) {
            \DB::rollback();
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateEditSetting($request)
    {
        foreach ($this->allLanguages as $language) {
            $validate_array['about_us_' . $language->language_universal] = 'required';
            $validate_array['terms_conditions_' . $language->language_universal] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }

    public function monthlyOrders()
    {
        try {
            $orders = Order::with(['category', 'fabric', 'design', 'user'])->whereMonth('delivery_date', Carbon::now()->format('m'))->get();
            if ($orders->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $orders);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function dailyOrders()
    {
        try {
            $orders = Order::with(['category', 'fabric', 'design', 'user'])->where('delivery_date', Carbon::today())->get();
            if ($orders->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $orders);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function weeklyOrders()
    {
        try {
            $orders = Order::with(['category', 'fabric', 'design', 'user'])->where('delivery_date', Carbon::today())->get();
            if ($orders->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $orders);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }
}