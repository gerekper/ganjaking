<?php
/**
 * Help-tip field.
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

<img
		height="16" width="16"
		class="help_tip <?php echo esc_attr( $class ); ?>"
		src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/help.png' ); ?>"
		data-tip="<?php echo esc_attr( $value ); ?>"

	<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>

/>
