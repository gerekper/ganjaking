<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WBOOSTSALES_Discount_Bar {
	private $settings;

	/**
	 * VI_WBOOSTSALES_Upsells constructor.
	 * Init setting
	 */
	public function __construct() {
		$this->settings = new VI_WBOOSTSALES_Data();
	}

	/**
	 * @return array
	 */
	public function show_html() {
		if ( ! $this->settings->get_option( 'enable_discount' ) ) {
			return array();
		}
		$coupon_position = $this->settings->get_option( 'coupon_position' );
		/*get discount type*/
		$discount_type = $this->get_discount_type();

		/*get minimum amount coupon*/
		$min_amount = $this->settings->get_coupon( 'min' );

		/*get current cart*/
		$total_cart = WC()->cart->cart_contents_total;

		$coupon         = new WC_Coupon( $this->settings->get_option( 'coupon' ) );
		$coupon_code    = $coupon->get_code();
		$total_discount = WC()->cart->get_coupon_discount_amount( $coupon_code );
		if ( $total_cart + $total_discount >= $min_amount ) {
			VI_WBOOSTSALES_Frontend_Discount_Bar::apply_coupon( $coupon_code );
			$thank_you_html = $this->thank_you_html();
			if ( $thank_you_html ) {
				return array( 'code' => 200, 'html' => $thank_you_html );
			} else {
				return array( 'code' => 400 );
			}
		} elseif($total_cart) {
			$current_percent = round( ( $total_cart * 100 ) / $min_amount, 2 );
			ob_start();
			?>
            <div class="vi-wbs-topbar <?php echo $coupon_position == 0 ? 'wbs_top_bar' : 'wbs_bottom_bar'; ?>">
                <div class="vi-wbs-goal"><?php esc_html_e( 'Discount:', 'woocommerce-boost-sales' ) ?>
                    <span><?php echo $discount_type; ?></span>
                </div>
				<?php if ( $min_amount == 0 && $total_cart !== 0 ) { ?>
                    <div class="vi-wbs_msg_apply_cp"><?php esc_html_e( 'Your cart have applied coupon', 'woocommerce-boost-sales' ); ?></div>
				<?php } else { ?>
                    <div class="vi-wbs-current-progress">
						<?php esc_html_e( 'Spend over: ', 'woocommerce-boost-sales' ) ?>
                        <span class="wbs-money"><?php echo wc_price( $min_amount ); ?></span>
                    </div>
                    <div class="vi-wbs-progress-limit vi-wbs-progress-limit-start"><?php echo wc_price( $total_cart ); ?></div>
                    <div class="vi-wbs-progress-container">
                        <div class="vi-wbs-progress">
                            <div class="vi-wbs-progress-bar vi-wbs-progress-bar-success" role="progressbar"
                                 aria-valuenow="<?php echo isset( $current_percent ) ? $current_percent : 0; ?>"
                                 aria-valuemin="0" aria-valuemax="100"
                                 style="width: <?php echo isset( $current_percent ) ? $current_percent : 0; ?>%;">
								<?php
								if ( isset( $current_percent ) && $current_percent > 0 ) {
									echo '<span class="vi-wbs-only">' . esc_html( $current_percent ) . '%</span>';
								}
								?>
                            </div>
                        </div>
                    </div>
                    <div class="vi-wbs-progress-limit vi-wbs-progress-limit-end"><?php echo wc_price( $min_amount ); ?></div>
				<?php } ?>
                <div class="vi-wbs_progress_close"></div>
            </div>
			<?php

			return array( 'code' => 201, 'html' => ob_get_clean() );
		}
	}

	public function thank_you_html() {
		global $wbs_language;
		$enable_thankyou = $this->settings->get_option( 'enable_thankyou' );
		if ( ! $enable_thankyou ) {
			return '';
		}
		$message_congrats  = $this->settings->get_option( 'message_congrats', $wbs_language );
		$discount          = $this->get_discount_type();
		$message           = str_replace( '{discount_amount}', '<span class="vi-wbs-highlight">' . $discount . '</span>', $message_congrats );
		$checkout_url      = wc_get_checkout_url();
		$text_btn_checkout = $this->settings->get_option( 'text_btn_checkout', $wbs_language );
		$enable_checkout   = $this->settings->get_option( 'enable_checkout' );

		if ( $enable_checkout ) {
			$time = $this->settings->get_option( 'redirect_after_second' );
			$time = 'data-time="' . $time . '"';
		} else {
			$time = '';
		}
		ob_start();
		?>
        <div class="wbs-overlay"></div>
        <div class="wbs-wrapper">
            <div class="wbs-content wbs-msg-congrats" <?php echo $time; ?>>
                <div class="wbs-content-inner">
                    <span class="wbs-close" title="Close"><span>X</span></span>

                    <div class="wbs-message-success">
                        <p><?php echo $message; ?></p>
                        <a href="<?php echo esc_url( $checkout_url ); ?>"
                           class="vi-wbs-btn-redeem"><?php esc_html_e( $text_btn_checkout, 'woocommerce-boost-sales' ) ?></a>
						<?php if ( $enable_checkout ) { ?>
                            <div class="auto-redirect"><?php echo esc_html__( 'Redirect to checkout after  ', 'woocommerce-boost-sales' ) ?>
                                <span><?php echo esc_html( $time ) ?></span><?php echo esc_html__( 's', 'woocommerce-boost-sales' ) ?>
                            </div>
						<?php } ?>
                    </div>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	public function get_discount_type() {
		$discount      = $this->settings->get_coupon( 'amount' );
		$discount_type = $this->settings->get_coupon( 'type' );
		switch ( $discount_type ) {
			case 'percent':
				return $discount . esc_html__( '% Cart', 'woocommerce-boost-sales' );
				break;
			case 'fixed_cart':
				return wc_price( apply_filters( 'woocommerce_boost_sales_coupon_amount_price', $discount ) ) . esc_html__( ' off Cart', 'woocommerce-boost-sales' );
				break;
			default:
				return wc_price( apply_filters( 'woocommerce_boost_sales_coupon_amount_price', $discount ) ) . esc_html__( ' per Product', 'woocommerce-boost-sales' );
		}
	}
}