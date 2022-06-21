<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dealer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Products;
use Illuminate\Support\Facades\Storage;
use App\Models\DealerCart;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubmitOrderMail;
use App\Models\Orders;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade as PDF;

use App\Models\Promotional_ads;
use App\Models\Catalogue_Order;
use App\Models\Category;
use App\Models\AtlasLoginLog;

use App\Models\CardedProducts;
use App\Models\ServiceParts;
use App\Models\Cart;
use App\Models\Faq;
use App\Models\Report;
use App\Models\Vendors;

class DealerController extends Controller
{
    public function __construct()
    {
        //// $this->middleware( 'auth:api', [ 'except' => [ 'login', 'register', 'test' ] ] );
        $this->result = (object) [
            'status' => false,
            'status_code' => 200,
            'message' => null,
            'data' => (object) null,
            'token' => null,
            'debug' => null,
        ];
    }

    public function login()
    {
        echo 'login page setup';
    }

    public function dealer_dashboard($account)
    {
        $completed_orders = Cart::where('dealer', $account)
            ->where('status', '1')
            ->count();

        $new_products = Products::where('check_new', '1')->count();
        $show_total = Cart::where('dealer', $account)->sum('price');

        $order_remaining = Vendors::count();

        $this->result->status = true;
        $this->result->status_code = 200;

        $this->result->data->completed_orders = $completed_orders;
        $this->result->data->new_products = $new_products;
        $this->result->data->show_total = $show_total;
        $this->result->data->order_remaining = $order_remaining;

        $this->result->message = 'Dealer Dashboard Data';
        return response()->json($this->result);
    }

    public function universal_search($search)
    {
        $vendor = Vendors::where('vendor_code', $search)->exists();
        $product = Products::where('atlas_id', $search)->exists();

        switch (true) {
            case $vendor:
                $item = Vendors::where('vendor_code', $search)
                    ->get()
                    ->first();

                $this->result->status = true;
                $this->result->status_code = 200;
                $this->result->message = 'get products with atlas id';
                $this->result->data = $item;
                break;

            case $product:
                $item = Products::where('atlas_id', $search)
                    ->get()
                    ->first();

                $this->result->status = true;
                $this->result->status_code = 200;
                $this->result->message = 'get products with atlas id';
                $this->result->data = $item;
                break;

            default:
                $this->result->status = false;
                $this->result->status_code = 404;
                $this->result->message = 'not found';

                break;
        }

        return response()->json($this->result);
    }

    public function create_report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'description' => 'required',
            'photo' => 'mimes:pdf,doc,docx,xls,jpg,jpeg,png,gif',
        ]);

        if ($validator->fails()) {
            $response['response'] = $validator->messages();
            $this->result->status = false;
            $this->result->status_code = 422;
            $this->result->message = $response;

            return response()->json($this->result);
        } else {
            if ($request->hasFile('file')) {
                $filenameWithExt = $request
                    ->file('file')
                    ->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request
                    ->file('file')
                    ->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $filepath =
                    env('APP_URL') .
                    Storage::url(
                        $request
                            ->file('file')
                            ->storeAs('public/reports', $fileNameToStore)
                    );
            }

            $subject = $request->input('subject');
            $description = $request->input('description');

            // subject, description , file_url , ticket_id, created_at, deleted_at, updated_at
            $create_report = Report::create([
                'subject' => $subject ? $subject : null,
                'description' => $description ? $description : null,
                'file_url' => $request->hasFile('file') ? $filepath : null,
                'ticket_id' => Str::random(8),
            ]);

            if (!$create_report) {
                $this->result->status = true;
                $this->result->status_code = 400;
                $this->result->message =
                    'An Error Ocurred, Vendor Addition failed';
                return response()->json($this->result);
            }

            $this->result->status = true;
            $this->result->status_code = 200;
            $this->result->message = 'Report Created Successfully';
            return response()->json($this->result);
        }
    }

    public function dealer_faq()
    {
        $fetch_faqs = Faq::orderBy('id', 'desc')
            ->where('status', 1)
            ->where('role', '4')
            ->get();

        if (!$fetch_faqs) {
            $this->result->status = true;
            $this->result->status_code = 400;
            $this->result->message =
                "An Error Ocurred, we couldn't fetch all the faqs";
            return response()->json($this->result);
        }

        $this->result->status = true;
        $this->result->status_code = 200;
        $this->result->data = $fetch_faqs;
        $this->result->message = 'Dealer FAQs fetched Successfully';
        return response()->json($this->result);
    }

    public function fetch_all_faqs()
    {
        $fetch_faqs = Faq::orderBy('id', 'desc')
            ->where('status', 1)
            ->get();

        if (!$fetch_faqs) {
            $this->result->status = true;
            $this->result->status_code = 400;
            $this->result->message =
                "An Error Ocurred, we couldn't fetch all the faqs";
            return response()->json($this->result);
        }

        $this->result->status = true;
        $this->result->status_code = 200;
        $this->result->data = $fetch_faqs;
        $this->result->message = 'FAQs fetched Successfully';
        return response()->json($this->result);
    }
}
