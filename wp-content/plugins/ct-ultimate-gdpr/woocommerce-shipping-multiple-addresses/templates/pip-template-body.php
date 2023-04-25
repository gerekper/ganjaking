<?php
$packages                  = $order->get_meta( '_wcms_packages' );
$packages_shipping_methods = $order->get_meta( '_shipping_methods' );
$order_shipping_methods    = $order->get_shipping_methods();

foreach ( $packages as $pkg_idx => $package ):
    if (! is_array($packages_shipping_methods) ) {
        $order_method = current( $order_shipping_methods );
        $packages_shipping_methods = array(
            $pkg_idx => array(
                'id' => $order_method['method_id'],
                'name' => $order_method['name']
            )
        );
    }

    $method = $packages_shipping_methods[$pkg_idx]['label'];
    $order_method = '';

    if ( isset($packages_shipping_methods[ $pkg_idx ]['id']) ) {
        foreach ( $order_shipping_methods as $ship_id => $ship_method ) {
            if ($ship_method['method_id'] == $packages_shipping_methods[ $pkg_idx ]['id']) {
                $method = $ship_method['name'];
                $order_method = $ship_method;
                unset( $order_shipping_methods[ $ship_id ] );
                break;
            }
        }
    }
?>
<header>
    <a class="print" href="#" onclick="window.print()"><?php _e('Print', 'woocommerce-pip'); ?></a>
    <div style="float: left; width: 49%;">
        <?php echo woocommerce_pip_print_logo(); ?>
        <?php if ($action == 'print_invoice') { ?>
            <h3><?php _e('Invoice', 'woocommerce-pip'); ?> (<?php echo woocommerce_pip_invoice_number(WC_MS_Compatibility::get_order_prop( $order, 'id' )); ?>)</h3>
        <?php } else { ?>
            <h3><?php _e('Packing list', 'woocommerce-pip'); ?></h3>
        <?php } ?>
        <h3><?php _e('Order', 'woocommerce-pip'); ?> <?php echo $order->get_order_number(); ?> &mdash; <time datetime="<?php echo date("Y/m/d", strtotime(WC_MS_Compatibility::get_order_prop( $order, 'order_date' ))); ?>"><?php echo date("Y/m/d", strtotime(WC_MS_Compatibility::get_order_prop( $order, 'order_date' ))); ?></time></h3>
    </div>
    <div style="float: right; width: 49%; text-align:right;">
        <?php echo woocommerce_pip_print_company_name(); ?>
        <?php echo woocommerce_pip_print_company_extra(); ?>
    </div>
    <div style="clear:both;"></div>

