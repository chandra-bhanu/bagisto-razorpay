<?php
Route::group(['middleware' => ['web']], function () {
    Route::prefix('razorpay/payment')->group(function () {

        Route::get('/redirect', 'Neexpg\Razorpay\Http\Controllers\RazorpayController@redirect')->name('razorpay.payment.redirect');

        Route::post('/success', 'Neexpg\Razorpay\Http\Controllers\RazorpayController@paymentSuccess')->name('razorpay.payment.success');
        Route::get('/fail', 'Neexpg\Razorpay\Http\Controllers\RazorpayController@paymentFail')->name('razorpay.payment.fail');
        
        Route::post('/razorpay-hook','Neexpg\Razorpay\Http\Controllers\RazorpayController@verifyPaymentHook')->name('razorpay.payment.hook');
    });
});