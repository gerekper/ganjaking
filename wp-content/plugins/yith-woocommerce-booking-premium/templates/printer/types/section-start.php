<?php
/**
 * Section start field.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $value
 * @var array  $data
 * @var array  $custom_attributes
 * @var string $section_html_tag
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

$html_tag = ! empty( $section_html_tag ) ? $section_html_tag : 'div';
?>

<<?php echo esc_attr( $html_tag ); ?> id="<?php echo esc_attr( $id ?? '' ); ?>"

<?php if ( ! ! $name ) : ?>
	name="<?php echo esc_attr( $name ); ?>"
<?php endif; ?>

class="<?php echo esc_attr( $class ?? '' ); ?>"

<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
>
