<?php
$container_style = "margin: 0 35px 0 2px;border: 1px solid #d8d8d8;border-top: none;box-sizing: border-box;";
$cta_url         = $cta_label = false;
?>

<div class='woocommerce-layout'>
    <div id="yith-pos-admin-root" class="yith-plugin-fw-panel-custom-tab-container" style="<?php echo $container_style ?>">
        <?php if ( !yith_pos_is_wc_admin_enabled() ): ?>
            <div class="yith-pos-dashboard-required-plugin yith-plugin-fw">
                <div class="yith-pos-dashboard-required-plugin__icon">
                    <?php yith_pos_svg( 'yith-and-wc-admin', true ) ?>
                </div>
                <div class="yith-pos-dashboard-required-plugin__message">
                    <?php
                    echo sprintf(
                        __( '%s requires %s plugin to show reports!', 'yith-point-of-sale-for-woocommerce' ),
                        '<strong>' . YITH_POS_PLUGIN_NAME . '</strong>',
                        '<strong>' . 'WooCommerce Admin' . '</strong>'
                    )
                    ?>
                </div>

                <?php
                $wc_admin_file      = 'woocommerce-admin/woocommerce-admin.php';
                $wc_admin_installed = file_exists( WP_PLUGIN_DIR . '/' . $wc_admin_file );

                if ( $wc_admin_installed ) {
                    if ( !is_plugin_active( $wc_admin_file ) && current_user_can( 'activate_plugin', $wc_admin_file ) ) {
                        $activation_url = "plugins.php?action=activate&plugin={$wc_admin_file}&plugin_status=active";
                        $cta_url        = wp_nonce_url( self_admin_url( $activation_url ), 'activate-plugin_' . $wc_admin_file );
                        $cta_label      = esc_html__( 'Activate WooCommerce Admin', 'yith-point-of-sale-for-woocommerce' );
                    } elseif ( is_plugin_active( $wc_admin_file ) && !yith_pos_check_wc_admin_min_version() ) {
                        echo "<div class='yith-pos-admin-notice yith-pos-admin-notice--error'>" . esc_html__( 'You are using an outdated version of WooCommerce Admin', 'yith-point-of-sale-for-woocommerce' ) . "</div>";

                        if ( current_user_can( 'update_plugins' ) ) {
                            $activation_url = "update.php?action=upgrade-plugin&plugin={$wc_admin_file}";
                            $cta_url        = wp_nonce_url( self_admin_url( $activation_url ), 'upgrade-plugin_' . $wc_admin_file );
                            $cta_label      = esc_html__( 'Update WooCommerce Admin', 'yith-point-of-sale-for-woocommerce' );
                        }
                    }
                } elseif ( current_user_can( 'install_plugins' ) ) {
                    $install_url = 'update.php?action=install-plugin&plugin=woocommerce-admin';
                    $cta_url     = wp_nonce_url( self_admin_url( $install_url ), 'install-plugin_woocommerce-admin' );
                    $cta_label   = esc_html__( 'Install WooCommerce Admin', 'yith-point-of-sale-for-woocommerce' );
                }
                ?>

                <?php if ( $cta_url && $cta_label ): ?>
                    <div class="yith-pos-dashboard-required-plugin__call-to-action">
                        <a href="<?php echo esc_url( $cta_url ); ?>" class="yith-pos-admin-hero-button"><?php echo $cta_label; ?></a>
                    </div>
                <?php endif; ?>


            </div>
        <?php endif; ?>
    </div>
</div><!-- /.woocommerce-layout -->
