<?php

use App\Http\Controllers\Api\BookApiController;
use Illuminate\Support\Facades\Route;

// S2-03: Endpoint API untuk Katalog Buku Bookself (Format JSON)
Route::get('/books', [BookApiController::class, 'index']);
