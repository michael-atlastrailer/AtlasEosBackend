<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

///////////////// Admin /////////////
Route::group(
    [
        'namespace' => 'App\Http\Controllers',
        ///  'middleware' => 'cors',
    ],
    function () {
        Route::post('/admin-login', 'AdminController@admin_login');
        Route::get('/admin-dashboard', 'AdminController@dashboard');
        Route::get(
            '/get-all-vendor-users',
            'AdminController@get_all_vendor_users'
        );

        Route::post('/upload-users', 'AdminController@upload_users');
        Route::post('/upload-vendors', 'AdminController@upload_vendors');
        Route::post('/register-vendors', 'AdminController@register_vendors');
        Route::post(
            '/register-vendor-users',
            'AdminController@register_vendor_users'
        );

        Route::post(
            '/register-dealer-users',
            'AdminController@register_dealer_users'
        );

        Route::get(
            '/get-all-vendor-users',
            'AdminController@get_all_vendor_users'
        );

        Route::post(
            '/upload-vendor-users',
            'AdminController@upload_vendor_users'
        ); # working fine
        Route::post('/upload-admin', 'AdminController@upload_admin'); # working fine
        Route::post('/edit-vendor-data', 'AdminController@edit_vendor_data');
        Route::get(
            '/deactivate-vendor/{id}',
            'AdminController@deactivate_vendor'
        );

        Route::get('/activate-vendor/{id}', 'AdminController@activate_vendor');

        Route::get(
            '/deactivate-vendor-user/{id}',
            'AdminController@deactivate_vendor_user'
        );
        Route::get(
            '/activate-vendor-user/{id}',
            'AdminController@activate_vendor_user'
        );

        Route::get('/get-vendor-user/{id}', 'AdminController@get_vendor_user');

        // Route::post('/upload-dealers', 'AdminController@upload_dealers');

        Route::post(
            '/edit-vendor-user',
            'AdminController@edit_vendor_user_data'
        );

        Route::post(
            '/upload-dealer-users',
            'AdminController@upload_dealer_users'
        );

        Route::get(
            '/get-all-dealer-users',
            'AdminController@get_all_dealer_users'
        );

        Route::get(
            '/deactivate-dealer-user/{id}',
            'AdminController@deactivate_dealer_user'
        );

        Route::post(
            '/upload-product-csv',
            'AdminController@upload_product_csv'
        );

        Route::get('/all-products', 'AdminController@get_all_products');
        Route::get(
            '/deactivate-product/{id}',
            'AdminController@deactivate_product'
        );

        Route::get('/get-product/{id}', 'AdminController@get_product');
        Route::get(
            '/get-product-atlas-id/{id}',
            'AdminController@get_product_by_atlas_id'
        );

        Route::post('/edit-product', 'AdminController@edit_product');
        Route::post('/add-product', 'AdminController@add_product');

        // Route::get(
        //     '/deactivate-product/{id}',
        //     'AdminController@deactivate_product'
        // );

        Route::post('/upload-admin-users', 'AdminController@upload_admin_csv');

        Route::post(
            '/register-admin-users',
            'AdminController@register_admin_users'
        );

        Route::get('/all-admins', 'AdminController@get_all_admins');
        Route::get(
            '/deactivate-admin/{id}',
            'AdminController@deactivate_admin'
        );

        Route::post('/create-faq', 'AdminController@create_faq');
        Route::post('/edit-faq', 'AdminController@edit_faq');

        Route::post('/create-seminar', 'AdminController@create_seminar');

        Route::get('/get-all-seminar', 'AdminController@get_all_seminar');

        Route::get('/deactivate-faq/{id}', 'AdminController@deactivate_faq');

        Route::get('/get-faq-id/{id}', 'AdminController@get_faq_id');

        Route::post('/save-chat-id', 'AdminController@save_chat_id');

        Route::get('/get-all-users', 'AdminController@get_all_users');

        Route::get(
            '/get-all-admin-users/{user}',
            'AdminController@get_all_admin_users'
        );

        Route::get(
            '/admin/get-vendor-unread-msg/{user}',
            'AdminController@get_vendor_unread_msg'
        );

        Route::get(
            '/admin/get-dealer-unread-msg/{user}',
            'AdminController@get_dealer_unread_msg'
        );

        Route::get('/get-all-reports', 'AdminController@get_all_reports');
        Route::get('/get-all-reports/{user_id}', 'AdminController@fetch_reports_by_user_id');



        // get_all_reports
        Route::post('/save-countdown', 'AdminController@save_countdown');

        Route::get('/get-countdown', 'AdminController@get_countdown');

        Route::get(
            '/admin/get-report-problem/{ticket}',
            'AdminController@get_report_problem'
        );

        Route::post(
            '/admin/reply-report',
            'AdminController@admin_reply_report'
        );

        Route::get(
            '/admin/all-promotional-flyer',
            'AdminController@get_all_promotional_flyer'
        );

        Route::get(
            '/admin/get-promotional-flyer/{id}',
            'AdminController@get_current_promotional_flier'
        );

        Route::get(
            '/admin/get-seminar-id/{id}',
            'AdminController@get_seminar_by_id'
        );

        Route::post('/edit-seminar', 'AdminController@edit_seminar');

        Route::get('/testing', 'AdminController@testing_api');
    }
);

