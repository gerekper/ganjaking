<?php

use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Project_Manage_Capability;
$router = Router::singleton();

$router->get( 'progress', 'WeDevs/PM/Activity/Controllers/Activity_Controller@index' )
    ->permission(['WeDevs\PM_Pro\Core\Permissions\Progress_Page_Access']);
