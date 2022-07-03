<?php

use App\Http\Controllers\Main;
use Illuminate\Support\Facades\Route;

Main::routes();

Route::view('/privacy-policies', 'public/privacy-policies');
Route::group(['middleware' => ['auth','roles'] ], function(){
    // EXTENDS USER ROUTE ----------------------------------------------------------------------------
    Route::group(['prefix' => 'sys'], function (){
        Route::group(['prefix' => 'user'], function (){
            Route::get('/data/fieldtech', '\App\Http\Controllers\Systems\User@dataFieldtech')->name('auth.user.data.fieldtech');
        });
    });

    // VENDOR ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'vendor'], function (){
        Route::get('/', 'Vendors\Vendor@index')->name('vendor');
        Route::get('/data', 'Vendors\Vendor@data')->name('vendor.data');
        Route::post('/push/{id?}',  'Vendors\Vendor@push')->name('vendor.push');
        Route::delete('/delete', 'Vendors\Vendor@delete')->name('vendor.delete');
    });


    // CLIENT ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'client'], function (){
        Route::get('/', 'Clients\Client@index')->name('client');
        Route::get('/data', 'Clients\Client@data')->name('client.data');
        Route::post('/push/{id?}', 'Clients\Client@push')->name('client.push');
        Route::delete('/delete', 'Clients\Client@delete')->name('client.delete');
    });

    // SITE ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'site'], function (){
        Route::get('/', 'Sites\Site@index')->name('site');
        Route::get('/data', 'Sites\Site@data')->name('site.data');
        Route::get('/export/excel', 'Sites\Site@exportExcel')->name('site.export.excel');
        Route::post('/push/{id?}', 'Sites\Site@push')->name('site.push');
        Route::delete('/delete', 'Sites\Site@delete')->name('site.delete');
    });


    // SERVICE ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'service'], function (){
        Route::get('/', 'Services\Service@index')->name('service');
        Route::get('/data', 'Services\Service@data')->name('service.data');
        Route::post('/push/{id?}', 'Services\Service@push')->name('service.push');
        Route::delete('/delete', 'Services\Service@delete')->name('service.delete');
    });

    // FIELDTECH ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'fieldtech'], function (){
        Route::get('/', 'Fieldtechs\Fieldtech@index')->name('fieldtech');
        Route::get('/data', 'Fieldtechs\Fieldtech@data')->name('fieldtech.data');
        Route::post('/push/{id?}', 'Fieldtechs\Fieldtech@push')->name('fieldtech.push');
        Route::delete('/delete', 'Fieldtechs\Fieldtech@delete')->name('fieldtech.delete');
    });

    // WORK SCHEDULE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'work/schedule'], function (){
        Route::get('/', 'WorkSchedule@index')->name('workschedule');
        Route::get('/data', 'WorkSchedule@data')->name('workschedule.data');
    });

    // SPAREPART ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'part'], function (){
        Route::get('/page/{id?}', 'Parts\Part@index')->name('part');
        Route::get('/data/{id?}', 'Parts\Part@data')->name('part.data');
        Route::get('/export/excel', 'Parts\Part@exportExcel')->name('part.export.excel');
        Route::post('/push/{id?}', 'Parts\Part@push')->name('part.push');
        Route::delete('/delete', 'Parts\Part@delete')->name('part.delete');

    });

    // WO ROUTE ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'wo'], function (){
        Route::get('/', 'WorkOrders\WorkOrder@index')->name('wo');
        Route::get('/archive', 'WorkOrders\WorkOrder@archive')->name('wo.archive');
        Route::get('/get/{id?}', 'WorkOrders\WorkOrder@get')->name('wo.get');
        Route::get('/detail/{id?}', 'WorkOrders\WorkOrder@detail')->name('wo.detail');
        Route::get('/data', 'WorkOrders\WorkOrder@data')->name('wo.data');
        Route::get('/export/excel', 'WorkOrders\WorkOrder@exportExcel')->name('wo.export.excel');
        Route::get('/data/archive', 'WorkOrders\WorkOrder@dataArchive')->name('wo.data.archive');
        Route::get('/data/site', 'WorkOrders\WorkOrder@dataSite')->name('wo.data.site');
        Route::get('/data/fieldtech', 'WorkOrders\WorkOrder@dataFieldtech')->name('wo.data.fieldtech');
        Route::get('/form/{id?}', 'WorkOrders\WorkOrder@form')->name('wo.form');
        Route::post('/push/wo', 'WorkOrders\WorkOrder@push')->name('wo.create');
        Route::post('/push/wo/{id?}', 'WorkOrders\WorkOrder@push')->name('wo.edit');
        Route::post('/push/action/{wo?}/{status?}', 'WorkOrders\WorkOrder@pushAction')->name('wo.push.action');
        Route::post('/push/part/{id?}', 'WorkOrders\WorkOrder@pushPart')->name('wo.push.part');
        Route::delete('/delete', 'WorkOrders\WorkOrder@delete')->name('wo.delete');
        Route::delete('/delete/part', 'WorkOrders\WorkOrder@deletePart')->name('wo.delete.part');
    });

    // REPORTING ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'reports'], function (){
        Route::get('/wo/pdf/{id?}', 'Reports\Report@woPdf')->name('report.wo.pdf');
        Route::get('/bast/pdf/{id?}', 'Reports\Report@bastPdf')->name('report.bast.pdf');
    });

    // DASHBOARD ----------------------------------------------------------------------------------
    Route::group(['prefix' => 'dashboard'], function (){
        Route::get('/', 'Dashboards\Dashboard@index')->name('dashboard');
    });
});