///////////////// Users (DEALERS AND VENDORS) /////////////
Route::group(
    [
        'namespace' => 'App\Http\Controllers',
        /////'middleware' => 'cors'
    ],
    function () {
        Route::post('/login', 'UserController@login');
        Route::get('/get-all-vendors', 'VendorController@get_all_vendors');
        Route::post('/create-report', 'DealerController@create_report');
        Route::get('/fetch-all-faqs', 'DealerController@fetch_all_faqs');
        Route::get('/dealer-faqs', 'DealerController@dealer_faq');

        Route::get(
            '/get-vendor-products/{code}',
            'DealerController@get_vendor_products'
        );

        Route::get(
            '/dealer/get-orders-by-vendor/{code}',
            'DealerController@get_editable_orders_by_vendor'
        );

        Route::get(
            '/dealer/get-ordered-vendor/{code}',
            'DealerController@get_ordered_vendor'
        );

        Route::get(
            '/dealer/delete-item-cart/{dealer}/{vendor}',
            'DealerController@delete_item_cart'
        );

        Route::post(
            '/dealer/edit-cart-order',
            'DealerController@edit_dealer_order'
        );

        Route::get(
            '/quick-order-filter-atlasid/{id}',
            'DealerController@quick_order_filter_atlasid'
        );

        Route::post('/add-item-to-cart', 'DealerController@add_item_cart');

        Route::get(
            '/universal-search/{search}',
            'DealerController@universal_search'
        );

        Route::get(
            '/dealer-dashboard/{account}',
            'DealerController@dealer_dashboard'
        );

        //---------------------- seminar apis here -------------------- //
        // Route::post('/create-seminar', 'SeminarController@create_seminar');
        Route::get(
            '/fetch-all-seminars',
            'SeminarController@fetch_all_seminars'
        );
        Route::get(
            '/fetch-scheduled-seminars',
            'SeminarController@fetch_scheduled_seminars'
        );
        Route::get(
            '/fetch-ongoing-seminars',
            'SeminarController@fetch_ongoing_seminars'
        );
        Route::get(
            '/fetch-watched-seminars',
            'SeminarController@fetch_watched_seminars'
        );
        Route::post('/join-seminar', 'SeminarController@join_seminar');

        Route::get(
            '/fetch-all-dealers-in-seminar/{seminar_id}',
            'SeminarController@fetch_all_dealers_in_seminar'
        );

        Route::get(
            '/fetch_only_dealer_emails/{seminar_id}',
            'SeminarController@fetch_only_dealer_emails'
        );

        // ------------------promotional flier ------------------//

        Route::post(
            '/create-promotional-flier',
            'PromotionalFlierController@create_promotional_flier'
        );
        Route::get(
            '/show-all-promotional-fliers',
            'PromotionalFlierController@show_all_promotional_fliers'
        );
        Route::get(
            '/show-promotional-flier-by-id/{id}',
            'PromotionalFlierController@show_promotional_flier_by_id'
        );
        Route::get(
            '/show-promotional-flier-by-vendor-id/{vendor_id}',
            'PromotionalFlierController@show_promotional_flier_by_vendor_id'
        );

        // hshkdksjdsd
        // sdjsjfkjds
        Route::patch(
            '/edit-promotional-flier/{id}',
            'PromotionalFlierController@edit_promotional_flier'
        );
        
        Route::delete(
            '/delete-promotional-flier/{id}',
            'PromotionalFlierController@delete_promotional_flier'
        );
        // ------------------ new products --------------------//
        Route::get(
            '/products/new',
            'ProductsController@fetch_all_new_products'
        );
        Route::get(
            '/products/new/vendor_id/{vendor_id}',
            'ProductsController@sort_newproduct_by_vendor_id'
        );
        Route::get(
            '/products/new/atlas_id/{atlas_id}',
            'ProductsController@sort_newproduct_by_atlas_id'
        );
        Route::get(
            '/get-product-by-vendor-id/{vendor_id}',
            'ProductsController@fetch_all_products_by_vendor_id'
        );
        Route::get(
            '/get-all-new-products',
            'ProductsController@fetch_all_new_products'
        );

        Route::get(
            '/vendor/get-vendor-coworkers/{code}/{user}',
            'VendorController@get_vendor_coworkers'
        );

        Route::get(
            '/dealer/get-dealer-coworkers/{code}/{user}',
            'DealerController@get_dealer_coworkers'
        );
        Route::get(
            '/sort-newproduct-by-vendor-id/{vendor_id}',
            'ProductsController@sort_newproduct_by_vendor_id'
        );
        Route::get(
            '/sort-newproduct-by-atlas-id/{atlas_id}',
            'ProductsController@sort_newproduct_by_atlas_id'
        );

        Route::get(
            '/vendor/get-dealers',
            'VendorController@get_distinct_dealers'
        );

        Route::get(
            '/vendor/get-selected-company-dealers/{code}/{user}',
            'VendorController@get_company_dealer_users'
        );

        Route::get(
            '/vendor/get-vendor-unread-msg/{user}',
            'VendorController@get_vendor_unread_msg'
        );

        Route::get('/dealer/get-vendors', 'DealerController@get_vendor');

        Route::get(
            '/dealer/get-selected-company-vendor/{code}/{user}',
            'DealerController@get_company_vendor_users'
        );

        Route::get(
            '/dealer/get-dealer-unread-msg/{user}',
            'DealerController@get_dealer_unread_msg'
        );

        Route::get(
            '/vendor/get-privileged-vendors/{user}/{code}',
            'VendorController@get_privileged_vendors'
        );

        Route::get(
            '/vendor/vendor-dashboard/{code}/{user}',
            'VendorController@vendor_dashboard'
        );

        Route::get(
            '/vendor/get-vendor-products/{code}',
            'VendorController@get_vendors_products'
        );

        Route::get(
            '/cart/dealer/{dealer_id}',
            'DealerController@fetch_all_cart_items'
        );

        // adds item to the quick order
        Route::post(
            '/quick_order',
            'DealerController@add_quick_order');

        Route::get(
            '/vendor/get-sales-by-item-summary/{code}',
            'VendorController@sales_by_item_summary'
        );

        Route::get(
            '/vendor/get-sales-by-item-detailed/{code}',
            'VendorController@sales_by_item_detailed'
        );

        Route::get(
            '/vendor/get-purchases-dealers/{code}',
            'VendorController@get_purchases_dealers'
        );

        Route::get('/vendor/get-vendor-faq', 'VendorController@get_vendor_faq');

        Route::get(
            '/seminars/remind',
            'SeminarController@select_seminars_to_remind'
        );
        Route::get(
            '/promotional_fliers/vendors',
            'PromotionalFlierController@get_all_vendors_with_promotional_fliers'
        );

        // move quick order to cart
        Route::post(
            '/move-quick-order',
            'DealerController@move_quick_order_to_cart'
        );

    }
);

///////////////// Dealer /////////////
// Route::group(
//     ['namespace' => 'App\Http\Controllers', 'middleware' => 'cors'],
//     function () {
//         Route::post('/dealer-login', 'DealerController@login');
//     }
// );

///////////////// Branch /////////////
Route::group(
    ['namespace' => 'App\Http\Controllers', 'middleware' => 'cors'],
    function () {
        Route::post('/branch-login', 'BranchController@login');
    }
);

///////////////// Chat /////////////
Route::group(
    ['namespace' => 'App\Http\Controllers', 'middleware' => 'cors'],
    function () {
        Route::post('/store-chat', 'ChatController@store_chat');
        Route::get(
            '/get-user-chat/{receiver}/{sender}',
            'ChatController@get_user_chat'
        );

        Route::get('/testing-chat', 'ChatController@testing_chat');
    }
);
