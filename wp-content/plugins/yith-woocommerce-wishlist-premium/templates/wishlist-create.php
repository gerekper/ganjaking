<?php
/**
 * Wishlist create template
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="yith-wcwl-wishlist-new">

	<p class="form-row form-row-wide">
		<input name="wishlist_name" type="text" class="wishlist-name input-text" placeholder="<?php echo esc_html( apply_filters( 'yith_wcwl_new_list_title_text', __( 'Name your list', 'yith-woocommerce-wishlist' ) ) ); ?>" required="required"/>
	</p>

	<p class="form-row form-row-wide wishlist-privacy-radio">
		<label>
			<input type="radio" checked="checked" name="wishlist_visibility" class="wishlist-visiblity" value="0"/>
			<?php echo yith_wcwl_get_privacy_label( 0, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</label>
		<label>
			<input type="radio" name="wishlist_visibility" class="wishlist-visiblity" value="1"/>
			<?php echo yith_wcwl_get_privacy_label( 1, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</label>
		<label>
			<input type="radio" name="wishlist_visibility" class="wishlist-visiblity" value="2"/>
			<?php echo yith_wcwl_get_privacy_label( 2, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</label>
	</p>
    <input class="create-wishlist-button" type="submit" name="create_wishlist" value="<?php echo esc_attr( apply_filters( 'yith_wcwl_create_wishlist_button_label', __( 'Create wishlist', 'yith-woocommerce-wishlist' ) ) ); ?>"/>

	<?php wp_nonce_field( 'yith_wcwl_create_action', 'yith_wcwl_create' ); ?>

</div>
