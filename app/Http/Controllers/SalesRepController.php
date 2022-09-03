<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Admin;
use App\Models\Dealer;
use App\Models\Users;
use App\Models\Vendors;
use App\Models\Faq;
use App\Models\Seminar;

// use Maatwebsite\Excel\Facades\Excel;
// use App\Imports\ProductsImport;
// use App\Http\Helpers;
use App\Models\Products;
// use App\Models\Category;
// use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Storage;
// use App\Models\Branch;
use App\Models\PromotionalFlier;
use App\Models\Cart;
use App\Models\Chat;
use App\Models\Report;
use App\Models\User;
use App\Models\ProgramCountdown;
use App\Models\ReportReply;
use App\Models\ProgramNotes;

use App\Models\PriceOverideReport;
use App\Models\SpecialOrder;
use App\Models\UserStatus;

class SalesRepController extends Controller
{
    //

    public function __construct()
    {
        // set timeout limit
        set_time_limit(25000000);
        $this->result = (object) [
            'status' => false,
            'status_code' => 200,
            'message' => null,
            'data' => (object) null,
            'token' => null,
            'debug' => null,
        ];
    }

    public function generate_sales_rep_purchasers_pdf($user)
    {
        $dealership_codes = [];
        $res_data = [];
        $selected_user = Users::where('id', $user)
            ->get()
            ->first();

        $privilaged_dealers = $selected_user->privileged_dealers;
        if ($privilaged_dealers != null) {
            $separator = explode(',', $privilaged_dealers);

            foreach ($separator as $value) {
                if (!in_array($value, $dealership_codes)) {
                    array_push($dealership_codes, $value);
                }
            }

            foreach ($dealership_codes as $value) {
                $value = trim($value);
                $vendor_purchases = Cart::where('dealer', $value)->get();

                if (count($vendor_purchases) > 0) {
                    foreach ($vendor_purchases as $cart_data) {
                        $user_id = $cart_data->uid;
                        $user_data = Users::where('id', $user_id)
                            ->get()
                            ->first();

                        $sum_user_total = Cart::where('uid', $user_id)
                            ->get()
                            ->sum('price');

                        if ($user_data) {
                            $data = [
                                'account_id' => $user_data->account_id,
                                'dealer_name' => $user_data->company_name,
                                'user' => $user_id,
                                'purchaser_name' =>
                                    $user_data->first_name .
                                    ' ' .
                                    $user_data->last_name,
                                'amount' => $sum_user_total,
                            ];

                            array_push($res_data, $data);
                        }
                    }
                }
            }

            /////// Sorting //////////
            usort($res_data, function ($a, $b) {
                //Sort the array using a user defined function
                return $a['amount'] > $b['amount'] ? -1 : 1; //Compare the scores
            });

            $res_data = array_map(
                'unserialize',
                array_unique(array_map('serialize', $res_data))
            );

            $this->result->status = true;
            $this->result->status_code = 200;
            $this->result->message = 'Purchasers by Dealers';
            $this->result->data = $res_data;
            return response()->json($this->result);
        }
    }

    public function view_dealer_summary_page($dealer)
    {
        $vendors = [];
        $res_data = [];
        $grand_total = 0;

        $dealer_data = Cart::where('dealer', $dealer)->get();
        $dealer_ship = Dealer::where('dealer_code', $dealer)
            ->get()
            ->first();

        foreach ($dealer_data as $value) {
            $vendor_code = $value->vendor;
            if (!\in_array($vendor_code, $vendors)) {
                array_push($vendors, $vendor_code);
            }
        }

        foreach ($vendors as $value) {
            $vendor_data = Vendors::where('vendor_code', $value)
                ->get()
                ->first();
            $cart_data = Cart::where('vendor', $value)
                ->where('dealer', $dealer)
                ->get();

            $total = 0;

            foreach ($cart_data as $value) {
                $total += $value->price;
                $atlas_id = $value->atlas_id;
                $pro_data = Products::where('atlas_id', $atlas_id)
                    ->get()
                    ->first();

                $value->description = $pro_data->description;
                $value->vendor_product_code = $pro_data->vendor_product_code;
            }

            $data = [
                'vendor_code' => $vendor_data->vendor_code,
                'vendor_name' => $vendor_data->vendor_name,
                'total' => floatval($total),
                'data' => $cart_data,
            ];

            $grand_total += $total;

            array_push($res_data, $data);
        }

        $this->result->status = true;
        $this->result->status_code = 200;
        $this->result->message = 'all dealer sales';
        $this->result->data = $res_data;
        // $this->result->data->atlas_id = $atlas_id_data;

        return response()->json($this->result);
    }

