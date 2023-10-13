<?php
/**
 * Template options in WC Product Panel
 *
 * @var YITH_WCBK_Availability_Rule[] $availability_rules The availability rules.
 * @var string                        $field_name         The field name.
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

$classes = array(
	'yith-wcbk-availability-rules',
	! ! $availability_rules ? 'yith-wcbk-availability-rules--has-rules' : '',
);
$classes = implode( ' ', array_filter( $classes ) );
?>
<div class="<?php echo esc_attr( $classes ); ?>">
	<?php
	yith_plugin_fw_get_component(
		array(
			'class'    => 'yith-wcbk-availability-rules__blank-state',
			'type'     => 'list-table-blank-state',
			'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
			'message'  => __( 'You have no rules!', 'yith-booking-for-woocommerce' ),
			'cta'      => array(
				'class' => 'yith-wcbk-availability-rules__new-rule',
				'title' => __( 'Add rule', 'yith-booking-for-woocommerce' ),
			),
		),
		true
	);
	?>
	<div class="yith-wcbk-settings-section-box__sortable-container yith-wcbk-availability-rules__list">
		<?php
		$index = 1;
		foreach ( $availability_rules as $key => $availability_rule ) {
			yith_wcbk_get_view( 'product-tabs/utility/html-availability-rule.php', compact( 'field_name', 'index', 'availability_rule' ) );
			$index ++;
		}
		?>
	</div>
	<div class="yith-wcbk-settings-section__content__actions">
		<span class="yith-plugin-fw__button--add yith-wcbk-availability-rules__new-rule"><?php esc_html_e( 'Add rule', 'yith-booking-for-woocommerce' ); ?></span>
	</div>
</div>

<script type="text/html" id="tmpl-yith-wcbk-availability-rule">
	<?php
	yith_wcbk_get_view(
		'product-tabs/utility/html-availability-rule.php',
		array(
			'field_name'        => $field_name,
			'index'             => '{{data.ruleIndex}}',
			'availability_rule' => new YITH_WCBK_Availability_Rule(),
			'add_button'        => true,
		)
	);
	?>
</script>

<script type="text/html" id="tmpl-yith-wcbk-availability-rule-date-range">
	<?php
	yith_wcbk_get_view(
		'product-tabs/utility/html-availability-rule-date-range.php',
		array(
			'field_name'       => $field_name,
			'index'            => '{{data.ruleIndex}}',
			'date_range_index' => '{{data.dateRangeIndex}}',
			'from'             => '',
			'to'               => '',
		)
	);
	?>
</script>

<script type="text/html" id="tmpl-yith-wcbk-availability-rule-availability">
	<?php
	$index        = '{{data.ruleIndex}}';
	$_field_name  = "{$field_name}[{$index}][availabilities]";
	$availability = new YITH_WCBK_Availability();

	yith_wcbk_get_view(
		'product-tabs/utility/html-availability.php',
		array(
			'main_class'   => 'yith-wcbk-availability-rule',
			'field_name'   => $_field_name,
			'index'        => '{{data.availabilityIndex}}',
			'availability' => $availability,
		)
	);
	?>
</script>

<script type="text/html" id="tmpl-yith-wcbk-availability-rule-availability-time-slot">
	<?php
	$index       = '{{data.ruleIndex}}';
	$_field_name = "{$field_name}[{$index}][availabilities][{{data.availabilityIndex}}][time_slots]";

	yith_wcbk_get_view(
		'product-tabs/utility/html-availability-time-slot.php',
		array(
			'main_class' => 'yith-wcbk-availability-rule',
			'field_name' => $_field_name,
			'index'      => '{{data.timeSlotIndex}}',
			'from'       => '00:00',
			'to'         => '00:00',
		)
	);
	?>
</script>
