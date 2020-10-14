<?php
use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Authentic;
use WeDevs\PM\Core\Permissions\Access_Project;
use WeDevs\PM\Core\Permissions\Project_Manage_Capability;
use WeDevs\PM_Pro\Modules\invoice\core\Permission\Payment;

$router = Router::singleton();

$router->get( 'projects/{project_id}/invoice', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@index' )
->permission( ['WeDevs\PM\Core\Permissions\Project_Manage_Capability'] );

$router->get( 'invoices', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@index' )
->permission( ['WeDevs\PM\Core\Permissions\Authentic'] );

$router->get( 'projects/{project_id}/invoice/{invoice_id}', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@show' )
->permission( ['WeDevs\PM\Core\Permissions\Project_Manage_Capability'] );

$router->get( 'projects/{project_id}/invoice/{invoice_id}/pdf', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@PDF' )
->permission( ['WeDevs\PM\Core\Permissions\Project_Manage_Capability'] );

$router->post( 'projects/{project_id}/invoice/{invoice_id}', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@update' )
->permission( ['WeDevs\PM\Core\Permissions\Project_Manage_Capability'] );

$router->post( 'projects/{project_id}/invoice/{invoice_id}/payment', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@payment' )
->permission( ['WeDevs\PM\Core\Permissions\Project_Manage_Capability', 'WeDevs\PM_Pro\Modules\invoice\core\Permission\Payment'] );

$router->post( 'projects/{project_id}/invoice/{invoice_id}/payment-validation', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@payment_validation' );

$router->delete( 'projects/{project_id}/invoice/{invoice_id}', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@destroy' )
->permission( ['WeDevs\PM\Core\Permissions\Project_Manage_Capability'] );

$router->post( 'projects/{project_id}/invoice/{invoice_id}/mail', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@mail' )
->permission( ['WeDevs\PM\Core\Permissions\Project_Manage_Capability'] );

$router->post( 'invoice/address', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@address' )
->permission( ['WeDevs\PM\Core\Permissions\Authentic'] );

$router->get( 'invoice/user-address/user/{user_id}', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@get_user_address' )
->permission( ['WeDevs\PM\Core\Permissions\Authentic'] );

$router->post( 'invoice/user-address/user/{user_id}', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@save_user_address' )
->permission( ['WeDevs\PM\Core\Permissions\Authentic'] );

$router->post( 'projects/{project_id}/invoice', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@store' )
->permission( ['WeDevs\PM\Core\Permissions\Project_Manage_Capability'] );

$router->post( 'projects/{project_id}/invoice/{invoice_id}/gateway_payment', 'WeDevs\PM_Pro\Modules\invoice\src\Controllers\Invoice_Controller@gateway_payment' )
->permission( ['WeDevs\PM\Core\Permissions\Project_Manage_Capability'] );
