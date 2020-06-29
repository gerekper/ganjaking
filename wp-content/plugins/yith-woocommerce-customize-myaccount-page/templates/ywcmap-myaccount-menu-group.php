<?php
/**
 * MY ACCOUNT TEMPLATE MENU ITEM
 *
 * @since   2.0.0
 * @package YITH WooCommerce Customize My Account Page
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

?>

<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

	<a href="#" class="group-opener">
		<?php if ( ! empty( $options['icon'] ) ) :
			// prevent double fa-
			$icon = strpos( $options['icon'], 'fa-' ) === false ? 'fa-' . $options['icon'] : $options['icon']; ?>
			<i class="fa <?php echo esc_attr( $icon ); ?>"></i>
		<?php endif; ?>
		<?php echo esc_html( $options['label'] ); ?>
		<i class="opener fa <?php echo esc_attr( $class_icon ); ?>"></i>
	</a>

	<ul class="myaccount-submenu" <?php echo $options['open'] ? '' : 'style="display:none"'; ?>>
		<?php foreach ( $options['children'] as $child => $child_options ) {
			/**
			 * Print single endpoint
			 */
			do_action( 'yith_wcmap_print_single_endpoint', $child, $child_options );
		} ?>
	</ul>
</li>