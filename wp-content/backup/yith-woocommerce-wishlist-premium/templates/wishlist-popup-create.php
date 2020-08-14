<?php
/**
 * Wishlist create popup
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $heading_icon string Heading icon HTML tag
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="create_new_wishlist">
	<form method="post" action="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'create' ) ); ?>">
		<?php do_action( 'yith_wcwl_before_wishlist_create' ); ?>

		<div class="yith-wcwl-popup-content">
			<?php if ( apply_filters( 'yith_wcwl_show_popup_heading_icon_instead_of_title', ! empty( $heading_icon ), $heading_icon ) ) : ?>
				<p class="heading-icon">
					<?php echo apply_filters( 'yith_wcwl_popup_heading_icon_class', $heading_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</p>
			<?php else : ?>
				<h3><?php esc_html_e( 'Create a new wishlist', 'yith-woocommerce-wishlist' ); ?></h3>
			<?php endif; ?>

			<p class="popup-description">
				<?php esc_html_e( 'Create a wishlist', 'yith-woocommerce-wishlist' ); ?>
			</p>

			<?php yith_wcwl_get_template_part( 'create' ); ?>
		</div>

		<?php do_action( 'yith_wcwl_after_wishlist_create' ); ?>
	</form>

</div>