    public function all_dealers_sales($user)
    {
        $res_data = [];
        $selected_user = Users::where('id', $user)
            ->get()
            ->first();

        if ($selected_user) {
            $privilaged_dealers = $selected_user->privileged_dealers;
            if ($privilaged_dealers != null) {
                $separator = explode(',', $privilaged_dealers);
                foreach ($separator as $value) {
                    $dealer = Dealer::where('dealer_code', $value)
                        ->get()
                        ->first();
                    if ($dealer) {
                        $total_sales = Cart::where('dealer', $value)->sum(
                            'price'
                        );

                        if ($total_sales > 0) {
                            $data = [
                                'dealer_name' => $dealer->dealer_name,
                                'dealer_code' => $dealer->dealer_code,
                                'sales' => $total_sales,
                            ];

                            array_push($res_data, $data);
                        }
                    }
                }
            }
        }

        $this->result->status = true;
        $this->result->status_code = 200;
        $this->result->message = 'all dealer sales';
        $this->result->data = $res_data;
        // $this->result->data->atlas_id = $atlas_id_data;

        return response()->json($this->result);
    }

    public function sales_by_item_detailed($user)
    {
        $res_data = [];
        // $selected_user = Users::where('id', $user)
        //     ->get()
        //     ->first();

        // $privilaged_dealers = $selected_user->privileged_dealers;
        // if ($privilaged_dealers != null) {
        //     $separator = explode(',', $privilaged_dealers);

        //     foreach ($separator as $value) {
        //         $value = trim($value);

        //     }

        // }

        $this->result->status = true;
        $this->result->status_code = 200;
        $this->result->message = 'Sales By Detailed';
        $this->result->data->res = $res_data;
        // $this->result->data->atlas_id = $atlas_id_data;

        return response()->json($this->result);
    }

    public function view_dealer_summary($user, $code)
    {
        $vendors = [];
        $res_data = [];
        $grand_total = 0;

        $res_data = [];
        $selected_user = Users::where('id', $user)
            ->get()
            ->first();

        $privilaged_dealers = $selected_user->privileged_dealers;
        if ($privilaged_dealers != null) {
            $separator = explode(',', $privilaged_dealers);

            foreach ($separator as $value) {
                $value = trim($value);
                ///   $vendor_purchases = Cart::where('dealer', $value)->get();

                $dealer_data = Cart::where('dealer', $value)->get();
                $dealer_ship = Dealer::where('dealer_code', $value)
                    ->get()
                    ->first();

                foreach ($dealer_data as $value) {
                    $vendor_code = $value->vendor;
                    if (!\in_array($vendor_code, $vendors)) {
                        array_push($vendors, $vendor_code);
                    }
                }
            }
        }

        foreach ($vendors as $value) {
            $vendor_data = Vendors::where('vendor_code', $value)
                ->get()
                ->first();
            $cart_data = Cart::where('vendor', $value)
                ->where('dealer', $code)
                ->get();

            $total = 0;

            foreach ($cart_data as $value) {
                $total += $value->price;
                $atlas_id = $value->atlas_id;
                $pro_data = Products::where('atlas_id', $atlas_id)
                    ->get()
                    ->first();

                $value->description = $pro_data->description;
                $value->vendor_product_code = $pro_data->vendor_product_code;
            }

            $data = [
                'vendor_code' => $vendor_data->vendor_code,
                'vendor_name' => $vendor_data->vendor_name,
                'total' => floatval($total),
                'data' => $cart_data,
            ];

            $grand_total += $total;

            array_push($res_data, $data);
        }

        $pdf_data = [
            'data' => $res_data,
            'dealer' => $dealer_ship,
            'grand_total' => $grand_total,
        ];

        $this->result->status = true;
        $this->result->status_code = 200;
        $this->result->data = $pdf_data;
        $this->result->message = 'View Dealer Summary';
        return response()->json($this->result);
    }

