<?php

use Illuminate\Support\Facades\Route;

Route::group([], base_path('routes/V2/api/public.php'));
Route::prefix('admin')
    ->group(base_path('routes/V2/api/admin.php'));
