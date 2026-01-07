<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\SizeType;
use App\Models\SizeTypeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SizeTypeController extends Controller
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

    public function listSizeType()
    {
        try {
            $sizeTypes = SizeType::withOut('sizeTypeUser')->paginate(10);
            if ($sizeTypes->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $sizeTypes);
        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function listAllSizeType()
    {
        try {
            $sizeTypes = SizeType::withOut('sizeTypeUser')->get();
            if ($sizeTypes->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $sizeTypes);
        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function addSizeType(Request $request)
    {
        try {
            error_log('SizeTypeController after save');
            $validator = $this->validateAddSizeType($request);
            error_log('SizeTypeController after validator');

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeType = new SizeType();
            foreach ($this->allLanguages as $language) {
                $name = 'name_' . $language->language_universal;
                $sizeType->translateOrNew($language->language_universal)->name = $request->input($name);
            }
            error_log('SizeTypeController after foreach');
            $sizeType->save();
            error_log('SizeTypeController after sizeType->save');
            if (!$sizeType) {
                return $this->outApiJson('error-insert', trans('main.error_insert'));
            }

            return $this->outApiJson('success', trans('main.size_type_created_successfully'), $sizeType);

        } catch (\Exception$th) {
            error_log($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateAddSizeType($request)
    {
        $validate_array = [];
        foreach ($this->allLanguages as $language) {
            $validate_array['name_' . $language->language_universal] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }

    public function editSizeType(Request $request)
    {
        try {
            $validator = $this->validateEditSizeType($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeType = SizeType::find($request->id);
            if (!$sizeType) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            foreach ($this->allLanguages as $language) {
                $name = 'name_' . $language->language_universal;
                $sizeType->translateOrNew($language->language_universal)->name = $request->input($name);
            }
            $sizeType->save();
            if (!$sizeType) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }
            $sizeType = SizeType::withOut('sizeTypeUser')->find($sizeType->id);
            return $this->outApiJson('success', trans('main.size_type_updated_successfully'), $sizeType);

        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateEditSizeType($request)
    {
        $validate_array = ['id' => 'required'];
        foreach ($this->allLanguages as $language) {
            $validate_array['name_' . $language->language_universal] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }

    public function fetchSizeType(Request $request)
    {
        try {
            $validator = $this->validateFetchSizeType($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeType = SizeType::withOut('sizeTypeUser')->find($request->id);

            if (!$sizeType) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $sizeType = $sizeType->makeVisible(['translations']);
            return $this->outApiJson('success', trans('main.size_type_returned_successfully'), $sizeType);

        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateFetchSizeType($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }

    public function deleteSizeType(Request $request)
    {
        try {
            $validator = $this->validateDeleteSizeType($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeType = SizeType::find($request->id);
            if (!$sizeType) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $checkAddedInSize = SizeTypeCategory::where('size_type_id', $request->id)->count();
            if ($checkAddedInSize > 0) {
                return $this->outApiJson('error-delete', trans('main.size_type_related_with_sizes'));
            }
            $deleteCategory = SizeType::where('id', $request->id)->delete();

            if ($deleteCategory) {
                return $this->outApiJson('success', trans('main.size_type_deleted_successfully'));
            }

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateDeleteSizeType($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }

}
