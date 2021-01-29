<?php

$tpbk = config('frontend.assets_url') .'images/tpbk.png';
$now = Carbon\Carbon::now();
$subday = Carbon\Carbon::now()->subHours(24);

?>

<div style="width:600px;  background: #fff;">
    <div style="width: 600px;">
        <div style="background-image: url('<?php echo $tpbk; ?>'); background-repeat: no-repeat; height: 174px; width: 600px;">
            <div style="font-family: 'Lato', sans-serif; font-wight: bold; color: #fff; font-size: 30px; padding-top: 26px; text-align: center; text-transform: uppercase;">
                <?php _e( 'Daily Digest', 'pm-pro' ); ?>
            </div>
        </div>
    </div>
    <div style="padding: 0 50px; text-align: justify; background-repeat: no-repeat;">
        <div style="margin-top: 40px; margin-bottom: 20px;">
            <?php

                foreach ( $projects as $project ) {

                    if( $project->status !== 'incomplete' ) {
                        continue ;
                    }

                    $manager = pm_is_manager($project['id'], $user_id);

                    if ( !in_array( $project['id'], $project_ids ) && !$manager ) {
                        continue ;
                    }
                    if ( $manager ) {
                        $milestones = $project->milestones()->whereHas( 'achieve_date_field', function( $query ) use ( $subday ) {
                                        $query->where( 'meta_value', '>=', $subday );
                                    } )->get();
                        $discuss_count = $project->discussion_boards()->whereDate('created_at','>=', $subday )->count();
                        $task_count = $project->tasks()->whereDate('created_at','>=', $subday )->count();
                        $task_complete_count = $project->tasks()->where( 'status', 1)->whereDate('updated_at','>=', $subday )->count();
                        $activity_count = $project->activities()->whereDate('created_at','>=', $subday )->count();

                        if ( !in_array( $project['id'], $project_ids ) && $milestones->isEmpty() && !$discuss_count && !$task_count && !$task_complete_count && !$activity_count ) {
                            continue;
                        }
                    }
            ?>
                    <table style="width: 100%; border: 1px solid #eee; margin-bottom: 10px;">
                        <tr>
                            <th style="background: #eee;padding: 10px;">
                                <a href="<?php echo $link .'#/projects/'. $project['id']. '/overview'; ?>" style="text-decoration: none;"><?php echo $project['title']; ?></a>
                            </th>
                        </tr>

                         <?php
                            if( $manager ) {

                            ?>
                            <tr>
                                <td>
                                    <strong style="width: 100%; display: block;overflow: hidden; padding: 12px"><?php _e( 'Project Overview', 'pm-pro' ); ?></strong>
                                    <div style="padding-left: 38px;">
                                        <span style="width: 30%;display: inline-block;"><?php _e( 'Discuss', 'pm-pro' ); ?></span>
                                        <span ><?php echo $discuss_count; ?></span>
                                    </div>
                                    <div style="padding-left: 38px;" >
                                        <span style="width: 30%;display: inline-block;"><?php _e( 'Task', 'pm-pro' ); ?></span>
                                        <span ><?php echo $task_count; ?></span>
                                    </div>
                                    <div style="padding-left: 38px;" >
                                        <span style="width: 30%;display: inline-block;"><?php _e( 'Completed Task', 'pm-pro' ); ?></span>
                                        <span ><?php echo $task_complete_count; ?></span>
                                    </div>
                                    <div style="padding-left: 38px;">
                                        <span style="width: 30%;display: inline-block;"><?php _e( 'Activity', 'pm-pro' ); ?></span>
                                        <span ><?php echo $activity_count; ?></span>
                                    </div>

                                </td>
                            </tr>
                            <?php if ( !$milestones->isEmpty() ) {  ?>
                                <tr>
                                    <td>
                                        <strong style="width: 100%; display: block;overflow: hidden;padding: 12px;">  <?php _e( 'Upcomnig Milestone', 'pm-pro' ); ?></strong>
                                        <ul>
                                            <?php foreach ( $milestones as $milestone ) {
                                               ?>
                                                <li><?php echo $milestone['title']; ?></li>

                                               <?php
                                            }
                                            ?>
                                        </ul>
                                    </td>
                                </tr>
                                <?php
                                }
                         }
                         ?>

                        <tr>

                            <td>
                                 <strong style="width: 100%; display: block;overflow: hidden;padding: 12px;">  <?php _e( 'Your due tasks', 'pm-pro' ); ?></strong>
                                <?php

                                    foreach( $project['task_lists'] as $task_list ) {
                                        $tasks = $task_list->tasks->filter( function ( $item ) use ( $tasks_ids ) {
                                            return in_array($item['id'], $tasks_ids);
                                        } );

                                        if ( !$tasks->count() ) {
                                           continue ;
                                        }
                                ?>

                                    <p style="margin:5px 15px;"> <?php echo $task_list['title']; ?></p>
                                    <div style="margin:5px 15px;">
                                        <ul>
                                            <?php
                                            foreach( $tasks as $task ) {
                                                // if ( $task->privacy && !pm_user_can( 'view_private_message', $project['id'], $user_id ) ) {
                                                //     continue ;
                                                // }
                                                ?>
                                                <li><a href="<?php echo $link.'#/projects/'. $project['id'] . '/task-lists/tasks/'. $task['id']; ?>" style="text-decoration: none;"> <?php echo $task['title'] ?></a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </td>
                        </tr>

                    </table>

            <?php  } ?>

        </div>

    </div>
</div>
