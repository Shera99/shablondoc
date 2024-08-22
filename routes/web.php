<?php

use App\Events\NewOrder;
use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.shablondoc.pages.dashboard');
});

Route::get('/websocket', [ProfileController::class, 'websocket']);
Route::get('/websocket-create', function () {
    $order = \App\Models\Order::first();
    //NewOrder::dispatch($order);
    broadcast(new NewOrder(['type' => 'hello world']))->toOthers();
    //event(new NewOrder('hello world'));
});
