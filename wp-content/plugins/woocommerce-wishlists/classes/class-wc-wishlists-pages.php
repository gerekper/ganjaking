<?php

class WC_Wishlists_Pages {

	public static function is_wishlist_page( $slug = false ) {
		if ( $slug == false ) {
			if ( self::is_wishlist_page( 'view-a-list' ) || self::is_wishlist_page( 'edit-my-list' ) || self::is_wishlist_page( 'wishlists' ) || self::is_wishlist_page( 'create-a-list' ) || self::is_wishlist_page( 'find-a-list' ) ) {
				return true;
			}
		}

		return WC_Wishlists_Settings::get_setting( 'wc_wishlists_page_id_' . $slug, false );
	}

	public static function get_url_for( $slug ) {

		if ( $slug == 'wishlists' ) {
			$slug = 'my-lists';
		}

		$page_id = self::get_page_id( $slug );

		return get_permalink( $page_id );
	}

	public static function get_page_id( $slug ) {
		return apply_filters( 'wc_wishlists_get_page_id', WC_Wishlists_Settings::get_setting( 'wc_wishlists_page_id_' . $slug, false ), $slug );
	}

	public static function set_page_id( $slug, $id ) {
		return WC_Wishlists_Settings::set_setting( 'wc_wishlists_page_id_' . $slug, (int) $id );
	}

}