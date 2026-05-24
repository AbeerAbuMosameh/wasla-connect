<?php

use App\Http\Controllers\MeetingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('meetings.index'));
Route::resource('meetings', MeetingController::class);
