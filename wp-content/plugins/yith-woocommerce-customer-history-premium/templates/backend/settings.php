<?php

defined( 'ABSPATH' ) or exit;

/*
 *	Settings
 */

global $wpdb;

if ( isset( $_POST['act'] ) && $_POST['act'] == 'save' ) {
    // update_option( 'yith-wcch-default-sender-name', $_POST['default_sender_name'] );
    // update_option( 'yith-wcch-default-sender-email', $_POST['default_sender_email'] );
    update_option( 'yith-wcch-default_save_admin_session', $_POST['default_save_admin_session'] );
    // update_option( 'yith-wcch-save_user_ip', $_POST['save_user_ip'] );
    update_option( 'yith-wcch-hide_users_with_no_orders', $_POST['hide_users_with_no_orders'] );
    update_option( 'yith-wcch-show_bot_sessions', $_POST['show_bot_sessions'] );
    update_option( 'yith-wcch-results_per_page', $_POST['results_per_page'] );
    update_option( 'yith-wcch-timezone', $_POST['timezone'] );
}
update_option( 'yith-wcch-save_user_ip', 0 );

$timezone = get_option('yith-wcch-timezone') ? get_option('yith-wcch-timezone') : 0;

?>

<div id="yith-woocommerce-customer-history">
	<div id="settings" class="wrap">

		<h1><?php echo __( 'Settings', 'yith-woocommerce-customer-history' ); ?></h1>

        <hr />

        <form id="group-form" action="admin.php?page=yith-wcch-settings.php" method="post">

            <input type="hidden" name="act" value="save">

            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="results_per_page"><?php echo __( 'Results per page', 'yith-woocommerce-customer-history' ); ?></label></th>
                        <td><input name="results_per_page" type="number" class="small-text" placeholder="50" value="<?php echo get_option( 'yith-wcch-results_per_page' ); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="default_save_admin_session"><?php echo __( 'Save "admin" sessions?', 'yith-woocommerce-customer-history' ); ?></label></th>
                        <td>
                            <select name="default_save_admin_session">
                                <option value="0"><?php echo __( 'No', 'yith-woocommerce-customer-history' ); ?></option>
                                <option value="1"<?php echo get_option('yith-wcch-default_save_admin_session') ? ' selected="selected"' : ''; ?>><?php echo __( 'Yes', 'yith-woocommerce-customer-history' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <!--
                    <tr>
                        <th scope="row"><label for="save_user_ip"><?php echo __( 'Save users IP address?', 'yith-woocommerce-customer-history' ); ?></label></th>
                        <td>
                            <select name="save_user_ip" disabled="disabled">
                                <option value="0"><?php echo __( 'No', 'yith-woocommerce-customer-history' ); ?></option>
                                <option value="1"<?php echo get_option('yith-wcch-save_user_ip') ? ' selected="selected"' : ''; ?>><?php echo __( 'Yes', 'yith-woocommerce-customer-history' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    -->
                    <tr>
                        <th scope="row"><label for="hide_users_with_no_orders"><?php echo __( 'Hide users with no orders?', 'yith-woocommerce-customer-history' ); ?></label></th>
                        <td>
                            <select name="hide_users_with_no_orders">
                                <option value="0"><?php echo __( 'No', 'yith-woocommerce-customer-history' ); ?></option>
                                <option value="1"<?php echo get_option('yith-wcch-hide_users_with_no_orders') ? ' selected="selected"' : ''; ?>><?php echo __( 'Yes', 'yith-woocommerce-customer-history' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="show_bot_sessions"><?php echo __( 'Show BOT sessions?', 'yith-woocommerce-customer-history' ); ?></label></th>
                        <td>
                            <select name="show_bot_sessions">
                                <option value="0"><?php echo __( 'No', 'yith-woocommerce-customer-history' ); ?></option>
                                <option value="1"<?php echo get_option('yith-wcch-show_bot_sessions') ? ' selected="selected"' : ''; ?>><?php echo __( 'Yes', 'yith-woocommerce-customer-history' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <!--
                    <tr>
                        <th scope="row"><label for="default_sender_name"><?php echo __( 'Default Sender Name', 'yith-woocommerce-customer-history' ); ?></label></th>
                        <td><input name="default_sender_name" type="text" class="regular-text" placeholder="<?php echo get_bloginfo('name'); ?>" value="<?php echo get_option( 'yith-wcch-default-sender-name' ); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="default_sender_email"><?php echo __( 'Default Sender Email', 'yith-woocommerce-customer-history' ); ?></label></th>
                        <td><input name="default_sender_email" type="text" class="regular-text" placeholder="<?php echo get_bloginfo('admin_email'); ?>" value="<?php echo get_option( 'yith-wcch-default-sender-email' ); ?>"></td>
                    </tr>
                    -->
                    <tr>
                        <th scope="row"><label for="timezone"><?php echo __( 'Timezone', 'yith-woocommerce-customer-history' ); ?> [<?php echo date( 'H:i:s', time() + 3600 * $timezone ); ?>]</label></th>
                        <td>
                            <select name="timezone">
                                <?php
                                    for ( $i=-11; $i<15; $i++ ) { 
                                        echo '<option value="' . $i . '"'. ( $i == $timezone ? ' selected="selected"' : '' ) . '>UTC ' . ( $i > -1 ? '+' : '' ) . $i . '</option>';
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><input type="submit" value="<?php echo __( 'Save', 'yith-woocommerce-customer-history' ); ?>" class="button button-primary button-large"></th>
                        <td></td>
                    </tr>
                </tbody>
            </table>

        </form>

        <hr style="margin-bottom: 30px;" />

        <!--
        <div class="settings-box">

            <h2><span class="dashicons dashicons-download"></span> <?php echo __( 'CSV Export', 'yith-woocommerce-customer-history' ); ?></h2>

            <form id="group-form" action="admin.php?page=yith-wcch-settings.php" method="post">
                <input type="hidden" name="csv-export" value="1">
                <p><?php echo __( 'You will download a CSV file with only "Sessions".', 'yith-woocommerce-customer-history' ); ?></p>
                <input type="submit" value="<?php echo __( 'Download', 'yith-woocommerce-customer-history' ); ?>" class="button button-primary button-large">
            </form>

        </div>
        -->

        <div class="settings-box">

            <h2><span class="dashicons dashicons-download"></span> <?php echo __( 'Backup Export', 'yith-woocommerce-customer-history' ); ?></h2>

            <form id="group-form" action="admin.php?page=yith-wcch-settings.php" method="post">
                <input type="hidden" name="export" value="1">
                <p><?php echo __( 'You will download a file with "Sessions", "Searches", "Emails" and "Stats".', 'yith-woocommerce-customer-history' ); ?></p>
                <input type="submit" value="<?php echo __( 'Download', 'yith-woocommerce-customer-history' ); ?>" class="button button-primary button-large">
            </form>

        </div>

        <div class="settings-box">

            <h2><span class="dashicons dashicons-upload"></span> <?php echo __( 'Backup Import', 'yith-woocommerce-customer-history' ); ?></h2>

            <form id="group-form" action="admin.php?page=yith-wcch-settings.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="import" value="1">
                <p><?php echo __( 'Imported data will be added to your database.', 'yith-woocommerce-customer-history' ); ?></p>
                <input type="file" name="wcch_import"><br />
                <br />
                <input type="submit" value="<?php echo __( 'Import', 'yith-woocommerce-customer-history' ); ?>" class="button button-primary button-large" onclick="return confirm('Are you sure?')">
            </form>

        </div>

        <div class="settings-box">

            <h2><span class="dashicons dashicons-trash"></span> <?php echo __( 'Empty Tables', 'yith-woocommerce-customer-history' ); ?></h2>
            <p class="warning"><span class="dashicons dashicons-warning"></span> <?php echo __( 'ATTENTION: you will lose all saved Sessions, Searches and Emails!', 'yith-woocommerce-customer-history' ); ?></p>

            <form id="group-form" action="admin.php?page=yith-wcch-settings.php" method="post">
                <input type="hidden" name="delete_sessions" value="1">
                <input type="submit" value="<?php echo __( 'Delete all Sessions & Searches', 'yith-woocommerce-customer-history' ); ?>" class="button button-primary button-large" onclick="return confirm('Are you sure?')">
            </form>

            <br />

            <form id="group-form" action="admin.php?page=yith-wcch-settings.php" method="post">
                <input type="hidden" name="delete_emails" value="1">
                <input type="submit" value="<?php echo __( 'Delete all Emails', 'yith-woocommerce-customer-history' ); ?>" class="button button-primary button-large" onclick="return confirm('Are you sure?')">
            </form>

        </div>

	</div>
</div>
