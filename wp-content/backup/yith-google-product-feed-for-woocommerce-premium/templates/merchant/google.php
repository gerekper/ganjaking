<?php

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$google_merchant =  YITH_Google_Product_Feed()->merchant_google;

if ( isset($show_templates) && !$show_templates ) {
    if ( $feed_edit ) {
        $update = $feed_edit['feed_template'];
    } else {
        $update = $google_merchant->default_rows();
    }
} else {
    if ( $template_id == 'default') {
        $update = $google_merchant->google_rows();
    } elseif (($template =get_post_meta($template_id,'yith_wcgpf_save_template',true))) {
        $update = $template;
    }else {
        $update = $google_merchant->default_rows();
    }
}

$count = count($update);

for ( $i=0; $i<$count;$i++ ) {
    ?>
    <tr class="yith-wcgpf-sortable">
        <td class="drag-icon">
            <i class="dashicons dashicons-move"></i>
        </td>
        <td class="yith-wcgpf-td">
            <select name="yith-wcgpf-attributes[]" id="yith-wcgpf-attributes"
                    class="yith-wcgpf-attributes-select yith-wcgpf-templates">
                <?php echo isset($update[$i]['attributes']) ? $google_merchant->get_attributes($update[$i]['attributes']) : $google_merchant->get_attributes()  ?>
            </select>
        </td>
        <td class="yith-wcgpf-td">
            <input type="text" name="yith_wcgpf_prexif[]" class="yith-wcgpf-prefix yith-wcgpf-templates" value="<?php echo isset($update[$i]['prefix']) ? $update[$i]['prefix'] : ''; ?>">
        </td>
        <td class="yith-wcgpf-td">
            <select name="yith-wcgpf-value[]" id="yith-wcgpf-value" class="yith-wcgpf-value-select yith-wcgpf-templates">
                <?php echo isset($update[$i]['value']) ? $google_merchant->get_values($update[$i]['value']) : $google_merchant->get_values() ; ?>
            </select>
        <td class="yith-wcgpf-td">
            <input type="text" name="yith_wcgpf_sufix[]" class="yith-wcgpf-sufix yith-wcgpf-templates" value="<?php echo isset($update[$i]['suffix']) ? $update[$i]['suffix'] : ''; ?>">
        </td>
        <td class="">
            <input type="button" class="button button-default yith-wcgpf-delete"
                   value="<?php esc_html_e('Delete', 'yith-google-product-feed-for-woocommerce') ?>">
        </td>
    </tr>
    <?php
}
