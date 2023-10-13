<?php
/**
 * Wishlist manage header template
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist\Manage
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $page_title string Page title
 * @var $template_part string Template part currently being loaded (manage)
 * @var $user_wishlists YITH_WCWL_Wishlist[] Array of user wishlists
 * @var $show_number_of_items bool Whether to show number of items or not
 * @var $show_date_of_creation bool Whether to show date of creation or not
 * @var $show_download_as_pdf bool Whether to show download button or not
 * @var $show_rename_wishlist bool Whether to show rename button or not
 * @var $show_delete_wishlist bool Whether to show delete button or not
 * @var $fragment_options array Array of items to use for fragment generation
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<form
	id="yith-wcwl-form" class="woocommerce yith-wcwl-form"
	action="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'manage' ) ); ?>"
	method="post"
	data-fragment-options="<?php echo wc_esc_json( wp_json_encode( $fragment_options ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
>
	<!-- TITLE -->
	<?php
	/**
	 * DO_ACTION: yith_wcwl_before_wishlist_title
	 *
	 * Allows to render some content or fire some action before the wishlist title.
	 */
	do_action( 'yith_wcwl_before_wishlist_title' );

	if ( ! empty( $page_title ) ) {
		/**
		 * APPLY_FILTERS: yith_wcwl_wishlist_title
		 *
		 * Filter the title of the Wishlist page.
		 *
		 * @param string $title Wishlist page title
		 *
		 * @return string
		 */
		echo wp_kses_post( apply_filters( 'yith_wcwl_wishlist_title', '<h2>' . $page_title . '</h2>' ) );
	}

	/**
	 * DO_ACTION: yith_wcwl_before_wishlist_manage
	 *
	 * Allows to render some content or fire some action before the wishlist manage actions.
	 */
	do_action( 'yith_wcwl_before_wishlist_manage' );
	?>
