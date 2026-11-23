<?php

use Illuminate\Support\Facades\Route;

Route::group([], base_path('routes/V1/api/public.php'));
Route::prefix('admin')
    ->group(base_path('routes/V1/api/admin.php'));
