<?php
/**
 * Price rules.
 *
 * @var array  $price_rules
 * @var string $field_name
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

?>
<div class="yith-wcbk-price-rules">
	<?php
	yith_plugin_fw_get_component(
		array(
			'class'    => 'yith-wcbk-price-rules__blank-state',
			'type'     => 'list-table-blank-state',
			'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
			'message'  => __( 'You have no rules!', 'yith-booking-for-woocommerce' ),
			'cta'      => array(
				'class' => 'yith-wcbk-price-rules__new-rule',
				'title' => __( 'Add rule', 'yith-booking-for-woocommerce' ),
			),
		),
		true
	);
	?>
	<div class="yith-wcbk-settings-section-box__sortable-container yith-wcbk-price-rules__list">
		<?php
		$index = 1;
		foreach ( $price_rules as $key => $price_rule ) {
			yith_wcbk_get_view( 'product-tabs/utility/html-price-rule.php', compact( 'field_name', 'index', 'price_rule' ) );
			$index ++;
		}
		?>
	</div>
	<div class="yith-wcbk-settings-section__content__actions">
		<span class="yith-plugin-fw__button--add yith-wcbk-price-rules__new-rule"><?php esc_html_e( 'Add rule', 'yith-booking-for-woocommerce' ); ?></span>
		<div id="yith-wcbk-price-rules__pre-new-rule"></div>
	</div>

	<script type="text/html" id="tmpl-yith-wcbk-price-rule">
		<?php
		yith_wcbk_get_view(
			'product-tabs/utility/html-price-rule.php',
			array(
				'field_name' => $field_name,
				'index'      => '{{data.ruleIndex}}',
				'price_rule' => new YITH_WCBK_Price_Rule(),
				'add_button' => true,
			)
		);
		?>
	</script>
	<script type="text/html" id="tmpl-yith-wcbk-price-rule-condition">
		<?php
		$index            = '{{data.ruleIndex}}';
		$condition_index  = '{{data.conditionIndex}}';
		$_field_name      = "{$field_name}[{$index}]";
		$_field_id_prefix = "{$field_name}-id--{$index}__";
		$condition        = array(
			'type' => 'custom',
			'from' => '',
			'to'   => '',
		);

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
		?>
	</script>
</div>
