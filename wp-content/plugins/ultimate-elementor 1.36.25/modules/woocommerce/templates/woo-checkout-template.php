<?php
/**
 * Trait Woo_Checkout_Template
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Woocommerce\Templates;

use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Trait Woo_Checkout_Template
 */
trait Woo_Checkout_Template {
	/**
	 * Stores Widget settings.
	 *
	 * @since 1.31.0
	 * @access public
	 * @var array
	 */
	public static $settings_data = array();

	/**
	 * Stores status to show/hide login on multistep layout.
	 *
	 * @since 1.34.0
	 * @access public
	 * @var boolean
	 */
	public static $enable_login_reminder = false;


	/**
	 * Returns widget settings from parent.
	 *
	 * @since 1.31.0
	 * @access public
	 * @return array
	 */
	public static function uael_get_woo_checkout_settings() {
		return self::$settings_data;
	}

	/**
	 * Sets the widget settings received from parent.
	 *
	 * @since 1.31.0
	 * @access public
	 * @param array $settings Widget settings.
	 */
	public static function uael_set_woo_checkout_settings( $settings ) {
		self::$settings_data = $settings;
	}

	/**
	 * UAEL checkout login form template function.
	 *
	 * @since 1.31.0
	 * @access public
	 */
	public static function uael_login_template() {
		$setting = self::uael_get_woo_checkout_settings();

		if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
			return;
		} ?>
		<div class="uael-woo-checkout-login">
			<div class="woocommerce-form-login-toggle">
				<?php wc_print_notice( apply_filters( 'woocommerce_checkout_login_message', $setting['login_title'] ) . ' <a href="#" class="showlogin">' . $setting['login_toggle_text'] . '</a>', 'notice' ); ?>
			</div>

