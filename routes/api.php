<?php

use App\Http\Controllers\Integration\FakeStoreSyncController;
use App\Http\Controllers\Catalog\ProductController;
use App\Http\Controllers\Catalog\CategoryController;
use App\Http\Controllers\Catalog\StatsController;
use App\Http\Middleware\IntegrationClientMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([IntegrationClientMiddleware::class])->group(function () {
    // Integração FakeStore
    Route::post('/integracoes/fakestore/sync', [FakeStoreSyncController::class, 'sync']);

    Route::group(['prefix' => '/catalogo'], function(){

        Route::get('/stats', [StatsController::class, 'index']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::group(['prefix' => '/products'], function(){
            Route::get('/', [ProductController::class, 'index']);
            Route::get('/{id}', [ProductController::class, 'show']);
            Route::delete('/all', [ProductController::class, 'deleteAll']);
        });
    });

});
