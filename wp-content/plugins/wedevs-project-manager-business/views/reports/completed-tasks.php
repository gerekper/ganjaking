<div><?php echo esc_html( $date ); ?></div>
<br>
<table class="pm-list-table widefat fixed striped posts">
    <thead>
        <tr>
            <td><?php esc_html_e( 'Project', 'pm-pro' ); ?></td>
            <td><?php esc_html_e( 'Co Worker', 'pm-pro' ); ?></td>
            <td><?php esc_html_e( 'Total Tasks', 'pm-pro' ); ?></td>
        </tr>
    </thead>

    <tbody>
        <tr class="even">
            <td><?php echo esc_html( $all_project ); ?></td>
            <td><?php echo esc_html( $all_user ); ?></td>
            <td><?php echo esc_html( $total ); ?></td>
        </tr>
    </tbody>
</table>

<?php
    foreach ( $pdf['data'] as $key => $result ) { ?>
        <br>
        <div>
            <span style="font-weight: 600;">
                <?php esc_html_e( 'Project title: ', 'pm-pro' ); ?>
            </span>
            <?php echo esc_html( $result['title'] ); ?></div>
        <br>
        <?php

        foreach ( $result['task_lists']['data'] as $key => $list_results ) { ?>
            <div>
                <span style="font-weight: 600;">
                    <?php esc_html_e( 'Task List title: ', 'pm-pro' ); ?>
                </span>
                <?php echo esc_html( $list_results['title'] ); ?>
            </div>

            <table class="pm-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <td><?php esc_html_e( 'Tasks', 'pm-pro' ); ?></td>
                        <td><?php esc_html_e( 'Due Date', 'pm-pro' ); ?></td>
                        <td><?php esc_html_e( 'Created At', 'pm-pro' ); ?></td>
                        <td><?php esc_html_e( 'Created By', 'pm-pro' ); ?></td>
                    </tr>
                </thead>

                <?php
                foreach ( $list_results['tasks']['data'] as $key => $task ) {
                    $date_between = $task['start_at']['date'] . __(' to ', 'pm-pro') . $task['due_date']['date'];
                    $due_date     = $task['due_date']['date'];
                    $created_at   = $task['created_at']['date'];
                    $creator      = $task['creator']['data']['display_name'];
                ?>
                    <tbody>
                        <tr class="<?php echo $key % 2 == 0 ? 'even' : 'odd'; ?>">
                            <td><?php echo esc_html( $task['title'] ); ?></td>
                            <td><?php echo esc_html( $due_date ); ?></td>
                            <td><?php echo esc_html( $created_at ); ?></td>
                            <td><?php echo esc_html( $creator ); ?></td>
                        </tr>
                    </tbody><?php
                }
                ?>
            </table>
            <br>
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
