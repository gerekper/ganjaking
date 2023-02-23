<?php
/**
 * Deposit Form Template
 *
 * @package woocommerce-deposits
 */

$product_id = $product->get_id();

// Return empty wrapper if deposits are disabled.
if ( ! WC_Deposits_Product_Manager::deposits_enabled( $product_id ) ) {
	echo '<div class="wc-deposits-wrapper"></div>';
	return;
}

$default_selected_type = WC_Deposits_Product_Manager::get_deposit_selected_type( $product_id );
$style                 = 'deposit' === $default_selected_type ? 'style="display:block;"' : 'style="display:none;"';
?>
<div class="wc-deposits-wrapper <?php echo WC_Deposits_Product_Manager::deposits_forced( $product_id ) ? 'wc-deposits-forced' : 'wc-deposits-optional'; ?>">
	<?php if ( ! WC_Deposits_Product_Manager::deposits_forced( $product_id ) ) : ?>
		<ul class="wc-deposits-option">
			<li>
				<input type="radio" name="wc_deposit_option" value="yes" id="wc-option-pay-deposit" <?php checked( $default_selected_type, 'deposit' ); ?> />
				<label for="wc-option-pay-deposit">
					<?php esc_html_e( 'Pay Deposit', 'woocommerce-deposits' ); ?>
				</label>
			</li>
			<li>
				<input type="radio" name="wc_deposit_option" value="no" id="wc-option-pay-full" <?php checked( $default_selected_type, 'full' ); ?> />
				<label for="wc-option-pay-full">
					<?php esc_html_e( 'Pay in Full', 'woocommerce-deposits' ); ?>
				</label>
			</li>
		</ul>
	<?php endif; ?>

	<?php if ( 'plan' === WC_Deposits_Product_Manager::get_deposit_type( $product_id ) ) : ?>
		<ul class="wc-deposits-payment-plans" <?php echo $style; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped ?>>
			<?php foreach ( WC_Deposits_Plans_Manager::get_plans_for_product( $product_id ) as $key => $plan ) : ?>
				<li class="wc-deposits-payment-plan">
					<input type="radio" name="wc_deposit_payment_plan" <?php checked( $key, 0 ); ?> value="<?php echo esc_attr( $plan->get_id() ); ?>" id="wc-deposits-payment-plan-<?php echo esc_attr( $plan->get_id() ); ?>" />
					<label for="wc-deposits-payment-plan-<?php echo esc_attr( $plan->get_id() ); ?>">
						<strong class="wc-deposits-payment-plan-name"><?php echo esc_html( $plan->get_name() ); ?></strong>
						<small class="wc-deposits-payment-plan-description"><?php echo wp_kses_post( $plan->get_description() ); ?></small>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
		<div class="wc-deposits-payment-description" <?php echo esc_attr( $style ); ?>>
			<?php foreach ( WC_Deposits_Plans_Manager::get_plans_for_product( $product_id ) as $key => $plan ) : ?>
				<p id="payment-plan-description-<?php echo esc_attr( $plan->get_id() ); ?>" style="display: none;">
					<?php
						echo wp_kses(
							WC_Deposits_Product_Manager::get_formatted_deposit_payment_plan_amount( $product_id, $plan->get_id() ) . ' (' . $plan->get_formatted_schedule() . ')',
							array( 'span' => array() )
						);
					?>
				</p>
			<?php endforeach; ?>			
		</div>
	<?php else : ?>
		<div class="wc-deposits-payment-description" <?php echo $style; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped ?>>
			<?php echo wp_kses_post( WC_Deposits_Product_Manager::get_formatted_deposit_amount( $product_id ) ); ?>
		</div>
	<?php endif; ?>
</div>
