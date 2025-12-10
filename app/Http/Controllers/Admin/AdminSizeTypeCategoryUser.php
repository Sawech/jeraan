<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\SizeTypeCategory;
use App\Models\SizeTypeCategoryUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminSizeTypeCategoryUser extends Controller
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

    public function addSizeTypeCategoryUser(Request $request)
    {
        try {
            $validator = $this->validateAddSizeTypeCategoryUser($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $category = Category::find($request->category_id);
            if (!$category) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }

            $user = User::find($request->user_id);
            if (!$user) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }

            $sizeTypes = $request->size_type_category_user;
            foreach ($sizeTypes as $option) {
                $sizeTypeCategory = SizeTypeCategory::where('category_id',$request->category_id)->where('size_type_id',$option['id'])->first();
                $sizeTypeCategoryUser = SizeTypeCategoryUser::updateOrCreate([
                    'id' => $sizeTypeCategory->id,
                    'user_id' => $request->user_id
                ],[
                    'value' => $option['value']
                ]);
            }
            return $this->outApiJson('success', trans('main.size_type_category_created_successfully'));

        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateAddSizeTypeCategoryUser($request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'category_id' => 'required',
            'size_type_category_user' => 'required',
        ]);
        return $validator;
    }

    public function listUserSizeTypeCategory(Request $request)
    {
        try {
            $validator = $this->validateListUserSizeTypeCategory($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeTypeCategories = SizeTypeCategoryUser::with('sizeTypeCategory')->get();
            if (!$sizeTypeCategories) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $sizeTypeCategories);
        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateListUserSizeTypeCategory($request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);
        return $validator;
    }

}
