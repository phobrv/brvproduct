<?php
Route::middleware(['web', 'auth', 'auth:sanctum', 'lang', 'verified'])->namespace('Phobrv\BrvCore\Http\Controllers')->group(function () {
	Route::middleware(['can:product_manage'])->prefix('admin')->group(function () {
		Route::resource('term/brand', 'TermController');
		Route::resource('term/product', 'TermController');
	});
});

Route::middleware(['web', 'auth', 'auth:sanctum', 'lang', 'verified'])->namespace('Phobrv\BrvProduct\Controllers')->group(function () {
	Route::middleware(['can:product_manage'])->prefix('admin')->group(function () {
		Route::resource('productitem', 'ProductController');
		Route::post('/productitem/updateUserSelectGroup', 'ProductController@updateUserSelectGroup')->name('productitem.updateUserSelectGroup');
		Route::post('/productitem/deleteMetaAPI', 'ProductController@deleteMetaAPI')->name('productitem.deleteMetaAPI');
		Route::post('/productitem/uploadGallery', 'ProductController@uploadGallery')->name('productitem.uploadGallery');

	});
});
