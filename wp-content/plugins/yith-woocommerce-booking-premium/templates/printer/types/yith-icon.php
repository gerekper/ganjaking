<?php
/**
 * YITH icon field.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $value
 * @var array  $data
 * @var array  $custom_attributes
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

$icon    = $icon ?? '';
$classes = array(
	'yith-icon yith-icon-' . $icon,
	$class ?? '',
);
$class   = implode( ' ', array_filter( $classes ) );
?>
<span
		id="<?php echo esc_attr( $id ?? '' ); ?>"
		class="<?php echo esc_attr( $class ?? '' ); ?>"

	<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
></span>
