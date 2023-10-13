<?php
/**
 * Help-tip-alt field.
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

wp_enqueue_script( 'yith-wcbk-fields' );
wp_enqueue_style( 'yith-wcbk-fields' );
?>

<span
		class="yith-wcbk-help-tip help_tip <?php echo esc_attr( $class ); ?>"
		data-tip="<?php echo esc_attr( $value ); ?>"

	<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
	<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
>
	<?php yith_wcbk_print_svg( 'info' ); ?>
</span>
