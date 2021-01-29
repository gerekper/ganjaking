<?php

use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM_Pro\Modules\Time_Tracker\Src\Validators\Time_Tracker_Validator;
use WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions\Time_Start;
use WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions\Time_Stop;
use WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions\Time_Delete;
use WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions\Time_Add;
use WeDevs\PM\Core\Permissions\Project_Create_Capability;
use WeDevs\PM\Core\Permissions\Access_Project;

$router = Router::singleton();
//projects/{project_id}/lists/{list_id}/tasks/{task_id}/time
$router->post( 'time', 'WeDevs\PM_Pro\Modules\Time_Tracker\Src\Controllers\Time_Tracker_Controller@store' )
	->permission( ['WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions\Time_Start'] );

$router->post( 'time/update', 'WeDevs\PM_Pro\Modules\Time_Tracker\Src\Controllers\Time_Tracker_Controller@update' )
    ->permission( ['WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions\Time_Stop'] );

$router->post( 'time/{time_id}/delete', 'WeDevs\PM_Pro\Modules\Time_Tracker\Src\Controllers\Time_Tracker_Controller@destroy' )
    ->permission( ['WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions\Time_Delete'] );

$router->post( 'custom-time', 'WeDevs\PM_Pro\Modules\Time_Tracker\Src\Controllers\Time_Tracker_Controller@custom_time' )
    ->permission( ['WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions\Time_Add'] );

$router->get( 'others-time', 'WeDevs\PM_Pro\Modules\Time_Tracker\Src\Controllers\Time_Tracker_Controller@others_time' )
    ->permission( ['WeDevs\PM\Core\Permissions\Access_Project'] );


$router->get( 'report-summary', 'WeDevs\PM_Pro\Modules\Time_Tracker\Src\Controllers\Time_Tracker_Controller@report_summary' );

$router->get( 'report-summary/csv', 'WeDevs\PM_Pro\Modules\Time_Tracker\Src\Controllers\Time_Tracker_Controller@report_summary_csv' );

