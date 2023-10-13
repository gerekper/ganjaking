<?php
/**
 * Wishlist manage template
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist\Manage
 * @version 3.0.12
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

<?php if ( ! empty( $user_wishlists ) ) : ?>
	<table class="shop_table cart wishlist_table wishlist_manage_table responsive" cellspacing="0">

		<?php $column_count = 2; ?>

		<thead>
		<tr>
			<th class="wishlist-name">
				<span class="nobr">
					<?php
					/**
					 * APPLY_FILTERS: yith_wcwl_wishlist_manage_name_heading
					 *
					 * Filter the heading of the column to show the Wishlist name in the Wishlists table.
					 *
					 * @param string $heading Heading text
					 *
					 * @return string
					 */
					echo esc_html( apply_filters( 'yith_wcwl_wishlist_manage_name_heading', __( 'Wishlists', 'yith-woocommerce-wishlist' ) ) );
					?>
				</span>
			</th>

			<th class="wishlist-privacy">
				<span class="nobr">
					<?php
					/**
					 * APPLY_FILTERS: yith_wcwl_wishlist_manage_privacy_heading
					 *
					 * Filter the heading of the column to show the Wishlist privacy in the Wishlists table.
					 *
					 * @param string $heading Heading text
					 *
					 * @return string
					 */
					echo esc_html( apply_filters( 'yith_wcwl_wishlist_manage_privacy_heading', __( 'Privacy', 'yith-woocommerce-wishlist' ) ) );
					?>
				</span>
			</th>

			<?php
			if ( $show_number_of_items ) :
				++$column_count;
				?>
				<th class="wishlist-item-count">
					<span class="nobr">
						<?php
						/**
						 * APPLY_FILTERS: yith_wcwl_wishlist_manage_number_of_items_heading
						 *
						 * Filter the heading of the column to show the number of items in the Wishlists table.
						 *
						 * @param string $heading Heading text
						 *
						 * @return string
						 */
						echo esc_html( apply_filters( 'yith_wcwl_wishlist_manage_number_of_items_heading', __( 'Count of items', 'yith-woocommerce-wishlist' ) ) );
						?>
					</span>
				</th>
			<?php endif; ?>

			<?php
			if ( $show_date_of_creation ) :
				++$column_count;
				?>
				<th class="wishlist-dateadded">
					<span class="nobr">
						<?php
						/**
						 * APPLY_FILTERS: yith_wcwl_wishlist_manage_dateadded_heading
						 *
						 * Filter the heading of the column to show the date when the wishlist was created in the Wishlists table.
						 *
						 * @param string $heading Heading text
						 *
						 * @return string
						 */
						echo esc_html( apply_filters( 'yith_wcwl_wishlist_manage_dateadded_heading', __( 'Created on', 'yith-woocommerce-wishlist' ) ) );
						?>
					</span>
				</th>
			<?php endif; ?>

			<?php
			if ( $show_download_as_pdf ) :
				++$column_count;
				?>
				<th class="wishlist-download">
					<span class="nobr">
						<?php
						/**
						 * APPLY_FILTERS: yith_wcwl_wishlist_manage_download_heading
						 *
						 * Filter the heading of the column to show 'Download PDF' icon in the Wishlists table.
						 *
						 * @param string $heading Heading text
						 *
						 * @return string
						 */
						echo esc_html( apply_filters( 'yith_wcwl_wishlist_manage_download_heading', __( 'PDF Download', 'yith-woocommerce-wishlist' ) ) );
						?>
					</span>
				</th>
			<?php endif; ?>

			<?php
			if ( $show_delete_wishlist ) :
				++$column_count;
				?>
				<th class="wishlist-delete">
					<span class="nobr">
						<?php
						/**
						 * APPLY_FILTERS: yith_wcwl_wishlist_manage_delete_heading
						 *
						 * Filter the heading of the column to show 'Delete' icon in the Wishlists table.
						 *
						 * @param string $heading Heading text
						 *
						 * @return string
						 */
						echo esc_html( apply_filters( 'yith_wcwl_wishlist_manage_delete_heading', __( 'Delete', 'yith-woocommerce-wishlist' ) ) );
						?>
					</span>
				</th>
			<?php endif; ?>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $user_wishlists as $wishlist ) :
			?>

			<tr data-wishlist-id="<?php echo esc_attr( $wishlist->get_id() ); ?>">
				<td class="wishlist-name">
					<div class="wishlist-title">
						<a title="<?php echo esc_attr( $wishlist->get_formatted_name() ); ?>" class="wishlist-anchor" href="<?php echo esc_url( $wishlist->get_url() ); ?>">
							<?php echo esc_html( $wishlist->get_formatted_name() ); ?>
						</a>

						<?php if ( $show_rename_wishlist ) : ?>
							<a href="#" class="show-title-form">
								<i class="fa fa-pencil"></i>
							</a>
						<?php endif; ?>
					</div>

					<div class="hidden-title-form">
						<input type="text" value="<?php echo esc_attr( $wishlist->get_formatted_name() ); ?>" name="wishlist_options[<?php echo esc_attr( $wishlist->get_id() ); ?>][wishlist_name]"/>
						<div class="edit-title-buttons">
							<a href="#" class="hide-title-form">
								<?php
								/**
								 * APPLY_FILTERS: yith_wcwl_cancel_wishlist_title_icon
								 *
								 * Filter the icon of the Cancel button when editing the Wishlist title.
								 *
								 * @param string $icon Cancel icon
								 *
								 * @return string
								 */
								echo yith_wcwl_kses_icon( apply_filters( 'yith_wcwl_cancel_wishlist_title_icon', '<i class="fa fa-remove"></i>' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</a>
							<a href="#" class="save-title-form">
								<?php
								/**
								 * APPLY_FILTERS: yith_wcwl_save_wishlist_title_icon
								 *
								 * Filter the icon of the Save button when editing the Wishlist title.
								 *
								 * @param string $icon Save icon
								 *
								 * @return string
								 */
								echo yith_wcwl_kses_icon( apply_filters( 'yith_wcwl_save_wishlist_title_icon', '<i class="fa fa-check"></i>' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</a>
						</div>
					</div>
				</td>

				<td class="wishlist-privacy">
					<select name="wishlist_options[<?php echo esc_attr( $wishlist->get_id() ); ?>][wishlist_privacy]" class="wishlist-visibility selectBox">
						<option value="0" class="public-visibility" <?php selected( $wishlist->get_privacy(), 0 ); ?> ><?php echo wp_kses_post( yith_wcwl_get_privacy_label( 0 ) ); ?></option>
						<option value="1" class="shared-visibility" <?php selected( $wishlist->get_privacy(), 1 ); ?> ><?php echo wp_kses_post( yith_wcwl_get_privacy_label( 1 ) ); ?></option>
						<option value="2" class="private-visibility" <?php selected( $wishlist->get_privacy(), 2 ); ?> ><?php echo wp_kses_post( yith_wcwl_get_privacy_label( 2 ) ); ?></option>
					</select>
				</td>

				<?php if ( $show_number_of_items ) : ?>
					<td class="wishlist-item-count">
						<?php
						// translators: 1. number of items in wishlist.
						echo esc_html( sprintf( __( '%d items', 'yith-woocommerce-wishlist' ), $wishlist->count_items() ) );
						?>
					</td>
				<?php endif; ?>

				<?php if ( $show_date_of_creation ) : ?>
					<td class="wishlist-dateadded">
						<?php echo esc_html( $wishlist->get_date_added_formatted() ); ?>
					</td>
				<?php endif; ?>

				<?php if ( $show_download_as_pdf ) : ?>
					<td class="wishlist-download">
						<a class="wishlist-download" href="<?php echo esc_url( $wishlist->get_download_url() ); ?>">
							<i class="fa fa-download"></i>
						</a>
					</td>
				<?php endif; ?>

				<?php if ( $show_delete_wishlist ) : ?>
					<td class="wishlist-delete">
						<a class="button wishlist-delete" onclick="return confirm('<?php esc_html_e( 'Are you sure you want to delete this wishlist?', 'yith-woocommerce-wishlist' ); ?>');" href="<?php echo esc_url( $wishlist->get_delete_url() ); ?>"><?php esc_html_e( 'Delete', 'yith-woocommerce-wishlist' ); ?></a>
					</td>
				<?php endif; ?>
			</tr>

			<?php
		endforeach;
		?>
		</tbody>
	</table>
<?php else : ?>
	<p class="wishlist-empty">
		<?php echo wp_kses_post( YITH_WCWL_Frontend_Premium()->get_no_wishlist_message() ); ?>
	</p>
<?php endif; ?>
