<?php
/**
 * Variable product template options for switch
 *
 * @package YITH WooCommerce Subscription
 * @since   2.2.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Vars used on this template.
 *
 * @var int    $loop Current variation.
 * @var string $_ywsbs_override_delivery_schedule Check if the delivery schedules are override.
 * @var array  $_ywsbs_delivery_synch Delivery schedules options.
 * @var array  $time_options List of time options.
 * @var int    $num_variations Variations total number.
 * @var int    $_ywsbs_switchable_priority Priority number.
 * @var string $_ywsbs_prorate_recurring_payment Recurring payment.
 * @var string $_ywsbs_prorate_fee Charge Fee.
 * @var string $_ywsbs_switchable Switchable option value.
 */
?>

<h4 class="ywsbs-title-section"><?php esc_html_e( 'Upgrade/Switch/Downgrade subscription settings', 'yith-woocommerce-subscription' ); ?></h4>

<div class="ywsbs_product_panel_row">
	<div class="ywsbs_product_panel_row_element">
		<p class="variable_ywsbs_switchable_priority">
			<label for="_ywsbs_switchable_priority"><?php esc_html_e( 'Variation priority', 'yith-woocommerce-subscription' ); ?></label>
			<span class="wrap">
						<select id="variable_ywsbs_switchable_priority_<?php echo esc_attr( $loop ); ?>"
							name="variable_ywsbs_switchable_priority[<?php echo esc_attr( $loop ); ?>]"
							class="select ywsbs-with-margin yith-short-select switchable_priority">
						<?php
						$found_value = false;
						for ( $i = 0; $i < $num_variations; $i++ ) :
							?>
							<option
								value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $_ywsbs_switchable_priority, true ); ?> ><?php echo esc_html( $i + 1 ); ?></option>
						<?php endfor; ?>
							<?php if ( (int)$_ywsbs_switchable_priority >= (int)$num_variations ) : ?>
								<option
									value="<?php echo esc_attr( $_ywsbs_switchable_priority ); ?>"
									selected="selected"> <?php echo esc_html( $_ywsbs_switchable_priority + 1 ); ?></option>
							<?php endif; ?>
					</select>
			</span>
			<span class="description"><?php esc_html_e( 'Use this option to set a hierarchy of this variations and define when the user upgrade to a variation with higher priority or downgrade to a variation with lower priority.', 'yith-woocommerce-subscription' ); ?></span>
		</p>
	</div>
	<div class="ywsbs_product_panel_row_element ywsbs_switchable">
		<?php
		$args = array(
			'label'       => esc_html__( 'Allow switching to this variation', 'yith-woocommerce-subscription' ),
			'description' => esc_html__( 'Set if the user that purchased a variation can switch or not to another one.', 'yith-woocommerce-subscription' ),
			'value'       => $_ywsbs_switchable,
			'default'     => 'no',
			'id'          => 'variable_ywsbs_switchable_' . $loop,
			'name'        => 'variable_ywsbs_switchable[' . $loop . ']',
			'options'     => array(
				'no'      => esc_html__( 'Never', 'yith-woocommerce-subscription' ),
				'upgrade' => esc_html__( 'Yes, only to a variation with a lower priority', 'yith-woocommerce-subscription' ),
				'yes'     => esc_html__( 'Yes, without limits', 'yith-woocommerce-subscription' ),
			),
		);
		woocommerce_wp_select( $args );
		?>
	</div>
</div>

<div class="ywsbs_product_panel_row">
	<div class="ywsbs_product_panel_row_element ywsbs_charge_fee">
		<?php
		$args = array(
			'label'       => esc_html__( 'With a plan change, charge signup fee', 'yith-woocommerce-subscription' ),
			'description' => esc_html__( 'Choose whether to charge the signup fee if a user changes subscription plan.', 'yith-woocommerce-subscription' ),
			'value'       => $_ywsbs_prorate_fee,
			'default'     => 'no',
			'id'          => 'variable_ywsbs_prorate_fee_' . $loop,
			'name'        => 'variable_ywsbs_prorate_fee[' . $loop . ']',
			'options'     => array(
				'no'         => esc_html__( 'No additional sign up fee', 'yith-woocommerce-subscription' ),
				'yes'        => esc_html__( 'Yes, charge the full signup fee', 'yith-woocommerce-subscription' ),
				'difference' => esc_html__( 'Yes, but only charge the difference', 'yith-woocommerce-subscription' ),
			),
		);
		woocommerce_wp_select( $args );
		?>
	</div>
	<div class="ywsbs_product_panel_row_element ywsbs_prorate_recurring_payment">
		<?php
		$args = array(
			'label'       => esc_html__( 'Prorate recurring payment', 'yith-woocommerce-subscription' ),
			'description' => esc_html__( 'Choose how to manage the price difference between the plans when the user switch.', 'yith-woocommerce-subscription' ),
			'value'       => $_ywsbs_prorate_recurring_payment,
			'std'         => 'no',
			'id'          => 'variable_ywsbs_prorate_recurring_payment_' . $loop,
			'name'        => 'variable_ywsbs_prorate_recurring_payment[' . $loop . ']',
			'options'     => array(
				'no'        => __( 'No, never', 'yith-woocommerce-subscription' ),
				'upgrade'   => __( 'Yes, but only for upgrades', 'yith-woocommerce-subscription' ),
				'downgrade' => __( 'Yes, but only for downgrades', 'yith-woocommerce-subscription' ) ,
				'yes'       => __( 'Yes, for all plans changes', 'yith-woocommerce-subscription' ),
			),
		);
		woocommerce_wp_select( $args );
		?>
	</div>
</div>
