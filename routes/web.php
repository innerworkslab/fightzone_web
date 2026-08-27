<?php

use Illuminate\Support\Facades\Route;

Route::view('/privacy-policies', 'public.privacy-policies')->name('privacy-policies');
Route::view('/contact-us', 'public.contact-us')->name('contact-us');

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
