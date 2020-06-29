<?php $logo = YITH_POS_ASSETS_URL . '/images/logo_receipt.png'; ?>
<div class="receipt-container">
    <div class="receipt-header">
        <div id="logo" default="<?php echo $logo ?>">
            <img src="<?php echo esc_attr( $logo ); ?>">
        </div>
        <div id="name" data-dep="_show_store_name">
            <h2><?php esc_attr_e( 'Store Name', 'yith-point-of-sale-for-woocommerce' ); ?></h2>
        </div>
        <div id="vat" data-dep="_show_vat" data-dep_label="_vat_label">
            <strong><?php esc_attr_e( 'VAT', 'yith-point-of-sale-for-woocommerce' ); ?>:</strong> B39000000
        </div>
        <div id="address" data-dep="_show_address">
            Calle Rúe del Percebe, 13<br/>28000 - Madrid
        </div>
        <div id="contact-info" data-dep="_show_contact_info">
            <div id="phone" data-dep="_show_phone">
                <strong><?php _e( 'Phone:', 'yith-point-of-sale-for-woocommerce' ) ?></strong> 555.555.666
            </div>
            <div id="email" data-dep="_show_email">
                <strong><?php _e( 'Email:', 'yith-point-of-sale-for-woocommerce' ) ?></strong> info@website.com
            </div>
            <div id="fax" data-dep="_show_fax">
                <strong><?php _e( 'Fax:', 'yith-point-of-sale-for-woocommerce' ) ?></strong> 555.444.222
            </div>
            <div id="website" data-dep="_show_website">
                <strong><?php _e( 'Web:', 'yith-point-of-sale-for-woocommerce' ) ?></strong> www.website.com
            </div>
        </div>
        <div id="social-info" data-dep="_show_social_info">
            <div id="facebook" data-dep="_show_facebook">
                <strong><?php _e( 'Facebook:', 'yith-point-of-sale-for-woocommerce' ) ?></strong>
                facebook.com/my-store<br/></div>
            <div id="twitter" data-dep="_show_twitter">
                <strong><?php _e( 'Twitter:', 'yith-point-of-sale-for-woocommerce' ) ?></strong> @my_store
            </div>
            <div id="instagram" data-dep="_show_instagram">
                <strong><?php _e( 'Instagram:', 'yith-point-of-sale-for-woocommerce' ) ?></strong>
                instagram.com/my_store
            </div>
            <div id="youtube" data-dep="_show_youtube">
                <strong><?php _e( 'YouTube:', 'yith-point-of-sale-for-woocommerce' ) ?></strong>
                youtube.com/store_channel
            </div>
        </div>
    </div>
    <div class="order">
        <div id="order-content">
            <div class="product">2 X <?php _e( 'Product', 'yith-point-of-sale-for-woocommerce' ) ?> 1</div>
            <div class="price"><?php echo wc_price( 1.8 ) ?></div>
            <div class="product">2 X <?php _e( 'Product', 'yith-point-of-sale-for-woocommerce' ) ?> 2</div>
            <div class="price"><?php echo wc_price( 1.65 ) ?></div>
            <div class="product">1 X <?php _e( 'Product', 'yith-point-of-sale-for-woocommerce' ) ?> 3</div>
            <div class="price"><?php echo wc_price( 1 ) ?></div>
            <div class="product">3 X <?php _e( 'Product', 'yith-point-of-sale-for-woocommerce' ) ?> 4</div>
            <div class="price"><?php echo wc_price( 1.5 ) ?></div>
        </div>
        <hr>
        <div id="order-content">
            <div class="total-title">
                <h2><?php _e( 'Total', 'yith-point-of-sale-for-woocommerce' ) ?></h2>
            </div>
            <div class="total-amount">
				<?php echo wc_price( 12.4 ) ?>
            </div>
        </div>
        <hr>
        <div id="order-content">
            <div class="payment-method">
				<?php _e( 'Cash', 'yith-point-of-sale-for-woocommerce' ) ?>
            </div>
            <div class="payment-total">
				<?php echo wc_price( 13 ) ?>
            </div>
            <div class="payment-change">
				<?php _e( 'Change', 'yith-point-of-sale-for-woocommerce' ) ?>
            </div>
            <div class="total-change">
				<?php echo wc_price( 0.6 ) ?>
            </div>
        </div>
    </div>
    <div class="order-data">
        <div id="order-date" data-dep="_show_order_date" data-dep_label="_order_date_label">
            <strong><?php esc_attr_e( 'Date', 'yith-point-of-sale-for-woocommerce' ); ?>
                :</strong> <?php echo date( get_option( 'date_format' ) ); ?></div>
        <div id="order-number" data-dep="_show_order_number" data-dep_label="_order_number_label">
            <strong><?php esc_attr_e( 'Order', 'yith-point-of-sale-for-woocommerce' ); ?>:</strong>
            452
        </div>
        <div id="order-customer" data-dep="_show_order_customer" data-dep_label="_order_customer_label">
            <strong><?php esc_attr_e( 'Customer', 'yith-point-of-sale-for-woocommerce' ); ?>
                :</strong> John Doe
        </div>
        <div id="order-register" data-dep="_show_order_register" data-dep_label="_order_register_label">
            <strong><?php esc_attr_e( 'Register', 'yith-point-of-sale-for-woocommerce' ); ?>
                :</strong> 23878457-2
        </div>
        <div id="cashier" data-dep="_show_cashier" data-dep_label="_cashier_label">
            <strong><?php esc_attr_e( 'Cashier', 'yith-point-of-sale-for-woocommerce' ); ?>
                :</strong> Jane Doe
        </div>
    </div>
    <div class="receipt-footer" data-dep_label="_receipt_footer">
        <strong><?php esc_attr_e( 'Thanks for your purchase', 'yith-point-of-sale-for-woocommerce' ); ?></strong>
    </div>
</div>