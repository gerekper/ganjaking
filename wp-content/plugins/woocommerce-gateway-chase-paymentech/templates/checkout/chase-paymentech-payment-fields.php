<?php
/**
 * WooCommerce Chase Paymentech
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Chase Paymentech to newer
 * versions in the future. If you wish to customize WooCommerce Chase Paymentech for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-chase-paymentech/
 *
 * @package   WC-Chase-Paymentech/Templates
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Renders an iframe which securely loads the checkout form
 *
 * @param string $iframe_src the iframe source
 * @param int $order_id order identifier
 * @param boolean $test_mode true if test mode is enabled, false if production mode
 * @param string $gateway_description optional configured gateway description
 * @param array $tokens array of Framework\SV_WC_Payment_Gateway_Payment_Token
 * @param boolean $tokenization_allowed whether tokenization is allowed
 * @param boolean $tokenization_forced true if tokenization is forced (new card must be tokenized, ie for subscriptions/pre-orders)
 * @param boolean $default_new_card whether the new card option should be the default, when tokenization is allowed
 * @param boolean $should_tokenize_payment_method whether the 'securely save card' option should be checked
 * @param boolean $certification_mode true if certification mode is enabled
 * @param int $tokenized_payment_method_selected the tokenized payment method ID selected, if there is one - 0 if not
 *
 * @version 1.5.0
 * @since 1.0
 */

defined( 'ABSPATH' ) or exit;

?>

<form name="checkout" method="post" class="pay-page-checkout">

	<div id="payment">
		<ul class="payment_methods methods">
			<li>
				<div class="payment_box payment_method_chase_paymentech">
					<?php if ( $gateway_description ) : ?>
						<p><?php echo wp_kses_post( $gateway_description ); ?></p>
					<?php endif; ?>
					<?php if ( $test_mode ) : ?>
						<p><?php esc_html_e( 'TEST MODE ENABLED', 'woocommerce-gateway-chase-paymentech' ); ?></p>
					<?php endif; ?>
					<fieldset>
						<?php
						if ( $tokens ) : ?>
							<p class="form-row form-row-wide">
								<?php if ( $certification_mode ) : ?>
									<label for="wc-chase-paymentech-test-failover">
										<input type="checkbox" id="wc-chase-paymentech-test-failover" name="wc-chase-paymentech-test-failover" value="yes" />
										<?php esc_html_e( 'Test Failover', 'woocommerce-gateway-chase-paymentech' ); ?>
									</label><br />
								<?php else : ?>
									<a class="button" style="float:right;" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>#wc-chase-paymentech-my-payment-methods"><?php echo wp_kses_post( apply_filters( 'wc_gateway_chase_paymentech_manage_my_payment_methods', __( "Manage My Cards", 'woocommerce-gateway-chase-paymentech' ) ) ); ?></a>
								<?php endif; ?>
								<?php foreach( $tokens as $token ) : ?>
									<input
										type="radio"
										id="wc-chase-paymentech-payment-token-<?php echo esc_attr( $token->get_id() ); ?>"
										name="wc-chase-paymentech-payment-token"
										class="js-wc-chase-paymentech-payment-token js-wc-payment-gateway-payment-token"
										style="width:auto;"
										value="<?php echo esc_attr( $token->get_id() ); ?>"
										<?php checked( $tokenized_payment_method_selected === $token->get_id() || ( null === $tokenized_payment_method_selected && $token->is_default() ) ); ?>
									/>
									<label style="display:inline;" for="wc-chase-paymentech-payment-token-<?php echo esc_attr( $token->get_id() ); ?>">
										<?php /* translators: Placeholders: %1$s - card type logo <img> or name, %2$s - last 4 card digits, %3$s - card expiration date MM/YY */
										printf( __( '%1$s ending in %2$s (expires %3$s)', 'woocommerce-gateway-chase-paymentech' ), $token->get_image_url() ? '<img width="32" height="20" title="' . esc_attr( $token->get_type_full() ) . '" src="' . esc_url( $token->get_image_url() ) . '" />' : esc_html( $token->get_type_full() ), esc_html( $token->get_last_four() ), esc_html( $token->get_exp_month() . '/' . $token->get_exp_year() ) ); ?>
										<?php echo ( $certification_mode ? esc_html__( 'Customer Ref Number', 'woocommerce-gateway-chase-paymentech' ) . ': ' . $token->get_id() : '' ); ?>
									</label><br />
								<?php endforeach; ?>
								<input type="radio" id="wc-chase-paymentech-use-new-payment-method" name="wc-chase-paymentech-payment-token" class="js-wc-chase-paymentech-payment-token" style="width:auto;" value="" <?php checked( $default_new_card ); ?> /> <label style="display:inline;" for="wc-chase-paymentech-use-new-payment-method"><?php esc_html_e( 'Use a new credit card', 'woocommerce-gateway-chase-paymentech' ); ?></label>
							</p>
							<div class="clear"></div>
						<?php endif; ?>

						<div class="wc-chase-paymentech-new-payment-method-form js-wc-chase-paymentech-new-payment-method-form" <?php echo ( $tokens ? 'style="display:none;"' : '' ); ?>>

							<?php if ( $tokenization_allowed || $tokenization_forced ) : ?>
								<p class="form-row">
									<input name="wc-chase-paymentech-tokenize-payment-method" id="wc-chase-paymentech-tokenize-payment-method" class="js-wc-chase-paymentech-tokenize-payment-method" type="checkbox" value="true" style="width:auto;" <?php checked( $should_tokenize_payment_method ); ?> />
									<label for="wc-chase-paymentech-tokenize-payment-method" style="display:inline;"><?php echo wp_kses_post( apply_filters( 'wc_gateway_chase_paymentech_tokenize_payment_method_text', __( "Securely Save Card to Account", 'woocommerce-gateway-chase-paymentech' ) ) ); ?></label>
								</p>
								<div class="clear"></div>
							<?php endif; ?>

							<iframe id="wc-chase-paymentech-pay-form" name="wc_chase_paymentech_pay_form" height="475" style="width:100%;margin-bottom:0;border:0;" src="<?php echo $iframe_src; ?>"></iframe>
						</div>

					</fieldset>
				</div>
			</li>
		</ul>

		<div class="form-row">
			<?php if ( count( $tokens ) > 0 ) : ?>
				<input type="submit" class="button alt" id="place_order" value="<?php esc_attr_e( 'Pay for order', 'woocommerce-gateway-chase-paymentech' ); ?>" />
				<input type="hidden" name="woocommerce_pay_page" value="1" />
				<input type="hidden" name="order_id"             value="<?php echo esc_attr( $order_id ); ?>" />
			<?php endif; ?>
		</div>
	</div>
</form>
