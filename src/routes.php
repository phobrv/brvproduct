<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'auth:sanctum', 'lang', 'verified'])->namespace('Phobrv\BrvCore\Http\Controllers')->group(function () {
	Route::middleware(['can:product_manage'])->prefix('admin')->group(function () {
		Route::resource('brand', 'TermController');
		Route::resource('productgroup', 'TermController');
	});
});

Route::middleware(['web', 'auth', 'auth:sanctum', 'lang', 'verified'])->namespace('Phobrv\BrvProduct\Controllers')->group(function () {
	Route::middleware(['can:product_manage'])->prefix('admin')->group(function () {
		Route::resource('product', 'ProductController');
		Route::post('/product/updateUserSelectGroup', 'ProductController@updateUserSelectGroup')->name('product.updateUserSelectGroup');
		Route::post('/product/deleteMetaAPI', 'ProductController@deleteMetaAPI')->name('product.deleteMetaAPI');
		Route::post('/product/uploadGallery', 'ProductController@uploadGallery')->name('product.uploadGallery');
		Route::get('/product-getData', 'ProductController@getData')->name('product.getData');
		Route::post('/product/apiDelete', 'ProductController@apiDelete')->name('product.apiDelete');
		
	});
});
