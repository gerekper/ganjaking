<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! function_exists( 'yith_deactive_jetpack_module' ) ) {
    function yith_deactive_jetpack_module( $yith_jetpack , $premium_constant , $to_active  ) {


        if ( ! isset( $yith_jetpack ) ) {
            return;
        }

        if ( is_callable( $yith_jetpack, 'deactivate_module_by_premium_constant' ) && $yith_jetpack->deactivate_module_by_premium_constant( $premium_constant ) ) {

             if( ! function_exists( 'wp_create_nonce' ) ){
                header( 'Location: plugins.php');
                exit();
            }


            global $status, $page, $s;
            $redirect    = 'plugins.php?action=activate&plugin=' . $to_active . '&plugin_status=' . $status . '&paged=' . $page . '&s=' . $s;
            $redirect    = esc_url_raw( add_query_arg( '_wpnonce', wp_create_nonce( 'activate-plugin_' . $to_active ), $redirect ) );

            header( 'Location: ' . $redirect );
            exit();
        }
    }
}

if( ! function_exists( 'install_premium_woocommerce_admin_notice' ) ) {
    /**
     * Print an admin notice if woocommerce is deactivated
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since 1.0
     * @return void
     * @use admin_notices hooks
     */
    function install_premium_woocommerce_admin_notice() { ?>
        <div class="error">
            <p><?php echo 'YITH WooCommerce Multi Vendor ' . __( 'is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-product-vendors' ); ?></p>
        </div>
    <?php
    }
}