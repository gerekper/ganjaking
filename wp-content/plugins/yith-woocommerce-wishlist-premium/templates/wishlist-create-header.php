<?php
/**
 * Wishlist create header template
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist\Create
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Template variables:
 *
 * @var $page_title string Page title
 */
?>

<form id="yith-wcwl-form" action="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'create' ) ); ?>" method="post">
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
		echo wp_kses_post( apply_filters( 'yith_wcwl_wishlist_title', '<h2>' . esc_html( $page_title ) . '</h2>' ) );
	}

	/**
	 * DO_ACTION: yith_wcwl_before_wishlist_create
	 *
	 * Allows to render some content or fire some action before the wishlist creation options.
	 */
	do_action( 'yith_wcwl_before_wishlist_create' );
	?>
