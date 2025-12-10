<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\SizeGown;
use App\Models\SizeGownOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SizeGownController extends Controller
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

    public function listSizeGown()
    {
        try {
            $sizeGowns = SizeGown::with(['images', 'sizeGownOptions'])->paginate(10);
            if ($sizeGowns->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $sizeGowns);
        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function fetchSizeGown(Request $request)
    {
        try {
            $validator = $this->validateFetchGown($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeGown = SizeGown::with(['images', 'sizeGownOptions'])->find($request->id);
            if (!$sizeGown) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $sizeGown = $sizeGown->makeVisible(['translations']);
            return $this->outApiJson('success', trans('main.success'), $sizeGown);

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateFetchGown($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }

    public function addSizeGown(Request $request)
    {
        try {
            $validator = $this->validateAddSizeGown($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeGown = new SizeGown();

            \DB::beginTransaction();
            foreach ($this->allLanguages as $language) {
                $name = 'name_' . $language->language_universal;
                $sizeGown->translateOrNew($language->language_universal)->name = $request->input($name);
            }
            $sizeGown->save();
            if (!$sizeGown) {
                \DB::rollback();
                return $this->outApiJson('error-insert', trans('main.error_insert'));
            }

            if ($request->image) {
                foreach ($request->image as $image) {
                    //dd($image);
                    $upload = $this->uploadImage($image, 'image', 'sizeGown');

                    if (!$upload[0]) {
                        \DB::rollback();
                        return $this->outApiJson('error-upload', trans('main.faild_upload_image'));
                    }

                    $sizeGown->images()->createMany([
                        ['image' => $upload[1]],
                    ]);
                }
            }

            if ($request->sizeGownOptions) {

                $sizeGownOptions = $request->sizeGownOptions;
                foreach ($sizeGownOptions as $option) {
                    $sizeGownOption = new SizeGownOption();
                    $sizeGownOption->size_gown_id = $sizeGown->id;
                    if ($option['image']) {

                        $upload = $this->uploadImage($option['image'], 'image', 'sizeGown');
                        if (!$upload[0]) {
                            \DB::rollback();
                            return $this->outApiJson('error-upload', trans('main.faild_upload_image'));
                        }

                        $sizeGownOption->image = $upload[1];
                    }


                    foreach ($this->allLanguages as $language) {
                        $name = 'name_' . $language->language_universal;
                        $sizeGownOption->translateOrNew($language->language_universal)->name = $option[$name];
                    }

                    $sizeGownOption->save();
                }
            }
            \DB::commit();
            $sizeGown = SizeGown::with(['images', 'sizeGownOptions'])->find($sizeGown->id);
            return $this->outApiJson('success', trans('main.success'), $sizeGown);

        } catch (\Exception$th) {
            dd($th->getMessage());
            \DB::rollback();
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateAddSizeGown($request)
    {
        $validate_array = [];
        $validate_array = ['type' => 'required',
            'sizeGownOptions' => 'required'];
        foreach ($this->allLanguages as $language) {
            $validate_array['name_' . $language->language_universal] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }


    public function editSizeGown(Request $request)
    {
        try {
            $validator = $this->validateEditSizeGown($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeGown = SizeGown::find($request->id);

            \DB::beginTransaction();
            foreach ($this->allLanguages as $language) {
                $name = 'name_' . $language->language_universal;
                $sizeGown->translateOrNew($language->language_universal)->name = $request->input($name);
            }
            $sizeGown->save();
            if (!$sizeGown) {
                \DB::rollback();
                return $this->outApiJson('error-insert', trans('main.error_insert'));
            }

            if ($request->image) {
                $sizeGown->images()->delete();
                foreach ($request->image as $image) {
                    //dd($image);
                    $upload = $this->uploadImage($image, 'image', 'sizeGown');

                    if (!$upload[0]) {
                        \DB::rollback();
                        return $this->outApiJson('error-upload', trans('main.faild_upload_image'));
                    }

                    $sizeGown->images()->createMany([
                        ['image' => $upload[1]],
                    ]);
                }
            }

            if ($request->sizeGownOptions) {
                $sizeGown->sizeGownOptions()->delete();
                $sizeGownOptions = json_decode($request->sizeGownOptions);
                foreach ($sizeGownOptions as $option) {
                    $sizeGownOption = new SizeGownOption();
                    $sizeGownOption->size_gown_id = $sizeGown->id;
                    if ($option->image) {
                        $upload = $this->uploadImage($image, 'image', 'sizeGown');

                        if (!$upload[0]) {
                            \DB::rollback();
                            return $this->outApiJson('error-upload', trans('main.faild_upload_image'));
                        }

                        $sizeGownOption->image = $upload[1];
                    }

                    foreach ($this->allLanguages as $language) {
                        $name = 'name_' . $language->language_universal;
                        $sizeGownOption->translateOrNew($language->language_universal)->name = $option->$name;
                    }
                    $sizeGownOption->save();
                }
            }
            \DB::commit();
            $sizeGown = SizeGown::with(['images', 'sizeGownOptions'])->find($sizeGown->id);
            return $this->outApiJson('success', trans('main.success'), $sizeGown);

        } catch (\Exception$th) {
            dd($th->getMessage());
            \DB::rollback();
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateEditSizeGown($request)
    {
        $validate_array = [];
        $validate_array = ['type' => 'required',
            'sizeGownOptions' => 'required',
        'id' => 'required'];
        foreach ($this->allLanguages as $language) {
            $validate_array['name_' . $language->language_universal] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }


    public function deleteSizeGown(Request $request)
    {
        try {
            $validator = $this->validateDeleteSizeGown($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $sizeGown = SizeGown::with(['sizeGownOptions'])->find($request->id);
            if (!$sizeGown) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $getSizeGownOptions = SizeGownOption::where('size_gown_id',$request->id)->get();

            $deleteSizeGown = SizeGown::with(['sizeGownOptions','images'])->where('id',$request->id)->delete();
            SizeGownOption::where('size_gown_id',$request->id)->delete();

            if($deleteSizeGown){
                return $this->outApiJson('success', trans('main.size_gown_deleted_successfully'));
            }
            
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateDeleteSizeGown($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }

}
