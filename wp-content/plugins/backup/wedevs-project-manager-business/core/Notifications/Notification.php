<?php
namespace WeDevs\PM_Pro\Core\Notifications;

use WeDevs\PM_Pro\Core\Notifications\Emails\Comment_Mention_Notification;
use WeDevs\PM_Pro\Core\Notifications\Emails\Daily_Digest;

/**
* Class for pro noticatiions
*/
class Notification {

    function __construct() {
       $this->add_action();
    }

    protected function add_action() {
        new Comment_Mention_Notification();
        new Daily_Digest();

        add_action( 'pm_user_profile', [$this, 'profile'] );
        add_action( 'pm_update_profile', [$this, 'profile_update'], 10, 2 );
    }

    public function profile_update( $user_id, $prev_data ) {
        $this->update_user_profile( $user_id, $prev_data );
    }

    public function profile( $profile_user ) {
        $this->user_parofile( $profile_user );
    }

    /**
     * User prfile update
     *
     * @since 1.1
     *
     * @return type
     */
    public function update_user_profile( $user_id, $old_user_data ) {

        $daily_digest_active_status                      = isset( $_POST['pm_daily_digets_status'] ) ? $_POST['pm_daily_digets_status'] : 'off';
        $email_notification_active_status                = isset( $_POST['pm_email_notification'] ) ? $_POST['pm_email_notification'] : 'off';

        $email_notification_new_project_active_status    = isset( $_POST['pm_email_notification_new_project'] ) ? $_POST['pm_email_notification_new_project'] : 'off';
        $email_notification_update_project_active_status = isset( $_POST['pm_email_notification_update_project'] ) ? $_POST['pm_email_notification_update_project'] : 'off';
        $email_notification_new_message_active_status    = isset( $_POST['pm_email_notification_new_message'] ) ? $_POST['pm_email_notification_new_message'] : 'off';
        $email_notification_new_comment_active_status    = isset( $_POST['pm_email_notification_new_comment'] ) ? $_POST['pm_email_notification_new_comment'] : 'off';
        $email_notification_update_comment_active_status = isset( $_POST['pm_email_notification_update_comment'] ) ? $_POST['pm_email_notification_update_comment'] : 'off';
        $email_notification_new_task_active_status       = isset( $_POST['pm_email_notification_new_task'] ) ? $_POST['pm_email_notification_new_task'] : 'off';
        $email_notification_update_task_active_status    = isset( $_POST['pm_email_notification_update_task'] ) ? $_POST['pm_email_notification_update_task'] : 'off';
        $email_notification_complete_task_active_status  = isset( $_POST['pm_email_notification_complete_task'] ) ? $_POST['pm_email_notification_complete_task'] : 'off';

        if ( is_user_logged_in() ) {
            update_user_meta( $user_id, '_user_daily_digets_status', $daily_digest_active_status );
            update_user_meta( $user_id, '_cpm_email_notification', $email_notification_active_status );

            update_user_meta( $user_id, '_cpm_email_notification_new_project', $email_notification_new_project_active_status );
            update_user_meta( $user_id, '_cpm_email_notification_update_project', $email_notification_update_project_active_status );
            update_user_meta( $user_id, '_cpm_email_notification_new_message', $email_notification_new_message_active_status );
            update_user_meta( $user_id, '_cpm_email_notification_new_comment', $email_notification_new_comment_active_status );
            update_user_meta( $user_id, '_cpm_email_notification_update_comment', $email_notification_update_comment_active_status );
            update_user_meta( $user_id, '_cpm_email_notification_new_task', $email_notification_new_task_active_status );
            update_user_meta( $user_id, '_cpm_email_notification_update_task', $email_notification_update_task_active_status );
            update_user_meta( $user_id, '_cpm_email_notification_complete_task', $email_notification_complete_task_active_status );

        }
    }

    /**
     * User profile custom field add
     *
     * @since 1.1
     *
     * @return type
     */
    public function user_parofile( $user ) {
        if ( !is_admin() ) {
            return;
        }

        $this->daily_digest_form( $user );
        $this->email_notification_form( $user );

        $this->email_notification_settings_form( $user );

        // if ( $this->hasPermissionUpdatePageAsess( $user ) ) {
        //     $this->menu_access_form( $user );
        // }
    }

