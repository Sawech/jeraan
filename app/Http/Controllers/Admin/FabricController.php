<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Fabric;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FabricController extends Controller
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

    public function listFabric()
    {
        try {
            $fabrics = Fabric::paginate(10);
            if ($fabrics->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $fabrics);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function addFabric(Request $request)
    {
        try {
            $validator = $this->validateAddFabric($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $fabric = new Fabric();
            
            $upload = $this->uploadImage($request->image, 'image', 'fabric');
            if (!$upload[0]) {
                return $this->outApiJson('error-upload', trans('main.faild_upload_image'));
            }
            $fabric->image = $upload[1];
            $fabric->number = $request->number;
            foreach ($this->allLanguages as $language) {
                $name = 'name_' . $language->language_universal;
                $description = 'description_' . $language->language_universal;

                $title = 'title_' . $language->language_universal;
                $raw_material = 'raw_material_' . $language->language_universal;
                $supplier = 'supplier_' . $language->language_universal;
                $item = 'item_' . $language->language_universal;
                $color = 'color_' . $language->language_universal;
                $source_country = 'source_country_' . $language->language_universal;
                $type = 'type_' . $language->language_universal;
                $fabric->translateOrNew($language->language_universal)->name = $request->input($name);
                $fabric->translateOrNew($language->language_universal)->description = $request->input($description);

                $fabric->translateOrNew($language->language_universal)->title = $request->input($title);
                $fabric->translateOrNew($language->language_universal)->raw_material = $request->input($raw_material);
                $fabric->translateOrNew($language->language_universal)->supplier = $request->input($supplier);
                $fabric->translateOrNew($language->language_universal)->item = $request->input($item);
                $fabric->translateOrNew($language->language_universal)->color = $request->input($color);
                $fabric->translateOrNew($language->language_universal)->source_country = $request->input($source_country);
                $fabric->translateOrNew($language->language_universal)->type = $request->input($type);
            }
            $fabric->save();
            if (!$fabric) {
                return $this->outApiJson('error-insert', trans('main.error_insert'));
            }

            return $this->outApiJson('success', trans('main.fabric_created_successfully'), $fabric);

        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateAddFabric($request)
    {
        $validate_array = ['image' => 'required|image|mimes:jpeg,jpg,png|min:1|max:2000','number' => 'required'];
        foreach ($this->allLanguages as $language) {
            $validate_array['name_' . $language->language_universal] = 'required';
            $validate_array['description_' . $language->language_universal] = 'required';

            $validate_array['title_' . $language->language_universal] = 'required';
            $validate_array['raw_material_' . $language->language_universal] = 'required';
            $validate_array['supplier_' . $language->language_universal] = 'required';
            $validate_array['item_' . $language->language_universal] = 'required';
            $validate_array['color_' . $language->language_universal] = 'required';
            $validate_array['source_country_' . $language->language_universal] = 'required';
            $validate_array['type_' . $language->language_universal] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }

    public function editFabric(Request $request)
    {
        try {
            $validator = $this->validateEditFabric($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $fabric = Fabric::find($request->id);
            if (!$fabric) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            if($request->image){
                $upload = $this->uploadImage($request->image, 'image', 'fabric');
                if (!$upload[0]) {
                    return $this->outApiJson('error-upload', trans('main.faild_upload_image'));
                }
                $fabric->image = $upload[1];                
            }
            $fabric->number = $request->number;
            foreach ($this->allLanguages as $language) {
                $name = 'name_' . $language->language_universal;
                $description = 'description_' . $language->language_universal;
                $title = 'title_' . $language->language_universal;
                $raw_material = 'raw_material_' . $language->language_universal;
                $supplier = 'supplier_' . $language->language_universal;
                $item = 'item_' . $language->language_universal;
                $color = 'color_' . $language->language_universal;
                $source_country = 'source_country_' . $language->language_universal;
                $type = 'type_' . $language->language_universal;

                $fabric->translateOrNew($language->language_universal)->name = $request->input($name);
                $fabric->translateOrNew($language->language_universal)->description = $request->input($description);
                $fabric->translateOrNew($language->language_universal)->title = $request->input($title);
                $fabric->translateOrNew($language->language_universal)->raw_material = $request->input($raw_material);
                $fabric->translateOrNew($language->language_universal)->supplier = $request->input($supplier);
                $fabric->translateOrNew($language->language_universal)->item = $request->input($item);
                $fabric->translateOrNew($language->language_universal)->color = $request->input($color);
                $fabric->translateOrNew($language->language_universal)->source_country = $request->input($source_country);
                $fabric->translateOrNew($language->language_universal)->type = $request->input($type);
            }
            $fabric->save();
            if (!$fabric) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }

            return $this->outApiJson('success', trans('main.fabric_updated_successfully'), $fabric);

        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateEditFabric($request)
    {
        $validate_array = ['id' => 'required',
        'image' => 'image|mimes:jpeg,jpg,png|min:1|max:2000',
        'number' => 'required'
        ];
        foreach ($this->allLanguages as $language) {
            $validate_array['name_' . $language->language_universal] = 'required';
            $validate_array['description_' . $language->language_universal] = 'required';

            $validate_array['title_' . $language->language_universal] = 'required';
            $validate_array['raw_material_' . $language->language_universal] = 'required';
            $validate_array['supplier_' . $language->language_universal] = 'required';
            $validate_array['item_' . $language->language_universal] = 'required';
            $validate_array['color_' . $language->language_universal] = 'required';
            $validate_array['source_country_' . $language->language_universal] = 'required';
            $validate_array['type_' . $language->language_universal] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }


    public function fetchFabric(Request $request)
    {
        try {
            $validator = $this->validateFetchFabric($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $fabric = Fabric::find($request->id);
            if (!$fabric) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $fabric = $fabric->makeVisible(['translations']);
            return $this->outApiJson('success', trans('main.fabric_returned_successfully'), $fabric);

        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateFetchFabric($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }

    public function deleteFabric(Request $request)
    {
        try {
            $validator = $this->validateDeleteFabric($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $fabric = Fabric::find($request->id);
            if (!$fabric) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $checkOrders = Order::where('fabric_id',$request->id)->count();
            if($checkOrders > 0){
                return $this->outApiJson('error-delete', trans('main.fabric_related_with_orders'));
            }
            $deleteFabric = fabric::where('id',$request->id)->delete();

            if($deleteFabric){
                return $this->outApiJson('success', trans('main.fabric_deleted_successfully'));
            }
            
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateDeleteFabric($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        return $validator;
    }
}
