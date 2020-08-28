<?php

use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Authentic;
$router = Router::singleton();


$router->get( 'license/check', 'WeDevs/PM_Pro/Update/Controllers/Update_Controller@index' )
    ->permission(['WeDevs\PM\Core\Permissions\Authentic']);

$router->post( 'license/activation', 'WeDevs/PM_Pro/Update/Controllers/Update_Controller@manage_license' )
    ->permission(['WeDevs\PM\Core\Permissions\Authentic']);

$router->post( 'license/delete', 'WeDevs/PM_Pro/Update/Controllers/Update_Controller@delete_license' )
    ->permission(['WeDevs\PM\Core\Permissions\Authentic']);

