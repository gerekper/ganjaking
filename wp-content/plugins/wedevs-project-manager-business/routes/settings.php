<?php

use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Authentic;
$router = Router::singleton();


$router->post( 'projects/{project_id}/settings/labels', 'WeDevs/PM_Pro/Settings/Controllers/Settings_Controller@store_label' )
    ->permission(['WeDevs\PM\Core\Permissions\Authentic']);

$router->post( 'projects/{project_id}/settings/labels/{label_id}', 'WeDevs/PM_Pro/Settings/Controllers/Settings_Controller@update_label' )
    ->permission(['WeDevs\PM\Core\Permissions\Authentic']);

$router->post( 'projects/{project_id}/settings/labels/{label_id}/delete', 'WeDevs/PM_Pro/Settings/Controllers/Settings_Controller@destroy_label' )
    ->permission(['WeDevs\PM\Core\Permissions\Authentic']);

