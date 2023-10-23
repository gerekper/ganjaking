<?php
/**
 * Template for displaying the notice component
 *
 * @var array $component The component.
 * @package YITH\PluginFramework\Templates\Components
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

list ( $component_id, $class, $attributes, $data, $notice_type, $message, $dismissible, $animate, $inline ) = yith_plugin_fw_extract( $component, 'id', 'class', 'attributes', 'data', 'notice_type', 'message', 'dismissible', 'animate', 'inline' );

$notice_type = $notice_type ?? 'info';
$message     = $message ?? '';
$dismissible = $dismissible ?? true;
$animate     = $animate ?? true;
$inline      = $inline ?? true;
$class       = $class ?? '';

$classes = array(
	'yith-plugin-fw__notice',
	"yith-plugin-fw__notice--{$notice_type}",
	$animate ? 'yith-plugin-fw-animate__appear-from-top' : '',
	$inline ? 'yith-plugin-fw--inline' : '',
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
	<?php echo wp_kses_post( $message ); ?>

	<?php if ( $dismissible ) : ?>
		<span class="yith-plugin-fw__notice__dismiss"></span>
	<?php endif; ?>
</div>
