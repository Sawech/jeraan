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
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'user_id' => 'nullable|exists:users,id', // Add this
        ]);

        if ($validator->fails()) {
            return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
        }

        // Use the user_id from request if provided (for admin viewing client's order)
        // Otherwise use authenticated user's ID (for client viewing their own order)
        $userId = $request->user_id ?? auth()->id();
        
        
        $category = Category::with(['sizeTypes' => function($query) {
            $query->without('sizeTypeUser');
        }])->find($request->category_id);

        if (!$category) {
            return $this->outApiJson('not-found-data', trans('main.not_found_data'));
        }

        // Get size values for the specific user (client)
        foreach ($category->sizeTypes as $sizeType) {
            $pivotRecord = \DB::table('size_types_categories')
                ->where('category_id', $category->id)
                ->where('size_type_id', $sizeType->id)
                ->first();
            
            
            if ($pivotRecord) {
                $userValues = \DB::table('size_types_categories_users')
                    ->where('size_type_category_id', $pivotRecord->id)
                    ->where('user_id', $userId) // Use the correct user ID
                    ->get();
                
                
                $sizeType->size_type_user = $userValues;
            } else {
                $sizeType->size_type_user = [];
            }
        }

        return $this->outApiJson('success', trans('main.success'), $category);
    } catch (\Exception $th) {
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

            $deleteSizeTypeCategory = SizeTypeCategory::where('category_id', $request->id)->delete();

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
            'id' => 'required',
        ]);
        return $validator;
    }

}
