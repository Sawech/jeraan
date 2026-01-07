<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SizeTypeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
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

    public function listCateogry()
    {
        try {
            $categories = Category::paginate(10);
            if ($categories->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $categories);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function listAllCateogry()
    {
        try {
            $categories = Category::get();
            if ($categories->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $categories);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function addCategory(Request $request)
    {
        try {error_log('CategoryController before validator');
            $validator = $this->validateAddCategory($request);
            error_log('CategoryController');
            Log::info('CategoryController');

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $category = new Category();
            $category->type = $request->type;
            error_log('CategoryController before upload image');
            Log::info('CategoryController before upload image');
            $upload = $this->uploadImage($request->image, 'image', 'category');
            error_log('CategoryController after upload image');
            Log::info('CategoryController after upload image0' . $upload[0]);
            Log::info('CategoryController after upload image' . $upload[1]);
            if (!$upload[0]) {
                return $this->outApiJson('error-upload', trans('main.faild_upload_image'));
            }
            $category->image = $upload[1];

            foreach ($this->allLanguages as $language) {
                $name = 'name_' . $language->language_universal;
                $category->translateOrNew($language->language_universal)->name = $request->input($name);
            }
            error_log('CategoryController before save');
            Log::info('CategoryController before save');
            $category->save();
            error_log('CategoryController after save');
            Log::info('CategoryController after save');
            if (!$category) {
                return $this->outApiJson('error-insert', trans('main.error_insert'));
            }

            return $this->outApiJson('success', trans('main.category_created_successfully'), $category);

        } catch (\Exception$th) {
            Log::info('CategoryController err' . $th->getMessage());
            error_log($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateAddCategory($request)
    {
        $validate_array = ['type' => 'required|string',
            'image' => 'required|image|mimes:jpeg,jpg,png|min:1|max:2000'];
        foreach ($this->allLanguages as $language) {
            $validate_array['name_' . $language->language_universal] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }

    public function editCategory(Request $request)
    {
        try {
            $validator = $this->validateEditCategory($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $category = Category::find($request->id);
            if (!$category) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }

            if($request->image){
                $upload = $this->uploadImage($request->image, 'image', 'category');
                if (!$upload[0]) {
                    return $this->outApiJson('error-upload', trans('main.faild_upload_image'));
                }
                $category->image = $upload[1];                
            }

            $category->type = $request->type;
            foreach ($this->allLanguages as $language) {
                $name = 'name_' . $language->language_universal;
                $category->translateOrNew($language->language_universal)->name = $request->input($name);
            }
            $category->save();
            if (!$category) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }

            return $this->outApiJson('success', trans('main.category_updated_successfully'), $category);

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateEditCategory($request)
    {
        $validate_array = ['id' => 'required', 'type' => 'required|string','image' => 'image|mimes:jpeg,jpg,png|min:1|max:2000'];
        foreach ($this->allLanguages as $language) {
            $validate_array['name_' . $language->language_universal] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }

    public function fetchCategory(Request $request)
    {
        try {
            $validator = $this->validateFetchCategory($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $category = Category::find($request->id);
            if (!$category) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $category = $category->makeVisible(['translations']);
            return $this->outApiJson('success', trans('main.category_returned_successfully'), $category);

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateFetchCategory($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }

    public function deleteCategory(Request $request)
    {
        try {
            $validator = $this->validateDeleteCategory($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $category = Category::find($request->id);
            if (!$category) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $checkAddedInSize = SizeTypeCategory::where('category_id', $request->id)->count();
            if ($checkAddedInSize > 0) {
                return $this->outApiJson('error-delete', trans('main.category_related_with_sizes'));
            }
            $deleteCategory = Category::where('id', $request->id)->delete();

            if ($deleteCategory) {
                return $this->outApiJson('success', trans('main.category_deleted_successfully'));
            }

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateDeleteCategory($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }

}
