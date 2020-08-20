<?php

use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Authentic;
$router = Router::singleton();


$router->get( 'search_by_client', 'WeDevs/PM_Pro/Search/Controllers/Search_Controller@search_by_client' )
    ->permission(['WeDevs\PM\Core\Permissions\Authentic']);
$router->get( 'search_all', 'WeDevs/PM_Pro/Search/Controllers/Search_Controller@pm_search' )
    ->permission(['WeDevs\PM\Core\Permissions\Authentic']);