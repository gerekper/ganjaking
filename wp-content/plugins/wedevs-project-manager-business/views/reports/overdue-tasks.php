<div><?php echo $date; ?></div>
<br>
<table class="pm-list-table widefat fixed striped posts">
    <thead>
        <tr>
            <th>
                <?php _e( 'Project', 'pm-pro' ); ?>
            </th>

            <th>
                <?php _e( 'Co Worker', 'pm-pro'); ?>
            </th>

            <th>
                <?php _e( 'Total Tasks', 'pm-pro'); ?>
            </th>
        </tr>
    </thead>

    <tbody>
        <tr class="even">
            <td>
                <?php echo $all_project; ?>
            </td>

            <td>
                <?php echo $all_user; ?>
            </td>

            <td>
                <?php echo $total; ?>
            </td>
        </tr>
    </tbody>
</table>



<?php
    foreach ( $results['data'] as $key => $result ) { ?>
        <br>
        <div>
            <span style="font-weight: 600;">
                <?php  _e( 'Project title: ', 'pm-pro'  ); ?>
            </span>
            <?php echo $result['title']; ?></div>
        <br>
        <?php

        foreach ( $result['task_lists']['data'] as $key => $list_results ) { ?>
            <div>
                <span style="font-weight: 600;">
                    <?php  _e( 'Task List title: ', 'pm-pro'  ); ?>
                </span>
                <?php echo $list_results['title']; ?>
            </div>

            <table class="pm-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th>
                            <?php _e('Tasks', 'pm-pro'); ?>
                        </th>

                        <th>
                            <?php _e('Date Between', 'pm-pro'); ?>
                        </th>

                        <th>
                            <?php _e('Created At', 'pm-pro'); ?>
                        </th>

                        <th>
                            <?php _e('Created By', 'pm-pro'); ?>
                        </th>
                    </tr>
                </thead>

                <?php
                foreach ( $list_results['tasks']['data'] as $key => $task ) {
                    $date_between = $task['start_at']['date'] . __(' to ', 'pm-pro') . $task['due_date']['date'];
                    $created_at   = $task['created_at']['date'];
                    $creator      = $task['creator']['data']['display_name']; ?>

                    <tbody>
                        <tr class="<?php echo $key % 2 == 0 ? 'even' : 'odd'; ?>">
                            <td>
                                <?php echo $task['title']; ?>
                            </td>

                            <td>
                                <?php echo $date_between; ?>
                            </td>

                            <td>
                                <?php echo $created_at; ?>
                            </td>

                            <td>
                                <?php echo $creator; ?>
                            </td>
                        </tr>
                    </tbody><?php
                }
                ?>
            </table><br>
            <?php
        }
    }
?>

<style type="text/css">
    .pm-list-table {
        border-spacing: 0;
    }
    .pm-list-table {
        border: 1px solid #e1e1e1;
    }
    .pm-list-table th {
        border-bottom: 1px solid #e1e1e1;
    }

    .pm-list-table th, .pm-list-table td {
        padding: 5px;
    }
    .pm-list-table .even {
        background: #f9f9f9;
    }
</style>
