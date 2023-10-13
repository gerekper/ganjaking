<?php
/**
 * Select-alt field.
 *
 * @var string       $id
 * @var string       $name
 * @var string       $class
 * @var string|array $value
 * @var array        $data
 * @var array        $custom_attributes
 * @var bool         $multiple
 * @var array        $options
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

$field_class   = $field_class ?? '';
$is_loading    = $is_loading ?? false;
$empty_message = $empty_message ?? __( 'No options', 'yith-booking-for-woocommerce' );

// Late enqueue script and styles.
wp_enqueue_style( 'yith-wcbk-fields' );
wp_enqueue_script( 'yith-wcbk-fields' );

$classes = array(
	'yith-wcbk-select-list',
	$class,
);
$classes = implode( ' ', array_filter( $classes ) );
?>

<div
		id="<?php echo esc_attr( $id ); ?>"
		class="<?php echo esc_attr( $classes ); ?>"
		data-empty-message="<?php echo esc_attr( $empty_message ); ?>"
		data-is-loading="<?php echo esc_attr( $is_loading ); ?>"

	<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
>
	<?php
	yith_wcbk_print_field(
		array(
			'type'    => 'select',
			'name'    => $name,
			'class'   => implode( ' ', array_filter( array( 'yith-wcbk-select-list__field', $field_class ) ) ),
			'value'   => $value,
			'options' => $options ?? array(),
		),
		true
	);
	?>

	<div class="yith-wcbk-select-list__options"></div>
</div>
