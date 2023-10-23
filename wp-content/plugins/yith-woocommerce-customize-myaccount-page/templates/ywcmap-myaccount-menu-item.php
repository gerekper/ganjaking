<?php
/**
 * MY ACCOUNT TEMPLATE MENU ITEM
 *
 * @since   3.0.0
 * @package YITH WooCommerce Customize My Account Page
 * @var array $options
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

// if array implode.
is_array( $classes ) && $classes = implode( ' ', $classes );
$target                          = ( isset( $options['target_blank'] ) && $options['target_blank'] ) ? 'target="_blank"' : '';
?>

<li class="<?php echo esc_attr( $classes ); ?>">
	<?php
	/**
	 * APPLY_FILTERS: yith_wcmap_endpoint_anchor_tag_class
	 *
	 * Filters the CSS class of the endpoint link.
	 *
	 * @param string $css_class CSS class.
	 *
	 * @return string
	 */
	/**
	 * APPLY_FILTERS: yith_wcmap_endpoint_anchor_url
	 *
	 * Filters the URL of the endpoint link.
	 *
	 * @param string $url      Endpoint URL.
	 * @param string $endpoint Endpoint key.
	 *
	 * @return string
	 */
	?>
	<a class="<?php echo esc_attr( apply_filters( 'yith_wcmap_endpoint_anchor_tag_class', 'yith-' . $endpoint ) ); ?>"
		href="<?php echo esc_url( apply_filters( 'yith_wcmap_endpoint_anchor_url', $url, $endpoint ) ); ?>" title="<?php echo esc_attr( $options['label'] ); ?>" <?php echo $target; ?>>

		<?php echo yith_wcmap_get_menu_item_icon_html( $options ); ?>
		<span class="item-label"><?php echo esc_html( $options['label'] ); ?></span>
	</a>
</li>
