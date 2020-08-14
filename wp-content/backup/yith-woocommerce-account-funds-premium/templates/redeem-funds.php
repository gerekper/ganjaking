<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vendor             = function_exists( 'yith_get_vendor' ) ? yith_get_vendor( 'current', 'user' ) : false;
$current_user_id    = get_current_user_id();

if ( $vendor && $vendor->is_valid() && $vendor->is_owner( $current_user_id ) ):

	$customer = new YITH_YWF_Customer( $current_user_id );
	$fund_available = $customer->get_funds();

	$min_to_redeem = get_option( 'ywf_min_fund_needs', 50 );

	$max_to_redeem = get_option( 'ywf_max_fund_redeem', '' );

	$default_currency = get_option( 'woocommerce_currency' );
	$current_currency = get_woocommerce_currency();

	$default_currency_symbol = get_woocommerce_currency_symbol( $default_currency );
	$default_currency_name   = get_woocommerce_currencies();
	$default_currency_name   = isset( $default_currency_name[ $default_currency ] ) ? $default_currency_name[ $default_currency ] : $default_currency_symbol;
	$price_format            = get_woocommerce_price_format();
	$lang                    = isset( $_GET['lang'] ) ? $_GET['lang'] : false;
	$button_label            = get_option( 'ywf_redeeming_button_label', 'Redeem funds!' );
	$default_value           = $fund_available;
	if ( $max_to_redeem > $fund_available ) {
		$max_to_redeem = $fund_available;
	} else {
		$default_value = $max_to_redeem;
	}
	?>
    <div id="yith_account_funds_redeem_funds_container">
		<?php
		$query_var = get_query_var( yith_account_funds_get_endpoint_slug( 'redeem-funds' ) );
		if ( 'redeem_made' == $query_var ) {

			wc_print_notice( __( 'Your funds are available to redeem shortly', 'yith-woocommerce-account-funds' ), 'success' );

		} else {
			if ( $current_currency !== $default_currency ) {

				wc_print_notice( sprintf( _x( 'In this page, your funds are in %s (%s), because the redeem action is available only with this currency', 'In this page, your funds are in United States Dollar ($), because the redeem is possible only with this currency', 'yith-woocommerce-account-funds' ), $default_currency_name, $default_currency_symbol ), 'notice' );
			}
			if ( $fund_available == 0 ) {
				wc_print_notice( sprintf( __( 'You don\'t have enough funds to redeem.', 'yith-woocommerce-account-funds' ) ), 'error' );
			} elseif ( ! empty( $min_to_redeem ) && $fund_available < $min_to_redeem ) {

				wc_print_notice( sprintf( __( 'You don\'t have enough funds to redeem. Minimum required funds to redeem is %s, available funds is %s', 'yith-woocommerce-account-funds' ), wc_price( $min_to_redeem, array( 'currency' => $default_currency ) ), wc_price( $fund_available, array( 'currency' => $default_currency ) ) ), 'error' );
			} else { ?>
                <div id="yith_account_funds_redeem_form">
                    <form name="redeem_funds" method="post"
                          action="<?php echo esc_url( wc_get_endpoint_url( yith_account_funds_get_endpoint_slug( 'redeem-funds' ) ) ); ?>"
                          class="yith_redeem_funds_form" enctype="multipart/form-data">
                        <label for="yith_redeem_funds"><?php _e( 'Amount to redeem', 'yith-woocommerce-account-funds' ); ?></label>
                        <span class="yith_funds_input_container">
                        <?php

                        $field_number    = "<input type='number' id ='yith_redeem_funds' name='yith_redeem_funds' min='$min_to_redeem' max='$max_to_redeem' value='$default_value' step='0.01'>";
                        $currency_symbol = "<span class='ywf_currency_symbol'>$default_currency_symbol</span>";
                        echo sprintf( $price_format, $currency_symbol, $field_number );
                        ?>
                    </span>
						<?php if ( $lang ): ?>
                            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
						<?php endif; ?>
                        <p>
                            <input type="submit" class="button" id="yith_redeem_button"
                                   value="<?php echo $button_label; ?>">
                        </p>

						<?php wp_nonce_field( 'yith_account_funds-redeem_funds', 'yith-account-funds-redeem-funds-nonce' ); ?>

                    </form>
                </div>
				<?php

			}
		}
		?>
    </div>
<?php
else:
	wc_print_notice( __( 'You can\'t access in this content', 'yith-woocommerce-account-funds' ), 'error' );
endif;
