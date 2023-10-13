<?php
/**
 * Label field.
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
?>

<label
		for="<?php echo esc_attr( $for ?? '' ); ?>"
		class="<?php echo esc_attr( $class ?? '' ); ?>"

	<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
>
	<?php echo wp_kses_post( $value ); ?>
</label>
