<?php

use App\Http\Controllers\Admin;

/*
  |--------------------------------------------------------------------------
  | Admin Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register Admin routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "admin" middleware group.
  |
 */

Route::resource('approvers', Admin\ApproverController::class)
    ->only(['index', 'store', 'update', 'destroy'])->parameters(['approvers' => 'department']);
Route::resource('funding-brands', Admin\FundingBrandController::class)
    ->only(['index', 'store', 'update', 'destroy'])->parameters(['fundingBrands' => 'reimDepartment']);
