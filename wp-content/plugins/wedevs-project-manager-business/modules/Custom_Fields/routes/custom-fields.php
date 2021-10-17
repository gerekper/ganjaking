<?php
use WeDevs\PM_Pro\Core\Router\Router;

$router = Router::singleton();

$router->post( 'projects/{project_id}/custom-fields', 'WeDevs\PM_Pro\Modules\Custom_Fields\Src\Controllers\Custom_Field_Controller@store' )
    ->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] )
    ->sanitizer( 'WeDevs\PM_Pro\Modules\Custom_Fields\Src\Sanitizers\Custom_Field_Sanitizer' );

$router->post( 'projects/{project_id}/custom-fields/{field_id}/update', 'WeDevs\PM_Pro\Modules\Custom_Fields\Src\Controllers\Custom_Field_Controller@update' )
    ->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] )
    ->sanitizer( 'WeDevs\PM_Pro\Modules\Custom_Fields\Src\Sanitizers\Custom_Field_Sanitizer' );

$router->post( 'projects/{project_id}/custom-fields/{field_id}/tasks/{task_id}/update', 'WeDevs\PM_Pro\Modules\Custom_Fields\Src\Controllers\Custom_Field_Controller@ajax_store_field_value' )
    ->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );

$router->post( 'projects/{project_id}/custom-fields/{field_id}/delete', 'WeDevs\PM_Pro\Modules\Custom_Fields\Src\Controllers\Custom_Field_Controller@destroy' )
    ->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );

$router->get( 'projects/{project_id}/custom-fields', 'WeDevs\PM_Pro\Modules\Custom_Fields\Src\Controllers\Custom_Field_Controller@index' )
    ->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );
