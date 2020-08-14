<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @var YITH_Vendor $vendor
 */

$allowed_countries = WC()->countries->get_allowed_countries();
$continents        = WC()->countries->get_continents();
$shipping_methods  = WC()->shipping->load_shipping_methods();
?>

<h3><?php esc_html_e( 'Shipping Areas', 'yith-woocommerce-product-vendors' ); ?></h3>

<input type="hidden" name="yith_vendor_data[zone_data]" data-attribute="zone_name" value="" />

<table class="wc-shipping-zones widefat">
    <thead>
    <tr>
        <th class="wc-shipping-zone-sort"><?php echo wc_help_tip( __( 'Drag and drop to re-order your custom zones. This is the order in which they will be matched against the customer address.', 'yith-woocommerce-product-vendors' ) ); ?></th>
        <th class="wc-shipping-zone-name"><?php esc_html_e( 'Zone Name', 'yith-woocommerce-product-vendors' ); ?></th>
        <th class="wc-shipping-zone-region"><?php esc_html_e( 'Region(s)', 'yith-woocommerce-product-vendors' ); ?></th>
        <th class="wc-shipping-zone-methods"><?php esc_html_e( 'Shipping Method(s)', 'yith-woocommerce-product-vendors' ); ?></th>
    </tr>
    </thead>
    <tbody class="wc-shipping-zone-rows">

    <?php

    if( is_array( $vendor->zone_data ) ) {
        
        foreach( $vendor->zone_data as $key => $zone ) {
            YITH_Vendor_Shipping()->admin->print_line_option( $key , $zone , $continents , $allowed_countries , $shipping_methods );
        }
    }

    ?>
    </tbody>
</table>

<script type="text/template" id="tmpl-wc-modal-add-shipping-method">
    <div class="wc-backbone-modal">
        <div class="wc-backbone-modal-content">
            <section class="wc-backbone-modal-main" role="main">
                <header class="wc-backbone-modal-header">
                    <h1><?php _e( 'Add shipping method', 'yith-woocommerce-product-vendors' ); ?></h1>
                    <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                        <span class="screen-reader-text"><?php _e( 'Close modal panel', 'yith-woocommerce-product-vendors' ); ?></span>
                    </button>
                </header>
                <article>
                    <form action="" method="post">
                        <div class="wc-shipping-zone-method-selector">
                            <p><?php esc_html_e( 'Choose the shipping method you wish to add. Only shipping methods which support zones are listed.', 'yith-woocommerce-product-vendors' ); ?></p>
                            <select id="yith-wpv-shipping-method-dropdown" name="add_method_id">
                                <?php
                                foreach ( $shipping_methods as $method ) {
                                    if ( ! $method->supports( 'shipping-zones' ) ) {
                                        continue;
                                    }

                                    echo '<option data-description="' . esc_attr( $method->method_description ) . '" value="' . esc_attr( $method->id ) . '">' . esc_attr( $method->method_title ) . '</li>';
                                }
                                ?>
                            </select>
                            <input type="hidden" name="yith_wpd_area_key" value="{{{ data.yith_wpd_area_key }}}" />
                        </div>
                    </form>
                </article>
                <footer>
                    <div class="inner">
                        <button id="btn-ok" class="button button-primary button-large"><?php _e( 'Add shipping method', 'yith-woocommerce-product-vendors' ); ?></button>
                    </div>
                </footer>
            </section>
        </div>
    </div>
    <div class="wc-backbone-modal-backdrop modal-close"></div>
</script>

<script type="text/template" id="tmpl-wc-modal-edit-shipping-method">
    <div class="wc-backbone-modal wc-backbone-modal-edit-shipping-mode">
        <div class="wc-backbone-modal-content">
            <section class="wc-backbone-modal-main" role="main">
                <header class="wc-backbone-modal-header">
                    <h1>{{{ data.yith_wpd_shipping_title }}} <?php _e( 'Settings', 'yith-woocommerce-product-vendors' ); ?></h1>
                    <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                        <span class="screen-reader-text"><?php _e( 'Close modal panel', 'yith-woocommerce-product-vendors' ); ?></span>
                    </button>
                </header>
                <article>
                    <form action="" method="post" >
                        {{{ data.yith_wpd_form_data }}}
                        <input type="hidden" name="yith_wpd_parent_key" value="{{{ data.yith_wpd_parent_key }}}" />
                        <input type="hidden" name="yith_wpd_shipping_key" value="{{{ data.yith_wpd_shipping_key }}}" />
                        <input type="hidden" name="yith_wpd_shipping_type" value="{{{ data.yith_wpd_shipping_type }}}" />
                    </form>
                </article>
                <footer>
                    <div class="inner">
                        <button id="btn-ok" class="yith-shipping-method-save-button button button-primary button-large"><?php _e( 'Save Changes', 'yith-woocommerce-product-vendors' ); ?></button>
                    </div>
                </footer>
            </section>
        </div>
    </div>
    <div class="wc-backbone-modal-backdrop modal-close"></div>
</script>