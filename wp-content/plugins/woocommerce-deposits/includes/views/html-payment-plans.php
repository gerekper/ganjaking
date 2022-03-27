<?php
/**
 * Payment plans template
 *
 * @package woocommerce-deposits
 */

?>
<div class="wrap woocommerce wc-deposits-admin">
	<h2><?php esc_html_e( 'Payment Plans', 'woocommerce-deposits' ); ?></h2>
	<div class="wc-col-container">
		<div class="wc-col-right">
			<div class="wc-col-wrap">
				<h3><?php esc_html_e( 'Existing Payment Plans', 'woocommerce-deposits' ); ?></h3>
				<?php $this->output_plans(); ?>
			</div>
		</div>
		<div class="wc-col-left">
			<div class="wc-col-wrap">
				<h3><?php esc_html_e( 'Add Payment Plan', 'woocommerce-deposits' ); ?></h3>
				<?php require 'html-payment-plan-form.php'; ?>
			</div>
		</div>
	</div>
</div>
