<?php

use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Authentic;
$router = Router::singleton();

$router->get( 'calendar-events', 'WeDevs/PM_Pro/Calendar/Controllers/Calendar_Controller@index' )
    ->permission(['WeDevs\PM\Core\Permissions\Authentic']);

$router->get( 'calendar-projects', 'WeDevs/PM_Pro/Calendar/Controllers/Calendar_Controller@get_projects' )
    ->permission(['WeDevs\PM\Core\Permissions\Authentic']);

$router->get( 'calendar-resource', 'WeDevs/PM_Pro/Calendar/Controllers/Calendar_Controller@get_resource' )
    ->permission(['WeDevs\PM\Core\Permissions\Authentic']);