</header>
<section>
    <div class="article">
        <header>

            <div style="float:left; width: 49%;">

                <h3><?php _e('Billing address', 'woocommerce-pip'); ?></h3>

                <p>
                    <?php echo $order->get_formatted_billing_address(); ?>
                </p>
                <?php do_action( 'wc_print_invoice_packing_template_body_after_billing_address', $order ); ?>
                <?php if ( $order->get_meta( 'VAT Number' ) && $action == 'print_invoice') : ?>
                    <p><strong><?php _e('VAT:', 'woocommerce-pip'); ?></strong> <?php echo $order->get_meta( 'VAT Number' ); ?></p>
                <?php endif; ?>
                <?php if ( WC_MS_Compatibility::get_order_prop( $order, 'billing_email' ) ) : ?>
                    <p><strong><?php _e('Email:', 'woocommerce-pip'); ?></strong> <?php echo WC_MS_Compatibility::get_order_prop( $order, 'billing_email' ); ?></p>
                <?php endif; ?>
                <?php if ( WC_MS_Compatibility::get_order_prop( $order, 'billing_phone' ) ) : ?>
                    <p><strong><?php _e('Tel:', 'woocommerce-pip'); ?></strong> <?php echo WC_MS_Compatibility::get_order_prop( $order, 'billing_phone' ); ?></p>
                <?php endif; ?>

            </div>

            <div style="float:right; width: 49%;">

                <h3><?php _e('Shipping address', 'woocommerce-pip'); ?></h3>

                <p><?php echo wcms_get_formatted_address( $package['destination'] ); ?></p>

                <?php if ( $order->get_meta( '_tracking_provider' ) ) : ?>
                    <p><strong><?php _e('Tracking provider:', 'woocommerce-pip'); ?></strong> <?php echo $order->get_meta( '_tracking_provider' ); ?></p>
                <?php endif; ?>
                <?php if ( $order->get_meta( '_tracking_number' ) ) : ?>
                    <p><strong><?php _e('Tracking number:', 'woocommerce-pip'); ?></strong> <?php echo $order->get_meta( '_tracking_number' ); ?></p>
                <?php endif; ?>

            </div>

            <div style="clear:both;"></div>

            <?php if ( 'print_packing' == $action && 'yes' == get_option( 'woocommerce_calc_shipping' ) ) : ?>
                <div>
                    <strong><?php _e( 'Shipping:', 'woocommerce-pip' ); ?></strong>
                    <?php echo $method; ?>
                </div>
            <?php endif; ?>

            <?php if ( !empty($package['note']) ) { ?>
                <div>
                    <h3><?php _e('Order notes', 'woocommerce-pip'); ?></h3>
                    <?php echo $package['note']; ?>
                </div>
            <?php } ?>

            <?php if ( !empty($package['date']) ) { ?>
                <div>
                    <h3><?php _e('Shipping Date', 'woocommerce-pip'); ?></h3>
                    <?php echo $package['date']; ?>
                </div>
            <?php } ?>

        </header>
        <div class="datagrid">
            <?php if ($action == 'print_invoice') { ?>
                <table>
                    <thead>
                    <tr>
                        <th scope="col" style="text-align:left; width: 15%;"><?php _e('SKU', 'woocommerce-pip'); ?></th>
                        <th scope="col" style="text-align:left; width: 40%;"><?php _e('Product', 'woocommerce-pip'); ?></th>
                        <th scope="col" style="text-align:left; width: 15%;"><?php _e('Quantity', 'woocommerce-pip'); ?></th>
                        <th scope="col" style="text-align:left; width: 30%;"><?php _e('Price', 'woocommerce-pip'); ?></th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:left; padding-top: 12px;">&nbsp;</th>
                        <th scope="row" style="text-align:right; padding-top: 12px;"><?php _e('Subtotal:', 'woocommerce-pip'); ?></th>
                        <td style="text-align:left; padding-top: 12px;"><?php echo wc_price( $package['contents_cost'] ); ?></td>
                    </tr>
                    <?php if (get_option('woocommerce_calc_shipping')=='yes') : ?><tr>
                        <th colspan="2" style="text-align:left; padding-top: 12px;">&nbsp;</th>
                        <th scope="row" style="text-align:right;"><?php _e('Shipping:', 'woocommerce-pip'); ?></th>
                        <td style="text-align:left;"><?php
                            $tax_display = get_option( 'woocommerce_tax_display_cart' );

                            if ( empty( $order_method ) ) {
                                echo '-';
                            } else {
                                if ( $order_method['cost'] > 0 ) {
                                    // Show shipping excluding tax
                                    $shipping = wc_price( $order_method['cost'], array(
                                        'currency' => version_compare( WC_VERSION, '3.0', '<' ) ? $order->get_order_currency() : $order->get_currency(),
                                    ) );

                                    $shipping .= sprintf( __( '&nbsp;<small>via %s</small>', 'wc_shipping_multiple_address' ), $order_method['name'] );

                                } elseif ( $order_method['name'] ) {
                                    $shipping = $order_method['name'];
                                } else {
                                    $shipping = __( 'Free!', 'wc_shipping_multiple_address' );
                                }

                                echo $shipping;
                            }

                        ?></td>
                        </tr><?php endif; ?>
                    <tr>
                        <th colspan="2" style="text-align:left; padding-top: 12px;">&nbsp;</th>
                        <th scope="row" style="text-align:right;"><?php _e('Total:', 'woocommerce-pip'); ?></th>
                        <td style="text-align:left;"><?php echo wc_price($package['contents_cost'] + $order_method['cost']); ?> <?php _e('- via', 'woocommerce-pip'); ?> <?php echo ucwords(WC_MS_Compatibility::get_order_prop( $order, 'payment_method_title' )); ?></td>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php
                    foreach( $package['contents'] as $item ) {

                        // get the product; if this variation or product has been deleted, this will return null...
                        $_product = $item->get_product();

                        $sku = $variation = '';

                        if ( $_product ) $sku = $_product->get_sku();

                        $attributes = WC_MS_Compatibility::get_item_data( $item, true );

                        if ( ! empty( $attributes ) ) {
                            $variation = '<small style="display: block; margin: 5px 0 10px 10px;">'. str_replace( "\n", "<br/>", $attributes ) .'</small>';
                        }

                        ?>
                        <tr>
			                <td style="text-align:left; padding: 3px;"><?php echo $sku; ?></td>
			                <td style="text-align:left; padding: 3px;"><?php echo apply_filters( 'woocommerce_order_product_title', get_the_title( $_product ), $_product ) . $variation; ?></td>
			                <td style="text-align:left; padding: 3px;"><?php echo $item['quantity']; ?></td>
                            <td style="text-align:left; padding: 3px;">
                                <?php
                                if ( 'excl' === get_option( 'woocommerce_tax_display_cart' ) || ! WC_MS_Compatibility::get_order_prop( $order, 'prices_include_tax' ) ) {
                                    $ex_tax_label = ( WC_MS_Compatibility::get_order_prop( $order, 'prices_include_tax' ) ) ? 1 : 0;
                                    echo wc_price( $order->get_line_subtotal( $item ), array( 'ex_tax_label' => $ex_tax_label ) );
                                } else {
                                    echo wc_price( $order->get_line_subtotal( $item, TRUE ) );
                                }
                                ?>
			                </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <table>
                    <thead>
                    <tr>
                        <th scope="col" style="text-align:left; width: 22.5%;"><?php _e('SKU', 'woocommerce-pip'); ?></th>
                        <th scope="col" style="text-align:left; width: 57.5%;"><?php _e('Product', 'woocommerce-pip'); ?></th>
                        <th scope="col" style="text-align:left; width: 15%;"><?php _e('Quantity', 'woocommerce-pip'); ?></th>
                        <th scope="col" style="text-align:left; width: 20%;"><?php _e('Total Weight', 'woocommerce-pip'); ?></th>
                    </tr>
                    </thead>
                        <tbody>
                        <?php
                        foreach( $package['contents'] as $item_key => $item ) {

                            $attributes = WC_MS_Compatibility::get_item_data( $item, true );

                            // get the product; if this variation or product has been deleted, this will return null...
                            $_product = $item->get_product();

                            $sku = $variation = '';

                            if ( $_product ) $sku = $_product->get_sku();

                            if ( ! empty( $attributes ) ) {
                                $variation = '<small style="display: block; margin: 5px 0 10px 10px;">'. str_replace( "\n", "<br/>", $attributes ) .'</small>';
                            }
                            ?>
                            <tr>
                            <td style="text-align:left; padding: 3px;"><?php echo $sku; ?></td>
                            <td style="text-align:left; padding: 3px;"><?php echo apply_filters( 'woocommerce_order_product_title', $_product->get_title(), $_product ) . $variation; ?></td>
                            <td style="text-align:left; padding: 3px;"><?php echo $item['quantity']; ?></td>
                            <td style="text-align:left; padding: 3px;">
                                <?php echo ( $_product && $_product->get_weight() ) ? $_product->get_weight() * $item['quantity'] . ' ' . get_option( 'woocommerce_weight_unit' ) : __( 'n/a', 'woocommerce-pip' ); ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
    <div class="article">
        <?php echo woocommerce_pip_print_return_policy(); ?>
    </div>
</section>
<div class="footer">
    <?php echo woocommerce_pip_print_footer(); ?>
</div>
<p class="pagebreak"></p>
<?php
endforeach;
