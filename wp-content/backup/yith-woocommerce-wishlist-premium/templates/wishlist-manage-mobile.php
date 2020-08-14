<?php
/**
 * Wishlist manage template - Modern layout
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $page_title            string Page title
 * @var $template_part         string Template part currently being loaded (manage)
 * @var $user_wishlists        YITH_WCWL_Wishlist[] Array of user wishlists
 * @var $show_number_of_items  bool Whether to show number of items or not
 * @var $show_date_of_creation bool Whether to show date of creation or not
 * @var $show_download_as_pdf  bool Whether to show download button or not
 * @var $show_rename_wishlist  bool Whether to show rename button or not
 * @var $show_delete_wishlist  bool Whether to show delete button or not
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<ul class="shop_table cart wishlist_table wishlist_manage_table responsive mobile" cellspacing="0">

	<?php
	if ( ! empty( $user_wishlists ) ):
		foreach ( $user_wishlists as $wishlist ):
			?>
			<li data-wishlist-id="<?php echo $wishlist->get_id() ?>">
				<div class="item-wrapper">
					<div class="item-details">
						<div class="wishlist-name wishlist-title <?php echo $show_rename_wishlist ? 'wishlist-title-with-form' : ''; ?>" >
							<h3>
								<a class="wishlist-anchor" href="<?php echo esc_url( $wishlist->get_url() ); ?>"><?php echo esc_html( $wishlist->get_formatted_name() ); ?></a>
							</h3>

							<?php if ( $show_rename_wishlist ): ?>
								<a class="show-title-form">
									<?php echo apply_filters( 'yith_wcwl_edit_title_icon', '<i class="fa fa-pencil"></i>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							<?php endif; ?>
						</div>

						<?php if ( $show_rename_wishlist ): ?>
							<div class="hidden-title-form">
								<input type="text" value="<?php echo esc_attr( $wishlist->get_formatted_name() ); ?>" name="wishlist_options[<?php echo esc_attr( $wishlist->get_id() ); ?>][wishlist_name]" />
								<div class="edit-title-buttons">
									<a href="#" class="hide-title-form">
										<?php echo apply_filters( 'yith_wcwl_cancel_wishlist_title_icon', '<i class="fa fa-remove"></i>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
									<a href="#" class="save-title-form">
										<?php echo apply_filters( 'yith_wcwl_cancel_wishlist_title_icon', '<i class="fa fa-check"></i>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
								</div>
							</div>
						<?php endif; ?>

						<table class="item-details-table">
							<?php if ( $show_number_of_items ): ?>
								<tr class="wishlist-item-count">
									<td class="label"><?php esc_html_e( 'Items:', 'yith-woocommerce-wishlist' ); ?></td>
									<td class="value"><?php echo esc_html( sprintf( __( '%d items', 'yith-woocommerce-wishlist' ), $wishlist->count_items() ) ); ?></td>
								</tr>
							<?php endif; ?>

							<tr class="wishlist-privacy">
								<td class="label"><?php esc_html_e( 'Visibility:', 'yith-woocommerce-wishlist' ); ?></td>
								<td class="value">
									<select name="wishlist_options[<?php echo esc_attr( $wishlist->get_id() ); ?>][wishlist_privacy]" class="wishlist-visibility selectBox">
										<option value="0" class="public-visibility" <?php selected( $wishlist->get_privacy(), 0 ); ?> ><?php echo yith_wcwl_get_privacy_label( 0 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></option>
										<option value="1" class="shared-visibility" <?php selected( $wishlist->get_privacy(), 1 ); ?> ><?php echo yith_wcwl_get_privacy_label( 1 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></option>
										<option value="2" class="private-visibility" <?php selected( $wishlist->get_privacy(), 2 ); ?> ><?php echo yith_wcwl_get_privacy_label( 2 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></option>
									</select>
								</td>
							</tr>

							<?php if ( $show_date_of_creation ): ?>
								<tr class="wishlist-dateadded">
									<td class="label"><?php esc_html_e( 'Created on:', 'yith-woocommerce-wishlist' ); ?></td>
									<td class="value"><?php echo esc_html( $wishlist->get_date_added_formatted() ); ?></td>
								</tr>
							<?php endif; ?>

							<?php if ( $show_delete_wishlist || $show_download_as_pdf ): ?>
								<tr>
									<td class="value" colspan="2">
										<?php if ( $show_download_as_pdf ): ?>
											<a class="wishlist-download" href="<?php echo esc_url( $wishlist->get_download_url() ); ?>">
												<i class="fa fa-download"></i>
											</a>
										<?php endif; ?>

										<?php if ( $show_delete_wishlist && ! $wishlist->is_default() ): ?>
											<a class="wishlist-delete" onclick="return confirm('<?php esc_html_e( 'Are you sure you want to delete this wishlist?', 'yith-woocommerce-wishlist' ); ?>');" href="<?php echo esc_url( $wishlist->get_delete_url() ); ?>"><i class="fa fa-trash"></i></a>
										<?php endif; ?>
									</td>
								</tr>
							<?php endif; ?>
						</table>
					</div>
				</div>
			</li>
		<?php
		endforeach;
	else:
		?>
		<li class="wishlist-empty"><?php echo esc_html( apply_filters( 'yith_wcwl_no_wishlist_message', __( 'Please, create your first wishlist', 'yith-woocommerce-wishlist' ) ) ); ?></li>
	<?php
	endif;
	?>

</ul>