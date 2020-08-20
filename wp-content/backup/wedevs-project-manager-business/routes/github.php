<?php

use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Authentic;
$router = Router::singleton();

$router->get( 'integrations/{project_id}', 'WeDevs/PM_Pro/Integrations/Controllers/Integrations_Controller@index' );
$router->post( 'integrations/{project_id}', 'WeDevs/PM_Pro/Integrations/Controllers/Integrations_Controller@index' );