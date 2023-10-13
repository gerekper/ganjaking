<?php
/**
 * Price rule.
 *
 * @var int                  $index      Index.
 * @var string               $field_name Field name.
 * @var YITH_WCBK_Price_Rule $price_rule Price rule object.
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$default_toggle_class = is_numeric( $index ) ? 'yith-wcbk-settings-section-box--closed' : '';

$_field_name      = "{$field_name}[{$index}]";
$_field_id_prefix = "{$field_name}-id--{$index}__";

$rule_name  = $price_rule->get_name( 'edit' );
$conditions = $price_rule->get_conditions( 'edit' );

$condition_defaults = array(
	'type' => 'custom',
	'from' => '',
	'to'   => '',
);

if ( ! $conditions || ! is_array( $conditions ) || count( $conditions ) < 1 ) {
	$conditions = array( $condition_defaults );
}

$operators = array(
	'add'            => esc_html__( '+ Increase the price by', 'yith-booking-for-woocommerce' ),
	'sub'            => esc_html__( '- Decrease the price by', 'yith-booking-for-woocommerce' ),
	'mul'            => esc_html__( '* Multiply the price by', 'yith-booking-for-woocommerce' ),
	'div'            => esc_html__( '/ Divide the price by', 'yith-booking-for-woocommerce' ),
	'set-to'         => esc_html__( '= Set the price to', 'yith-booking-for-woocommerce' ),
	'add-percentage' => esc_html__( '+% Increase the price as a percentage by', 'yith-booking-for-woocommerce' ),
	'sub-percentage' => esc_html__( '-% Decrease the price as a percentage by', 'yith-booking-for-woocommerce' ),
);

$base_price_enabled = ! ( in_array( $price_rule->get_base_price_operator( 'edit' ), array( 'add', 'sub', 'add-percentage', 'sub-percentage' ), true ) && ! $price_rule->get_base_price( 'edit' ) );
$base_fee_enabled   = ! ( in_array( $price_rule->get_base_fee_operator( 'edit' ), array( 'add', 'sub', 'add-percentage', 'sub-percentage' ), true ) && ! $price_rule->get_base_fee( 'edit' ) );
?>
<div class="yith-wcbk-settings-section-box yith-wcbk-price-rule <?php echo esc_attr( $default_toggle_class ); ?>"
		data-index="<?php echo esc_attr( $index ); ?>"
>
	<?php
	yith_wcbk_print_field(
		array(
			'type'  => 'hidden',
			'class' => 'yith-wcbk-settings-section-box__sortable-position',
			'name'  => $_field_name . '[position]',
			'value' => $index,
		)
	);
	?>
	<div class="yith-wcbk-settings-section-box__title yith-wcbk-settings-section-box__sortable-anchor">
		<h3><?php echo esc_html( ! ! $rule_name ? $rule_name : __( 'Untitled', 'yith-booking-for-woocommerce' ) ); ?></h3>
		<span class="yith-wcbk-settings-section-box__toggle">
			<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"></path>
			</svg>
		</span>
		<span class="yith-wcbk-settings-section-box__enabled">
			<?php
			yith_wcbk_print_field(
				array(
					'type'  => 'onoff',
					'name'  => $_field_name . '[enabled]',
					'value' => $price_rule->get_enabled(),
				)
			);
			?>
		</span>
	</div>
	<div class="yith-wcbk-settings-section-box__content">
		<?php
		yith_wcbk_form_field(
			array(
				'title'  => __( 'Rule name', 'yith-booking-for-woocommerce' ),
				'class'  => 'yith-wcbk-settings-section-box__edit-title',
				'fields' => array(
					array(
						'type'  => 'text',
						'class' => 'yith-wcbk-price-rule__title-field',
						'value' => $rule_name,
						'name'  => $_field_name . '[name]',
					),
				),
			)
		);

		$conditions_html = '';
		$condition_index = 1;
		foreach ( $conditions as $condition ) {
			$condition = wp_parse_args( $condition, $condition_defaults );
			ob_start();
			yith_wcbk_get_view(
				'product-tabs/utility/html-price-rule-condition.php',
				array(
					'condition'                 => $condition,
					'index'                     => $index,
					'condition_index'           => $condition_index,
					'condition_type'            => $condition['type'],
					'condition_from'            => $condition['from'],
					'condition_to'              => $condition['to'],
					'condition_field_name'      => $_field_name . '[conditions][' . $condition_index . ']',
					'condition_field_id_prefix' => $_field_id_prefix . "condition-{$condition_index}__",
				)
			);
			$conditions_html .= ob_get_clean();

			$condition_index ++;
		}

		$condition_actions_html = '<span class="yith-wcbk-admin-action-link yith-wcbk-price-rule__conditions__new-condition">';

		$condition_actions_html .= '+ ' . esc_html__( 'Add new condition', 'yith-booking-for-woocommerce' );
		$condition_actions_html .= '</span>';

		yith_wcbk_form_field(
			array(
				'title'  => __( 'Conditions', 'yith-booking-for-woocommerce' ),
				'fields' => array(
					'type'   => 'section',
					'class'  => 'yith-wcbk-price-rule__conditions',
					'fields' => array(
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-price-rule__conditions__list',
							'fields' => array(
								array(
									'type'  => 'html',
									'value' => $conditions_html,
								),
							),
						),
						array(
							'type'   => 'section',
							'class'  => 'yith-wcbk-price-rule__conditions__actions',
							'fields' => array(
								array(
									'type'  => 'html',
									'value' => $condition_actions_html,
								),
							),
						),
					),
				),
			)
		);

		yith_wcbk_form_field(
			array(
				'title'  => __( 'Rule changes the base price', 'yith-booking-for-woocommerce' ),
				'desc'   => __( 'Enable to use this rule to change the base price', 'yith-booking-for-woocommerce' ),
				'fields' => array(
					array(
						'type'       => 'onoff',
						'yith-field' => true,
						'class'      => 'yith-wcbk-price-rule__base-price-enabled',
						'value'      => $base_price_enabled ? 'yes' : 'no',
					),
				),
			)
		);

		yith_wcbk_form_field(
			array(
				// translators: %s is the booking duration.
				'title'  => sprintf( __( 'Base price for %s', 'yith-booking-for-woocommerce' ), yith_wcbk_product_metabox_dynamic_duration() ),
				'class'  => 'yith_booking_multi_fields yith-wcbk-operator-and-amount-fields yith-wcbk-price-rule__base-price-fields',
				'data'   => array(
					'currency-symbol' => get_woocommerce_currency_symbol(),
				),
				'fields' => array(
					array(
						'type'    => 'select',
						'class'   => 'yith-wcbk-operator-and-amount-fields__operator',
						'name'    => $_field_name . '[base_price_operator]',
						'options' => $operators,
						'value'   => $price_rule->get_base_price_operator( 'edit' ),
					),
					array(
						'type'  => 'text',
						'class' => 'yith-wcbk-mini-field yith-wcbk-operator-and-amount-fields__amount',
						'name'  => $_field_name . '[base_price]',
						'value' => $price_rule->get_base_price( 'edit' ),
					),
					array(
						'type'  => 'html',
						'value' => '<span class="yith-wcbk-operator-and-amount-fields__symbol"></span>',
					),
				),
			)
		);

		yith_wcbk_form_field(
			array(
				'title'  => __( 'Rule changes the fixed base fee', 'yith-booking-for-woocommerce' ),
				'desc'   => __( 'Enable to use this rule to change the fixed base fee', 'yith-booking-for-woocommerce' ),
				'fields' => array(
					array(
						'type'       => 'onoff',
						'yith-field' => true,
						'class'      => 'yith-wcbk-price-rule__base-fee-enabled',
						'value'      => $base_fee_enabled ? 'yes' : 'no',
					),
				),
			)
		);

		yith_wcbk_form_field(
			array(
				'title'  => __( 'Fixed base fee', 'yith-booking-for-woocommerce' ),
				'class'  => 'yith_booking_multi_fields yith-wcbk-operator-and-amount-fields yith-wcbk-price-rule__base-fee-fields',
				'data'   => array(
					'currency-symbol' => get_woocommerce_currency_symbol(),
				),
				'fields' => array(
					array(
						'type'    => 'select',
						'class'   => 'yith-wcbk-operator-and-amount-fields__operator',
						'name'    => $_field_name . '[base_fee_operator]',
						'options' => $operators,
						'value'   => $price_rule->get_base_fee_operator( 'edit' ),
					),
					array(
						'type'  => 'text',
						'class' => 'yith-wcbk-mini-field yith-wcbk-operator-and-amount-fields__amount',
						'name'  => $_field_name . '[base_fee]',
						'value' => $price_rule->get_base_fee( 'edit' ),
					),
					array(
						'type'  => 'html',
						'value' => '<span class="yith-wcbk-operator-and-amount-fields__symbol"></span>',
					),
				),
			)
		);
		?>
		<div class="yith-wcbk-settings-section-box__content__actions yith-wcbk-right">
			<?php if ( ! empty( $add_button ) ) : ?>
				<span class="yith-plugin-fw__button--primary yith-plugin-fw__button--with-icon  yith-wcbk-price-rules__add-rule">
					<i class="yith-icon yith-icon-check"></i>
					<?php
					esc_html_e( 'Add rule', 'yith-booking-for-woocommerce' );
					?>
				</span>
			<?php endif; ?>
			<span class="yith-plugin-fw__button--trash yith-wcbk-price-rules__delete-rule"><?php esc_html_e( 'Delete rule', 'yith-booking-for-woocommerce' ); ?></span>
		</div>
	</div>
</div>
