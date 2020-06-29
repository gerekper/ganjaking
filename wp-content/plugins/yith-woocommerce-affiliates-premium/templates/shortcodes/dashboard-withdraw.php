<?php
/**
 * Affiliate Dashboard Withdraw
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

<div class="yith-wcaf yith-wcaf-withdraw woocommerce">

	<?php
	if( function_exists( 'wc_print_notices' ) ){
		wc_print_notices();
	}
	?>

	<?php do_action( 'yith_wcaf_before_dashboard_section', 'withdraw' ) ?>

	<div class="left-column <?php echo ( ! $show_right_column ) ? 'full-width' : '' ?>">
		<?php if( ! $can_withdraw ): ?>
			<?php echo apply_filters( 'yith_wcaf_affiliate_cannot_withdraw_message', sprintf( __('You already have an active payment request; please check payment status in <a href="%s">Payments\' page</a>','yith-woocommerce-affiliates' ), $payments_endpoint ) ) ?>
		<?php else: ?>
			<form method="POST" enctype="multipart/form-data">
				<div class="first-step">
					<p class="form-row form-row-first">
						<label for="withdraw_from"><?php _e( 'From:', 'yith-woocommerce-affiliates' ) ?></label>
						<input type="text" id="withdraw_from" name="withdraw_from" class="datepicker" value="<?php echo esc_attr( $withdraw_from )?>" />
					</p>

					<p class="form-row form-row-last">
						<label for="withdraw_to"><?php _e( 'To:', 'yith-woocommerce-affiliates' ) ?></label>
						<input type="text" id="withdraw_to" name="withdraw_to" class="datepicker" value="<?php echo esc_attr( $withdraw_to )?>" />
					</p>

					<div class="clearfix"></div>

					<div class="information-panel form-row form-row-wide">

						<p class="total">
							<b><?php esc_html_e( 'Withdraw Total:', 'yith-woocommerce-affiliates' ); ?></b>
							<span class="withdraw-current-total"><?php echo wc_price( $current_amount ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</p>

						<?php if ( $min_withdraw ) : ?>
							<p class="min-withdraw">
								<b><?php esc_html_e( 'Minimum amount to withdraw:', 'yith-woocommerce-affiliates' ); ?></b>
								<?php echo wc_price( $min_withdraw ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</p>
						<?php endif; ?>

						<p class="max-withdraw">
							<b><?php esc_html_e( 'Current balance:', 'yith-woocommerce-affiliates' ); ?></b>
							<?php echo wc_price( $max_withdraw ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</p>

					</div>
				</div>

				<div class="second-step">
					<p class="form-row">
						<label for="payment_email"><?php esc_html_e( 'Payment email', 'yith-woocommerce-affiliates' ); ?></label>
						<input type="email" id="payment_email" name="payment_email" value="<?php echo esc_attr( $payment_email ); ?>" />
					</p>
				</div>

				<?php if ( $require_invoice ) : ?>
					<div class="third-step woocommerce-billing-fields">
						<?php if ( 'upload' === $invoice_mode || 'both' === $invoice_mode ) : ?>
							<h4><?php esc_html_e( 'Upload your invoice', 'yith-woocommerce-affiliates' ); ?></h4>
							<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo esc_attr( 1048576 * apply_filters( 'yith_wcaf_max_invoice_size', 3 ) ); ?>" />
							<input type="file" id="invoice_file" name="invoice_file" accept="<?php echo esc_attr( apply_filters( 'yith_wcaf_invoice_upload_mime', 'application/pdf' ) ); ?>" />

							<?php if ( $invoice_example ) : ?>
								<p class="description">
									<?php
									// translators: 1. Url to example invoice.
									echo apply_filters( 'yith_wcaf_example_invoice_text', sprintf( __( 'Please, refer to the following <a href="%s">example</a> for invoice creation', 'yith-woocommerce-affiliates' ), $invoice_example ), $invoice_example ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</p>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ( 'both' === $invoice_mode && ! empty( $invoice_fields ) ) : ?>
							<span class="invoice-mode-separator"><?php esc_html_e( 'or', 'yith-woocommerce-affiliates' ); ?></span>
						<?php endif; ?>

						<?php if ( ( 'generate' === $invoice_mode || 'both' === $invoice_mode ) && ! empty( $invoice_fields ) ) : ?>
							<h4>
								<?php echo 'both' === $invoice_mode ? esc_html__( 'Generate a new one', 'yith-woocommerce-affiliates' ) : esc_html__( 'Generate a new invoice', 'yith-woocommerce-affiliates' ); ?>
							</h4>
							<?php
							foreach ( $invoice_fields as $field ) :
								$field_data = YITH_WCAF_Shortcode_Premium::get_field( $field );
								$value      = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing
								$value      = ( ! $value && isset( $invoice_profile[ $field ] ) ) ? $invoice_profile[ $field ] : $value;

								woocommerce_form_field( $field, $field_data, $value );
							endforeach;
							?>

							<?php
							if ( 'yes' === $show_terms_field ) :

								$terms_link = sprintf( '<a target="_blank" href="%s">%s</a>', $terms_anchor_url, $terms_anchor_text );
								$label      = apply_filters( 'yith_wcaf_terms_label', str_replace( '%TERMS%', $terms_link, $terms_label ) );
								$required   = apply_filters( 'yith_wcaf_terms_required', true );

								?>
								<p class="form-row form-row-wide">
									<label for="terms">
										<input type="checkbox" name="terms" id="terms" value="yes" <?php checked( isset( $_POST['terms'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing ?> />
										<?php echo wp_kses_post( $label ); ?> <?php echo $required ? '<span class="required">*</span>' : ''; ?>
									</label>
								</p>
								<?php
							endif;
							?>

						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php wp_nonce_field( 'yith_wcaf_withdraw', '_withdraw_nonce' ); ?>

				<input class="button submit" type="submit" value="<?php echo esc_attr( apply_filters( 'yith_wcaf_withdraw_submit_button', __( 'Request Withdraw', 'yith-woocommerce-affiliates' ) ) ); ?>" />

			</form>
		<?php endif; ?>
	</div>

	<!--NAVIGATION MENU-->
	<?php
	$atts = array(
		'show_right_column'    => $show_right_column,
		'show_left_column'     => true,
		'show_dashboard_links' => $show_dashboard_links,
		'dashboard_links'      => $dashboard_links,
	);
	yith_wcaf_get_template( 'navigation-menu.php', $atts, 'shortcodes' )
	?>

	<?php do_action( 'yith_wcaf_after_dashboard_section', 'withdraw' ); ?>

</div>