    public function get_purchases_dealers($user)
    {
        $dealership_codes = [];
        $res_data = [];
        $selected_user = Users::where('id', $user)
            ->get()
            ->first();

        $privilaged_dealers = isset($selected_user->privileged_dealers)
            ? $selected_user->privileged_dealers
            : null;
        if ($privilaged_dealers != null) {
            $separator = explode(',', $privilaged_dealers);

            foreach ($separator as $value) {
                if (!in_array($value, $dealership_codes)) {
                    array_push($dealership_codes, $value);
                }
            }

            foreach ($dealership_codes as $value) {
                $value = trim($value);
                $vendor_purchases = Cart::where('dealer', $value)->get();

                if (count($vendor_purchases) > 0) {
                    foreach ($vendor_purchases as $cart_data) {
                        $user_id = $cart_data->uid;
                        $user_data = Users::where('id', $user_id)
                            ->get()
                            ->first();

                        $sum_user_total = Cart::where('uid', $user_id)
                            ->get()
                            ->sum('price');

                        if ($user_data) {
                            $data = [
                                'account_id' => $user_data->account_id,
                                'dealer_name' => $user_data->company_name,
                                'user' => $user_id,
                                'purchaser_name' =>
                                    $user_data->first_name .
                                    ' ' .
                                    $user_data->last_name,
                                'amount' => $sum_user_total,
                            ];

                            array_push($res_data, $data);
                        }
                    }
                }
            }

            /////// Sorting //////////
            usort($res_data, function ($a, $b) {
                //Sort the array using a user defined function
                return $a['amount'] > $b['amount'] ? -1 : 1; //Compare the scores
            });

            $res_data = array_map(
                'unserialize',
                array_unique(array_map('serialize', $res_data))
            );

            $this->result->status = true;
            $this->result->status_code = 200;
            $this->result->message = 'Purchasers by Dealers';
            $this->result->data = $res_data;
            return response()->json($this->result);
        }
    }

    public function sales_rep_dashboard_analysis($user)
    {
        $total_sales = 0;
        $total_orders = 0;
        $total_dealers = 0;
        $total_logged_in = 0;
        $total_not_logged_in = 0;
        $selected_user = Users::where('id', $user)
            ->get()
            ->first();

        if ($selected_user) {
            $privilaged_dealers = $selected_user->privileged_dealers;
            if ($privilaged_dealers != null) {
                $separator = explode(',', $privilaged_dealers);
                $total_dealers = count($separator);
                if (\in_array(null, $separator)) {
                    $total_dealers = $total_dealers - 1;
                }

                foreach ($separator as $value) {
                    $total_logged_in += Users::where('account_id', $value)
                        ->where('last_login', '!=', null)
                        ->count();

                    $total_not_logged_in += Users::where('account_id', $value)
                        ->where('last_login', '=', null)
                        ->count();
                }

                $all_vendor_data = Vendors::all();
                foreach ($all_vendor_data as $value) {
                    $vendor_code = $value->vendor_code;
                    if (in_array($vendor_code, $separator)) {
                        $total_sales += Cart::where(
                            'vendor',
                            $vendor_code
                        )->sum('price');
                        $total_orders += Cart::where(
                            'vendor',
                            $vendor_code
                        )->sum('qty');
                    }
                }
            }
        }

        $this->result->status = true;
        $this->result->status_code = 200;
        $this->result->message = 'get sales rep dashboard analysis';
        $this->result->data->total_sales = $total_sales;
        $this->result->data->total_orders = $total_orders;
        $this->result->data->total_dealers = $total_dealers;

        $this->result->data->total_logged_in = $total_logged_in;
        $this->result->data->total_not_logged_in = $total_not_logged_in;

        return response()->json($this->result);
    }

    public function dashboard()
    {
    }
}
