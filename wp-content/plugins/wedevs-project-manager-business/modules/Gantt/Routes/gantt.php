<?php
use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM_Pro\Modules\Gantt\Core\Permission\Permission;
use WeDevs\PM\Core\Permissions\Access_Project;


$router = Router::singleton();

$router->post( 'projects/{project_id}/gantt', 'WeDevs\PM_Pro\Modules\Gantt\Src\Controllers\Gantt_Controller@store' )
->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );
$router->post( 'projects/{project_id}/gantt/{link_id}/delete', 'WeDevs\PM_Pro\Modules\Gantt\Src\Controllers\Gantt_Controller@destroy' )
->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );
