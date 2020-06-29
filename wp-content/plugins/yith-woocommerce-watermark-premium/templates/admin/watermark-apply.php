<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$value = get_option( $option['id'] );
?>
    <tr valign="top">
        <th scope="row" class="titledesc"><?php _e( 'Apply all watermarks', 'yith-woocommerce-watermark' ); ?></th>
        <td class="forminp">
            <input type="button" class="button button-primary ywcwat_apply_all_watermark"
                   value="<?php _e( 'Apply All Watermarks', 'yith-woocommerce-watermark' ); ?>">
            <span class="description"> <?php _e( 'Apply all watermarks created to all your product images', 'yith-woocommerce-watermark' ); ?></span>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc"><?php _e( 'Reset', 'yith-woocommerce-watermark' ); ?></th>
        <td class="forminp forminp-button">
            <input type="button" class="button button-primary" id="ywcwat_reset_watermark"
                   value="<?php _e( 'Reset', 'yith-woocommerce-watermark' ); ?>">
            <span class="description"> <?php _e( 'Delete all product images with watermark (once completed, you have to deactivate the plugin and regenerate image thumbnails).', 'yith-woocommerce-watermark' ); ?></span>
        </td>
    </tr>
    <tr valign="top">
        <td colspan="2">
            <div class="ywcwat_messages">
                <span class="ywcwat_icon"></span>
                <span class="ywcwat_text"></span>
            </div>
        </td>
    </tr>
    <tr valign="top">
        <td class="forminp forminp-progressbar" colspan="2">
            <div class="ywcwat-progressbar" id="ywcwat-progressbar_all">
                <div class="ywcwat-progressbar-percent" id="ywcwat-progressbar-percent_all"></div>
            </div>
        </td>
    </tr>
    <tr valign="top" class="ywcwat_log_row">
        <td></td>
        <td colspan="2" class="ywcwat_log_content">
			<?php
			$show_log = __( 'Show Log', 'yith-woocommerce-watermark' );
			$hide_log = __( 'Hide Log', 'yith-woocommerce-watermark' );
			?>
            <input type="button" class="button button-secondary" id="ywcwat_show_log" value="<?php echo $show_log; ?>"
                   data-hide_log="<?php echo $hide_log; ?>"/>
            <div id="ywcwat_log_container" style="display: none;"></div>
        </td>
    </tr>
<?php
