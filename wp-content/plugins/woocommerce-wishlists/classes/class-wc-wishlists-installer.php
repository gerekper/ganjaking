<?php

class WC_Wishlists_Installer {

	public static function activate() {
		self::check_install();
	}

	public static function check_install() {
		if ( WC_Wishlists_Settings::get_setting( 'wc_wishlists_db_version' ) != WC_WISHLISTS_VERSION ) {
			add_action( 'init', array( __CLASS__, 'install' ) );
		}
	}

	public static function install() {
		$result = self::_do_install();
		if ( $result ) {
			WC_Wishlists_Settings::set_setting( 'wc_wishlists_db_version', WC_WISHLISTS_VERSION );
		}
	}

	private static function _do_install() {
		$root = self::create_page( 'my-lists', __( 'Wishlists', 'wc_wishlist' ), '[wc_wishlists_my_archive]', 0 );

		self::create_page( 'create-a-list', __( 'Create a List', 'wc_wishlist' ), '[wc_wishlists_create]', $root );
		self::create_page( 'view-a-list', __( 'View a List', 'wc_wishlist' ), '[wc_wishlists_single ]', $root );
		self::create_page( 'find-a-list', __( 'Find a List', 'wc_wishlist' ), '[wc_wishlists_search]', $root );
		self::create_page( 'edit-my-list', __( 'Manage List', 'wc_wishlist' ), '[wc_wishlists_edit]', $root );

		update_option( 'wc_wishlists_sharing_facebook', 'yes' );
		update_option( 'wc_wishlists_sharing_twitter', 'yes' );
		update_option( 'wc_wishlists_sharing_email', 'yes' );
		update_option( 'wc_wishlists_sharing_pinterest', 'yes' );


		return true;
	}

	/**
	 * Create a page
	 *
	 * @access public
	 *
	 * @param mixed $slug Slug for the new page
	 * @param mixed $option Option name to store the page's ID
	 * @param string $page_title (default: '') Title for the new page
	 * @param string $page_content (default: '') Content for the new page
	 * @param int $post_parent (default: 0) Parent for the new page
	 *
	 * @return string
	 */
	public static function create_page( $slug, $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;

		$option_value = WC_Wishlists_Pages::get_page_id( $slug );

		if ( $option_value > 0 && get_post( $option_value ) ) {
			return $option_value;
		}

		$page_found = $wpdb->get_var( "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$slug' LIMIT 1;" );
		if ( $page_found ) :
			WC_Wishlists_Pages::set_page_id( $slug, $page_found );

			return $page_found;
		endif;

		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $page_title,
			'post_content'   => $page_content,
			'post_parent'    => $post_parent,
			'comment_status' => 'closed'
		);
		$page_id   = wp_insert_post( $page_data );

		WC_Wishlists_Pages::set_page_id( $slug, $page_id );

		return $page_id;
	}

}