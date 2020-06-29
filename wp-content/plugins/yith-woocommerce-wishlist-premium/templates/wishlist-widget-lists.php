<?php
/**
 * Wishlist list widget
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $before_widget          string HTML to print before widget
 * @var $after_widget           string HTML to print after widget
 * @var $instance               array Widget class instance
 * @var $fragment_options       array Array of options to be used for fragment generation
 * @var $wishlist_url           string url to wishlist page
 * @var $users_wishlists        array Array of user wishlists
 * @var $multi_wishlist_enabled bool Whether MultiWishlist is enabled
 * @var $default_wishlist_title string Default wishlist title
 * @var $create_page_title      string Create page title
 * @var $manage_page_title      string Manage page title
 * @var $search_page_title      string Search page title
 * @var $current_wishlist       \YITH_WCWL_Wishlist|bool Current wishlist object, or false if no wishlist
 * @var $active                 string Current wishlist endpoint being visited, or empty string if no endpoint
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php echo apply_filters( 'yith_wcwl_before_wishlist_widget', $before_widget ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<?php if ( ! empty( $instance['title'] ) ): ?>
	<h3 class="widget-title"><?php echo esc_html( $instance['title'] ); ?></h3>
<?php endif; ?>

	<div class="content yith-wcwl-lists-<?php echo esc_attr( $instance['unique_id'] ); ?> woocommerce wishlist-fragment on-first-load" data-fragment-options="<?php echo esc_attr( json_encode( $fragment_options ) ); ?>">

		<?php if ( ! $instance['ajax_loading'] ): ?>
			<?php if ( ! empty( $instance['wishlist_link'] ) ): ?>
				<a href="<?php echo esc_url( $wishlist_url ); ?>" class="<?php echo esc_html( apply_filters( 'yith_wcwl_widget_dropdown_toggle_classes', 'wishlist-dropdown-toggle' ) ); ?>" title="<?php echo esc_attr( $instance['wishlist_link'] ); ?>"><?php echo esc_html( $instance['wishlist_link'] ); ?></a>
			<?php endif; ?>

			<ul class="<?php echo esc_attr( apply_filters( 'yith_wcwl_widget_main_ul_classes', 'dropdown' ) ); ?>">
				<li class="<?php echo esc_attr( apply_filters( 'yith_wcwl_widget_li_classes', 'dropdown-section lists-section' ) ); ?>">
					<ul class="<?php echo esc_attr( apply_filters( 'yith_wcwl_widget_list_ul_classes', 'lists' ) ); ?>">

						<?php if ( ! empty( $users_wishlists ) ): ?>
							<?php foreach ( $users_wishlists as $wishlist ): ?>
								<li class="<?php echo esc_attr( apply_filters( 'yith_wcwl_widget_wishlist_classes', 'list' ) ); ?> <?php echo ( $current_wishlist && $current_wishlist->get_token() === $wishlist->get_token() ) ? 'current' : '' ?>">
									<a title="<?php echo esc_attr( $wishlist->get_formatted_name() ); ?>" class="wishlist-anchor" href="<?php echo esc_url( $wishlist->get_url() ); ?>">
										<?php echo esc_html( $wishlist->get_formatted_name() ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						<?php else: ?>
							<li class="<?php echo esc_attr( apply_filters( 'yith_wcwl_widget_wishlist_classes', 'list' ) ); ?> <?php echo yith_wcwl_is_wishlist_page() ? 'current' : '' ?>">
								<a title="<?php echo esc_attr( $default_wishlist_title ); ?>" class="wishlist-anchor" href="<?php echo esc_url( $wishlist_url ); ?>">
									<?php echo esc_html( $default_wishlist_title ); ?>
								</a>
							</li>
						<?php endif; ?>

					</ul>
				</li>

				<?php if ( isset( $instance['show_create_link'] ) && $instance['show_create_link'] === 'yes' && $multi_wishlist_enabled ) : ?>
					<li class="<?php echo esc_html( apply_filters( 'yith_wcwl_widget_li_classes', 'dropdown-section' ) ); ?> <?php echo ( $active == 'create' ) ? 'current' : '' ?>">
						<a href="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'create' ) ); ?>" title="<?php echo esc_attr( $create_page_title ); ?>"><?php echo esc_html( $create_page_title ); ?></a>
					</li>
				<?php endif; ?>

				<?php if ( isset( $instance['show_search_link'] ) && $instance['show_search_link'] === 'yes' ) : ?>
					<li class="<?php echo esc_attr( apply_filters( 'yith_wcwl_widget_li_classes', 'dropdown-section' ) ); ?> <?php echo ( $active == 'search' ) ? 'current' : '' ?>">
						<a href="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'search' ) ); ?>" title="<?php echo esc_attr( $search_page_title ); ?>"><?php echo esc_html( $search_page_title ); ?></a>
					</li>
				<?php endif; ?>

				<?php if ( isset( $instance['show_manage_link'] ) && $instance['show_manage_link'] === 'yes' && $multi_wishlist_enabled ): ?>
					<li class="<?php echo esc_attr( apply_filters( 'yith_wcwl_widget_li_classes', 'dropdown-section' ) ); ?> <?php echo ( $active == 'manage' ) ? 'current' : '' ?>">
						<a href="<?php echo esc_url ( YITH_WCWL()->get_wishlist_url( 'manage' ) ); ?>" title="<?php echo esc_attr( $manage_page_title ); ?>"><?php echo esc_html( $manage_page_title ); ?></a>
					</li>
				<?php endif; ?>

			</ul>
		<?php endif; ?>

	</div>

<?php echo apply_filters( 'yith_wcwl_after_wishlist_widget', $after_widget ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>