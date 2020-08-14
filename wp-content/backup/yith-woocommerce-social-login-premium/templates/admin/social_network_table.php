<?php
/**
 * Show Social Connection table in general settings
 *
 * @package YITH WooCommerce Social Login Premium
 * @since   1.0.0
 * @author  YITH
 */
?>
<tr valign="top">
    <th scope="row" class="titledesc"><?php _e('Social Connections', 'yith-woocommerce-social-login') ?></th>
    <td class="forminp">
<table class="ywsl_social_networks widefat" cellspacing="0">
    <thead>
    <tr>
        <th class="name">Social Network</th>
        <th class="status">Status</th>
        <th class="settings">&nbsp;</th>
    </tr>
    </thead>
    <tbody class="ui-sortable">

    <?php foreach ( $tabs as $key => $tab ) :
            $status = ( get_option('ywsl_'.$key.'_enable') == 'yes' ) ? array('enabled',__('Enabled', 'yith-woocommerce-social-login') ) : array('disabled',__('Disabled', 'yith-woocommerce-social-login') );
        ?>
        <tr>
            <td class="name ui-sortable-handle">
                <input type="hidden" name="<?php echo $id ?>[]" value="<?php echo $key ?>">
                <span class="icon-social"><img src="<?php echo YITH_YWSL_ASSETS_URL.'/images/'.$key.'.png' ?>"></span>
                <?php echo $tab ?>
            </td>
            <td class="status ui-sortable-handle">
                <span class="status-<?php echo $status[0] ?>"><?php echo $status[1] ?></span>
            </td>
            <td class="settings ui-sortable-handle">
                <a class="button" href="<?php echo admin_url( "admin.php?page={$panel_page}&tab={$key}" ) ?>"><?php _e( 'Settings', 'yith-woocommerce-social-login' ) ?></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="3">
            <span class="description"><?php _e( 'Drag and drop the above listed icons for social networks to set their display order.', 'yith-woocommerce-social-login' ) ?></span>
        </th>
    </tr>
    </tfoot>
</table>
        </td>
    </tr>