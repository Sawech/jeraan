<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Category;
use App\Models\Design;
use App\Models\Fabric;
use App\Models\SizeGown;
use Auth;
use Illuminate\Http\Request;
use App\Models\SizeTypeCategoryUser;

class ListController extends Controller
{
    use Helper;

    public function __construct()
    {
        if (app('request')->header('lang')) {
            app()->setLocale(app('request')->header('lang'));
        }
    }

    public function listCateogry()
    {
        try {
            $categories = Category::paginate(10);
            if ($categories->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $categories);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function listDesign()
    {
        try {
            $designs = Design::with('images')->get();
            if ($designs->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $designs);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function listFabric()
    {
        try {
            $fabrics = Fabric::get();
            if ($fabrics->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $fabrics);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function sizeGown(Request $request)
    {
        try {
            $sizeGowns = SizeGown::with('images', 'sizeGownOptions');
            if($request->type){
                $sizeGowns = $sizeGowns->where('type',$request->type);
            }
            $sizeGowns = $sizeGowns->get();
            if ($sizeGowns->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $sizeGowns);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function sizeTypeCategoryUser()
    {
        try {
            $sizeTypes = Category::with('sizeTypes')->get();
            if ($sizeTypes->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $sizeTypes);
        } catch (\Exception $th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }
}
