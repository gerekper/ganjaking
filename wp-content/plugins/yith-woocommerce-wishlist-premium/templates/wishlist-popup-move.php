<?php
/**
 * Wishlist move popup
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist                     \YITH_WCWL_Wishlist Wishlist
 * @var $move_to_another_wishlist_url string Url to use as action for wishlist form
 * @var $users_wishlists              \YITH_WCWL_Wishlist[] User wishlists
 * @var $wishlist_token               string Wishlist token
 * @var $heading_icon                 string Heading icon HTML tag
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="move_to_another_wishlist">
	<form action="<?php echo esc_attr( $move_to_another_wishlist_url ); ?>" method="post" class="wishlist-move-to-another-wishlist-popup">
		<div class="yith-wcwl-popup-content">
			<?php
			/**
			 * APPLY_FILTERS: yith_wcwl_show_popup_heading_icon_instead_of_title
			 *
			 * Filter whether to show the icon in the 'Move to another wishlist' popup.
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
					 * Filter the heading icon in the 'Move to another wishlist' popup.
					 *
					 * @param string $heading_icon Heading icon
					 *
					 * @return string
					 */
					echo yith_wcwl_kses_icon( apply_filters( 'yith_wcwl_popup_heading_icon_class', $heading_icon ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</p>
				<p class="popup-description">
					<?php esc_html_e( 'Move to another wishlist', 'yith-woocommerce-wishlist' ); ?>
				</p>
			<?php else : ?>
				<h3><?php esc_html_e( 'Move to another wishlist', 'yith-woocommerce-wishlist' ); ?></h3>
			<?php endif; ?>

			<p class="form-row">
				<?php
				// translators: 1. Wishlist formatted name.
				wp_kses_post( sprintf( __( 'This item is already in the <b>%s wishlist</b>.<br/>You can move it in another list:', 'yith-woocommerce-wishlist' ), $wishlist->get_formatted_name() ) );
				?>
			</p>

			<p class="form-row form-row-wide">
				<select name="new_wishlist_id" class="change-wishlist">
					<?php
					foreach ( $users_wishlists as $wl ) :
						if ( $wl->get_token() === $wishlist_token ) {
							continue;
						}
						?>
						<option value="<?php echo esc_attr( $wl->get_id() ); ?>">
							<?php echo esc_html( sprintf( '%s - %s', $wl->get_formatted_name(), $wl->get_formatted_privacy() ) ); ?>
						</option>
						<?php
					endforeach;
					?>
				</select>
			</p>
		</div>
		<div class="yith-wcwl-popup-footer">
			<button class="move-to-another-wishlist-button move-to-another-wishlist-button-popup wishlist-submit button alt">
				<?php
				/**
				 * APPLY_FILTERS: yith_wcwl_move_to_another_wishlist_text
				 *
				 * Filter the button text to move item to another wishlist.
				 *
				 * @param string $text Button text
				 *
				 * @return string
				 */
				echo esc_html( apply_filters( 'yith_wcwl_move_to_another_wishlist_text', __( 'Move', 'yith-woocommerce-wishlist' ) ) );
				?>
			</button>
		</div>
	</form>
</div>
