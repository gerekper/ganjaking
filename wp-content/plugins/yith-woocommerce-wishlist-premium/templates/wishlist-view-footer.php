<?php
/**
 * Wishlist footer
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist\View
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist                      \YITH_WCWL_Wishlist
 * @var $wishlist_token                string Current wishlist token
 * @var $show_cb                       bool Whether to show checkbox column
 * @var $show_update                   bool Whether to show update button or not
 * @var $is_user_owner                 bool Whether current user is wishlist owner
 * @var $is_private                    bool Whether current wishlist is private
 * @var $share_enabled                 bool Whether share buttons should appear
 * @var $share_atts                    array Array of options; shows which share links should be shown
 * @var $show_ask_estimate_button      bool Whether to show Ask an Estimate form
 * @var $ask_estimate_url              string Ask an estimate destination url
 * @var $ask_an_estimate_icon          string Ask an estimate button icon
 * @var $ask_an_estimate_text          string Ask an estimate button text
 * @var $ask_an_estimate_classes       string Classes to use for Ask for an estimate button
 * @var $additional_info               bool Whether to show Additional info textarea in Ask an estimate form
 * @var $enable_add_all_to_cart        bool Whether to show "Add all to Cart" button
 * @var $move_to_another_wishlist      bool Whether to show Move to another wishlist select
 * @var $move_to_another_wishlist_type string Whether to show a select or a popup for wishlist change
 * @var $available_multi_wishlist      bool Whether multi wishlist is enabled and available
 * @var $users_wishlists               array Array of current user wishlists
 * @var $count                         int Count of items in wishlist
 * @var $var                           array Array of variable passed to the template
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>
	<div class="yith_wcwl_wishlist_footer">
		<?php if ( $count && $show_cb ) : ?>
			<!-- Bulk actions form -->
			<div class="yith_wcwl_wishlist_bulk_action">
				<label for="bulk_actions">
					<?php
					/**
					 * APPLY_FILTERS: yith_wcwl_wishlist_bulk_actions_label
					 *
					 * Filter the label of the bulk action selector in the Wishlist page.
					 *
					 * @param string $label Label
					 *
					 * @return string
					 */
					echo esc_html( apply_filters( 'yith_wcwl_wishlist_bulk_actions_label', __( 'Apply this action to all the selected items:', 'yith-woocommerce-wishlist' ) ) );
					?>
				</label>
				<select name="bulk_actions" id="bulk_actions">
					<option value="add_to_cart"><?php esc_html_e( 'Add to cart', 'yith-woocommerce-wishlist' ); ?></option>

					<?php if ( $wishlist->current_user_can( 'remove_from_wishlist' ) ) : ?>
						<option value="delete">
							<?php
							/**
							 * APPLY_FILTERS: yith_wcwl_wishlist_bulk_remove_label
							 *
							 * Filters the label for the buk action to remove the items from the wishlist.
							 *
							 * @param string $label Label.
							 *
							 * @return string
							 */
							echo esc_html( apply_filters( 'yith_wcwl_wishlist_bulk_remove_label', __( 'Remove from wishlist', 'yith-woocommerce-wishlist' ) ) );
							?>
						</option>
					<?php endif; ?>

					<?php if ( $available_multi_wishlist && count( $users_wishlists ) > 1 && $is_user_owner ) : ?>
						<?php
						foreach ( $users_wishlists as $wl ) :
							/**
							 * Each of the wishlists owned by current user.
							 *
							 * @var $wl \YITH_WCWL_Wishlist
							 */
							if ( $wl->get_token() === $wishlist_token ) {
								continue;
							}
							?>
							<option value="<?php echo esc_attr( $wl->get_token() ); ?>">
								<?php
								// translators: 1. Wishlist formatted name.
								echo esc_html( sprintf( __( 'Move to %s', 'yith-woocommerce-wishlist' ), $wl->get_formatted_name() ) );
								?>
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
				<input type="submit" name="apply_bulk_actions" value="<?php esc_html_e( 'Apply', 'yith-woocommerce-wishlist' ); ?>"/>
			</div>
		<?php endif; ?>


		<?php if ( $count && $show_update ) : ?>
			<!-- Update wishlist button -->
			<div class="yith_wcwl_wishlist_update">
				<input type="submit" name="update_wishlist" value="<?php esc_html_e( 'Update', 'yith-woocommerce-wishlist' ); ?>"/>
			</div>
		<?php endif; ?>


		<?php if ( $count && $share_enabled ) : ?>
			<!-- Sharing section -->
			<?php yith_wcwl_get_template( 'share.php', array_merge( $share_atts, array( 'wishlist' => $wishlist ) ) ); ?>
		<?php endif; ?>

		<?php if ( $count && ( $show_ask_estimate_button || $enable_add_all_to_cart ) ) : ?>
			<div class="yith_wcwl_footer_additional_action">
				<?php if ( $count && $show_ask_estimate_button ) : ?>
					<!-- Ask an estimate button -->
					<a href="<?php echo esc_url( ( $additional_info || ! is_user_logged_in() ) ? '#ask_an_estimate_popup' : $ask_estimate_url ); ?>" class="<?php echo esc_attr( $ask_an_estimate_classes ); ?> ask-an-estimate-button" <?php echo ( $additional_info || ! is_user_logged_in() ) ? 'data-rel="prettyPhoto[ask_an_estimate]"' : ''; ?> >
						<?php
						/**
						 * APPLY_FILTERS: yith_wcwl_ask_an_estimate_icon
						 *
						 * Filter the icon for the 'Ask for an estimate'.
						 *
						 * @param string $icon Icon
						 *
						 * @return string
						 */
						echo yith_wcwl_kses_icon( apply_filters( 'yith_wcwl_ask_an_estimate_icon', $ask_an_estimate_icon ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
						<?php
						/**
						 * APPLY_FILTERS: yith_wcwl_ask_an_estimate_text
						 *
						 * Filter the text for the 'Ask for an estimate'.
						 *
						 * @param string $text Text
						 *
						 * @return string
						 */
						echo esc_html( apply_filters( 'yith_wcwl_ask_an_estimate_text', $ask_an_estimate_text ) );
						?>
					</a>
				<?php endif; ?>

				<?php if ( $count && $enable_add_all_to_cart ) : ?>
					<!-- Add all to cart button -->
					<input type="submit" name="add_all_to_cart" value="<?php esc_html_e( 'Add all to cart', 'yith-woocommerce-wishlist' ); ?>"/>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php wp_nonce_field( 'yith_wcwl_edit_wishlist_action', 'yith_wcwl_edit_wishlist' ); ?>
	<input type="hidden" value="<?php echo esc_attr( $wishlist_token ); ?>" name="wishlist_id" id="wishlist_id">

	<?php
	/**
	 * DO_ACTION: yith_wcwl_after_wishlist
	 *
	 * Allows to render some content or fire some action after the wishlist.
	 *
	 * @param YITH_WCWL_Wishlist $wishlist Wishlist object
	 */
	do_action( 'yith_wcwl_after_wishlist', $wishlist );
	?>

</form>

<?php
/**
 * DO_ACTION: yith_wcwl_after_wishlist_form
 *
 * Allows to render some content or fire some action after the wishlist form.
 *
 * @param YITH_WCWL_Wishlist $wishlist Wishlist object
 */
do_action( 'yith_wcwl_after_wishlist_form', $wishlist );
?>

<?php
/**
 * APPLY_FILTERS: yith_wcwl_ask_an_estimate_conditions
 *
 * Filter the condition to load the 'Ask for an estimate' template.
 *
 * @param bool $condition Condition to load the template
 *
 * @return bool
 */
if ( apply_filters( 'yith_wcwl_ask_an_estimate_conditions', $wishlist && $show_ask_estimate_button && ( ! is_user_logged_in() || $additional_info ) ) ) {
	yith_wcwl_get_template( 'wishlist-popup-ask-an-estimate.php', $var );
}
?>

<?php
/**
 * APPLY_FILTERS: yith_wcwl_move_to_another_wishlist_popup_conditions
 *
 * Filter the condition to load the template to move products to another wishlist.
 *
 * @param bool $condition Condition to load the template
 *
 * @return bool
 */
if ( apply_filters( 'yith_wcwl_move_to_another_wishlist_popup_conditions', $wishlist && $move_to_another_wishlist && 'popup' === $move_to_another_wishlist_type && $available_multi_wishlist && count( $users_wishlists ) > 1, $wishlist ) ) {
	yith_wcwl_get_template( 'wishlist-popup-move.php', $var );
}
?>
