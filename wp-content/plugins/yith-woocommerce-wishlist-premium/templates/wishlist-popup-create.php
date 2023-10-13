<?php
/**
 * Wishlist create popup
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist
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
		<?php
		/**
		 * DO_ACTION: yith_wcwl_before_wishlist_create
		 *
		 * Allows to render some content or fire some action before the wishlist creation options.
		 */
		do_action( 'yith_wcwl_before_wishlist_create' );
		?>

		<div class="yith-wcwl-popup-content">
			<?php
			/**
			 * APPLY_FILTERS: yith_wcwl_show_popup_heading_icon_instead_of_title
			 *
			 * Filter whether to show the icon in the 'Create wishlist' popup.
			 *
			 * @param bool   $show_icon    Whether to show icon or not
			 * @param string $heading_icon Heading icon
			 *
			 * @return bool
			 */
			if ( apply_filters( 'yith_wcwl_show_popup_heading_icon_instead_of_title', ! empty( $heading_icon ), $heading_icon ) ) :
				?>
				<p class="heading-icon">
					<?php
					/**
					 * APPLY_FILTERS: yith_wcwl_popup_heading_icon_class
					 *
					 * Filter the heading icon in the 'Create wishlist' popup.
					 *
					 * @param string $heading_icon Heading icon
					 *
					 * @return string
					 */
					echo yith_wcwl_kses_icon( apply_filters( 'yith_wcwl_popup_heading_icon_class', $heading_icon ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</p>
			<?php else : ?>
				<h3><?php esc_html_e( 'Create a new wishlist', 'yith-woocommerce-wishlist' ); ?></h3>
			<?php endif; ?>

			<p class="popup-description">
				<?php
				/**
				 * APPLY_FILTERS: yith_wcwl_create_wishlist_label
				 *
				 * Filter the label to create a wishlist.
				 *
				 * @param string $label Label
				 *
				 * @return string
				 */
				echo esc_html( apply_filters( 'yith_wcwl_create_wishlist_label', __( 'Create a wishlist', 'yith-woocommerce-wishlist' ) ) );
				?>
			</p>

			<?php yith_wcwl_get_template_part( 'create' ); ?>
		</div>

		<?php
		/**
		 * DO_ACTION: yith_wcwl_after_wishlist_create
		 *
		 * Allows to render some content or fire some action after the wishlist creation options.
		 */
		do_action( 'yith_wcwl_after_wishlist_create' );
		?>
	</form>

</div>
