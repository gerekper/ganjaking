<?php

use WeDevs\PM_Pro\Core\Router\Router;

$router = Router::singleton();

$router->post( 'projects/{project_id}/files/{file_id}', 'WeDevs/PM_Pro/File/Controllers/File_Controller@update' )
    ->permission( ['WeDevs\PM\Core\Permissions\Edit_File'] )
    ->sanitizer( 'WeDevs\PM_Pro\File\Sanitizers\File_Sanitizer' );

// $router->post( 'projects/{project_id}/files/{file_id}/delete', 'WeDevs/PM_Pro/File/Controllers/File_Controller@destroy' )
//     ->permission( ['WeDevs\PM\Core\Permissions\Edit_File'] );

$router->post( 'projects/{project_id}/files', 'WeDevs/PM_Pro/File/Controllers/File_Controller@store' )
    ->permission( ['WeDevs\PM\Core\Permissions\Create_File'] )
    ->sanitizer( 'WeDevs\PM_Pro\File\Sanitizers\File_Sanitizer' );

$router->get( 'projects/{project_id}/files', 'WeDevs/PM_Pro/File/Controllers/File_Controller@index' )
    ->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );

$router->post( 'projects/{project_id}/files/sorting', 'WeDevs/PM_Pro/File/Controllers/File_Controller@sorting' )
    ->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );

$router->get( 'projects/{project_id}/files/searchfolder', 'WeDevs/PM_Pro/File/Controllers/File_Controller@file_search' )
    ->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );

$router->get( 'projects/{project_id}/files/folders', 'WeDevs/PM_Pro/File/Controllers/File_Controller@get_all_folder' )
    ->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );
