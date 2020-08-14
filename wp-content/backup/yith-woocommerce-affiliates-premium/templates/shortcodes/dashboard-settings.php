<?php
/**
 * Affiliate Dashboard Settings
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.5
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="yith-wcaf yith-wcaf-settings woocommerce">

	<?php
	if ( function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	}
	?>

	<?php do_action( 'yith_wcaf_before_dashboard_section', 'settings' ); ?>

	<form method="post">

		<?php do_action( 'yith_wcaf_settings_form_start' ); ?>

		<?php if ( apply_filters( 'yith_wcaf_show_additional_fields', 'yes' === $show_additional_fields, 'settings' ) ) : ?>

			<?php if ( 'yes' === $show_name_field ) : ?>
				<p class="form form-row">
					<label for="first_name"><?php esc_html_e( 'First name', 'yith-woocommerce-affiliates' ); ?></label>
					<input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $affiliate_name ); ?>" />
					<small><?php esc_html_e( '(First name for your account)', 'yith-woocommerce-affiliates' ); ?></small>
				</p>
			<?php endif; ?>

			<?php if ( 'yes' === $show_surname_field ) : ?>
				<p class="form form-row">
					<label for="last_name"><?php esc_html_e( 'Last name', 'yith-woocommerce-affiliates' ); ?></label>
					<input type="text" name="last_name" id="first_name" value="<?php echo esc_attr( $affiliate_surname ); ?>" />
					<small><?php esc_html_e( '(Last name for your account)', 'yith-woocommerce-affiliates' ); ?></small>
				</p>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( apply_filters( 'yith_wcaf_payment_email_required', true ) ) : ?>
		<p class="form form-row">
			<label for="payment_email"><?php esc_html_e( 'Payment email', 'yith-woocommerce-affiliates' ); ?></label>
			<input type="email" name="payment_email" id="payment_email" value="<?php echo esc_attr( $payment_email ); ?>" />
			<small><?php esc_html_e( '(Email address where you want to receive PayPal payments for commissions)', 'yith-woocommerce-affiliates' ); ?></small>
		</p>
		<?php endif; ?>

		<?php do_action( 'yith_wcaf_settings_form_after_payment_email' ); ?>

		<?php do_action( 'yith_wcaf_settings_form' ); ?>

		<input type="submit" name="settings_submit" value="<?php esc_attr_e( 'Submit', 'yith-woocommerce-affiliates' ); ?>" />

	</form>

	<?php do_action( 'yith_wcaf_after_dashboard_section', 'settings' ); ?>

</div>
