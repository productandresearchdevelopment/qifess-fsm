<?php

use App\Http\Controllers\Main;
use App\SystemModels\Auth\User;
use Illuminate\Http\Request;

Main::routes();

Route::view('/privacy-policies', 'public/privacy-policies');

Route::get('/mobile/redirect/login', function (Request $request) {
    $username = $request->input('username');
    $password = $request->input('password');
    $user = User::where(['username' => $username])->first();
    if ($user) {
        if (Hash::check($password, $user->password)) {
            Auth::guard()->login($user);
            $wo = route('wo');
            return redirect($wo);
        }
        return 'Your Password Is Wrong';
    }
    return 'User Notfound';
});

Route::group(['middleware' => ['auth', 'roles']], function () {
    // EXTENDS USER ROUTE ----------------------------------------------------------------------------
    Route::group(['prefix' => 'sys'], function () {
        Route::group(['prefix' => 'user'], function () {
            Route::get('/data/fieldtech', '\App\Http\Controllers\Systems\User@dataFieldtech')->name('auth.user.data.fieldtech');
        });

        Route::get('/dev/test/push_api', '\App\Controllers\WorkOrders\WorkOrder@testApi')->name('sys.dev.test.push_api');
    });

    // VENDOR ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'vendor'], function () {
        Route::get('/', 'Vendors\Vendor@index')->name('vendor');
        Route::get('/data', 'Vendors\Vendor@data')->name('vendor.data');
        Route::get('/export/excel', 'Vendors\Vendor@exportExcel')->name('vendor.export.excel');
        Route::get('/export/format/import', 'Vendors\Vendor@importFormat')->name('vendor.export.excel.format.import');
        Route::post('/import', 'Vendors\Vendor@importData')->name('vendor.import');
        Route::post('/push/{id?}',  'Vendors\Vendor@push')->name('vendor.push');
        Route::put('/restore', 'Vendors\Vendor@restore')->name('vendor.restore');
        Route::delete('/delete', 'Vendors\Vendor@delete')->name('vendor.delete');
        Route::delete('/forcedelete', 'Vendors\Vendor@forceDelete')->name('vendor.forcedelete');
    });

    // CLIENT ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'client'], function () {
        Route::get('/', 'Clients\Client@index')->name('client');
        Route::get('/data', 'Clients\Client@data')->name('client.data');
        Route::get('/export/excel', 'Clients\Client@exportExcel')->name('client.export.excel');
        Route::get('/export/format/import', 'Clients\Client@importFormat')->name('client.export.excel.format.import');
        Route::post('/import', 'Clients\Client@importData')->name('client.import');
        Route::post('/push/{id?}', 'Clients\Client@push')->name('client.push');
        Route::put('/restore', 'Clients\Client@restore')->name('client.restore');
        Route::delete('/delete', 'Clients\Client@delete')->name('client.delete');
        Route::delete('/forcedelete', 'Clients\Client@forceDelete')->name('client.forcedelete');
    });

    // SITE ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'site'], function () {
        Route::get('/', 'Sites\Site@index')->name('site');
        Route::get('/data', 'Sites\Site@data')->name('site.data');
        Route::get('/export/excel', 'Sites\Site@exportExcel')->name('site.export.excel');
        Route::get('/export/format/import', 'Sites\Site@importFormat')->name('site.export.excel.format.import');
        Route::post('/import', 'Sites\Site@importData')->name('site.import');
        // Route::post('/push/{id?}', 'Sites\Site@push')->name('site.push');
        Route::post('/push', 'Sites\Site@push')->name('site.create');
        Route::post('/push/{id?}', 'Sites\Site@push')->name('site.edit');
        Route::put('/restore', 'Sites\Site@restore')->name('site.restore');
        Route::delete('/delete', 'Sites\Site@delete')->name('site.delete');
        Route::delete('/forcedelete', 'Sites\Site@forceDelete')->name('site.forcedelete');
    });


    // SERVICE ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'service'], function () {
        Route::get('/', 'Services\Service@index')->name('service');
        Route::get('/data', 'Services\Service@data')->name('service.data');
        Route::get('/export/excel', 'Services\Service@exportExcel')->name('service.export.excel');
        Route::get('/export/format/import', 'Services\Service@importFormat')->name('service.export.excel.format.import');
        Route::post('/import', 'Services\Service@importData')->name('service.import');
        Route::post('/push/{id?}', 'Services\Service@push')->name('service.push');
        Route::put('/restore', 'Services\Service@restore')->name('service.restore');
        Route::delete('/delete', 'Services\Service@delete')->name('service.delete');
        Route::delete('/forcedelete', 'Services\Service@forceDelete')->name('service.forcedelete');
    });

    // FIELDTECH ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'fieldtech'], function () {
        Route::get('/', 'Fieldtechs\Fieldtech@index')->name('fieldtech');
        Route::get('/data', 'Fieldtechs\Fieldtech@data')->name('fieldtech.data');
        Route::get('/export/excel', 'Fieldtechs\Fieldtech@exportExcel')->name('fieldtech.export.excel');
        Route::get('/export/format/import', 'Fieldtechs\Fieldtech@importFormat')->name('fieldtech.export.excel.format.import');
        Route::post('/import', 'Fieldtechs\Fieldtech@importData')->name('fieldtech.import');
        Route::post('/push/{id?}', 'Fieldtechs\Fieldtech@push')->name('fieldtech.push');
        Route::put('/restore', 'Fieldtechs\Fieldtech@restore')->name('fieldtech.restore');
        Route::delete('/delete', 'Fieldtechs\Fieldtech@delete')->name('fieldtech.delete');
        Route::delete('/forcedelete', 'Fieldtechs\Fieldtech@forceDelete')->name('fieldtech.forcedelete');
    });

    // WORK SCHEDULE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'work/schedule'], function () {
        Route::get('/', 'WorkSchedule@index')->name('workschedule');
        Route::get('/data', 'WorkSchedule@data')->name('workschedule.data');
    });

    // SPAREPART ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'part'], function () {
        Route::get('/page/{id?}', 'Parts\Part@index')->name('part');
        Route::get('/data/{id?}', 'Parts\Part@data')->name('part.data');
        Route::get('/export/excel', 'Parts\Part@exportExcel')->name('part.export.excel');
        Route::post('/push/{id?}', 'Parts\Part@push')->name('part.push');
        Route::delete('/delete', 'Parts\Part@delete')->name('part.delete');
    });

    // WO ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'wo'], function () {
        Route::get('/', 'WorkOrders\WorkOrder@index')->name('wo');
        Route::get('/archive', 'WorkOrders\WorkOrder@archive')->name('wo.archive');
        Route::get('/get/{id?}', 'WorkOrders\WorkOrder@get')->name('wo.get');
        Route::get('/detail/{id?}', 'WorkOrders\WorkOrder@detail')->name('wo.detail');
        Route::get('/data', 'WorkOrders\WorkOrder@data')->name('wo.data');
        Route::get('/data/archive', 'WorkOrders\WorkOrder@dataArchive')->name('wo.data.archive');
        Route::get('/data/site', 'WorkOrders\WorkOrder@dataSite')->name('wo.data.site');
        Route::get('/data/fieldtech', 'WorkOrders\WorkOrder@dataFieldtech')->name('wo.data.fieldtech');
        Route::get('/form/{id?}', 'WorkOrders\WorkOrder@form')->name('wo.form');
        Route::post('/push/wo', 'WorkOrders\WorkOrder@push')->name('wo.create');
        Route::post('/push/wo/api/{id?}', 'WorkOrders\WorkOrder@reloadTicket')->name('wo.push.api');
        Route::post('/push/wo/{id?}', 'WorkOrders\WorkOrder@push')->name('wo.edit');
        Route::post('/push/action/{wo?}/{status?}', 'WorkOrders\WorkOrder@pushAction')->name('wo.push.action');
        Route::post('/push/part/{id?}', 'WorkOrders\WorkOrder@pushPart')->name('wo.push.part');
        Route::delete('/delete', 'WorkOrders\WorkOrder@delete')->name('wo.delete');
        Route::delete('/delete/part', 'WorkOrders\WorkOrder@deletePart')->name('wo.delete.part');
        Route::delete('/delete/action/{id?}', 'WorkOrders\WorkOrder@deleteAction')->name('wo.delete.action');
        Route::get('/export/excel', 'WorkOrders\WorkOrder@exportExcel')->name('wo.export.excel');
        Route::get('/export/pdf/{id?}', 'WorkOrders\WorkOrder@exportPdf')->name('wo.export.pdf');
        Route::get('/export/balap/{id?}', 'WorkOrders\WorkOrder@exportBalapPdf')->name('wo.export.balap');
    });

    // REPORTING ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'reports'], function () {
        Route::get('/wo/pdf/{id?}', 'Reports\Report@woPdf')->name('report.wo.pdf');
        Route::get('/bast/pdf/{id?}', 'Reports\Report@bastPdf')->name('report.bast.pdf');
    });

    // DASHBOARD ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', 'Dashboards\Dashboard@index')->name('dashboard');
    });

    Route::group(['prefix' => 'maps'], function () {
        Route::get('/', 'Maps\Map@index')->name('map');
        Route::get('/wo/{site_id}', 'Maps\Map@getWorkOrders')->name('map.workorders');
        Route::get('/sites', 'Maps\Map@getSites')->name('map.sites');
        Route::get('/summary', 'Maps\Map@summary')->name('map.summary');
        Route::get('/summary/site/{site_id}', 'Maps\Map@siteSummary')->name('map.summary.site');
        Route::get('/export/excel', 'Maps\Map@exportExcel')->name('map.export.excel');
        Route::get('/export/raw/excel', 'Maps\Map@exportRawExcel')->name('map.export.raw.excel');
    });

    Route::group(['prefix' => 'listvendor'], function () {
        Route::get('/', 'ListVendor\ListVendor@index')->name('listvendor');
        Route::get('/data', 'ListVendor\ListVendor@data')->name('listvendor.data');
        Route::post('/push/{id?}', 'ListVendor\ListVendor@push')->name('listvendor.push');
        Route::put('/restore', 'ListVendor\ListVendor@restore')->name('listvendor.restore');
        Route::delete('/delete', 'ListVendor\ListVendor@delete')->name('listvendor.delete');
        Route::delete('/forcedelete', 'ListVendor\ListVendor@forceDelete')->name('listvendor.forcedelete');
    });
});
