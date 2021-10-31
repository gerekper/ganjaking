<?php
/**
 * Content wrappers
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$extra_class_escaped = '';
global $porto_layout, $porto_product_layout;
if ( 'widewidth' === $porto_layout && ( ! porto_is_product() || ! isset( $porto_product_layout ) || 'full_width' !== $porto_product_layout ) ) {
	$extra_class_escaped .= ' m-b-lg m-t';
}
?>
<div id="primary" class="content-area"><main id="content" class="site-main<?php echo ! $extra_class_escaped ? '' : $extra_class_escaped; ?>">
