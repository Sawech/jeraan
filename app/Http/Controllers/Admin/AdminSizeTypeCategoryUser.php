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
use Illuminate\Support\Facades\Log;  // <-- Add this import

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
        Log::info('addSizeTypeCategoryUser called', [
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'size_type_category_user' => $request->size_type_category_user,
            'full_request' => $request->all()
        ]);

        try {
            $validator = $this->validateAddSizeTypeCategoryUser($request);

            if ($validator->fails()) {
                Log::warning('Validation failed in addSizeTypeCategoryUser', $validator->errors()->toArray());
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $category = Category::find($request->category_id);
            if (!$category) {
                Log::warning('Category not found', ['category_id' => $request->category_id]);
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            Log::info('Category found', ['category_id' => $category->id]);

            $user = User::find($request->user_id);
            if (!$user) {
                Log::warning('User not found', ['user_id' => $request->user_id]);
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            Log::info('User found', ['user_id' => $user->id]);

            $sizeTypes = $request->size_type_category_user;
            Log::info('Processing size types', ['count' => count($sizeTypes), 'data' => $sizeTypes]);

            foreach ($sizeTypes as $option) {
                $sizeTypeCategory = SizeTypeCategory::where('category_id', $request->category_id)
                    ->where('size_type_id', $option['id'])
                    ->first();

                if (!$sizeTypeCategory) {
                    Log::warning('SizeTypeCategory not found for option', $option);
                    continue; // or handle error as needed
                }

                $sizeTypeCategoryUser = SizeTypeCategoryUser::updateOrCreate([
                    'size_type_category_id' => $sizeTypeCategory->id,  // <-- Fixed: was incorrectly using 'id'
                    'user_id' => $request->user_id
                ], [
                    'value' => $option['value']
                ]);

                Log::info('SizeTypeCategoryUser updated/created', [
                    'size_type_category_id' => $sizeTypeCategory->id,
                    'user_id' => $request->user_id,
                    'value' => $option['value']
                ]);
            }

            Log::info('addSizeTypeCategoryUser completed successfully');
            return $this->outApiJson('success', trans('main.size_type_category_created_successfully'));

        } catch (\Exception $th) {
            Log::error('Exception in addSizeTypeCategoryUser', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            // Remove dd() in production!
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
        Log::info('listUserSizeTypeCategory called', $request->all());

        try {
            $validator = $this->validateListUserSizeTypeCategory($request);

            if ($validator->fails()) {
                Log::warning('Validation failed in listUserSizeTypeCategory', $validator->errors()->toArray());
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeTypeCategories = SizeTypeCategoryUser::with('sizeTypeCategory')
                ->where('category_id', $request->category_id)  // <-- Added missing category filter based on validation
                ->get();

            if ($sizeTypeCategories->isEmpty()) {
                Log::info('No SizeTypeCategoryUser records found for category', ['category_id' => $request->category_id]);
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }

            Log::info('listUserSizeTypeCategory returned records', ['count' => $sizeTypeCategories->count()]);
            return $this->outApiJson('success', trans('main.success'), $sizeTypeCategories);

        } catch (\Exception $th) {
            Log::error('Exception in listUserSizeTypeCategory', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
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