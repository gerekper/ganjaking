<?php
/**
 * Section end field.
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

$html_tag = ! empty( $section_html_tag ) ? $section_html_tag : 'p';
?>

</<?php echo esc_attr( $html_tag ); ?>>
