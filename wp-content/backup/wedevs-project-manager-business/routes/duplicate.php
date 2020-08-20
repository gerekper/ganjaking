<?php

use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM_Pro\Core\Permissions\Administrator;
use WeDevs\PM\Core\Permissions\Project_Create_Capability;
$router = Router::singleton();

$router->post( 'duplicate/project/{id}', 'WeDevs/PM_Pro/Duplicate/Controllers/Duplicate_Controller@project_duplicate' )
    ->permission(['WeDevs\PM\Core\Permissions\Project_Create_Capability']);

$router->get( 'duplicate/list/{id}', 'WeDevs/PM_Pro/Duplicate/Controllers/Duplicate_Controller@list_duplicate' )
    ->permission(['WeDevs\PM\Core\Permissions\Project_Create_Capability']);
