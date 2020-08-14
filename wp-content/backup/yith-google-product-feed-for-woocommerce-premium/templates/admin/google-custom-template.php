<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


$custom_fields = get_option('yith_wcgpf_custom_fields',array());

$custom_fields = empty( $custom_fields ) || !is_array( $custom_fields ) ? array( '' ) : $custom_fields;
?>

<h2><?php esc_html_e( 'Google Custom Fields', 'yith-google-product-feed-for-woocommerce' ) ?></h2>
<form method="post" id="ywcgpf-form">
    <div id="yith-wcgpf-custom-fields-tab-wrapper">

        <?php foreach ( $custom_fields as $custom_field ) : ?>
            <div class="yith-wcgpf-custom-field-wrap">
                <input type="text" class="yith-wcgpf-custom-field" name="yith-wcgpf-custom-field[]" value="<?php echo $custom_field; ?>">
                <input type="button" class="button button-primary yith-wcgpf-add-row"
                       value="<?php esc_html_e('Add new row', 'yith-google-product-feed-for-woocommerce') ?>">
                <input type="button" class="button button-default yith-wcgpf-delete-row"
                       value="<?php esc_html_e('Delete', 'yith-google-product-feed-for-woocommerce') ?>">
            </div>
        <?php endforeach; ?>

    </div>

    <div id="yith-wcgpf-custom-fields-tab-actions">
        <input type="submit" id="yith-wcgpf-custom-fields-tab-actions-save"
               class="button button-primary" value="<?php esc_html_e( 'Save', 'yith-google-product-feed-for-woocommerce' ) ?>">
    </div>
</form>