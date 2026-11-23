<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(base_path('routes/V1/api.php'));
Route::prefix('v2')->group(base_path('routes/V2/api.php'));
