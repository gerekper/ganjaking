<?php
/**
 * Add to wishlist popup template
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\AddToWishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $base_url                  string Current page url
 * @var $lists                     YITH_WCWL_Wishlist[]
 * @var $show_exists               bool Whether to show Exists message or not
 * @var $product_id                int Current product id
 * @var $parent_product_id         int Parent for current product
 * @var $show_count                bool Whether to show count of times item was added to wishlist
 * @var $exists                    bool Whether the product is already in list
 * @var $already_in_wishslist_text string Already in wishlist message
 * @var $browse_wishlist_text      string Browse wishlist message
 * @var $wishlist_url              string View wishlist url
 * @var $link_classes              string Classes for the Add to Wishlist link
 * @var $link_popup_classes        string Classes for Open Add to Wishlist Popup link
 * @var $label_popup               string Label for Open Add to Wishlist Popup link
 * @var $popup_title               string Popup title
 * @var $product_image             string Product image url (not is use)
 * @var $icon                      string Icon HTML tag
 * @var $heading_icon              string Heading icon HTML tag
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

$unique_id = wp_rand();
?>

<div class="yith-wcwl-add-button">
	<!-- WISHLIST POPUP OPENER -->
	<?php
	/**
	 * APPLY_FILTERS: yith_wcwl_add_to_wishlist_title
	 *
	 * Filter the 'Add to wishlist' label.
	 *
	 * @param string $label Label
	 *
	 * @return string
	 */
	?>
	<a href="#add_to_wishlist_popup_<?php echo esc_attr( $product_id ); ?>_<?php echo esc_attr( $unique_id ); ?>" rel="nofollow" class="<?php echo esc_attr( $link_classes ); ?> open-pretty-photo" data-rel="prettyPhoto[add_to_wishlist_<?php echo esc_attr( $product_id ); ?>_<?php echo esc_attr( $unique_id ); ?>]" data-title="<?php echo esc_attr( apply_filters( 'yith_wcwl_add_to_wishlist_title', $label ) ); ?>">
		<?php
			echo yith_wcwl_kses_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		/**
		 * APPLY_FILTERS: yith_wcwl_show_text_on_the_image
		 *
		 * Filter whether to show the 'Add to wishlist' text on the product image.
		 *
		 * @param bool $condition Whether show or not the text in the image
		 *
		 * @return bool
		 */
		if ( apply_filters( 'yith_wcwl_show_text_on_the_image', true ) ) {
			echo wp_kses_post( $label );
		}
		?>
	</a>

	<!-- WISHLIST POPUP -->
	<div id="add_to_wishlist_popup_<?php echo esc_attr( $product_id ); ?>_<?php echo esc_attr( $unique_id ); ?>" class="yith-wcwl-popup">
		<form class="yith-wcwl-popup-form" method="post" action="<?php echo esc_url( wp_nonce_url( add_query_arg( 'add_to_wishlist', $product_id, $base_url ), 'add_to_wishlist' ) ); ?>">
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
				<?php else : ?>
					<h3><?php echo esc_html( $popup_title ); ?></h3>
				<?php endif; ?>


				<p class="popup-description">
					<?php
					/**
					 * APPLY_FILTERS: yith_wcwl_choose_wishlist_text
					 *
					 * Filter the label text to choose a wishlist from the list.
					 *
					 * @param string $label Label
					 *
					 * @return string
					 */
					?>
					<a href="#" class="show-tab active" data-tab="choose"><?php echo esc_attr( apply_filters( 'yith_wcwl_choose_wishlist_text', __( 'Choose a wishlist', 'yith-woocommerce-wishlist' ) ) ); ?></a>
					<?php esc_html_e( 'or', 'yith-woocommerce-wishlist' ); ?>
					<a href="#" class="show-tab" data-tab="create"><?php echo esc_html( apply_filters( 'yith_wcwl_create_new_list_text', __( 'Create a new list', 'yith-woocommerce-wishlist' ) ) ); ?></a>
				</p>

				<div class="tab choose">
					<div class="yith-wcwl-wishlist-select-container">
						<p class="form-row form-row-wide">
							<select name="wishlist_id" class="wishlist-select">
								<?php if ( empty( $lists ) ) : ?>
									<?php
									/**
									 * APPLY_FILTERS: yith_wcwl_default_wishlist_name
									 *
									 * Filter the default Wishlist name.
									 *
									 * @param string $wishlist_name Default wishlist name
									 *
									 * @return string
									 */
									?>
									<option value="0" <?php selected( true ); ?> ><?php echo esc_html( apply_filters( 'yith_wcwl_default_wishlist_name', get_option( 'yith_wcwl_wishlist_title' ) ) ); ?></option>
								<?php else : ?>
									<?php foreach ( $lists as $list ) : ?>
										<?php if ( $list ) : ?>
											<option value="<?php echo esc_attr( $list->get_id() ); ?>"><?php echo esc_html( $list->get_formatted_name() ); ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
								<?php
								/**
								 * APPLY_FILTERS: yith_wcwl_create_new_list_text
								 *
								 * Filter the label text to create a new wishlist.
								 *
								 * @param string $label Label
								 *
								 * @return string
								 */
								?>
								<option value="new"><?php echo esc_html( apply_filters( 'yith_wcwl_create_new_list_text', __( 'Create a new list', 'yith-woocommerce-wishlist' ) ) ); ?></option>
							</select>
						</p>
					</div>
				</div>

				<div class="tab create">
					<p class="form-row form-row-wide">
						<?php
						/**
						 * APPLY_FILTERS: yith_wcwl_new_list_title_text
						 *
						 * Filter the placeholder of the field to set the name for the new wishlist.
						 *
						 * @param string $placeholder Placeholder text
						 *
						 * @return string
						 */
						?>
						<input name="wishlist_name" class="wishlist-name input-text" type="text" placeholder="<?php echo esc_html( apply_filters( 'yith_wcwl_new_list_title_text', __( 'Enter wishlist name', 'yith-woocommerce-wishlist' ) ) ); ?>"/>
					</p>

					<p class="form-row form-row-wide">
						<label>
							<input type="radio" name="wishlist_visibility" value="0" class="public-visibility wishlist-visibility" <?php checked( true ); ?> />
							<?php echo wp_kses_post( yith_wcwl_get_privacy_label( 0, true ) ); ?>
						</label>
						<label>
							<input type="radio" name="wishlist_visibility" value="1" class="shared-visibility wishlist-visibility"/>
							<?php echo wp_kses_post( yith_wcwl_get_privacy_label( 1, true ) ); ?>
						</label>
						<label>
							<input type="radio" name="wishlist_visibility" value="2" class="private-visibility wishlist-visibility"/>
							<?php echo wp_kses_post( yith_wcwl_get_privacy_label( 2, true ) ); ?>
						</label>
					</p>
				</div>
			</div>

			<div class="yith-wcwl-popup-footer">
				<a rel="nofollow" class="wishlist-submit <?php echo esc_attr( $link_popup_classes ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-product-type="<?php echo esc_attr( $product_type ); ?>" data-original-product-id="<?php echo esc_attr( $parent_product_id ); ?>">
					<?php echo esc_html( $label_popup ); ?>
				</a>
			</div>
		</form>
	</div>
</div>
