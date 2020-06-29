<?php

use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM_Pro\Core\Permissions\Administrator;
use WeDevs\PM\Core\Permissions\Project_Manage_Capability;
$router = Router::singleton();

$router->get( 'module-lists', 'WeDevs/PM_Pro/Module_Lists/Controllers/Module_Lists_Controller@index' )
    ->permission(['WeDevs\PM\Core\Permissions\Project_Manage_Capability']);

$router->post( 'module-update', 'WeDevs/PM_Pro/Module_Lists/Controllers/Module_Lists_Controller@update' )
    ->permission(['WeDevs\PM\Core\Permissions\Project_Manage_Capability']);
