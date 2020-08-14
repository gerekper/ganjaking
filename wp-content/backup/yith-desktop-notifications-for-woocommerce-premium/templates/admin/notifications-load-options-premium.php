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
$key   = uniqid();

$instance = YITH_Desktop_Notifications()->notifications;
?>
<div id="<?php echo $id ?>-container" class="wcdn-add-notifications ui-sortable">

    <form id="form-<?php echo $key ?>" method="post">
        <input type="hidden" name="wcdn-action" value="save-options" id="wcdn-action"/>
        <input type="hidden" name="section-key" value="<?php echo $key ?>" id="wcdn-key"/>
        <div class="section-body">
            <h3><?php esc_html_e('New Notification','yith-desktop-notifications-for-woocommerce')?></h3>
            <table class="yith-table-new-notification">
                <tbody class="yith_tbody_new_notification">
                <tr class="yith-wcdn-notification-type">
                    <th>
                        <?php esc_html_e('Notification type','yith-desktop-notifications-for-woocommerce') ?>
                    </th>
                    <td>
                        <select name="yith-wcdn-notification-type"
                                id="yith-wcdn-notification-type"
                                class="yith-wcdn-notification-type-select yith_wcdn_multiple_role" data-field="notification-type">

                            <?php foreach ( $instance->get_notification_type() as $key_type => $type ): ?>
                                <option
                                    value="<?php echo $key_type ?>"><?php echo $type ?></option>
                            <?php endforeach ?>
                        </select>
                    </td>
                </tr>
                <tr class="yith-wcdn-specific-status" id="yith-wcdn-id-status">
                    <th>
                        <?php esc_html_e('Specific status','yith-desktop-notifications-for-woocommerce') ?>
                    </th>
                    <td>
                        <select multiple name="yith-wcdn-specific-status"
                                id="yith-wcdn-specific-status"
                                class="_yith-wcdn-update-notification-type-select yith_wcdn_multiple_role" data-field="notification-specific-status">
                            <?php foreach ( wc_get_order_statuses() as $key_type => $type ): ?>
                                <option
                                    value="<?php echo $key_type ?>"><?php echo $type ?></option>
                            <?php endforeach ?>
                        </select>
                    </td>
                </tr>
                <tr class="yith-wcdn-product-sold" id="yith-wcdn-id-product-sold">
                    <th>
                        <?php esc_html_e('Select products','yith-desktop-notifications-for-woocommerce') ?>
                    </th>
                    <td>
                        <?php if( version_compare( WC()->version, '2.7.0', '>=' ) ) { ?>

                            <select class="wc-product-search" multiple="multiple" style="width: 350px;" id="yith-wcdn-select-products" name="product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'yith-desktop-notifications-for-woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations"></select>

                        <?php } else { ?>

                            <input type="hidden" class="wc-product-search" id="yith-wcdn-select-products" name="product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'yith-desktop-notifications-for-woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" />

                        <?php } ?>

                    </td>
                </tr>
                <tr class="yith-wcdn-notification-name">
                    <th>
                        <?php esc_html_e('Title','yith-desktop-notifications-for-woocommerce') ?>
                    </th>
                    <td>
                        <input type="text" name="_yith_desktop_notifications_title" class="yith-wcdn-style" id="_yith_desktop_notifications_title" value="" />
                    </td>
                </tr>
                <tr class="yith-wcdn-notification-message">
                    <th>
                        <?php esc_html_e('Description','yith-desktop-notifications-for-woocommerce') ?>
                    </th>
                    <td>
                        <textarea name="_yith_desktop_notifications_description" class="yith-wcdn-style" id="_yith_desktop_notifications_description"></textarea>
                        <div>
                            <?php esc_html_e('Available placeholders:','yith-desktop-notifications-for-woocommerce') ?>
                            <ul class="yith-wcdn-placeholder-available">
                                <li class="yith-wcdn-list yith-wcdn-out_of_stock yith-wcdn-new_booking yith-wcdn-new_request_booking">{product_id}</li>
                                <li class="yith-wcdn-list yith-wcdn-out_of_stock">{product_name}</li>
                                <li class="yith-wcdn-list yith-wcdn-sold yith-wcdn-placed yith-wcdn-refund yith-wcdn-status_changed yith-wcdn-new_quote yith-wcdn-new_booking yith-wcdn-new_request_booking">{username}</li>
                                <li class="yith-wcdn-list yith-wcdn-sold yith-wcdn-placed yith-wcdn-refund yith-wcdn-status_changed">{order_id}</li>
                                <?php
                                if ( defined( 'YITH_YWRAQ_PREMIUM' ) ) {
                                    ?>
                                    <li class="yith-wcdn-list yith-wcdn-new_quote">{quote_id}</li>
                                    <?php
                                }
                                if ( defined( 'YITH_WCBK_PREMIUM' ) ) {
                                    ?>
                                    <li class="yith-wcdn-list yith-wcdn-new_booking yith-wcdn-new_request_booking">{booking_id}</li>
                                    <?php
                                }
                                ?>
                                <li class="yith-wcdn-list yith-wcdn-placed yith-wcdn-refund yith-wcdn-status_changed">{order_total}</li>
                                <li class="yith-wcdn-list yith-wcdn-status_changed">{new_status}</li>
                                <li class="yith-wcdn-list yith-wcdn-status_changed">{old_status}</li>
                                <li class="yith-wcdn-list yith-wcdn-sold">{products_sold}</li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr class="yith-wcdn-notification-role-user">
                    <th>
                        <?php esc_html_e('Role notification','yith-desktop-notifications-for-woocommerce') ?>
                    </th>
                    <td>
                        <select multiple class="yith_wcdn_multiple_role" id="_yith_desktop_notifications_role_user">
                            <?php wp_dropdown_roles(); ?>
                            <select>
                    </td>
                </tr>
                <tr class="yith-wcdn-notification-icon">
                    <th>
                        <?php esc_html_e('Select notification icon:','yith-desktop-notifications-for-woocommerce') ?>
                    </th>
                    <td>
                        <select class="yith_wcdn_multiple_role yith_wcdn_icon" id="_yith_desktop_notifications_icon">
                            <?php
                            $dh = opendir(YITH_WCDN_PATH.'assets/yith-notifications/notification-icons');
                            while (false !== ($filename = readdir($dh))) {
                                if($filename == '.' || $filename == '..') continue;
                                echo '<option  value="'.YITH_WCDN_ASSETS_URL.'yith-notifications/notification-icons/'.$filename.'" >'.$filename.'</option>';
                            }

                            $list_of_icons = get_option('yith_wcdn_upload_icon');
                            if($list_of_icons !== false) {
                                foreach ($list_of_icons as $icons) {
                                    echo '<option  value="' . $icons . '" >' . basename($icons) . '</option>';
                                }
                            }
                            ?>
                            <select>
                                <img src="" id="_yith_new_notification_logo" width="50"/>
                    </td>

                </tr>
                <tr class="yith-wcdn-notification-sound">
                    <th>
                        <?php esc_html_e('Select notification sound:','yith-desktop-notifications-for-woocommerce') ?>
                    </th>
                    <td>
                        <select class="yith_wcdn_multiple_role yith_wcdn_sound" id="_yith_desktop_notifications_sound">
                            <?php
                            $dh = opendir(YITH_WCDN_PATH.'assets/yith-notifications/notification-sounds');
                            while (false !== ($filename = readdir($dh))) {
                                if($filename == '.' || $filename == '..') continue;
                                echo '<option  value="'.YITH_WCDN_ASSETS_URL.'yith-notifications/notification-sounds/'.$filename.'" >'.$filename.'</option>';
                            }
                            $list_of_audios = get_option('yith_wcdn_upload_audio');
                            if($list_of_audios !== false){
                                foreach ($list_of_audios as $audios) {
                                    echo '<option  value="'.$audios.'" >'.basename($audios).'</option>';
                                }
                            }
                            ?>
                            <select>
                                <input id="yith_click_new_audio_preview" class="_yith_click_new_audio_preview button" type="button" class="button button-default" value="<?php esc_html_e('click here to preview','yith-desktop-notifications-for-woocommerce'); ?>" />
                                <audio id="yith_new_audio_preview"><source src="" type="audio/mp3"></audio>
                    </td>

                </tr>
                <tr class="yith-wcdn-notification-length">
                    <th>
                        <?php esc_html_e('Notification length:','yith-desktop-notifications-for-woocommerce') ?>
                    </th>
                    <td>
                        <input type="number" name="_yith_desktop_notifications_length" class="yith-wcdn-style" id="_yith_desktop_notifications_length" value="0"/>
                        <?php echo wc_help_tip(esc_html__('Time in seconds, "0" = Default notification length','yith-desktop-notifications-for-woocommerce')); ?>
                    </td>

                </tr>
                <tr>
                    <td>
                        <input style="float: left; margin-right: 10px;" class="button button-primary" type="submit" id="_yith_wcdn_save_notification"
                               value="<?php esc_html_e( 'Create new notification', 'yith-desktop-notifications-for-woocommerce' ) ?>"/>
                    </td>
                    <td>
                        <input class="button" type="button" id="_yith_wcdn_demo_notification"
                               value="<?php esc_html_e( 'Preview notification', 'yith-desktop-notifications-for-woocommerce' ) ?>"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>

