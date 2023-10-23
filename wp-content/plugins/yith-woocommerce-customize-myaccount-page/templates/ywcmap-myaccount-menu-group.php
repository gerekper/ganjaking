<?php
/**
 * MY ACCOUNT TEMPLATE MENU ITEM
 *
 * @since   2.0.0
 * @package YITH WooCommerce Customize My Account Page
 * @var array $options
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

?>

<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

	<a href="#" class="group-opener">
		<?php echo yith_wcmap_get_menu_item_icon_html( $options ); ?>
		<span class="item-label"><?php echo esc_html( $options['label'] ); ?></span>
		<span class="item-opener"><i class="fa <?php echo esc_attr( $class_icon ); ?>"></i>
	</a>

	<ul class="myaccount-submenu" <?php echo $options['open'] ? '' : 'style="display:none"'; ?>>
		<?php
		foreach ( $options['children'] as $child => $child_options ) {
			/**
			 * Print single endpoint
			 */
			/**
			 * DO_ACTION: yith_wcmap_print_single_endpoint
			 *
			 * Allows to render some content when printing the endpoints.
			 *
			 * @param string $endpoint Endpoint key.
			 * @param array  $options  Endpoint options.
			 */
			do_action( 'yith_wcmap_print_single_endpoint', $child, $child_options );
		}
		?>
	</ul>
</li>
