<?php

class WC_Wishlist_Saved_For_Later_Display {
	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Wishlist_Saved_For_Later_Display();
		}
	}

	function __construct() {
		add_action( 'woocommerce_after_cart', [ $this, 'on_woocommerce_after_cart' ] );
	}

	function on_woocommerce_after_cart() {
		$lists = WC_Wishlists_User::get_wishlists();
		$saved_for_later = [];
		foreach($lists as $list) {
			$items = WC_Wishlists_Wishlist_Item_Collection::get_items($list->id);
			foreach($items as $key => $item) {
				if (isset($item['saved_for_later'])) {
					$item['wishlist'] = $list;
					$saved_for_later[$key] = $item;
				}
			}
		}

		if ($saved_for_later) {
			wc_get_template('saved-for-later.php', ['wishlist_items' => $saved_for_later], '/templates/', WC_Wishlists_Plugin::plugin_path() . '/templates/');
		}

	}

}