<div id="<?php echo $id ?>-container" class="wcdn-sections-group ui-sortable">
    <h3><?php echo esc_html__('Active Notifications', 'yith-desktop-notifications-for-woocommerce'); ?></h3>
    <?php if ( is_array( $db_value ) ) :
        foreach ( $db_value as $key => $value ) :
            ?>
            <div id="form-<?php echo $key ?>" class="yith-wcdn-notifications">
                <form class="form-notifications-rules" id="form-<?php echo $key ?>" method="post">
                    <input type="hidden" name="section-key" value="<?php echo $key ?>" id="wcdn-update-key" class="wcdn-update-key"/>
                    <div class="wcdn-section wcdn-select-wrapper section-<?php echo $key ?>">
                        <div class="section-head yith-wcdn-head-notification-create">
                            <span
                                class="wcdn-active <?php echo ( $db_value[ $key ]['active'] == 'yes' ) ? 'activated' : '' ?>"
                                data-section="<?php echo $key ?>">
                                <?php echo $db_value[ $key ]['title'] ?>
                            </span>
                            <span class="wcdn-remove" data-section="<?php echo $key ?>"></span>
                        </div>
                        <div class="section-body">
                            <table class="yith_foreach_notifications">
                                <tr class="yith-wcdn-notification-type">
                                    <th>
                                        <?php esc_html_e('Notification type','yith-desktop-notifications-for-woocommerce') ?>
                                    </th>
                                    <td>
                                        <select name="yith-wcdn-notification-type"
                                                id="yith-wcdn-update-notification-type"
                                                class="_yith-wcdn-update-notification-type-select yith_wcdn_multiple_role" data-field="notification-type">
                                            <?php foreach ( $instance->get_notification_type() as $key_type => $type ): ?>
                                                <option
                                                    value="<?php echo $key_type ?>" <?php selected( $db_value[ $key ]['notification'], $key_type ) ?>><?php echo $type ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="yith-wcdn-specific-status" id="yith-wcdn-update-specific-status">
                                    <th>
                                        <?php esc_html_e('Specific status','yith-desktop-notifications-for-woocommerce') ?>
                                    </th>
                                    <td>
                                        <?php if ( isset($db_value[ $key ]['specific_status'] )) {?>

                                            <select multiple name="yith-wcdn-specific-status-update"
                                                    id="_yith-wcdn-update-specific-status"
                                                    class="_yith-wcdn-update-specific-status-select yith_wcdn_multiple_role" data-field="notification-type">
                                                <?php foreach (  wc_get_order_statuses() as $key_type => $type ): ?>
                                                    <option
                                                        value="<?php echo $key_type ?>" <?php selected(in_array($key_type,(array)$db_value[ $key ]['specific_status'])) ?>><?php echo $type ?></option>
                                                <?php endforeach ?>
                                            </select>

                                        <?php } else {?>
                                            <select multiple
                                                    id="_yith-wcdn-update-specific-status"
                                                    class="_yith-wcdn-update-specific-status-select yith_wcdn_multiple_role" data-field="notification-type">
                                                <?php foreach ( wc_get_order_statuses() as $key_type => $type ): ?>
                                                    <option
                                                        value="<?php echo $key_type ?>"><?php echo $type ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        <?php }?>
                                    </td>
                                </tr>
                                <tr class="yith-wcdn-product-sold-update" id="yith-wcdn-id-product-sold-update">
                                    <th>
                                        <?php esc_html_e('Select products','yith-desktop-notifications-for-woocommerce') ?>
                                    </th>
                                    <td>
                                        <?php if ( isset($db_value[ $key ]['products'] )) { ?>

                                            <?php if( version_compare( WC()->version, '2.7.0', '>=' ) ) { ?>

                                                <select class="wc-product-search" multiple="multiple" style="width:350px;"
                                                        id="yith-wcdn-select-products" name="product_ids[]"
                                                        data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'yith-desktop-notifications-for-woocommerce'); ?>"
                                                        data-action="woocommerce_json_search_products_and_variations">
                                                    <?php
                                                    $product_ids = $db_value[$key]['products'];

                                                        if (is_array($product_ids)) {
                                                            foreach ($product_ids as $product_id) {
                                                                $product = wc_get_product($product_id);
                                                                if (is_object($product)) {
                                                                    echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
                                                                }
                                                            }
                                                        }

                                                    ?>
                                                </select>

                                            <?php } else { ?>

                                                <input type="hidden" class="wc-product-search" id="yith-wcdn-select-products" name="product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'yith-desktop-notifications-for-woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" data-selected="<?php
                                                $product_ids = array_filter( array_map( 'absint', explode( ',',$db_value[ $key ]['products']) ) );
                                                $json_ids    = array();

                                                foreach ( $product_ids as $product_id ) {
                                                    $product = wc_get_product( $product_id );
                                                    if ( is_object( $product ) ) {
                                                        $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
                                                    }
                                                }

                                                echo esc_attr( json_encode( $json_ids ) );
                                                ?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />

                                            <?php } ?>

                                        <?php } else {?>

                                            <?php if( version_compare( WC()->version, '2.7.0', '>=' ) ) { ?>

                                                <select class="wc-product-search" multiple="multiple" style="width: 350px;" id="yith-wcdn-select-products" name="product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'yith-desktop-notifications-for-woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations"></select>

                                            <?php } else { ?>

                                                <input type="hidden" class="wc-product-search" id="yith-wcdn-select-products" name="product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'yith-desktop-notifications-for-woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" />

                                            <?php } ?>

                                        <?php }?>
                                    </td>
                                </tr>
                                <tr class="yith-wcdn-notification-name">
                                    <th>
                                        <?php esc_html_e('Title:','yith-desktop-notifications-for-woocommerce') ?>
                                    </th>
                                    <td>
                                        <input type="text" name="_yith_desktop_notifications_title" class="_yith-desktop-update-notifications-title yith-wcdn-style" id="_yith_desktop_update_notifications_title" value="<?php echo $db_value[ $key ]['title'] ?>"/>
                                    </td>
                                </tr>
                                <tr class="yith-wcdn-notification-message">
                                    <th>
                                        <?php esc_html_e('Description:','yith-desktop-notifications-for-woocommerce') ?>
                                    </th>
                                    <td>
                                        <textarea name="_yith_desktop_notifications_description" class="_yith-desktop-update-notifications-description yith-wcdn-style" id="_yith_desktop_update_notifications_description"><?php echo $db_value[ $key ]['description'] ?></textarea>
                                        <div>
                                            <?php esc_html_e('Available placeholders:','yith-desktop-notifications-for-woocommerce') ?>
                                            <ul class="yith-wcdn-update-placeholder-available">
                                                <li class="yith-wcdn-list yith-wcdn-out_of_stock yith-wcdn-low_stock">{product_id}</li>
                                                <li class="yith-wcdn-list yith-wcdn-out_of_stock yith-wcdn-low_stock">{product_name}</li>
                                                <li class="yith-wcdn-list yith-wcdn-sold yith-wcdn-placed yith-wcdn-refund yith-wcdn-status_changed yith-wcdn-new_quote">{username}</li>
                                                <li class="yith-wcdn-list yith-wcdn-sold yith-wcdn-placed yith-wcdn-refund yith-wcdn-status_changed">{order_id}</li>
                                                <?php
                                                if ( defined( 'YITH_YWRAQ_PREMIUM' ) ) {
                                                    ?>
                                                    <li class="yith-wcdn-list yith-wcdn-new_quote">{quote_id}</li>
                                                    <?php
                                                }
                                                ?>
                                                <li class="yith-wcdn-list yith-wcdn-placed yith-wcdn-refund yith-wcdn-status_changed">{order_total}</li>
                                                <li class="yith-wcdn-list yith-wcdn-status_changed">{new_status}</li>
                                                <li class="yith-wcdn-list yith-wcdn-status_changed">{old_status}</li>
                                                <li class="yith-wcdn-list yith-wcdn-sold">{products_sold}</li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="yith-wcdn-notification-role-user">
                                    <th>
                                        <?php esc_html_e('Role notification:','yith-desktop-notifications-for-woocommerce') ?>
                                    </th>
                                    <td>
                                        <select multiple class="_yith-desktop-update-notifications-role-user yith_wcdn_multiple_role" id="_yith_desktop_update_notifications_role_user">
                                            <?php foreach (get_editable_roles() as $role => $rolename ): ?>
                                                <option
                                                    value="<?php echo $role ?>" <?php selected(in_array($role,(array)$db_value[ $key ]['role_user'])) ?>><?php echo $rolename['name'] ?></option>
                                            <?php endforeach ?>
                                            <select>
                                    </td>
                                </tr>
                                <tr class="yith-wcdn-notification-icon">
                                    <th>
                                        <?php esc_html_e('Notification icon:','yith-desktop-notifications-for-woocommerce') ?>
                                    </th>
                                    <td>
                                        <select class="_yith_list_desktop_notifications_icon yith_wcdn_multiple_role yith_wcdn_icon">
                                            <?php
                                            $dh = opendir(YITH_WCDN_PATH.'assets/yith-notifications/notification-icons');
                                            while (false !== ($filename = readdir($dh))) {
                                                if($filename == '.' || $filename == '..') continue;
                                                echo '<option  value="'.YITH_WCDN_ASSETS_URL.'yith-notifications/notification-icons/'.$filename.'"'.selected(YITH_WCDN_ASSETS_URL.'yith-notifications/notification-icons/'.$filename,$db_value[ $key ]['icon']).'>'.$filename.'</option>';
                                            }
                                            $list_of_icons = get_option('yith_wcdn_upload_icon');
                                            if($list_of_icons !== false){
                                                foreach ($list_of_icons as $icons) {
                                                    echo '<option  value="'.$icons.'"'.selected($icons,$db_value[ $key ]['icon']).'>'.basename($icons).'</option>';

                                                }
                                            }
                                            ?>
                                            <select>
                                                <img src="" class="_yith_list_notification_logo" width="50"/>
                                    </td>

                                </tr>
                                <tr class="yith-wcdn-notification-sound">
                                    <th>
                                        <?php esc_html_e('Notification sound:','yith-desktop-notifications-for-woocommerce') ?>
                                    </th>
                                    <td>
                                        <select class="_yith_list_desktop_notifications_sound yith_wcdn_multiple_role yith_wcdn_sound">
                                            <?php
                                            $dh = opendir(YITH_WCDN_PATH.'assets/yith-notifications/notification-sounds');
                                            while (false !== ($filename = readdir($dh))) {
                                                if($filename == '.' || $filename == '..') continue;
                                                echo '<option  value="'.YITH_WCDN_ASSETS_URL.'yith-notifications/notification-sounds/'.$filename.'"'.selected(YITH_WCDN_ASSETS_URL.'yith-notifications/notification-sounds/'.$filename,$db_value[ $key ]['sound']).' >'.$filename.'</option>';
                                            }
                                            $list_of_audios = get_option('yith_wcdn_upload_audio');
                                            if($list_of_audios !== false){
                                                foreach ($list_of_audios as $audios) {
                                                    echo '<option  value="'.$audios.'"'.selected($audios,$db_value[ $key ]['sound']).' >'.basename($audios).'</option>';

                                                }
                                            }
                                            ?>
                                            <select>
                                                <input class="yith_list_click_audio_preview _yith_click_new_audio_preview button" type="button" class="button button-default" value="<?php esc_html_e('Click here to preview','yith-desktop-notifications-for-woocommerce'); ?>" />
                                                <audio class="yith_list_audio_preview"><source src="" type="audio/mp3"></audio>
                                    </td>

                                </tr>
                                <tr class="yith-wcdn-notification-length">
                                    <th>
                                        <?php esc_html_e('Notification length:','yith-desktop-notifications-for-woocommerce') ?>
                                    </th>
                                    <td>
                                        <input type="number" name="_yith_desktop_notifications_length" class="_yith_desktop_update_notifications_length yith-wcdn-style" value="<?php echo $db_value[ $key ]['time_notification'] ?>"/>
                                        <?php echo wc_help_tip(esc_html__('Time in seconds, "0" = Default notification length','yith-desktop-notifications-for-woocommerce')); ?>
                                    </td>

                                </tr>

                                <tr class="yith-wcdn-notification-update-or-delete">
                                    <td>
                                        <div>
                                            <input style="" class=" button button-primary _yith_wcdn_update_notification" type="button"
                                                   value="<?php esc_html_e( 'Update', 'yith-desktop-notifications-for-woocommerce' ) ?>"/>

                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <input class=" button button-default _yith_wcdn_delete_notification" type="button"
                                                   value="<?php esc_html_e( 'Delete', 'yith-desktop-notifications-for-woocommerce' ) ?>"/>
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
        <?php endforeach; ?>
    <?php endif; ?>
</div>
