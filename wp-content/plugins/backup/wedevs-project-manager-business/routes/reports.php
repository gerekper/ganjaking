<?php

use WeDevs\PM_Pro\Core\Router\Router;
use WeDevs\PM_Pro\Core\Permissions\Administrator;
use WeDevs\PM\Core\Permissions\Project_Manage_Capability;
$router = Router::singleton();

$router->get( 'task-reports', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@task_reports' );

$router->get( 'overdue-tasks', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@overdue_tasks' );

$router->get( 'users/{current_user_id}/advance-report/overdue-tasks/csv', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@overdue_tasks_csv' );

$router->get( 'users/{current_user_id}/advance-report/overdue-tasks/overdue-tasks-PDF', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@overdue_tasks_PDF' );

$router->get( 'completed-tasks', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@completed_tasks' );

$router->get( 'users/{current_user_id}/advance-report/completed-tasks/csv', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@completed_tasks_csv' );

$router->get( 'users/{current_user_id}/advance-report/completed-tasks/pdf', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@completed_tasks_PDF' );

$router->get( 'user-activities', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@user_activities' );

$router->get( 'users/{current_user_id}/advance-report/user-activities/csv', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@user_activities_csv' );

$router->get( 'users/{current_user_id}/advance-report/user-activities/pdf', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@user_activities_PDF' );

$router->get( 'project-tasks', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@project_tasks' );

$router->get( 'milestone-tasks', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@milestone_tasks' );

$router->get( 'users/{current_user_id}/advance-report/milestone/csv', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@milestone_tasks_CSV' );

$router->get( 'users/{current_user_id}/advance-report/milestone/pdf', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@milestone_tasks_PDF' );

$router->get( 'unassigned-tasks', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@unassigned_tasks' );

$router->get( 'users/{current_user_id}/advance-report/unassigned/csv', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@unassigned_CSV' );

$router->get( 'users/{current_user_id}/advance-report/unassigned/pdf', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@unassigned_PDF' );

$router->get( 'advance-report', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@advance_tasks' );

$router->get( 'users/{current_user_id}/advance-report/csv', 'WeDevs/PM_Pro/Reports/Controllers/Reports_Controller@advance_tasks_csv' );

