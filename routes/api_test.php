<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-route', function() {
    return ['message' => 'API test route works!'];
});