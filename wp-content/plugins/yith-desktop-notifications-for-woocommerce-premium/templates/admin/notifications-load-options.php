<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_WCDN_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Template for Notifications
 *
 * @package YITH Desktop Notifications for WooCommerce
 * @since   1.0.0
 * @author  Yithemes
 */

global $wp_roles;

$id    = 'yit_wcdn_options_id';
$name  = 'yit_wcdn_options[id]';

?>
<div id="<?php echo $id ?>-container" class="wcdn-sections-group ui-sortable">
    <h3><?php echo esc_html_x('Active Notifications', 'yith-desktop-notifications-for-woocommerce'); ?></h3>
    <div id="form" class="yith-wcdn-notifications">
        <form class="form-notifications-rules" id="form" method="post">
            <div class="wcdn-section wcdn-select-wrapper section">
                <div class="section-head yith-wcdn-head-notification-create">
                    <span
                        class="wcdn-active <?php echo ( $db_value['active'] == 'yes' ) ? 'activated' : '' ?>">
                        <?php echo ($db_value['title']) ? $db_value['title'] : ''  ?>
                    </span>
                </div>
                <div class="section-body">
                    <table class="yith_foreach_notifications">
                        <tr class="yith-wcdn-notification-name">
                            <th>
                                <?php esc_html_e('Title:','yith-desktop-notifications-for-woocommerce') ?>
                            </th>
                            <td>
                                <input type="text" name="_yith_desktop_notifications_title" class="_yith-desktop-update-notifications-title yith-wcdn-style" id="_yith_desktop_notifications_title" value="<?php echo $db_value['title'] ? $db_value['title'] : ''  ?>"/>
                            </td>
                        </tr>
                        <tr class="yith-wcdn-notification-message">
                            <th>
                                <?php esc_html_e('Description:','yith-desktop-notifications-for-woocommerce') ?>
                            </th>
                            <td>
                                <textarea name="_yith_desktop_notifications_description" class="_yith-desktop-update-notifications-description yith-wcdn-style" id="_yith_desktop_notifications_description"><?php echo $db_value['description'] ? $db_value['description'] : '' ?></textarea>
                                <div>
                                    <?php esc_html_e('Available placeholders:','yith-desktop-notifications-for-woocommerce') ?>
                                    <ul class="yith-wcdn-update-placeholder-available">
                                        <li class="yith-wcdn-list yith-wcdn-sold yith-wcdn-placed yith-wcdn-refund yith-wcdn-status_changed">{username}</li>
                                        <li class="yith-wcdn-list yith-wcdn-sold yith-wcdn-placed yith-wcdn-refund yith-wcdn-status_changed">{order_id}</li>
                                        <li class="yith-wcdn-list yith-wcdn-placed yith-wcdn-refund yith-wcdn-status_changed">{order_total}</li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr class="yith-wcdn-notification-icon">
                            <th>
                                <?php esc_html_e('Notification icon:','yith-desktop-notifications-for-woocommerce') ?>
                            </th>
                            <td>
                                <select class="_yith_list_desktop_notifications_icon yith_wcdn_multiple_role yith_wcdn_icon" id="_yith_desktop_notifications_icon">
                                    <?php
                                    $dh = opendir(YITH_WCDN_PATH.'assets/yith-notifications/notification-icons');
                                    while (false !== ($filename = readdir($dh))) {
                                        if($filename == '.' || $filename == '..') continue;
                                        echo '<option  value="'.YITH_WCDN_ASSETS_URL.'yith-notifications/notification-icons/'.$filename.'"'.selected(YITH_WCDN_ASSETS_URL.'yith-notifications/notification-icons/'.$filename,$db_value['icon']).'>'.$filename.'</option>';
                                    }
                                    ?>
                                    <select>
                                        <img src="" class="_yith_list_notification_logo" width="50"/>
                            </td>
                            <td>
                                <input type="hidden" name="section-sound" value="<?php echo YITH_WCDN_ASSETS_URL.'yith-notifications/notification-sounds/melody.mp3'?>" id="_yith_desktop_notifications_sound"/>
                            </td>

                        </tr>
                        <tr class="yith-wcdn-notification-length">
                            <th>
                                <?php esc_html_e('Notification length:','yith-desktop-notifications-for-woocommerce') ?>
                            </th>
                            <td>
                                <input type="number" name="_yith_desktop_notifications_length" class="_yith_desktop_update_notifications_length yith-wcdn-style" id="_yith_desktop_notifications_length" value="<?php echo $db_value['time_notification'] ? $db_value['time_notification'] : 0  ?>"/>
                                <?php echo wc_help_tip(esc_html_x('Time in seconds, "0" = Default notification length','yith-desktop-notifications-for-woocommerce')); ?>
                            </td>
                        </tr>
                        <tr class="yith-wcdn-notification-update-or-delete">
                            <td>
                                <div>
                                    <?php if (empty($db_value) && !is_array($db_value)) { ?>
                                        <input style="float: left; margin-right: 10px;" class="button button-primary  _yith_wcdn_save_update_notification" type="submit"
                                               value="<?php esc_html_e( 'Create new notification', 'yith-desktop-notifications-for-woocommerce' ) ?>"/>
                                    <?php } else { ?>
                                        <input style="" class=" button button-primary _yith_wcdn_save_update_notification" type="button"
                                               value="<?php esc_html_e( 'Update', 'yith-desktop-notifications-for-woocommerce' ) ?>"/>
                                    <?php } ?>

                                </div>
                            </td>
                            <td>
                                <div>
                                    <input style="margin-left: 40px;" class="button yith_wcdn_preview_notification" type="button"
                                           value="<?php esc_html_e( 'Preview notification', 'yith-desktop-notifications-for-woocommerce' ) ?>"/>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
