<?php
if (!defined('ABSPATH')) {
    exit;
}


?>

<tr valign="top">
    <th scope="row" class="titledesc"></th>
    <td class="forminp" colspan="1" style="padding: 0px 20px;">

        <input type="button" class="button button-primary ywgc_update_cron_button"
               value="<?php echo __('Update Cron', 'yith-woocommerce-barcodes'); ?>">
        <span class="description"><?php esc_attr_e($desc); ?></span>
	</td>
</tr>


