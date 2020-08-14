<?php
/**
 * Wishlist search template
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $page_title             string Page title
 * @var $pages_links            string Pagination links
 * @var $search_string          string Searched value
 * @var $search_results         array Search results
 * @var $template_part          string Template part currently being loaded (search)
 * @var $default_wishlist_title string Default wishlist title
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="yith-wcwl-wishlist-search-form">

	<input type="text" name="wishlist_search" id="wishlist_search" placeholder="<?php esc_html_e( 'Type a name or an email address', 'yith-woocommerce-wishlist' ); ?>" value="<?php echo esc_attr( $search_string ); ?>"/>
	<button class="wishlist-search-button">
		<?php echo apply_filters( 'yith_wcwl_search_button_icon', '<i class="fa fa-search"></i>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</button>

</div>

<?php do_action( 'yith_wcwl_before_wishlist_search_results' ); ?>

<?php if ( ! empty( $search_string ) ): ?>
	<table class="shop_table wishlist_table cart wishlist_search yith-wcwl-search-results">
		<thead>
		<tr>
			<th class="wishlist-owner">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_search_owner_heading', __( 'Wishlist of:', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
			<th class="wishlist-name">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_search_name_heading', __( 'Name', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
			<th class="item-count">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_search_count_heading', __( 'Count of items', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
		</tr>
		</thead>

		<tbody>
		<?php if ( ! empty( $search_results ) ): ?>
			<?php foreach ( $search_results as $user ): ?>

				<?php
				$user_obj   = get_user_by( 'id', $user );
				$avatar     = get_avatar( $user, 40 );
				$first_name = $user_obj->first_name;
				$last_name  = $user_obj->last_name;
				$login      = $user_obj->user_login;
				$user_email = $user_obj->user_email;
				$wishlists  = YITH_WCWL()->get_wishlists( array( 'user_id' => $user, 'session_id' => false, 'wishlist_visibility' => 'public' ) );
				?>

				<?php if ( ! empty( $wishlists ) ) : ?>
					<?php foreach ( $wishlists as $wishlist ) : ?>
						<tr class="yith-wcwl-search-result" data-wishlist-id="<?php echo esc_attr( $wishlist->get_id() ); ?>">
							<td class="wishlist-owner">
								<div class="thumb">
									<?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
								<?php echo esc_html( ( ! empty( $first_name ) || ! empty( $last_name ) ) ? $first_name . " " . $last_name : $login ); ?>
							</td>
							<td class="wishlist-name">
								<a href="<?php echo esc_url( $wishlist->get_url() ); ?>"><?php echo esc_html( $wishlist->get_formatted_name() ); ?></a>
							</td>
							<td class="item-count">
								<?php echo esc_html( sprintf( __( '%d items', 'yith-woocommerce-wishlist' ), $wishlist->count_items() ) ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>

			<?php endforeach; ?>
		<?php else: ?>
			<tr class="yith-wcwl-empty-search-result">
				<td colspan="3">
					<?php echo esc_html( sprintf( apply_filters( 'yith_wcwl_empty_search_result', __( '0 results for "%s" in the wishlist', 'yith-woocommerce-wishlist' ) ), $search_string ) ); ?>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>

		<?php if ( $pages_links ): ?>
			<tfoot>
			<tr class="yith-wcwl-search-pagination">
				<td colspan="3"><?php echo $pages_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
			</tfoot>
		<?php endif; ?>
	</table>
<?php endif; ?>

<?php do_action( 'yith_wcwl_after_wishlist_search_results' ); ?>
