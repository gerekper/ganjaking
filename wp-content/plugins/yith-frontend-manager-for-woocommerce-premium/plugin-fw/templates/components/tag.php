<?php
/**
 * Template for displaying the notice component
 *
 * @var array $component The component.
 * @package YITH\PluginFramework\Templates\Components
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

list ( $component_id, $class, $attributes, $data, $color, $label ) = yith_plugin_fw_extract( $component, 'id', 'class', 'attributes', 'data', 'color', 'label' );

$color = $color ?? 'info';
$label = $label ?? '';
$class = $class ?? '';

$classes = array(
	'yith-plugin-fw__tag',
	"yith-plugin-fw__tag--{$color}-color",
	$class,
);

$class = implode( ' ', array_filter( $classes ) );
?>
<div
		id="<?php echo esc_attr( $component_id ); ?>"
		class="<?php echo esc_attr( $class ); ?>"
	<?php echo yith_plugin_fw_html_attributes_to_string( $attributes ); ?>
	<?php echo yith_plugin_fw_html_data_to_string( $data ); ?>
>
	<?php echo wp_kses_post( $label ); ?>
</div>
