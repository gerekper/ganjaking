<?php

use NinjaTablesPro\App\Http\Controllers\PermissionController;
use NinjaTablesPro\App\Http\Controllers\SortableController;
use NinjaTablesPro\App\Http\Controllers\CustomFilterController;

/**
 * @var $router NinjaTables\Framework\Http\Router
 */

$router->prefix('pro')->group(function ($route) {
    $route->post('permission', [PermissionController::class, 'store']);

    $route->withPolicy('NinjaTablesPro\App\Http\Policies\UserPolicy')->group(function ($route) {
        $route->group(['prefix' => 'sortable'], function ($route) {
            $route->post('/init', [SortableController::class, 'init']);
            $route->post('/', [SortableController::class, 'store']);
        });

        $route->group(['prefix' => 'custom-filter'], function ($route) {
            $route->get('', [CustomFilterController::class, 'index']);
            $route->post('/', [CustomFilterController::class, 'store']);
        });
    });

});
