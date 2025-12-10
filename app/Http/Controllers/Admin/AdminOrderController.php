<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderSizeGoneOption;
use App\Models\SizeGown;
use App\Models\Category;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminOrderController extends Controller
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

    public function listOrder(Request $request)
    {
        try {
            $status = "new";
            if ($request->status) {
                $status = $request->status;
            }
            $orders = Order::with(['category', 'fabric', 'design', 'user']);
            if (Auth::user()->role->type == 'shear_factor') {
                $orders = $orders->where('status', 'cut_case');
            } elseif (Auth::user()->role->type == 'sewing_worker') {
                $orders = $orders->where('status', 'sewing_case');
            } elseif (Auth::user()->role->type == 'button_operator') {
                $orders = $orders->where('status', 'button_case');
            } else {
                $orders = $orders->where('status', $status);
            }
            $orders = $orders->orderBy('id', 'DESC')->paginate(10);
            //$orders = $orders->makeVisible(['created_at', 'user_id', 'category_id', 'fabric_id', 'design_id', 'category', 'fabric', 'design']);
            if ($orders->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $orders);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function showOrder(Request $request)
    {
        try {

            $validator = $this->validateshowOrder($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $order = Order::with(['category', 'fabric', 'design', 'user'])->find($request->order_id);

            if (!$order) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            $order = $order->makeVisible(['created_at', 'user_id', 'category_id', 'fabric_id', 'design_id', 'category', 'fabric', 'design']);
            $order['details'] = $this->orderDetails($request->order_id);
            return $this->outApiJson('success', trans('main.success'), $order);
        } catch (\Exception$th) {

            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateshowOrder($request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        return $validator;
    }

    public function orderDetails($order_id)
    {
        try {
            $orderDetails = OrderSizeGoneOption::where('order_id', $order_id)->get();
            //$details = collect($orderDetails);
            $sizeGowns = SizeGown::with('sizeGownOptions')->get();
            foreach ($sizeGowns as $sizeGown) {
                $sizeGownoptions = $sizeGown->sizeGownOptions;
                foreach ($sizeGownoptions as $key => $option) {
                    //$getOption = $details->where('size_gown_option_id', $option->id);
                    $orderDetail = OrderSizeGoneOption::where('order_id', $order_id)->where('size_gown_option_id', $option->id)->first();
                    if ($orderDetail) {
                        $newArray[] = [
                            'size_gown_id' => $sizeGown->id,
                            'size_gown_name' => $sizeGown->name,
                            'size_gown_option' => $option->name,
                            'size_gown_option_image' => $option->image,
                            'order_value' => $orderDetail->value,
                        ];

                    }
                }
                $newDetails[$sizeGown->name] = $newArray;
                $newArray = [];
            }
            return $newDetails;
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function editOrder(Request $request)
    {

        if (Auth::user()->role->type != 'admin' && Auth::user()->role->type != 'seller') {
            return $this->outApiJson('JWT_Exception', trans('main.not_permission'));
        }
        try {
            $validator = $this->validateEditOrder($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $order = Order::find($request->order_id);
            if (!$order) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }

            $order = order::where('id', $request->order_id)->update([
                'deposit_amount' => $request->deposit_amount,
                'amount' => $request->amount,
                'status' => $request->status,
                'description' => $request->description,
                'delivery_date' => $request->delivery_date,
            ]);

            if (!$order) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }
            return $this->outApiJson('success', trans('main.success'));

        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateEditOrder($request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        return $validator;
    }

    public function deleteOrder(Request $request)
    {
        if (Auth::user()->role->type != 'admin' && Auth::user()->role->type != 'seller') {
            return $this->outApiJson('JWT_Exception', trans('main.not_permission'));
        }

        try {
            $validator = $this->validateDeleteOrder($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $order = Order::where('id', $request->order_id)->first();
            if (!$order) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            Order::where('id', $request->order_id)->delete();
            return $this->outApiJson('success', trans('main.deleted_sucess'));
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateDeleteOrder($request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        return $validator;
    }

    public function changeStatusOrder(Request $request)
    {
        try {
            $validator = $this->validateChangeStatusOrder($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $order = Order::find($request->order_id);
            if (!$order) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }

            if ($order->status == "new") {
                $staus = "waiting_payment";
            } elseif ($order->status == "waiting_payment") {
                $staus = "cut_case";
            } elseif ($order->status == "cut_case") {
                $staus = "sewing_case";
            } elseif ($order->status == "sewing_case") {
                $staus = "button_case";
            } else {
                $staus = "delivered";
            }

            $order = Order::where('id', $request->order_id)->update([
                'status' => $staus,
            ]);

            if (!$order) {
                return $this->outApiJson('error-update', trans('main.faild_update'));
            }
            return $this->outApiJson('success', trans('main.success'));
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateChangeStatusOrder($request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);
        return $validator;
    }

    public function saveOrder(Request $request)
    {
        try {
            $validator = $this->validateSaveOrder($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            /*$checkAllPayment = Order::where('user_id', Auth::id())->whereColumn('deposit_amount', '<>', 'amount')->count();
            if ($checkAllPayment > 0) {
            return $this->outApiJson('error-insert', trans('main.complete_payment'));
            }*/

            if ($request->order_type == "category") {
                $userSize = Category::with('sizeTypes')->where('id', $request->category_id)->first();

                /*if ($userSize->sizeTypes->isEmpty()) {
            return $this->outApiJson('error-insert', trans('main.return_back_to_complete_sizes'));
            }

            foreach ($userSize->sizeTypes as $type) {
            if ($type->sizeTypeUser->isEmpty()) {
            return $this->outApiJson('error-insert', trans('main.return_back_to_complete_sizes'));
            }
            }*/
            }
            \DB::beginTransaction();
            //dd($request->order_type);

            $image = Null;
            if($request->payment_image){
                $upload = $this->uploadImage($request->image, 'image', 'payments');
                if (!$upload[0]) {
                    return $this->outApiJson('error-upload', trans('main.faild_upload_image'));
                }
                $image = $upload[1];                
            }

            $order = Order::create([
                'user_id' => $request->user_id,
                'category_id' => $request->category_id,
                'fabric_id' => $request->fabric_id,
                'design_id' => $request->design_id,
                'description' => $request->description,
                'order_type' => $request->order_type,
                'deposit_amount' => $request->deposit_amount,
                'amount' => $request->amount,
                'status' => $request->status,
                'delivery_date' => $request->delivery_date,
                'payment_image' => $image,
            ]);

            if ($order) {
                if ($request->order_type == "category") {
                    if ($userSize->type == 'gown') {
                        if ($request->order_details == null) {
                            return $this->outApiJson('error-insert', trans('main.complete_data'));
                        }
                        $orderDetails = json_decode($request->order_details);
                        //$orderDetails = $request->order_details;
                        $details = [];
                        foreach ($orderDetails as $option) {
                            //$option = json_decode($option);
                            $details[] = [
                                'size_gown_option_id' => $option->size_gown_option_id,
                                'value' => $option->value,
                            ];
                        }
                        $addOrderDetails = $order->orderDetails()->createMany($details);
                        if (!$addOrderDetails) {
                            return $this->outApiJson('error-insert', trans('main.faild_insert'));
                        }
                    }
                }
                if ($request->order_type == "fabric") {
                    if ($request->order_details == null) {
                        return $this->outApiJson('error-insert', trans('main.complete_data'));
                    }
                    $orderDetails = json_decode($request->order_details);
                    //$orderDetails = $request->order_details;
                    $details = [];
                    foreach ($orderDetails as $option) {
                        //$option = json_decode($option);
                        $details[] = [
                            'size_gown_option_id' => $option->size_gown_option_id,
                            'value' => $option->value,
                        ];
                    }
                    $addOrderDetails = $order->orderDetails()->createMany($details);
                    if (!$addOrderDetails) {
                        return $this->outApiJson('error-insert', trans('main.faild_insert'));
                    }
                }
                \DB::commit();
                $newOrder = Order::find($order->id);
                if (!$newOrder) {
                    return $this->outApiJson('error-insert', trans('main.faild_insert'));
                }
                $newOrder['delivery_date'] = Carbon::parse($newOrder->delivery_date)->toDateString();
                $newOrder['created_date'] = Carbon::parse($newOrder->created_at)->toDateString();
                if ($request->order_type == "category") {
                    $newOrder['category_name'] = $newOrder->category->name;
                }
                if ($request->order_type == "fabric") {
                    $newOrder['fabric_name'] = $newOrder->fabric->name;
                }
                if ($request->order_type == "design") {
                    $newOrder['design_name'] = $newOrder->design->name;
                }
                $newOrder['details'] = $this->orderDetails2($newOrder->id);
                return $this->outApiJson('success', trans('main.success'), $newOrder);
            }
            \DB::rollback();
            return $this->outApiJson('error-insert', trans('main.faild_insert'));
        } catch (\Exception$th) {
            dd($th->getMessage());
            \DB::rollback();
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateSaveOrder($request)
    {
        $validate_array = ['order_type' => 'required|string',
            'user_id' => 'required'];

        if ($request->order_type == "category") {
            $validate_array['category_id'] = 'required';
        }
        if ($request->order_type == "fabric") {
            $validate_array['fabric_id'] = 'required';
        }
        if ($request->order_type == "design") {
            $validate_array['design_id'] = 'required';
        }
        $validator = Validator::make($request->all(), $validate_array);

        return $validator;
    }

    public function orderDetails2($order_id)
    {
        try {

            $orderDetails = OrderSizeGoneOption::where('order_id', $order_id)->get();
            //$details = collect($orderDetails);
            $sizeGowns = SizeGown::with('sizeGownOptions')->get();
            $newArray = [];
            $newDetails = "";
            foreach ($sizeGowns as $sizeGown) {
                $sizeGownoptions = $sizeGown->sizeGownOptions;
                //$newArray = [];
                foreach ($sizeGownoptions as $key => $option) {
                    //$getOption = $details->where('size_gown_option_id', $option->id);
                    $orderDetail = OrderSizeGoneOption::where('order_id', $order_id)->where('size_gown_option_id', $option->id)->first();
                                           
                    if ($orderDetail) {
                        $newArray[] = [
                            'size_gown_id' => $sizeGown->id,
                            'size_gown_name' => $sizeGown->name,
                            'size_gown_type' => $sizeGown->type,
                            'size_gown_option' => $option->name,
                            'size_gown_option_image' => $option->image,
                            'order_value' => $orderDetail->value,
                        ];

                    }
                }
                //dd($newArray);
                if($newArray){
                    //$newDetails[$sizeGown->name] = $newArray;
                    $newDetails = $newArray;
                }
                //$newArray = [];
            }
            return $newDetails;
        } catch (\Exception$th) {
            dd($th->getMessage());
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }
}
