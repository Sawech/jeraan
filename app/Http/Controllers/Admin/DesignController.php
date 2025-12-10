<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Design;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesignController extends Controller
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

    public function listDesign()
    {
        try {
            $designs = Design::with('images')->paginate(10);
            if ($designs->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $designs);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function addDesign(Request $request)
    {
        try {
            $validator = $this->validateAddDesign($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $design = new Design();

            \DB::beginTransaction();
            foreach ($this->allLanguages as $language) {
                $name = 'name_' . $language->language_universal;
                $description = 'description_' . $language->language_universal;
                $design->translateOrNew($language->language_universal)->name = $request->input($name);
                $design->translateOrNew($language->language_universal)->description = $request->input($description);
            }
            $design->save();
            if (!$design) {
                \DB::rollback();
                return $this->outApiJson('error-insert', trans('main.error_insert'));
            }

            if ($request->image) {
                foreach ($request->image as $image) {
                    $upload = $this->uploadImage($image, 'image', 'design');

                    if (!$upload[0]) {
                        \DB::rollback();
                        return $this->outApiJson('error-upload', trans('main.faild_upload_image'));
                    }

                    $design->images()->createMany([
                        ['image' => $upload[1]]
                    ]);
                }
            }
            \DB::commit();
            $design = Design::with('images')->find($design->id);
            return $this->outApiJson('success', trans('main.design_created_successfully'), $design);

        } catch (\Exception$th) {
            \DB::rollback();
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateAddDesign($request)
    {
        $validate_array = [];
        foreach ($this->allLanguages as $language) {
            $validate_array['name_' . $language->language_universal] = 'required';
            $validate_array['description_' . $language->language_universal] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }

    public function editDesign(Request $request)
    {
        try {
            $validator = $this->validateEditDesign($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $design = Design::find($request->id);
            if (!$design) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            \DB::beginTransaction();
            foreach ($this->allLanguages as $language) {
                $name = 'name_' . $language->language_universal;
                $description = 'description_' . $language->language_universal;
                $design->translateOrNew($language->language_universal)->name = $request->input($name);
                $design->translateOrNew($language->language_universal)->description = $request->input($description);
            }
            $design->save();
            if (!$design) {
                \DB::rollback();
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }

            if ($request->image) {
                foreach ($request->image as $image) {
                    $upload = $this->uploadImage($image, 'image', 'design');

                    if (!$upload[0]) {
                        \DB::rollback();
                        return $this->outApiJson('error-upload', trans('main.faild_upload_image'));
                    }

                    $design->images()->createMany([
                        ['image' => $upload[1]]
                    ]);
                }
            }
            \DB::commit();
            $design = Design::with('images')->find($design->id);
            return $this->outApiJson('success', trans('main.design_updated_successfully'), $design);

        } catch (\Exception $th) {
            \DB::rollback();
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateEditDesign($request)
    {
        $validate_array = ['id' => 'required'];
        foreach ($this->allLanguages as $language) {
            $validate_array['name_' . $language->language_universal] = 'required';
            $validate_array['description_' . $language->language_universal] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }

    public function fetchDesign(Request $request)
    {
        try {
            $validator = $this->validateFetchDesign($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $design = Design::with('images')->find($request->id);
            if (!$design) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $design = $design->makeVisible(['translations']);
            return $this->outApiJson('success', trans('main.design_returned_successfully'), $design);

        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateFetchDesign($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }

    public function deleteDesign(Request $request)
    {
        try {
            $validator = $this->validateDeleteDesign($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $design = Design::find($request->id);
            if (!$design) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $checkOrders = Order::where('design_id', $request->id)->count();
            if ($checkOrders > 0) {
                return $this->outApiJson('error-delete', trans('main.design_related_with_orders'));
            }
            $deleteDesign = Design::where('id', $request->id)->delete();

            if ($deleteDesign) {
                return $this->outApiJson('success', trans('main.design_deleted_successfully'));
            }

        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateDeleteDesign($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }
}
