<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\SpecialOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\Users;

class SpecialOrderController extends Controller
{
    //
    public function __construct()
    {
        // $this->middleware( 'auth:api');
        $this->result = (object) [
            'status' => false,
            'status_code' => 200,
            'message' => null,
            'data' => (object) null,
            'token' => null,
            'debug' => null,
        ];
    }

    // add special orders
    public function add_special_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required',
            'dealer_id' => 'required',
            'product_array' => 'required',
        ]);

        if ($validator->fails()) {
            $response['response'] = $validator->messages();
            $this->result->status = false;
            $this->result->status_code = 422;
            $this->result->data = [];
            $this->result->message = $response;

            return response()->json($this->result);
        } else {
            $user_id = $request->input('uid');
            $dealer_id = $request->input('dealer_id');
            // lets get the items from the array
            $product_array = $request->input('product_array');

            $decode_product_array = json_decode($product_array);

            // `uid`, `quantity`, `vendor_id`, `description`,
            if (count($decode_product_array) > 0) {
                foreach ($decode_product_array as $product) {
                    // insert them to the db
                    // `id`, `uid`, `quantity`, `vendor_id`, `description`,
                    //  `created_at`, `updated_at`, `deleted_at`


                    // check if the item already exists in the db
                    $check_atlas_id = Products::where('atlas_id', $product->vendor_no)->first();

                    if ($check_atlas_id) {
                        $this->result->status = false;
                        $this->result->status_code = 200;
                        $this->result->data = [];
                        $this->result->message =
                            'sorry item with atlas id: ' . $product->vendor_no . ' already exists in the database';
                    }

                    $add_items = SpecialOrder::create([
                        'uid' => $user_id,
                        'quantity' => $product->quantity,
                        'vendor_code' => $product->vendor_code,
                        'description' => $product->description,
                        'vendor_no' => $product->vendor_no,
                        'dealer_id' => $dealer_id,
                    ]);

                    if (!$add_items) {
                        $this->result->status = false;
                        $this->result->status_code = 200;
                        $this->result->data = [];
                        $this->result->message =
                            'sorry special order item could not be added';
                    }
                }

                $this->result->status = true;
                $this->result->status_code = 200;
                $this->result->data = [];
                $this->result->message = 'Quick order items added successfully';
                return response()->json($this->result);
            } else {
                $this->result->status = false;
                $this->result->status_code = 200;
                $this->result->data = [];
                $this->result->message =
                    'please add an item to the product array';
                return response()->json($this->result);
            }
        }
    }

    // edit special orders
    public function edit_special_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required',
            'product_array' => 'required',
        ]);

        if ($validator->fails()) {
            $response['response'] = $validator->messages();
            $this->result->status = false;
            $this->result->status_code = 422;
            $this->result->data = [];
            $this->result->message = $response;

            return response()->json($this->result);
        } else {
            $user_id = $request->input('uid');
            // lets get the items from the array
            $product_array = $request->input('product_array');

            $decode_product_array = json_decode($product_array);

            // `uid`, `quantity`, `vendor_id`, `description`,
            if (count($decode_product_array) > 0) {
                foreach ($decode_product_array as $product) {
                    // insert them to the db
                    // `id`, `uid`, `quantity`, `vendor_id`, `description`,
                    //  `created_at`, `updated_at`, `deleted_at`
                    $special_order_id = $product->id;
                    $check_item = SpecialOrder::find($special_order_id);

                    if (!$check_item) {
                        $this->result->status = false;
                        $this->result->status_code = 422;
                        $this->result->data = [];
                        $this->result->message =
                            'sorry special order item could not be added';
                        return response()->json($this->result);
                    }

                    $update_special_order = $check_item->update([
                        'uid' => $user_id,
                        'quantity' => $product->quantity,
                        'vendor_code' => $product->vendor_code,
                        'description' => $product->description,
                        'vendor_no' => $product->vendor_no,
                    ]);
                }

                $this->result->status = true;
                $this->result->status_code = 200;
                $this->result->data = [];
                $this->result->message =
                    'Quick order items updated successfully';
                return response()->json($this->result);
            } else {
                $this->result->status = true;
                $this->result->status_code = 422;
                $this->result->data = [];
                $this->result->message =
                    'please add an item to the product array';
                return response()->json($this->result);
            }
        }
    }

    // delete special order by id
    public function delete_special_order($dealer_id, $id)
    {
        $check_order = SpecialOrder::where('dealer_id', $dealer_id)
            ->where('id', $id)
            ->first();

        // oops we couldnt find the special order
        if ($check_order == null) {
            $this->result->status = false;
            $this->result->status_code = 422;
            $this->result->data = [];
            $this->result->message =
                'sorry special order item could not be found';
            return response()->json($this->result);
        }

        if ($check_order != null) {
            // delete the special order
            $delete_special_order = $check_order->delete();

            // oops we could not delete the order
            if (!$delete_special_order) {
                $this->result->status = false;
                $this->result->status_code = 422;
                $this->result->data = [];
                $this->result->message =
                    'sorry special order item could not be deleted';
                return response()->json($this->result);
            }

            // return success response
            $this->result->status = true;
            $this->result->status_code = 200;
            $this->result->data = [];
            $this->result->message = 'Special order item deleted successfully';
            return response()->json($this->result);
        }
    }

    // fetch special order by uid
    public function fetch_special_order_by_dealer_id($dealer_id)
    {
        $check_special_order_exists = SpecialOrder::where(
            'dealer_id',
            $dealer_id
        )->get();

        // oops we couldnt find the special order
        if (
            !$check_special_order_exists ||
            count($check_special_order_exists) == 0
        ) {
            $this->result->status = true;
            $this->result->status_code = 200;
            $this->result->data = [];
            $this->result->message =
                'sorry special order item could not be found';
            return response()->json($this->result);
        }

        $check_special_order = DB::table('special_orders')
            ->join(
                'vendors',
                'vendors.vendor_code',
                '=',
                'special_orders.vendor_code'
            )
            ->where('special_orders.dealer_id', $dealer_id)
            ->select('vendors.*', 'special_orders.*')
            ->get();
        //         $check_special_order = SpecialOrder::
        //                 'vendors.vendor_code as vendor_code',
        //                 'vendors.vendor_name as vendor_name',
        //                 'vendors.role as vendor_role',
        //                 'vendors.role_name as vendor_role_name',
        //                 'vendors.status as vendor_role_name',
        //                 'vendors.created_at as vendor_created_at',
        //                 'vendors.updated_at as vendor_updated_at',

        if (count($check_special_order) > 0) {
            foreach ($check_special_order as $item) {
                $get_dealer_users = Users::where(
                    'account_id',
                    $dealer_id
                )->get();
                $item->users = $get_dealer_users;
            }
        }

        // return success response
        $this->result->status = true;
        $this->result->status_code = 200;
        $this->result->data = $check_special_order;
        $this->result->message = 'Special order item fetched successfully';
        return response()->json($this->result);
    }

    // fetch special order by uid
    public function fetch_special_order_by_dealer_id_vendor_id($dealer_id, $vendor_code)
    {
        // return $dealer_id . " => " .$vendor_code;

        $check_special_order_exists = SpecialOrder::where(
            'dealer_id',
            $dealer_id)->where('vendor_code', $vendor_code)->get();

        // return $check_special_order_exists;

        // oops we couldnt find the special order
        if (
            !$check_special_order_exists ||
            count($check_special_order_exists) == 0
        ) {
            $this->result->status = true;
            $this->result->status_code = 200;
            $this->result->data = [];
            $this->result->message =
                'sorry special order item for atlas id and vendor could not be found';
            return response()->json($this->result);
        }

        $check_special_order = DB::table('special_orders')
            ->join(
                'vendors',
                'vendors.vendor_code',
                '=',
                'special_orders.vendor_code'
            )
            ->where('special_orders.dealer_id', $dealer_id)
            ->where('special_orders.vendor_code', $vendor_code)
            ->select('vendors.*', 'special_orders.*')
            ->get();
        //         $check_special_order = SpecialOrder::
        //                 'vendors.vendor_code as vendor_code',
        //                 'vendors.vendor_name as vendor_name',
        //                 'vendors.role as vendor_role',
        //                 'vendors.role_name as vendor_role_name',
        //                 'vendors.status as vendor_role_name',
        //                 'vendors.created_at as vendor_created_at',
        //                 'vendors.updated_at as vendor_updated_at',

        if (count($check_special_order) > 0) {
            foreach ($check_special_order as $item) {
                $get_dealer_users = Users::where(
                    'account_id',
                    $dealer_id
                )->get();
                $item->users = $get_dealer_users;
            }
        }

        // return success response
        $this->result->status = true;
        $this->result->status_code = 200;
        $this->result->data = $check_special_order;
        $this->result->message = 'Special order with vendor code fetched successfully';
        return response()->json($this->result);
    }
}
