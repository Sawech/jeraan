<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderSizeGoneOption;
use App\Models\SizeGown;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use Helper;

    public function __construct()
    {
        if (app('request')->header('lang')) {
            app()->setLocale(app('request')->header('lang'));
        }
    }

    public function index()
    {
        try {
            $orders = Order::where('user_id', Auth::id())->paginate(10);
            if ($orders->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $orders);
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function addOrder()
    {

        try {

            /*$validator = $this->validateAddOrder($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }*/
            $checkAllPayment = Order::where('user_id', Auth::id())->whereColumn('deposit_amount', '<>', 'amount')->count();

            if ($checkAllPayment > 0) {
                return $this->outApiJson('error-insert', trans('main.complete_payment'));
            }
            /*$userSize = Category::with('sizeTypes')->where('id', $request->category_id)->first();

            if ($userSize->sizeTypes->isEmpty()) {
                return $this->outApiJson('error-insert', trans('main.return_back_to_complete_sizes'));
            }
            foreach ($userSize->sizeTypes as $type) {
                if ($type->sizeTypeUser->isEmpty()) {
                    return $this->outApiJson('error-insert', trans('main.return_back_to_complete_sizes'));
                }
            }*/
            //return $this->outApiJson('success', trans('main.success'), $userSize);
                        return $this->outApiJson('success', trans('main.success'));

        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateAddOrder($request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
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

            $checkAllPayment = Order::where('user_id', Auth::id())->whereColumn('deposit_amount', '<>', 'amount')->count();
            if ($checkAllPayment > 0) {
                return $this->outApiJson('error-insert', trans('main.complete_payment'));
            }

            if ($request->order_type == "category") {
                $userSize = Category::with('sizeTypes')->where('id', $request->category_id)->first();

                if ($userSize->sizeTypes->isEmpty()) {
                    return $this->outApiJson('error-insert', trans('main.return_back_to_complete_sizes'));
                }

                foreach ($userSize->sizeTypes as $type) {
                    if ($type->sizeTypeUser->isEmpty()) {
                        return $this->outApiJson('error-insert', trans('main.return_back_to_complete_sizes'));
                    }
                }
            }
            \DB::beginTransaction();
            //dd($request->order_type);
            $order = Order::create([
                'category_id' => $request->category_id,
                'fabric_id' => $request->fabric_id,
                'design_id' => $request->design_id,
                'user_id' => Auth::id(),
                'description' => $request->description,
                'order_type' => $request->order_type
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
                $newOrder['details'] = $this->orderDetails($newOrder->id);
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
        $validate_array = ['order_type' => 'required|string'];

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

    public function showOrder(Request $request)
    {
        try {

            $validator = $this->validateshowOrder($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $order = Order::find($request->order_id);

            if (!$order || ($order->user_id != Auth::id())) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }

            $order['delivery_date'] = Carbon::parse($order->delivery_date)->toDateString();
            $order['created_date'] = Carbon::parse($order->created_at)->toDateString();
            if ($order->order_type == "category") {
                $order['category_name'] = $order->category->name;
            }
            if ($order->order_type == "fabric") {
                $order['fabric_name'] = $order->fabric->name;
            }
            if ($order->order_type == "design") {
                $order['design_name'] = $order->design->name;
            }
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

    public function uploadPaymentImage(Request $request)
    {
        try {
            $validator = $this->validateUploadPaymentImage($request);

            if ($validator->fails()) {
                return $this->outApiJson('validation', trans('main.validation_errors'), $validator->errors());
            }

            $order = Order::find($request->order_id);
            if (!$order || ($order->user_id != Auth::id())) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }

            $upload = $this->uploadImage($request->payment_image, 'image', 'payments');
            if (!$upload[0]) {
                return $this->outApiJson('error-upload', trans('main.faild_upload_payment_image'));
            }
            Order::where('id', $request->order_id)->update(['payment_image' => $upload[1]]);
            return $this->outApiJson('success', trans('main.success_upload'));
        } catch (\Exceptio$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }

    public function validateUploadPaymentImage($request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'payment_image' => 'required|image|mimes:jpeg,jpg,png|min:1|max:2000',
        ]);

        return $validator;
    }

    public function deleteOrders(Request $request)
    {
        try {
            if ($request->order_id) {
                $order = Order::where('id', $request->order_id)->where(function ($query) {
                    $query->where('status', 'delivered')
                        ->orWhere('status', 'new');
                })->first();
                if (!$order || ($order->user_id != Auth::id())) {
                    return $this->outApiJson('not-found-data', trans('main.not_found_data'));
                }
                Order::where('id', $request->order_id)->delete();
            } else {
                Order::where('user_id', Auth::id())->where(function ($query) {
                    $query->where('status', 'delivered')
                        ->orWhere('status', 'new');
                })->delete();
            }
            return $this->outApiJson('success', trans('main.deleted_sucess'));
        } catch (\Exception$th) {
            return $this->outApiJson('exception', trans('main.exception'));
        }
    }
}
