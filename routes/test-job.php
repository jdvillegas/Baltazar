<?php

use App\Http\Controllers\TestJobController;
use Illuminate\Support\Facades\Route;

Route::get('/test-job', [TestJobController::class, 'runJob']);
