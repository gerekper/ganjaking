<?php

use WeDevs\PM_Pro\Core\Router\Router;

$router = Router::singleton();

$router->get( 'tasks/{task_id}/sub-tasks', 'WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Controllers\Sub_Tasks_Controller@index' );

$router->get( 'tasks/{task_id}/sub-tasks/{sub_task_id}', 'WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Controllers\Sub_Tasks_Controller@show' );

$router->post( 'tasks/{task_id}/sub-tasks', 'WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Controllers\Sub_Tasks_Controller@store' )
    ->permission( ['WeDevs\PM_Pro\Modules\Sub_Tasks\Permissions\Create_Sub_Task'] )
    ->validator( 'WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Validators\Create_Sub_Task' )
    ->sanitizer( 'WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Sanitizers\Sub_Task_Sanitizer' );

$router->post( 'tasks/{task_id}/sub-tasks/{sub_task_id}/update', 'WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Controllers\Sub_Tasks_Controller@update' )
    ->permission( ['WeDevs\PM_Pro\Modules\Sub_Tasks\Permissions\Edit_Sub_Task'] )
    ->sanitizer( 'WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Sanitizers\Sub_Task_Sanitizer' );

$router->post( 'tasks/{task_id}/sub-tasks/{sub_task_id}/delete', 'WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Controllers\Sub_Tasks_Controller@destroy' )
    ->permission( ['WeDevs\PM_Pro\Modules\Sub_Tasks\Permissions\Edit_Sub_Task'] );

$router->post( 'tasks/{task_id}/sub-tasks/{sub_task_id}/make-task', 'WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Controllers\Sub_Tasks_Controller@subtask_to_task' )
    ->permission( ['WeDevs\PM_Pro\Modules\Sub_Tasks\Permissions\Edit_Sub_Task'] );

$router->post( 'sub-tasks/sorting', 'WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Controllers\Sub_Tasks_Controller@sorting' )
    ->permission( ['WeDevs\PM_Pro\Modules\Sub_Tasks\Permissions\Create_Sub_Task'] );

