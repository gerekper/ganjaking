<?php
/**
 * Wishlist Factory class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Wishlist_Factory' ) ) {
	/**
	 * This class is used to create all Wishlist object required by the plugin
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Wishlist_Factory {
		/**
		 * Retrieve a specific wishlist from ID or token
		 *
		 * @param $wishlist_id string|int|bool Wishlist id or token or false, when you want to retrieve default
		 * @param $context string Context; when on edit context, and no wishlist matches selection, default wishlist will be created and returned
		 * @return \YITH_WCWL_Wishlist|bool Wishlist object or false on failure
		 */
		public static function get_wishlist( $wishlist_id = false, $context = 'view' ) {
			if ( ! $wishlist_id ) {
				return self::get_default_wishlist( false, $context );
			}

			try {
				return new YITH_WCWL_Wishlist( $wishlist_id );
			} catch ( Exception $e ) {
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return false;
			}
		}

		/**
		 * Query database to search for wishlists that matches specific parameters
		 *
		 * @param $args mixed Array of valid arguments<br/>
		 *              [<br/>
		 *              'id'                  // Wishlist id to search, if any<br/>
		 *              'user_id'             // User owner<br/>
		 *              'wishlist_slug'       // Slug of the wishlist to search<br/>
		 *              'wishlist_name'       // Name of the wishlist to search<br/>
		 *              'wishlist_token'      // Token of the wishlist to search<br/>
		 *              'wishlist_visibility' // Wishlist visibility: all, visible, public, shared, private<br/>
		 *              'user_search'         // String to match against first name / last name or email of the wishlist owner<br/>
		 *              'is_default'          // Whether wishlist should be default or not<br/>
		 *              'orderby'             // Column used to sort final result (could be any wishlist lists column)<br/>
		 *              'order'               // Sorting order<br/>
		 *              'limit'               // Pagination param: maximum number of elements in the set. 0 to retrieve all elements<br/>
		 *              'offset'              // Pagination param: offset for the current set. 0 to start from the first item<br/>
		 *              'show_empty'          // Whether to show empty lists os not<br/>
		 *              ]
		 *
		 * @return \YITH_WCWL_Wishlist[]|bool A list of matching wishlists or false on failure
		 */
		public static function get_wishlists( $args = array() ) {
			$args = apply_filters( 'yith_wcwl_wishlist_query_args', $args );

			try {
				$results = WC_Data_Store::load( 'wishlist' )->query( $args );
				return apply_filters( 'yith_wcwl_wishlist_query', $results, $args );
			} catch( Exception $e ){
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return false;
			}
		}

		/**
		 * Query database to count wishlists that matches specific parameters
		 *
		 * @param $args array Same parameters allowed for {@see get_wishlists}
		 * @return int Count
		 */
		public static function get_wishlists_count( $args = array() ) {
			$args = apply_filters( 'yith_wcwl_wishlists_count_query_args', $args );

			try {
				$result = WC_Data_Store::load( 'wishlist' )->count( $args );
				return apply_filters( 'yith_wcwl_wishlist_count_query', $result, $args );
			} catch( Exception $e ){
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return 0;
			}
		}

		/**
		 * Search user ids whose wishlists match passed parameters
		 * NOTE: this will only retrieve wishlists for a logged in user, while guests wishlist will be ignored
		 *
		 * @param $args mixed Array of valid arguments<br/>
		 * [<br/>
		 *     'search' // String to match against first name / last name / user login or user email of wishlist owner<br/>
		 *     'limit'  // Pagination param: number of items to show in one page. 0 to show all items<br/>
		 *     'offset' // Pagination param: offset for the current set. 0 to start from the first item<br/>
		 * ]
		 * @return int[]|bool Array of user ids, or false on failure
		 */
		public static function get_wishlist_users( $args = array() ) {
			$args = apply_filters( 'yith_wcwl_wishlist_users_query_args', $args );

			try {
				$results = WC_Data_Store::load( 'wishlist' )->search_users( $args );
				return apply_filters( 'yith_wcwl_wishlist_user_query', $results, $args );
			} catch( Exception $e ){
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return false;
			}
		}

		/**
		 * Retrieve current wishlist, basing on query string parameters, user or session
		 *
		 * @param $args array Array of arguments<br/>
		 *              [<br/>
		 *              'action_params' // query string parameters
		 *              'user_id'       // user we need to retrieve wishlist for
		 *              'wishlist_id'   // id of the wishlist we need to retrieve
		 *              ]
		 * @return YITH_WCWL_Wishlist|bool
		 */
		public static function get_current_wishlist( $args = array() ) {
			$defaults = array(
				'action_params' => get_query_var( YITH_WCWL()->wishlist_param, false ),
				'user_id' => isset( $_GET['user_id'] ) ? $_GET['user_id'] : false,
				'wishlist_id' => false
			);

			/**
			 * @var $action_params
			 * @var $user_id
			 * @var $wishlist_id
			 */
			$args = wp_parse_args( $args, $defaults );
			extract( $args );

			// retrieve options from query string
			$action_params = explode( '/', apply_filters( 'yith_wcwl_current_wishlist_view_params', $action_params ) );

			$action = ( isset( $action_params[0] ) ) ? $action_params[0] : 'view';
			$value = ( isset( $action_params[1] ) ) ? $action_params[1] : '';

			if( ! empty( $wishlist_id ) ){
				return self::get_wishlist( $wishlist_id );
			}

			if( ! empty( $user_id ) ){
				return self::get_default_wishlist( $user_id );
			}

			if(
				empty( $action ) ||
				! in_array( $action, YITH_WCWL()->get_available_views() ) ||
				in_array( $action, array( 'view', 'user' ) ) ||
				( in_array( $action, array( 'manage', 'create' ) ) && ! YITH_WCWL()->is_multi_wishlist_enabled() )
			){
				switch( $action ){
					case 'user':
						$user_id = $value;
						$user_id = ( ! $user_id ) ? get_query_var( $user_id, false ) : $user_id;

						return self::get_default_wishlist( intval( $user_id ) );
					case 'view':
					default:
						return self::get_wishlist( sanitize_text_field( $value ) );
				}
			}

			return false;
		}

		/**
		 * Retrieve default wishlist for current user (or current session)
		 *
		 * @param string|int|bool $id      Customer or session id; false if you want to use current customer or session.
		 * @param string          $context Context; when on edit context, wishlist will be created, if not exists.
		 *
		 * @return \YITH_WCWL_Wishlist|bool Wishlist object or false on failure
		 */
		public static function get_default_wishlist( $id = false, $context = 'read' ) {
			try {
				$default_wishlist = WC_Data_Store::load( 'wishlist' )->get_default_wishlist( $id, $context );
				return apply_filters( 'yith_wcwl_default_wishlist', $default_wishlist, $id, $context );
			} catch ( Exception $e ) {
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return false;
			}
		}

		/**
		 * Retrieve default wishlist for current user (or current session)
		 *
		 * @param $id string|int|bool Customer or session id; false if you want to use current customer or session
		 * @return \YITH_WCWL_Wishlist|bool Wishlist object or false on failure
		 */
		public static function generate_default_wishlist( $id = false ) {
			return self::get_default_wishlist( $id );
		}

		/**
		 * Generate new token for a wishlist
		 *
		 * @return string|bool Brand new token, or false on failure
		 */
		public static function generate_wishlist_token() {
			try {
				$token = WC_Data_Store::load( 'wishlist' )->generate_token();
				return $token;
			} catch( Exception $e ){
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return false;
			}
		}

		/**
		 * Retrieve a specific wishlist item from ID
		 *
		 * @param $item_id int|\YITH_WCWL_Wishlist_Item|stdClass Item identifier, or item itself
		 * @return \YITH_WCWL_Wishlist_Item|bool Wishlist item, or false on failure
		 */
		public static function get_wishlist_item( $item_id = 0 ) {
			if ( is_numeric( $item_id ) ) {
				$id = $item_id;
			} elseif ( $item_id instanceof YITH_WCWL_Wishlist_Item ) {
				$id = $item_id->get_id();
			} elseif ( is_object( $item_id ) && ! empty( $item_id->ID ) ) {
				$id = $item_id->ID;
			} else {
				$id = false;
			}

			if ( $id ) {
				try {
					return new YITH_WCWL_Wishlist_Item( $id );
				} catch ( Exception $e ) {
					return false;
				}
			}
			return false;
		}

		/**
		 * Retrieve item from a wishlist by product id
		 *
		 * @param $wishlist_id int|string Wishlist id or token
		 * @param $product_id int Product ID
		 * @return YITH_WCWL_Wishlist_Item|bool Item, or false when no item found
		 */
		public static function get_wishlist_item_by_product_id( $wishlist_id, $product_id ) {
			$wishlist = self::get_wishlist( $wishlist_id );

			if( $wishlist ){
				return $wishlist->get_product( $product_id );
			}

			return false;
		}

		/**
		 * Query database to search for wishlist items that matches specific parameters
		 *
		 * @param $args mixed Arguments array; it may contains any of the following:<br/>
		 * [<br/>
		 *     'user_id'             // Owner of the wishlist; default to current user logged in (if any), or false for cookie wishlist<br/>
		 *     'product_id'          // Product to search in the wishlist<br/>
		 *     'wishlist_id'         // wishlist_id for a specific wishlist, false for default, or all for any wishlist<br/>
		 *     'wishlist_token'      // wishlist token, or false as default<br/>
		 *     'wishlist_visibility' // all, visible, public, shared, private<br/>
		 *     'is_default' =>       // whether searched wishlist should be default one <br/>
		 *     'id' => false,        // only for table select<br/>
		 *     'limit' => false,     // pagination param; number of items per page. 0 to get all items<br/>
		 *     'offset' => 0         // pagination param; offset for the current set. 0 to start from the first item<br/>
		 * ]
		 *
		 * @return \YITH_WCWL_Wishlist_Item[]|bool A list of matching items or false on failure
		 */
		public static function get_wishlist_items( $args = array() ) {
			$args = apply_filters( 'yith_wcwl_wishlist_items_query_args', $args );

			try {
				$results = WC_Data_Store::load( 'wishlist-item' )->query( $args );
				return apply_filters( 'yith_wcwl_wishlist_item_query', $results, $args );
			} catch( Exception $e ){
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return false;
			}
		}

		/**
		 * Query database to count wishlist items that matches specific parameters
		 *
		 * @param $args array Same parameters allowed for {@see get_wishlist_items}
		 * @return int Count
		 */
		public static function get_wishlist_items_count( $args = array() ) {
			$args = apply_filters( 'yith_wcwl_wishlist_items_count_query_args', $args );

			try {
				$result = WC_Data_Store::load( 'wishlist-item' )->count( $args );
				return apply_filters( 'yith_wcwl_wishlist_item_count_query', $result, $args );
			} catch( Exception $e ){
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return 0;
			}
		}

		/**
		 * Count how many times a specific product was added to wishlist
		 *
		 * @param $product_id int Product id
		 * @return int Count of times product was added to cart
		 */
		public static function get_times_added_count( $product_id ) {
			try {
				$result = WC_Data_Store::load( 'wishlist-item' )->count_times_added( $product_id );
				return apply_filters( 'yith_wcwl_wishlist_times_added_count_query', $result, $product_id );
			} catch( Exception $e ){
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return 0;
			}
		}

		/**
		 * Count how many times a specific product was added to wishlist by the current user
		 *
		 * @param $product_id int Product id
		 * @return int Count of times product was added to cart
		 */
		public static function get_times_current_user_added_count( $product_id ) {
			try {
				$result = WC_Data_Store::load( 'wishlist-item' )->count_times_added( $product_id, 'current' );
				return apply_filters( 'yith_wcwl_wishlist_times_current_user_added_count_query', $result, $product_id );
			} catch( Exception $e ){
				wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				return 0;
			}
		}
	}
}