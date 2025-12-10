<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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

    public function listUser(Request $request)
    {
        try {
            if ($request->type == "user") {
                $users = User::where('role_id', 1)->paginate(10);
            } else {
                $users = User::where('role_id', '!=', 1)->paginate(10);
            }
            if ($users->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $users);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function listAllUser(Request $request)
    {
        try {
            $users = User::where('role_id', 1)->get();
            if ($users->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $users);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function listAdminRole(Request $request)
    {
        try {
            $roles = Role::where('type', '!=', 'user')->get();
            if ($roles->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $roles);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function addUser(Request $request)
    {
        try {
            $validator = $this->validateAddUser($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'role_id' => $request->role_id,
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

    public function validateAddUser($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'role_id' => 'required',
            'email' => 'required|string|email|unique:users',
            'mobile' => 'required|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        return $validator;
    }

    public function editUser(Request $request)
    {
        try {
            $validator = $this->validateEditUser($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $user = User::find($request->user_id);
            if (!$user) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }

            if ($request->password != null) {
                $password = bcrypt($request->password);
            } else {
                $password = $user->password;
            }

            $user = User::where('id', $request->user_id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'role_id' => $request->role_id,
                'password' => $password,
            ]);

            if (!$user) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }
            $newUser = User::find($request->user_id);
            if (!$newUser) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }
            return $this->outApiJson('success', trans('main.success'), $newUser);
        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateEditUser($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'role_id' => 'required',
            'email' => 'required|string|email|unique:users,email,' . $request->user_id,
            'mobile' => 'required|unique:users,mobile,' . $request->user_id,
            'user_id' => 'required',
        ]);

        return $validator;
    }

    public function fetchUser(Request $request)
    {
        try {
            $validator = $this->validateFetchUser($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $user = User::find($request->id);
            if (!$user) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $user);

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateFetchUser($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }

    public function changeStatusUser(Request $request)
    {
        try {
            $validator = $this->validateChangeStatusUser($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $user = User::find($request->user_id);
            if (!$user || (Auth::id() == $request->user_id)) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }

            if ($user->status == "active") {
                $staus = "inactive";
            } else {
                $staus = "active";
            }

            $user = User::where('id', $request->user_id)->update([
                'status' => $staus,
            ]);

            if (!$user) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }
            $newUser = User::find($request->user_id);
            if (!$newUser) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }
            return $this->outApiJson('success', trans('main.success'), $newUser);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateChangeStatusUser($request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        return $validator;
    }

    public function deleteUser(Request $request)
    {
        try {
            $validator = $this->validateDeleteUser($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $user = User::find($request->id);
            if (!$user || (Auth::id() == $request->id)) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $checkOrders = Order::where('user_id', $request->id)->count();
            if ($checkOrders > 0) {
                return $this->outApiJson('error-delete', trans('main.user_related_with_orders'));
            }
            $deleteUser = User::where('id', $request->id)->delete();

            if ($deleteUser) {
                return $this->outApiJson('success', trans('main.user_deleted_successfully'));
            }

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateDeleteUser($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }

}
