<?php
use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM_Pro\Modules\gantt\core\Permissions\Gantt;
use WeDevs\PM\Core\Permissions\Access_Project;


$router = Router::singleton();

$router->post( 'projects/{project_id}/gantt', 'WeDevs\PM_Pro\Modules\gantt\src\Controllers\Gantt_Controller@store' )
->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );
$router->post( 'projects/{project_id}/gantt/{link_id}/delete', 'WeDevs\PM_Pro\Modules\gantt\src\Controllers\Gantt_Controller@destroy' )
->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );
