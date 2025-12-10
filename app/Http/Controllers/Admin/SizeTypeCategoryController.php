<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SizeTypeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SizeTypeCategoryController extends Controller
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

    public function listSizeTypeCategory()
    {
        try {
            $sizeTypeCategories = Category::with('sizeTypes')->paginate(10);
            if ($sizeTypeCategories->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $sizeTypeCategories);
        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function listAllSizeTypeCategory(Request $request)
    {
        try {
            $validator = $this->validateListSizeTypeCategory($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeTypeCategories = Category::with('sizeTypes')->find($request->category_id);
            if (!$sizeTypeCategories) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $sizeTypeCategories);
        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateListSizeTypeCategory($request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);
        return $validator;
    }

    public function addSizeTypeCategory(Request $request)
    {
        try {
            $validator = $this->validateAddSizeTypeCategory($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $category = Category::find($request->category_id);
            if (!$category) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $deleteSizeTypeCategory = SizeTypeCategory::where('category_id', $request->category_id)->delete();

            $sizeTypes = $request->size_type_id;

            foreach ($sizeTypes as $type) {
                $sizeTypeCategory = new SizeTypeCategory();
                $sizeTypeCategory->category_id = $request->category_id;
                $sizeTypeCategory->size_type_id = $type;
                $sizeTypeCategory->save();
            }
            if (!$sizeTypeCategory) {
                return $this->outApiJson('error-insert', trans('main.error_insert'));
            }

            $sizeTypeCategory = Category::with('sizeTypes')->find($request->category_id);
            return $this->outApiJson('success', trans('main.size_type_category_created_successfully'), $sizeTypeCategory);

        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateAddSizeTypeCategory($request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'size_type_id' => 'required',
        ]);
        return $validator;
    }

    public function fetchSizeTypeCategory(Request $request)
    {
        try {
            $validator = $this->validateFetchSizeTypeCategory($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeTypeCategories = SizeTypeCategory::where('category_id', $request->category_id)->pluck('size_type_id')->toArray();
            if (!$sizeTypeCategories) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.size_type_category_returned_successfully'), $sizeTypeCategories);

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateFetchSizeTypeCategory($request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);
        return $validator;
    }

    public function deleteSizeTypeCategory(Request $request)
    {
        try {
            $validator = $this->validateDeleteSizeTypeCategory($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $deleteSizeTypeCategory = SizeTypeCategory::where('category_id', $request->category_id)->delete();

            if ($deleteSizeTypeCategory) {
                return $this->outApiJson('success', trans('main.size_type_deleted_successfully'));
            }
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateDeleteSizeTypeCategory($request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);
        return $validator;
    }

}
