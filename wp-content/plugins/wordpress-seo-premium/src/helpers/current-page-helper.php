<?php

namespace Yoast\WP\SEO\Premium\Helpers;

/**
 * Class Current_Page_Helper.
 */
class Current_Page_Helper {

	/**
	 * Determine whether the current page is the homepage and shows posts.
	 *
	 * @return bool
	 */
	public function is_home_posts_page() {
		return ( \is_home() && \get_option( 'show_on_front' ) !== 'page' );
	}

	/**
	 * Determine whether the current page is a static homepage.
	 *
	 * @return bool
	 */
	public function is_home_static_page() {
		return ( \is_front_page() && \get_option( 'show_on_front' ) === 'page' && \is_page( \get_option( 'page_on_front' ) ) );
	}

	/**
	 * Determine whether this is the posts page, regardless of whether it's the frontpage or not.
	 *
	 * @return bool
	 */
	public function is_posts_page() {
		return ( \is_home() && ! \is_front_page() );
	}
}