			<?php
					$message  = esc_html( $setting['login_form_text'] );
					$redirect = wc_get_checkout_url();
					$hidden   = true;
			?>
			<form class="woocommerce-form woocommerce-form-login login" method="post" <?php echo ( $hidden ) ? 'style="display:none;"' : ''; ?>>

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<?php echo ( $message ) ? wpautop( wptexturize( $message ) ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<p class="form-row form-row-first">
					<label for="username"><?php esc_html_e( 'Username or email', 'uael' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="input-text" name="username" id="username" autocomplete="username" />
				</p>
				<p class="form-row form-row-last">
					<label for="password"><?php esc_html_e( 'Password', 'uael' ); ?>&nbsp;<span class="required">*</span></label>
					<input class="input-text" type="password" name="password" id="password" autocomplete="current-password" />
				</p>
				<div class="clear"></div>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="form-row">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'uael' ); ?></span>
					</label>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ); ?>" />
					<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Login', 'uael' ); ?>"><?php esc_html_e( 'Login', 'uael' ); ?></button>
				</p>
				<p class="lost_password">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'uael' ); ?></a>
				</p>

				<div class="clear"></div>

				<?php do_action( 'woocommerce_login_form_end' ); ?>

			</form>
		</div>

		<?php
	}

	/**
	 * UAEL checkout coupon form template function.
	 *
	 * @since 1.31.0
	 * @access public
	 */
	public static function uael_coupon_template() {
		$setting = self::uael_get_woo_checkout_settings();

		if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
			return;
		}
		if ( 'yes' === $setting['coupon_switcher'] ) {
			?>
			<div class="uael-woo-checkout-coupon">
				<div class="woocommerce-form-coupon-toggle">
					<?php wc_print_notice( apply_filters( 'woocommerce_checkout_coupon_message', $setting['coupon_title'] . ' <a href="#" class="showcoupon">' . $setting['coupon_toggle_text'] . '</a>' ), 'notice' ); ?>
				</div>

				<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none">

					<p><?php echo esc_html( $setting['coupon_form_text'] ); ?></p>

					<div class="uael-custom-coupon-field">

						<p class="form-row form-row-first">
							<input type="text" name="coupon_code" class="input-text" placeholder="<?php echo esc_attr( $setting['coupon_field_placeholder'] ); ?>" id="coupon_code" value="" />
						</p>

						<p class="form-row form-row-last">
							<button type="submit" class="button" name="apply_coupon" value="<?php echo esc_attr( $setting['coupon_button_text'] ); ?>"><?php echo esc_html( $setting['coupon_button_text'] ); ?></button>
						</p>
					</div>
					<div class="clear"></div>
				</form>
			</div>

			<?php
		}
	}

	/**
	 * UAEL checkout billing form template function.
	 *
	 * @since 1.31.0
	 * @access public
	 */
	public static function uael_billing_form_template() {
		$setting  = self::uael_get_woo_checkout_settings();
		$checkout = WC()->checkout();
		?>
			<div class="uael-woo-checkout-billing-form">
				<div class="woocommerce-billing-fields">
					<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

						<div class="uael-checkout-form-billing-title">
							<h3><?php esc_html_e( 'Billing &amp; Shipping', 'uael' ); ?></h3>
						</div>

					<?php else : ?>

						<div class="uael-checkout-form-billing-title">
							<h3><?php echo esc_html( $setting['labels_billing_section'] ); ?></h3>
						</div>

					<?php endif; ?>

					<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

					<div class="woocommerce-billing-fields__field-wrapper">
						<?php
						$fields = $checkout->get_checkout_fields( 'billing' );

						foreach ( $fields as $key => $field ) {
							woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
						}
						?>
					</div>

					<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
				</div>

				<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
					<div class="woocommerce-account-fields">
						<?php if ( ! $checkout->is_registration_required() ) : ?>
							<?php
							$create_account  = $checkout->get_value( 'createaccount' );
							$account_checked = apply_filters( 'woocommerce_create_account_default_checked', false );
							?>
							<p class="form-row form-row-wide create-account">
								<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
									<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $create_account || ( true === $account_checked ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'uael' ); ?></span>
								</label>
							</p>

						<?php endif; ?>

						<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

						<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

							<div class="create-account">
								<?php
								$checkout_fields = $checkout->get_checkout_fields( 'account' );
								foreach ( $checkout_fields as $key => $field ) :
									?>
									<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
								<?php endforeach; ?>
								<div class="clear"></div>
							</div>

						<?php endif; ?>

						<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php
	}

	/**
	 * UAEL checkout shipping form template function.
	 *
	 * @since 1.31.0
	 * @access public
	 */
	public static function uael_shipping_form_template() {
		$setting         = self::uael_get_woo_checkout_settings();
		$checkout        = WC()->checkout();
		$shipping_needed = WC()->cart->needs_shipping_address();
		?>
		<?php if ( $shipping_needed ) { ?>
			<div class="uael-woo-checkout-shipping-form">
				<div class="woocommerce-shipping-fields">
					<?php if ( true === $shipping_needed ) : ?>
						<?php
						$shipping_address = apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 );
						?>
						<div class="uael-checkout-form-shipping-title">
							<h3 id="ship-to-different-address">
								<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
									<input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked( $shipping_address, 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" /> <span><?php echo esc_html( $setting['labels_shipping_section'] ); ?></span>
								</label>
							</h3>
						</div>

						<div class="shipping_address">

							<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

							<div class="woocommerce-shipping-fields__field-wrapper">
								<?php
								$fields = $checkout->get_checkout_fields( 'shipping' );

								foreach ( $fields as $key => $field ) {
									woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
								}
								?>
							</div>

							<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

						</div>

					<?php endif; ?>
				</div>
			<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>

				<div class="woocommerce-additional-fields">
					<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

						<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>

							<h3><?php esc_html_e( 'Additional information', 'uael' ); ?></h3>

						<?php endif; ?>

						<div class="woocommerce-additional-fields__field-wrapper">
							<?php
							$order_fields = $checkout->get_checkout_fields( 'order' );
							foreach ( $order_fields as $key => $field ) :
								?>
								<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
							<?php endforeach; ?>
						</div>

					<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
				</div>
			<?php endif; ?>
			</div>
			<?php } else { ?>
				<?php
				if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) :
					?>
					<div class="woocommerce-additional-fields">
						<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

							<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>

								<h3><?php esc_html_e( 'Additional information', 'uael' ); ?></h3>

							<?php endif; ?>

							<div class="woocommerce-additional-fields__field-wrapper">
								<?php
								$order_data = $checkout->get_checkout_fields( 'order' );
								foreach ( $order_data as $key => $field ) :
									?>
									<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
								<?php endforeach; ?>
							</div>
						<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
					</div>
				<?php endif; ?>
			<?php
			}
	}

	/**
	 * UAEL checkout order review template function.
	 *
	 * @since 1.31.0
	 * @access public
	 */
	public static function uael_order_review_template() {
		$setting  = self::uael_get_woo_checkout_settings();
		$checkout = WC()->checkout();
		?>
		<div class="uael-checkout-review-order-table">
			<div class="uael-woo-checkout-order-review">
				<div class="uael-checkout-section-order-title">
					<h3><?php echo esc_html( $setting['labels_order_section'] ); ?></h3>
				</div>
				<ul class="uael-order-review-table">

					<?php
					do_action( 'woocommerce_review_order_before_cart_contents' );
					$cart_data = WC()->cart->get_cart();
					foreach ( $cart_data as $cart_item_key => $cart_item ) {
						$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							?>
							<li class="table-row <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
								<div class="table-col-1 product-thum-name">
									<div class="product-thumbnail">
										<?php
										$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
										echo $thumbnail; // phpcs:ignore XSS ok.
										?>
									</div>
									<div class="product-name">
										<?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								</div>
								<div class="table-col-3 product-total">
									<?php
									$product_subtotal = WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] );
									echo apply_filters( 'woocommerce_cart_item_subtotal', $product_subtotal, $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</div>
							</li>
							<?php
						}
					}

					do_action( 'woocommerce_review_order_after_cart_contents' );
					?>
				</ul>

				<div class="uael-order-review-table-footer">
					<div class="uae-shop-main-div">
					<?php
					if ( 'yes' === $setting['enable_back_to_cart_btn'] ) {
						?>
							<div class="back-to-shop">
								<a class="back-to-shop-link" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'cart' ) ) ); ?>">
									<i class="fas fa-long-arrow-alt-left"></i>
								<?php echo esc_html( $setting['labels_back_to_cart'] ); ?>
								</a>
							</div>
						<?php } ?>
						<?php if ( 'yes' === $setting['enable_shop_link'] ) { ?>
							<div class="uae-shop-link">
								<a class="uae-back-to-shop-link" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
									<?php echo esc_html( $setting['shop_link_text'] ); ?>
									<i class="fas fa-long-arrow-alt-right"></i>
								</a>
							</div>
						<?php } ?>
					</div>

					<div class="footer-content">
						<div class="cart-subtotal">
							<div><?php esc_html_e( 'Sub-total', 'uael' ); ?></div>
							<div><?php wc_cart_totals_subtotal_html(); ?></div>
						</div>

						<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
							<div class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
								<div><?php wc_cart_totals_coupon_label( $coupon ); ?></div>
								<div><?php wc_cart_totals_coupon_html( $coupon ); ?></div>
							</div>
						<?php endforeach; ?>

						<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
							<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
							<div class="shipping-area">
								<?php
								WC()->cart->calculate_totals();
								wc_cart_totals_shipping_html();
								?>
							</div>
							<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
						<?php endif; ?>

						<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
							<div class="fee">
								<div><?php echo esc_html( $fee->name ); ?></div>
								<div><?php wc_cart_totals_fee_html( $fee ); ?></div>
							</div>
						<?php endforeach; ?>

						<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
							<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
								<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited ?>
									<div class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
										<div><?php echo esc_html( $tax->label ); ?></div>
										<div><?php echo wp_kses_post( $tax->formatted_amount ); ?></div>
									</div>
								<?php endforeach; ?>
							<?php else : ?>
								<div class="tax-total">
									<div><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></div>
									<div><?php wc_cart_totals_taxes_total_html(); ?></div>
								</div>
							<?php endif; ?>
						<?php endif; ?>

						<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

						<div class="order-total">
							<div><?php esc_html_e( 'Order Total', 'uael' ); ?></div>
							<div><?php wc_cart_totals_order_total_html(); ?></div>
						</div>

						<?php
						if ( class_exists( 'WC_Subscriptions_Cart' ) && ( \WC_Subscriptions_Cart::cart_contains_subscription() ) ) {
							echo '<table class="recurring-wrapper">';
							do_action( 'woocommerce_review_order_after_order_total' );
							echo '</table>';
						}
						?>
						<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * UAEL checkout payment template function.
	 *
	 * @since 1.31.0
	 * @access public
	 */
	public static function uael_checkout_payment_template() {
		$setting = self::uael_get_woo_checkout_settings();
		if ( WC()->cart->needs_payment() ) {
			$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
			WC()->payment_gateways()->set_current_gateway( $available_gateways );
		} else {
			$available_gateways = array();
		}
		?>
		<div class="uael-woo-checkout-payment">
			<div class="uael-checkout-section-payment-title">
				<h3><?php echo esc_html( $setting['labels_payment_section'] ); ?></h3>
			</div>
			<?php
			wc_get_template(
				'checkout/payment.php',
				array(
					'checkout'           => WC()->checkout(),
					'available_gateways' => $available_gateways,
					'order_button_text'  => apply_filters( 'woocommerce_order_button_text', esc_html( 'Place Order' ) ),
				)
			);
			?>
		</div>
		<?php
	}

	/**
	 * UAEL checkout order pay template function.
	 *
	 * @since 1.31.0
	 * @access public
	 * @param int $order_id Order ID.
	 * @throws \Exception When order is invalid.
	 */
	public static function uael_pay_order( $order_id ) {
		do_action( 'before_woocommerce_pay' );

		$order_id = absint( $order_id );

		if ( isset( $_GET['pay_for_order'], $_GET['key'] ) && $order_id ) { // phpcs:ignore input var ok, CSRF ok.
			try {
				$order_key          = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : ''; // phpcs:ignore
				$order              = wc_get_order( $order_id );
				$hold_stock_minutes = (int) get_option( 'woocommerce_hold_stock_minutes', 0 );

				if ( ! $order || $order->get_id() !== $order_id || ! hash_equals( $order->get_order_key(), $order_key ) ) {
					throw new Exception( __( 'Sorry, this order is invalid and cannot be paid for.', 'uael' ) );
				}

				if ( ! current_user_can( 'pay_for_order', $order_id ) && ! is_user_logged_in() ) {
					echo '<div class="woocommerce-info">' . esc_html__( 'Please log in to your account below to continue to the payment form.', 'uael' ) . '</div>';
					woocommerce_login_form(
						array(
							'redirect' => $order->get_checkout_payment_url(),
						)
					);
					return;
				}

				if ( ! $order->get_user_id() && is_user_logged_in() ) {
					if ( $order->get_billing_email() !== wp_get_current_user()->user_email ) {
						wc_print_notice( __( 'You are paying for a guest order. Please continue with payment only if you recognize this order.', 'uael' ), 'error' );
					}
				}

				if ( ! current_user_can( 'pay_for_order', $order_id ) ) {
					throw new Exception( __( 'This order cannot be paid for. Please contact us if you need assistance.', 'uael' ) );
				}

				if ( ! $order->needs_payment() ) {
					/* translators: %s: order status */
					throw new Exception( sprintf( __( 'This order&rsquo;s status is &ldquo;%s&rdquo;&mdash;it cannot be paid for. Please contact us if you need assistance.', 'uael' ), wc_get_order_status_name( $order->get_status() ) ) );
				}

				if ( ! $order->has_status( wc_get_is_pending_statuses() ) ) {
					$quantities = array();

					foreach ( $order->get_items() as $item_key => $item ) {
						if ( $item && is_callable( array( $item, 'get_product' ) ) ) {
							$product = $item->get_product();

							if ( ! $product ) {
								continue;
							}

							$quantities[ $product->get_stock_managed_by_id() ] = isset( $quantities[ $product->get_stock_managed_by_id() ] ) ? $quantities[ $product->get_stock_managed_by_id() ] + $item->get_quantity() : $item->get_quantity();
						}
					}

					foreach ( $order->get_items() as $item_key => $item ) {
						if ( $item && is_callable( array( $item, 'get_product' ) ) ) {
							$product = $item->get_product();

							if ( ! $product ) {
								continue;
							}

							if ( ! apply_filters( 'woocommerce_pay_order_product_in_stock', $product->is_in_stock(), $product, $order ) ) {
								/* translators: %s: product name */
								throw new Exception( sprintf( __( 'Sorry, "%s" is no longer in stock so this order cannot be paid for. We apologize for any inconvenience caused.', 'uael' ), $product->get_name() ) );
							}

							if ( ! $product->managing_stock() || $product->backorders_allowed() ) {
								continue;
							}

							$held_stock     = ( $hold_stock_minutes > 0 ) ? wc_get_held_stock_quantity( $product, $order->get_id() ) : 0;
							$required_stock = $quantities[ $product->get_stock_managed_by_id() ];

							if ( ! apply_filters( 'woocommerce_pay_order_product_has_enough_stock', ( $product->get_stock_quantity() >= ( $held_stock + $required_stock ) ), $product, $order ) ) {
								/* translators: 1: product name 2: quantity in stock */
								throw new Exception( sprintf( __( 'Sorry, we do not have enough "%1$s" in stock to fulfill your order (%2$s available). We apologize for any inconvenience caused.', 'uael' ), $product->get_name(), wc_format_stock_quantity_for_display( $product->get_stock_quantity() - $held_stock, $product ) ) );
							}
						}
					}
				}

				WC()->customer->set_props(
					array(
						'billing_country'  => $order->get_billing_country() ? $order->get_billing_country() : null,
						'billing_state'    => $order->get_billing_state() ? $order->get_billing_state() : null,
						'billing_postcode' => $order->get_billing_postcode() ? $order->get_billing_postcode() : null,
					)
				);
				WC()->customer->save();

				$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

				if ( count( $available_gateways ) ) {
					current( $available_gateways )->set_current();
				}

				wc_get_template(
					'checkout/form-pay.php',
					array(
						'order'              => $order,
						'available_gateways' => $available_gateways,
						'order_button_text'  => apply_filters( 'woocommerce_pay_order_button_text', __( 'Pay for order', 'uael' ) ),
					)
				);

			} catch ( Exception $e ) {
				wc_print_notice( $e->getMessage(), 'error' );
			}
		} elseif ( $order_id ) {

			$order_key = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, input var ok, CSRF ok.
			$order     = wc_get_order( $order_id );

			if ( $order && $order->get_id() === $order_id && hash_equals( $order->get_order_key(), $order_key ) ) {

				if ( $order->needs_payment() ) {

					wc_get_template( 'checkout/order-receipt.php', array( 'order' => $order ) );

				} else {
					/* translators: %s: order status */
					wc_print_notice( sprintf( __( 'This order&rsquo;s status is &ldquo;%s&rdquo;&mdash;it cannot be paid for. Please contact us if you need assistance.', 'uael' ), wc_get_order_status_name( $order->get_status() ) ), 'error' );
				}
			} else {
				wc_print_notice( __( 'Sorry, this order is invalid and cannot be paid for.', 'uael' ), 'error' );
			}
		} else {
			wc_print_notice( __( 'Invalid order.', 'uael' ), 'error' );
		}

		do_action( 'after_woocommerce_pay' );
	}

	/**
	 * UAEL checkout received order template function.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @throws \WC_Data_Exception Throws exceptions if any.
	 * @since 1.31.0
	 * @access public
	 */
	public static function uael_received_order( $order_id = 0 ) {
		$order = false;

		$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $order_id ) );
		$order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( wp_unslash( $_GET['key'] ) ) ); // phpcs:ignore

		if ( $order_id > 0 ) {
			$order = wc_get_order( $order_id );
			if ( ! $order || ! hash_equals( $order->get_order_key(), $order_key ) ) {
				$order = false;
			}
		}

		unset( WC()->session->order_awaiting_payment );

		if ( $order && $order->is_created_via( 'admin' ) ) {
			$order->set_customer_ip_address( \WC_Geolocation::get_ip_address() );
			$order->save();
		}

		wc_empty_cart();

		wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
	}

	/**
	 * Main functions that renders all checkout layouts accordingly.
	 *
	 * @since 1.31.0
	 * @access public
	 */
	public static function uael_checkout() {
		$setting = self::uael_get_woo_checkout_settings();
		do_action( 'woocommerce_before_checkout_form_cart_notices' );

		if ( WC()->cart->is_empty() && ! is_customize_preview() && apply_filters( 'woocommerce_checkout_redirect_empty_cart', true ) ) {
			return;
		}

		do_action( 'woocommerce_check_cart_items' );

		WC()->cart->calculate_totals();

		$checkout = WC()->checkout();

		if ( empty( $_POST ) && wc_notice_count( 'error' ) > 0 ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing

			wc_get_template( 'checkout/cart-errors.php', array( 'checkout' => $checkout ) );
			wc_clear_notices();

		} else {

			$non_js_checkout = ! empty( $_POST['woocommerce_checkout_update_totals'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

			if ( wc_notice_count( 'error' ) === 0 && $non_js_checkout ) {
				wc_add_notice( __( 'The order totals have been updated. Please confirm your order by pressing the "Place order" button at the bottom of the page.', 'uael' ) );
			}

			switch ( $setting['layout'] ) {
				case '1':
					echo self::single_column_layout( $checkout ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					break;

				case '2':
					echo self::two_column_layout( $checkout ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					break;

				case '3':
					echo self::multistep_layout( $checkout, $setting ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					break;

				default:
			}
		}
	}

	/**
	 * Renders the Single column checkout page layout.
	 *
	 * @since 1.31.0
	 * @access public
	 * @param \WC_Checkout $checkout Main WC_Checkout Instance.
	 */
	public static function single_column_layout( $checkout ) {
		?>
		<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
		<?php
		// If checkout registration is disabled and not logged in, the user cannot checkout.
		if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
			?>
			<div class="uael-woo-checkout-login-msg">
				<?php
				echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'uael' ) ) );
				?>
			</div>
			<?php
			return;
		}
		?>
		<div class="single-layout-container">

			<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

				<?php if ( $checkout->get_checkout_fields() ) : ?>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div class="customer_details col2-set" id="customer_details">
						<div class="col-1">
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
						</div>

						<div class="col-2">
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>
						</div>
					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<?php endif; ?>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<?php do_action( 'woocommerce_checkout_order_review' ); ?>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

			</form>

			<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

		</div>
		<?php
	}

	/**
	 * Renders the Two column checkout page layout.
	 *
	 * @since 1.31.0
	 * @access public
	 * @param \WC_Checkout $checkout Main WC_Checkout Instance.
	 */
	public static function two_column_layout( $checkout ) {
		?>
		<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
		<?php
		// If checkout registration is disabled and not logged in, the user cannot checkout.
		if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
			?>
			<div class="uael-woo-checkout-login-msg">
				<?php
				echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'uael' ) ) );
				?>
			</div>
			<?php
			return;
		}
		?>
		<div class="column-layout-container">

				<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
					<div class="single-col-1">
						<?php if ( $checkout->get_checkout_fields() ) : ?>

							<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

							<div class="col2-set customer_details" id="customer_details">
								<div class="col-1">
									<?php do_action( 'woocommerce_checkout_billing' ); ?>
								</div>

								<div class="col-2">
									<?php do_action( 'woocommerce_checkout_shipping' ); ?>
								</div>
							</div>

							<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

						<?php endif; ?>
					</div>
					<div class="single-col-2">
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
					</div>
				</form>

				<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

		</div>
		<?php
	}

	/**
	 * Renders the multistep progress bar layout.
	 *
	 * @since 1.34.0
	 * @access public
	 * @param boolean $is_login_allow Is login allowed.
	 * @param array   $setting Widget Settings.
	 * @param string  $style Progress bar style.
	 * @param boolean $skip_shipping If shipping needs to be skiped.
	 */
	public static function get_progress_bar_markup( $is_login_allow, $setting, $style, $skip_shipping ) {
		$style_class = array(
			'default' => '',
			'icons'   => 'uael-step-icon',
			'dot'     => 'uael-step-dot',
			'counter' => 'uael-step-counter',
		);
		?>
		<ul id="uael-tabs" class="uael-tabs <?php echo esc_attr( $style_class[ $style ] ); ?> <?php echo esc_attr( $setting['tab_alignment'] ); ?>">
			<?php
			$step1_class = 'first active';
			$tab1_class  = 'uael-tab-after';
			if ( $is_login_allow ) {
				self::$enable_login_reminder = true;
				$step1_class                 = '';
				$tab_class                   = '';
				?>
				<li class="uael-tab uael-tab-after">
					<a href="javascript:void(0)" id="step-0" data-step="0" class="first active">
						<?php if ( 'icons' === $style ) : ?>
							<span> <?php Icons_Manager::render_icon( $setting['login_step_icon'], array( 'aria-hidden' => 'true' ) ); ?> </span>
						<?php endif; ?>
						<?php esc_html_e( 'Login', 'uael' ); ?>
					</a>
				</li>
				<?php
			}
			?>

			<li class="uael-tab <?php echo esc_attr( $tab1_class ); ?>">
				<a href="javascript:void(0)" id="step-1" data-step="1" class="<?php echo esc_attr( $step1_class ); ?>">
					<?php if ( 'icons' === $style ) : ?>
						<span> <?php Icons_Manager::render_icon( $setting['billing_step_icon'], array( 'aria-hidden' => 'true' ) ); ?> </span>
					<?php endif; ?>
					<?php esc_html_e( 'Billing', 'uael' ); ?>
				</a>
			</li>
			<?php if ( ! $skip_shipping ) : ?>
			<li class="uael-tab">
				<a href="javascript:void(0)" id="step-2" data-step="2">
					<?php if ( 'icons' === $style ) : ?>
						<span> <?php Icons_Manager::render_icon( $setting['shipping_step_icon'], array( 'aria-hidden' => 'true' ) ); ?> </span>
					<?php endif; ?>
					<?php esc_html_e( 'Shipping', 'uael' ); ?>
				</a>
			</li>
			<?php endif; ?>
			<li class="uael-tab">
				<a href="javascript:void(0)" id="<?php $skip_shipping ? esc_attr_e( 'step-2', 'uael' ) : esc_attr_e( 'step-3', 'uael' ); ?>" data-step="<?php $skip_shipping ? esc_attr_e( '2', 'uael' ) : esc_attr_e( '3', 'uael' ); ?>" class="last">
					<?php if ( 'icons' === $style ) : ?>
						<span> <?php Icons_Manager::render_icon( $setting['payment_step_icon'], array( 'aria-hidden' => 'true' ) ); ?> </span>
					<?php endif; ?>
					<?php esc_html_e( 'Payment', 'uael' ); ?>
				</a>
			</li>
		</ul>
		<?php
	}

	/**
	 * Renders the multistep checkout page layout.
	 *
	 * @since 1.31.0
	 * @access public
	 * @param \WC_Checkout $checkout Main WC_Checkout Instance.
	 * @param array        $setting Widget Settings.
	 */
	public static function multistep_layout( $checkout, $setting ) {
		$is_login_allow = ( ! is_user_logged_in() && 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) ? true : false;
		$style          = $setting['multistep_style'];
		$skip_shipping  = self::is_shipping_skipped();
		?>
		<?php
		wc_print_notices();

		do_action( 'woocommerce_before_checkout_form', $checkout );

		// If checkout registration is disabled and not logged in, the user cannot checkout.
		if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
			?>
			<div class="uael-woo-checkout-login-msg">
				<?php
				echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'uael' ) ) );
				?>
			</div>
			<?php
			return;
		}

		?>

		<div id="uael_multistep_container" class="uael_multistep_container uael_horizontal_box">
			<?php self::get_progress_bar_markup( $is_login_allow, $setting, $style, $skip_shipping ); ?>
			<div id="uael-tab-panels" class="uael-tab-panels">
				<?php
				if ( self::$enable_login_reminder ) {
					?>
					<div class="uael-tab-panel" id="uael-tab-panel-0">
						<?php echo self::uael_login_template(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php
				}
				?>

				<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

					<?php if ( $checkout->get_checkout_fields() ) : ?>

						<div class="customer_details uael-tab-panel" id="uael-tab-panel-1">
							<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
							<?php if ( $skip_shipping ) : ?>
								<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) { ?>
									<div class="woocommerce-additional-fields">
										<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

										<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>

											<h3><?php esc_html_e( 'Additional information', 'uael' ); ?></h3>

										<?php endif; ?>

										<div class="woocommerce-additional-fields__field-wrapper">
											<?php
											$order_fields = $checkout->get_checkout_fields( 'order' );
											foreach ( $order_fields as $key => $field ) :
												?>
												<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
											<?php endforeach; ?>
										</div>

										<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
									</div>
								<?php } ?>
							<?php endif; ?>
						</div>

						<?php if ( ! $skip_shipping ) : ?>
						<div class="customer_details uael-tab-panel" id="uael-tab-panel-2">
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>
							<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
						</div>
						<?php endif; ?>

					<?php endif; ?>

					<div class="uael-tab-panel" id="<?php $skip_shipping ? esc_attr_e( 'uael-tab-panel-2', 'uael' ) : esc_attr_e( 'uael-tab-panel-3', 'uael' ); ?>">

						<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

						<div id="order_review" class="woocommerce-checkout-review-order">
							<?php do_action( 'woocommerce_checkout_order_review' ); ?>
						</div>

						<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

					</div>
				</form>
			</div>
			<div class="uael-buttons">
				<input type="button" id="action-prev" class="button-prev" value="<?php echo esc_attr( $setting['labels_previous_btn'] ); ?>">
				<input type="button" id="action-next" class="button-next" value="<?php echo esc_attr( $setting['labels_next_btn'] ); ?>">
			</div>
		</div>

		<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
		<?php
	}

	/**
	 * Returns true if the shipping step is disabled in multi-step checkout.
	 *
	 * @since 1.34.0
	 * @access public
	 * @return bool
	 */
	public static function is_shipping_skipped() {
		$skip = false;
		// Filter to skip/hide/disable the shipping step from multi-step checkout.
		// Usage: add_filter('uael_multistep_checkout_hide_shipping_step', '__return_true').
		return apply_filters( 'uael_multistep_checkout_hide_shipping_step', $skip );
	}

	/**
	 * Added all hooks.
	 *
	 * @since 1.31.0
	 * @access public
	 */
	public function uael_woo_checkout_add_actions() {
		$setting = self::uael_get_woo_checkout_settings();

		WC()->cart->calculate_totals();

		$checkout = WC()->checkout();

		if ( 'yes' !== $setting['additional_info_box'] ) {
			add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );
		}

		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		if ( '1' === $setting['layout'] || '2' === $setting['layout'] || ( '3' === $setting['layout'] && ! $checkout->is_registration_enabled() && $checkout->is_registration_required() ) ) {
			add_action( 'woocommerce_before_checkout_form', array( $this, 'uael_login_template' ), 10 );
		}
		if ( is_user_logged_in() || ( ! ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() ) ) ) {
			add_action( 'woocommerce_before_checkout_form', array( $this, 'uael_coupon_template' ), 10 );
		}

		$woo_checkout_instance = WC()->checkout();
		remove_action( 'woocommerce_checkout_billing', array( $woo_checkout_instance, 'checkout_form_billing' ) );
		remove_action( 'woocommerce_checkout_shipping', array( $woo_checkout_instance, 'checkout_form_shipping' ) );
		remove_action( 'woocommerce_checkout_billing', array( $woo_checkout_instance, 'checkout_form_shipping' ) );
		add_action( 'woocommerce_checkout_billing', array( $this, 'uael_billing_form_template' ), 10 );
		add_action( 'woocommerce_checkout_shipping', array( $this, 'uael_shipping_form_template' ), 10 );

		remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
		remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

		add_action( 'woocommerce_checkout_order_review', array( $this, 'uael_order_review_template' ), 10 );

		add_action( 'woocommerce_checkout_order_review', array( $this, 'uael_checkout_payment_template' ), 20 );
	}

}
