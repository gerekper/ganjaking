<?php

use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Project_Create_Capability;

$router = Router::singleton();

$router->get( 'woo-project/products', 'WeDevs\PM_Pro\Modules\Woo_Project\Src\Controllers\Woo_Project_Controller@search_products' )
    ->permission( ['WeDevs\PM\Core\Permissions\Project_Create_Capability'] );

$router->get( 'woo-project/project', 'WeDevs\PM_Pro\Modules\Woo_Project\Src\Controllers\Woo_Project_Controller@search_project' )
    ->permission( ['WeDevs\PM\Core\Permissions\Project_Create_Capability'] );
