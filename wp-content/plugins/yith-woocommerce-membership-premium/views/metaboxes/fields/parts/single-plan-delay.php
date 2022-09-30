<?php
/**
 * @var string $plan_name The plan name.
 * @var int    $plan_id   The plan ID.
 * @var int    $delay     The delay.
 * @var string $name      The name.
 */
defined( 'YITH_WCMBS' ) || exit();


$field = yith_plugin_fw_get_field(
	array(
		'type'              => 'number',
		'class'             => 'yith-wcmbs-short-inline-field yith-wcmbs-single-plan-delay-field',
		'name'              => "{$name}[{$plan_id}]",
		'value'             => $delay,
		'custom_attributes' => ! ! $delay ? '' : 'disabled',
	),
	false,
	false
);
?>
<div class="yith-wcmbs-form-field yith-wcmbs-single-plan-delay-row" data-plan-id="<?php echo esc_attr( $plan_id ); ?>">
	<label class="yith-wcmbs-form-field__label"><?php echo esc_html( sprintf( __( 'For the "%s" plan, make it available', 'yith-woocommerce-membership' ), $plan_name ) ); ?></label>
	<div class="yith-wcmbs-form-field__content">
		<?php
		yith_plugin_fw_get_field(
			array(
				'type'    => 'radio',
				'id'      => "_yith_wcmbs_single_plan_delay_{$plan_id}",
				'name'    => "_yith_wcmbs_single_plan_delay_{$plan_id}",
				'options' => array(
					'now'   => __( 'Immediately', 'yith-woocommerce-membership' ),
					'delay' => sprintf( __( 'After %s days from signup', 'yith-woocommerce-membership' ), $field ),
				),
				'value'   => ! ! $delay ? 'delay' : 'now',
			),
			true
		);
		?>
	</div>
</div>