    function hasPermissionUpdatePageAsess( $user ) {
        if ( user_can( $user->ID, 'manage_options' ) ) {
            return false;
        }

        if ( pm_has_manage_capability( $user->ID ) ) {
            return false;
        }

        if ( $user->ID == get_current_user_id() ) {
            //return false;
        }

        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        return false;
    }

    protected function daily_digest_form( $user ) {
        $check_satus = get_user_meta( $user->ID, '_user_daily_digets_status', true );
        $check_satus = ( ! in_array( $check_satus, array('on', 'off' ) ) ) ? 'on' : $check_satus;
        ?>
        <table class="form-table">
        <tr>
            <th>
                <label for="tc_location"><?php _e( 'Daily Digest', 'pm-pro' ); ?></label>
            </th>
            <td>
                <label for="pm_daily_digest_status">
                    <input type="checkbox" id="pm_daily_digest_status" <?php checked( 'on', $check_satus ); ?> name="pm_daily_digets_status"  value="on"/>
                    <span class="description"><?php _e('Enable project manager Daily Digest', 'pm-pro'); ?></span>
                </label>
            </td>
        </tr>

        </table>
        <?php
    }


    protected function email_notification_form( $user ) {
        $user_email_notification = get_user_meta( $user->ID, '_cpm_email_notification', true );
        $user_email_notification = ( ! in_array( $user_email_notification, array('on', 'off' ) ) ) ? 'on' : $user_email_notification;
        ?>
        <table class="form-table">
            <tr>
                <th><?php _e( 'Email Notification', 'pm-pro' ); ?> </th>
                <td>
                    <label for="pm-email-notification">
                        <input type="checkbox" value="on" <?php checked(  'on', $user_email_notification ); ?> id="pm-email-notification" name="pm_email_notification">
                        <span class="description"><?php _e( 'Enable project manager email', 'pm-pro' ); ?></span></em>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }

