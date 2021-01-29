<?php
use WeDevs\PM_Pro\Core\Router\Router;

$router = Router::singleton();

$router->post( 'projects/{project_id}/invoice/{invoice_id}/gateway_stripe', 'WeDevs\PM_Pro\Modules\Stripe\Src\Controllers\Stripe_Controller@gateway_stripe' )
->permission( ['WeDevs\PM\Core\Permissions\Access_Project', 'WeDevs\PM_Pro\Modules\Invoice\Core\Permission\Payment'] );
