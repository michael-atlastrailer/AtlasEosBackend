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
            '/fetch-all-dealers-in-seminar',
            'SeminarController@fetch_all_dealers_in_seminar'
        );
        Route::get(
            '/fetch-all-dealers-not-in-seminar',
            'SeminarController@fetch_all_dealers_not_in_seminar'
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
            '/edit-promotional-flier/{id}',
            'PromotionalFlierController@edit_promotional_flier'
        );
        Route::get(
            '/delete-promotional-flier/{id}',
            'PromotionalFlierController@delete_promotional_flier'
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