    protected function email_notification_settings_form( $user ) {

        $user_email_notification_new_project = get_user_meta( $user->ID, '_cpm_email_notification_new_project', true );
        $user_email_notification_update_project = get_user_meta( $user->ID, '_cpm_email_notification_update_project', true );
        $user_email_notification_new_message = get_user_meta( $user->ID, '_cpm_email_notification_new_message', true );
        $user_email_notification_new_comment = get_user_meta( $user->ID, '_cpm_email_notification_new_comment', true );
        $user_email_notification_update_comment = get_user_meta( $user->ID, '_cpm_email_notification_update_comment', true );
        $user_email_notification_new_task = get_user_meta( $user->ID, '_cpm_email_notification_new_task', true );
        $user_email_notification_update_task = get_user_meta( $user->ID, '_cpm_email_notification_update_task', true );
        $user_email_notification_complete_task = get_user_meta( $user->ID, '_cpm_email_notification_complete_task', true );

        $user_email_notification_new_project = ( ! in_array( $user_email_notification_new_project, array('on', 'off' ) ) ) ? 'on' : $user_email_notification_new_project;
        $user_email_notification_update_project = ( ! in_array( $user_email_notification_update_project, array('on', 'off' ) ) ) ? 'on' : $user_email_notification_update_project;
        $user_email_notification_new_message = ( ! in_array( $user_email_notification_new_message, array('on', 'off' ) ) ) ? 'on' : $user_email_notification_new_message;
        $user_email_notification_new_comment = ( ! in_array( $user_email_notification_new_comment, array('on', 'off' ) ) ) ? 'on' : $user_email_notification_new_comment;
        $user_email_notification_update_comment = ( ! in_array( $user_email_notification_update_comment, array('on', 'off' ) ) ) ? 'on' : $user_email_notification_update_comment;
        $user_email_notification_new_task = ( ! in_array( $user_email_notification_new_task, array('on', 'off' ) ) ) ? 'on' : $user_email_notification_new_task;
        $user_email_notification_update_task = ( ! in_array( $user_email_notification_update_task, array('on', 'off' ) ) ) ? 'on' : $user_email_notification_update_task;
        $user_email_notification_complete_task = ( ! in_array( $user_email_notification_complete_task, array('on', 'off' ) ) ) ? 'on' : $user_email_notification_complete_task;

        ?>
        <table class="form-table">
            <tr>
                <th><?php _e( 'Notifications for', 'pm-pro' ); ?> </th>
                <td>
                    <fieldset>
                        <label for="pm-email-notification-new-project">
                            <input type="checkbox" value="on" <?php checked(  'on', $user_email_notification_new_project ); ?> id="pm-email-notification-new-project" name="pm_email_notification_new_project">
                            <span class="description"><?php _e( 'New Projects ', 'pm-pro' ); ?></span></em>
                        </label><br>
                        <label for="pm-email-notification-update-project">
                            <input type="checkbox" value="on" <?php checked(  'on', $user_email_notification_update_project ); ?> id="pm-email-notification-update-project" name="pm_email_notification_update_project">
                            <span class="description"><?php _e( 'Update Projects ', 'pm-pro' ); ?></span></em>
                        </label><br>
                        <label for="pm-email-notification-new-message">
                            <input type="checkbox" value="on" <?php checked(  'on', $user_email_notification_new_message ); ?> id="pm-email-notification-new-message" name="pm_email_notification_new_message">
                            <span class="description"><?php _e( 'New Message ', 'pm-pro' ); ?></span></em>
                        </label><br>
                        <label for="pm-email-notification-new-comment">
                            <input type="checkbox" value="on" <?php checked(  'on', $user_email_notification_new_comment ); ?> id="pm-email-notification-new-comment" name="pm_email_notification_new_comment">
                            <span class="description"><?php _e( 'New Comment ', 'pm-pro' ); ?></span></em>
                        </label><br>
                        <label for="pm-email-notification-update-comment">
                            <input type="checkbox" value="on" <?php checked(  'on', $user_email_notification_update_comment ); ?> id="pm-email-notification-update-comment" name="pm_email_notification_update_comment">
                            <span class="description"><?php _e( 'Update Comment ', 'pm-pro' ); ?></span></em>
                        </label><br>
                        <label for="pm-email-notification-new-task">
                            <input type="checkbox" value="on" <?php checked(  'on', $user_email_notification_new_task ); ?> id="pm-email-notification-new-task" name="pm_email_notification_new_task">
                            <span class="description"><?php _e( 'New Task ', 'pm-pro' ); ?></span></em>
                        </label><br>
                        <label for="pm-email-notification-update-task">
                            <input type="checkbox" value="on" <?php checked(  'on', $user_email_notification_update_task ); ?> id="pm-email-notification-update-task" name="pm_email_notification_update_task">
                            <span class="description"><?php _e( 'Update Task ', 'pm-pro' ); ?></span></em>
                        </label><br>
                        <label for="pm-email-notification-complete-task">
                            <input type="checkbox" value="on" <?php checked(  'on', $user_email_notification_complete_task ); ?> id="pm-email-notification-complete-task" name="pm_email_notification_complete_task">
                            <span class="description"><?php _e( 'Complete Task ', 'pm-pro' ); ?></span></em>
                        </label><br>
                    </fieldset>
                </td>
            </tr>
        </table>
        <?php
    }

    function profile_settings( $profileuser ) {
        $notification = get_user_meta( $profileuser->ID, 'erp_hr_disable_notification', true );
        $checked      = ! empty( $notification ) ? 'checked' : '';
        ?>
        <h3><?php esc_html_e( 'ERP Profile Settings', 'erp' ); ?></h3>
        <table class="form-table">
            <tbody>
            <tr>
                <th><label for="erp-hr-disable-notification"><?php esc_html_e( 'Notification', 'erp' ); ?></label></th>
                <td>
                    <input type="checkbox" id="erp-hr-disable-notification" <?php echo esc_attr( $checked ); ?>
                           name="erp_hr_disable_notification">
                    <span class="description"><?php esc_html_e( 'Disable WP ERP email notifications', 'erp' ); ?></span>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }
}
