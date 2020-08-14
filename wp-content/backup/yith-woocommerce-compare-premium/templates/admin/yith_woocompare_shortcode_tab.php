<?php
/**
 * Shortcode tab template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
    exit;
} // Exit if accessed directly

$message = __( 'Select products to add in compare table', 'yith-woocommerce-compare')
?>

<h3><?php esc_html_e( 'Build your own shortcode', 'yith-woocommerce-compare')   ?></h3>

<div class="yith-woocompare-shortcode-tab">

    <div class="shortcode-options">

        <table class="form-table">
            <tbody>

            <tr>
                <th>
                    <label for="yith_products"><?php esc_html_e('Add products', 'yith-woocommerce-compare'); ?></label>
                    <img class="help_tip" data-tip='<?php echo esc_html( $message ); ?>' src="<?php echo WC()->plugin_url(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>/assets/images/help.png" height="16" width="16" />
                </th>
                <td>
                    <?php
                        yit_add_select2_fields( array(
                            'class'             => 'wc-product-search yith_woocompare_tab_shortcode_products',
                            'id'                => 'yith_products',
                            'name'              => 'yith_products',
                            'data-placeholder'  => __( 'Search for a product..', 'yith-woocommerce-compare' ),
                            'data-multiple'     => true,
                            'data-action'       => 'woocommerce_json_search_products',
                        ) );
                    ?>
                </td>
            </tr>

            </tbody>
        </table>

    </div>

    <div class="shortcode-preview">
        <?php echo '[yith_woocompare_table]' ?>
    </div>
    <span class="description"><?php esc_html_e('Copy and paste this shortcode in your page.', 'yith-woocommerce-compare'); ?></span>

</div>
