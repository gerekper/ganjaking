<?php
/**
 * Main class
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL' ) ) {
	/**
	 * WooCommerce Wishlist
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCWL
		 * @since 2.0.0
		 */
		protected static $instance;

		/**
		 * Last operation token
		 *
		 * @var string
		 * @since 2.0.0
		 */
		public $last_operation_token;

		/**
		 * Query string parameter used to generate Wishlist urls
		 *
		 * @var string
		 * @since 2.1.2
		 */
		public $wishlist_param = 'wishlist-action';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCWL
		 * @since 2.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCWL
		 * @since 1.0.0
		 */
		public function __construct() {
			// register data stores
			add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ) );

			// init frontend class
			$this->wcwl_frontend = YITH_WCWL_Frontend();

			// init crons
			$this->wcwl_cron = YITH_WCWL_Cron();

			// init session
			$this->wcwl_session = YITH_WCWL_Session();

			// init admin handling
			if( is_admin() ){
				$this->wcwl_admin = YITH_WCWL_Admin();
			}

			// load plugin-fw
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'privacy_loader' ), 20 );

			// add rewrite rule
			add_action( 'init', array( $this, 'add_rewrite_rules' ), 0 );
			add_filter( 'query_vars', array( $this, 'add_public_query_var' ) );
		}

		/* === PLUGIN FW LOADER === */

		/**
		 * Loads plugin fw, if not yet created
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if( ! empty( $plugin_fw_data ) ){
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/* === PRIVACY LOADER === */

		/**
		 * Loads privacy class
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function privacy_loader() {
			if( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once( YITH_WCWL_INC . 'class.yith-wcwl-privacy.php' );
				new YITH_WCWL_Privacy();
			}
		}

		/* === ITEMS METHODS === */

		/**
		 * Add a product in the wishlist.
		 *
		 * @param $atts array Array of parameters; when not passed, params will be searched in $_REQUEST
		 * @return void
		 * @throws Exception When an error occurs with Add to Wishlist operation
		 * @throws YITH_WCWL_Exception When an error occurs with Add to Wishlist operation
		 * @since 1.0.0
		 */
		public function add( $atts = array() ) {
			$defaults = array(
				'add_to_wishlist' => 0,
				'wishlist_id' => 0,
				'quantity' => 1,
				'user_id' => false,
				'dateadded' => '',
				'wishlist_name' => '',
				'wishlist_visibility' => 0
			);

			$atts = empty( $atts ) && ! empty( $this->details ) ? $this->details : $atts;
			$atts = ! empty( $atts ) ? $atts : $_REQUEST;
			$atts = wp_parse_args( $atts, $defaults );

			// filtering params
			$prod_id = apply_filters( 'yith_wcwl_adding_to_wishlist_prod_id', intval( $atts['add_to_wishlist'] ) );
			$wishlist_id = apply_filters( 'yith_wcwl_adding_to_wishlist_wishlist_id', $atts['wishlist_id'] );
			$quantity = apply_filters( 'yith_wcwl_adding_to_wishlist_quantity', intval( $atts['quantity'] ) );
			$user_id = apply_filters( 'yith_wcwl_adding_to_wishlist_user_id', intval( $atts['user_id'] ) );
			$dateadded = apply_filters( 'yith_wcwl_adding_to_wishlist_dateadded', $atts['dateadded'] );

			do_action( 'yith_wcwl_adding_to_wishlist', $prod_id, $wishlist_id, $user_id );

			if( ! $this->can_user_add_to_wishlist() ){
				throw new YITH_WCWL_Exception( apply_filters( 'yith_wcwl_user_cannot_add_to_wishlist_message', __( 'The item cannot be added to this wishlist', 'yith-woocommerce-wishlist' ) ), 1 );
			}

			if ( $prod_id == false ) {
				throw new YITH_WCWL_Exception( __( 'An error occurred while adding the products to the wishlist.', 'yith-woocommerce-wishlist' ), 0 );
			}

			$wishlist = 'new' === $wishlist_id ? $this->add_wishlist( $atts ) : YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_id, 'edit' );

			if( ! $wishlist instanceof YITH_WCWL_Wishlist || ! $wishlist->current_user_can( 'add_to_wishlist' ) ){
				throw new YITH_WCWL_Exception( __( 'An error occurred while adding the products to the wishlist.', 'yith-woocommerce-wishlist' ), 0 );
			}

			$this->last_operation_token = $wishlist->get_token();

			if( $wishlist->has_product( $prod_id ) ) {
				throw new YITH_WCWL_Exception( apply_filters( 'yith_wcwl_product_already_in_wishlist_message', get_option( 'yith_wcwl_already_in_wishlist_text' ) ), 1 );
			}

			$item = new YITH_WCWL_Wishlist_Item();

			$item->set_product_id( $prod_id );
			$item->set_quantity( $quantity );
			$item->set_wishlist_id( $wishlist->get_id() );
			$item->set_user_id( $wishlist->get_user_id() );

			if( $dateadded ){
				$item->set_date_added( $dateadded );
			}

			$wishlist->add_item( $item );
			$wishlist->save();

			wp_cache_delete( 'wishlist-count-' . $wishlist->get_token(), 'wishlists' );

			if( $user_id = $wishlist->get_user_id() ) {
				wp_cache_delete( 'wishlist-user-total-count-' . $user_id, 'wishlists' );
			}

			do_action( 'yith_wcwl_added_to_wishlist', $prod_id, $item->get_wishlist_id(), $item->get_user_id() );
		}

		/**
		 * Remove an entry from the wishlist.
		 *
		 * @param $atts array Array of parameters; when not passed, parameters will be retrieved from $_REQUEST
		 *
		 * @return void
		 * @throws Exception When something was wrong with removal
		 * @since 1.0.0
		 */
		public function remove( $atts = array() ) {
			$defaults = array(
				'remove_from_wishlist' => 0,
				'wishlist_id' => 0,
				'user_id' => false
			);

			$atts = empty( $atts ) && ! empty( $this->details ) ? $this->details : $atts;
			$atts = ! empty( $atts ) ? $atts : $_REQUEST;
			$atts = wp_parse_args( $atts, $defaults );

			$prod_id = intval( $atts['remove_from_wishlist'] );
			$wishlist_id = intval( $atts['wishlist_id'] );
			$user_id = intval( $atts['user_id'] );

			do_action( 'yith_wcwl_removing_from_wishlist', $prod_id, $wishlist_id, $user_id );

			if( $prod_id == false ){
				throw new YITH_WCWL_Exception( apply_filters( 'yith_wcwl_unable_to_remove_product_message', __( 'Error. Unable to remove the product from the wishlist.', 'yith-woocommerce-wishlist' ) ), 0 );
			}

			$wishlist = YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_id );

			if( ! $wishlist instanceof YITH_WCWL_Wishlist || ! $wishlist->current_user_can( 'remove_from_wishlist' ) ){
				throw new YITH_WCWL_Exception( apply_filters( 'yith_wcwl_unable_to_remove_product_message', __( 'Error. Unable to remove the product from the wishlist.', 'yith-woocommerce-wishlist' ) ), 0 );
			}

			$wishlist->remove_product( $prod_id );
			$wishlist->save();

			wp_cache_delete( 'wishlist-count-' . $wishlist->get_token(), 'wishlists' );

			if( $user_id = $wishlist->get_user_id() ) {
				wp_cache_delete( 'wishlist-user-total-count-' . $user_id );
			}

			do_action( 'yith_wcwl_removed_from_wishlist', $prod_id, $wishlist->get_id(), $wishlist->get_user_id() );
		}

		/**
		 * Check if the product exists in the wishlist.
		 *
		 * @param int $product_id Product id to check
		 * @param int|bool $wishlist_id Wishlist where to search (use false to search in default wishlist)
		 * @return bool
		 * @since 1.0.0
		 */
		public function is_product_in_wishlist( $product_id, $wishlist_id = false ) {
			$wishlist = YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_id );

			if( ! $wishlist ){
				return false;
			}

			return apply_filters( 'yith_wcwl_is_product_in_wishlist', $wishlist->has_product( $product_id ), $product_id, $wishlist_id );
		}

		/**
		 * Retrieve elements of the wishlist for a specific user
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
		 * @return YITH_WCWL_Wishlist_Item[]|bool
		 * @since 2.0.0
		 */
		public function get_products( $args = array() ) {
			return YITH_WCWL_Wishlist_Factory::get_wishlist_items( $args );
		}

		/**
		 * Retrieve the number of products in the wishlist.
		 *
		 * @param $wishlist_token string|bool Wishlist token if any; false for default wishlist
		 *
		 * @return int
		 * @since 1.0.0
		 */
		public function count_products( $wishlist_token = false ) {
		   $wishlist = YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_token );

			if( ! $wishlist ){
				return 0;
			}

			$count = wp_cache_get( 'wishlist-count-' . $wishlist->get_token(), 'wishlists' );

			if( false === $count ){
				$count = $wishlist->count_items();
				wp_cache_set( 'wishlist-count-' . $wishlist->get_token(), $count, 'wishlists' );
			}

			return $count;
		}

		/**
		 * Count all user items in wishlists
		 *
		 * @return int Count of items added all over wishlist from current user
		 * @since 2.0.12
		 */
		public function count_all_products() {
			$args = array(
				'wishlist_id' => 'all'
			);

			if( is_user_logged_in() ){
				$id = get_current_user_id();
				$args['user_id'] = $id;
			}
			else{
				$id = YITH_WCWL_Session()->get_session_id();
				$args['session_id'] = $id;
			}

			if( false === $count = wp_cache_get( 'wishlist-user-total-count-' . $id, 'wishlists' ) ) {
				$count = YITH_WCWL_Wishlist_Factory::get_wishlist_items_count( $args );
				wp_cache_set( 'wishlist-user-total-count-' . $id, $count, 'wishlists' );
			}

			return $count;
		}

		/**
		 * Count number of times a product was added to users wishlists
		 *
		 * @param $product_id int|bool Product id; false will force method to use global product
		 *
		 * @return int Number of times the product was added to wishlist
		 * @since 2.0.13
		 */
		public function count_add_to_wishlist( $product_id = false ) {
			global $product;

			$product_id = ! ( $product_id ) ? yit_get_product_id( $product ) : $product_id;

			if( ! $product_id ){
				return 0;
			}

			$count = YITH_WCWL_Wishlist_Factory::get_times_added_count( $product_id );

			return $count;
		}

		/**
		 * Count product occurrences in users wishlists
		 *
		 * @param $product_id int|bool Product id; false will force method to use global product
		 *
		 * @return int
		 * @since 2.0.0
		 */
		public function count_product_occurrences( $product_id = false ) {
			global $product;

			$product_id = ! ( $product_id ) ? yit_get_product_id( $product ) : $product_id;

			if( ! $product_id ){
				return 0;
			}

			$count = YITH_WCWL_Wishlist_Factory::get_wishlist_items_count( array( 'product_id' => $product_id, 'user_id' => false, 'session_id' => false, 'wishlist_id' => 'all' ) );

			return $count;
		}

		/**
		 * Retrieve details of a product in the wishlist.
		 *
		 * @param int $product_id
		 * @param int|bool $wishlist_id
		 * @return YITH_WCWL_Wishlist_Item|bool
		 * @since 1.0.0
		 */
		public function get_product_details( $product_id, $wishlist_id = false ) {
			$product = $this->get_products(
				array(
					'prod_id' => $product_id,
					'wishlist_id' => $wishlist_id
				)
			);

			if( empty( $product ) ){
				return false;
			}

			return array_shift( $product );
		}

		/* === WISHLISTS METHODS === */

		/**
		 * Add a new wishlist for the user.
		 *
		 * @param $atts array Array of params for wishlist creation
		 * @return int Id of the wishlist created
		 * @since 2.0.0
		 */
		public function add_wishlist( $atts = array() ) {
			$defaults = array(
				'user_id' => false
			);

			$atts = empty( $atts ) && ! empty( $this->details ) ? $this->details : $atts;
			$atts = ! empty( $atts ) ? $atts : $_REQUEST;
			$atts = wp_parse_args( $atts, $defaults );

			$user_id = ( ! empty( $atts['user_id'] ) ) ? $atts['user_id'] : false;

			return $this->generate_default_wishlist( $user_id );
		}

		/**
		 * Update wishlist with arguments passed as second parameter
		 *
		 * @param $wishlist_id int
		 * @param $args array Array of parameters to use in update process
		 * @return void
		 * @since 2.0.0
		 */
		public function update_wishlist( $wishlist_id, $args = array() ) {
			return;
		}

		/**
		 * Delete indicated wishlist
		 *
		 * @param $wishlist_id int
		 * @return void
		 * @since 2.0.0
		 */
		public function remove_wishlist( $wishlist_id ) {
			return;
		}

		/**
		 * Retrieve all the wishlist matching specified arguments
		 *
		 * @param $args mixed Array of valid arguments<br/>
		 * [<br/>
		 *     'id'                  // Wishlist id to search, if any<br/>
		 *     'user_id'             // User owner<br/>
		 *     'wishlist_slug'       // Slug of the wishlist to search<br/>
		 *     'wishlist_name'       // Name of the wishlist to search<br/>
		 *     'wishlist_token'      // Token of the wishlist to search<br/>
		 *     'wishlist_visibility' // Wishlist visibility: all, visible, public, shared, private<br/>
		 *     'user_search'         // String to match against first name / last name or email of the wishlist owner<br/>
		 *     'is_default'          // Whether wishlist should be default or not<br/>
		 *     'orderby'             // Column used to sort final result (could be any wishlist lists column)<br/>
		 *     'order'               // Sorting order<br/>
		 *     'limit'               // Pagination param: maximum number of elements in the set. 0 to retrieve all elements<br/>
		 *     'offset'              // Pagination param: offset for the current set. 0 to start from the first item<br/>
		 *     'show_empty'          // Whether to show empty lists os not<br/>
		 * ]
		 *
		 * @return YITH_WCWL_Wishlist[]
		 * @since 2.0.0
		 */
		public function get_wishlists( $args = array() ){
			return YITH_WCWL_Wishlist_Factory::get_wishlists( $args );
		}

		/**
		 * Wrapper for \YITH_WCWL::get_wishlists, will return wishlists for current user
		 *
		 * @return YITH_WCWL_Wishlist[]
		 * @since 2.0.0
		 */
		public function get_current_user_wishlists() {
			$id = is_user_logged_in() ? get_current_user_id() : YITH_WCWL_Session()->get_session_id();

			$lists = wp_cache_get( 'user-wishlists-' . $id, 'wishlists' );

			if ( ! $lists ) {
				$lists = YITH_WCWL_Wishlist_Factory::get_wishlists(
					array(
						'orderby' => 'dateadded',
						'order' => 'ASC',
					)
				);

				wp_cache_set( 'user-wishlists-' . $id, $lists, 'wishlists' );
			}

			return $lists;
		}

		/**
		 * Returns details of a wishlist, searching it by wishlist id
		 *
		 * @param $wishlist_id int
		 * @return YITH_WCWL_Wishlist
		 * @since 2.0.0
		 */
		public function get_wishlist_detail( $wishlist_id ) {
			return YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_id );
		}

		/**
		 * Returns details of a wishlist, searching it by wishlist token
		 *
		 * @param $wishlist_token string
		 * @return YITH_WCWL_Wishlist
		 * @since 2.0.0
		 */
		public function get_wishlist_detail_by_token( $wishlist_token ) {
			return YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_token );
		}

		/**
		 * Generate default wishlist for current user or session
		 *
		 * @return int Default wishlist id
		 * @since 2.0.0
		 */
		public function generate_default_wishlist( $id = false ){
			$wishlist = YITH_WCWL_Wishlist_Factory::generate_default_wishlist( $id );

			if( $wishlist ){
				return $wishlist->get_id();
			}

			return false;
		}

		/**
		 * Generate a token to visit wishlist
		 *
		 * @return string token
		 * @since 2.0.0
		 */
		public function generate_wishlist_token(){
			return YITH_WCWL_Wishlist_Factory::generate_wishlist_token();
		}

		/**
		 * Returns an array of users that created and populated a public wishlist
		 *
		 * @param $args mixed Array of valid arguments<br/>
		 * [<br/>
		 *     'search' // String to match against first name / last name / user login or user email of wishlist owner<br/>
		 *     'limit'  // Pagination param: number of items to show in one page. 0 to show all items<br/>
		 *     'offset' // Pagination param: offset for the current set. 0 to start from the first item<br/>
		 * ]
		 * @return array
		 * @since 2.0.0
		 */
		public function get_users_with_wishlist( $args = array() ){
			return YITH_WCWL_Wishlist_Factory::get_wishlist_users( $args );
		}

		/**
		 * Count users that have public wishlists
		 *
		 * @param $search string
		 * @return int
		 * @since 2.0.0
		 */
		public function count_users_with_wishlists( $search  ){
			return count( $this->get_users_with_wishlist( array( 'search' => $search ) ) );
		}

		/* === GENERAL METHODS === */

		/**
		 * Checks whether current user can add to the wishlist
		 *
		 * @param $user_id int|bool User id to test; false to use current user id
		 * @return bool Whether current user can add to wishlist
		 * @since 3.0.0
		 */
		public function can_user_add_to_wishlist( $user_id = false ) {
			$user_id = $user_id ? $user_id : get_current_user_id();
			$disable_wishlist_for_unauthenticated_users = get_option( 'yith_wcwl_disable_wishlist_for_unauthenticated_users' );
			$return = true;

			if( 'yes' == $disable_wishlist_for_unauthenticated_users && ! $user_id ){
				$return = false;
			}

			return apply_filters( 'yith_wcwl_can_user_add_to_wishlist', $return, $user_id );
		}

		/**
		 * Register custom plugin Data Stores classes
		 *
		 * @param $data_stores array Array of registered data stores
		 * @return array Array of filtered data store
		 */
		public function register_data_stores( $data_stores ) {
			$data_stores['wishlist'] = 'YITH_WCWL_Wishlist_Data_Store';
			$data_stores['wishlist-item'] = 'YITH_WCWL_Wishlist_Item_Data_Store';

			return $data_stores;
		}

		/**
		 * Add rewrite rules for wishlist
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function add_rewrite_rules() {
			global $wp_query;

			// filter wishlist param
			$this->wishlist_param = apply_filters( 'yith_wcwl_wishlist_param', $this->wishlist_param );

			$wishlist_page_id = isset( $_POST['yith_wcwl_wishlist_page_id'] ) ? $_POST['yith_wcwl_wishlist_page_id'] : get_option( 'yith_wcwl_wishlist_page_id' );
			$wishlist_page_id = yith_wcwl_object_id( $wishlist_page_id );

			if( empty( $wishlist_page_id ) ){
				return;
			}

			$wishlist_page = get_post( $wishlist_page_id );
			$wishlist_page_slug = $wishlist_page ? $wishlist_page->post_name : false;

			if ( empty( $wishlist_page_slug ) ){
				return;
			}

			if( defined( 'POLYLANG_VERSION' ) || defined( 'ICL_PLUGIN_PATH' ) ){
				return;
			}

			$regex_paged = '(([^/]+/)*' . $wishlist_page_slug . ')(/(.*))?/page/([0-9]{1,})/?$';
			$regex_simple = '(([^/]+/)*' . $wishlist_page_slug . ')(/(.*))?/?$';

			add_rewrite_rule( $regex_paged, 'index.php?pagename=$matches[1]&' . $this->wishlist_param . '=$matches[4]&paged=$matches[5]', 'top' );
			add_rewrite_rule( $regex_simple, 'index.php?pagename=$matches[1]&' . $this->wishlist_param . '=$matches[4]', 'top' );

			$rewrite_rules = get_option( 'rewrite_rules' );

			if( ! is_array( $rewrite_rules ) || ! array_key_exists( $regex_paged, $rewrite_rules ) || ! array_key_exists( $regex_simple, $rewrite_rules ) ){
				flush_rewrite_rules();
			}
		}

		/**
		 * Adds public query var for wishlist
		 *
		 * @param $public_var array
		 * @return array
		 * @since 2.0.0
		 */
		public function add_public_query_var( $public_var ) {
			$public_var[] = $this->wishlist_param;
			$public_var[] = 'wishlist_id';

			return $public_var;
		}

		/**
		 * Build wishlist page URL.
		 *
		 * @param $action string
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public function get_wishlist_url( $action = '' ) {
			global $sitepress;
			$wishlist_page_id = yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) );
			$wishlist_permalink = get_the_permalink( $wishlist_page_id );

			$action_params = explode( '/', $action );
			$view = $action_params[0];
			$data = isset( $action_params[1] ) ? $action_params[1] : '';

			if( $action == 'view' && empty( $data ) ){
				return $wishlist_permalink;
			}

			if( get_option( 'permalink_structure' ) && ! defined( 'ICL_PLUGIN_PATH' ) && ! defined( 'POLYLANG_VERSION' ) ) {
				$wishlist_permalink = trailingslashit( $wishlist_permalink );
				$base_url = trailingslashit( $wishlist_permalink . $action );
			}
			else{
				$base_url = $wishlist_permalink;
				$params = array();

				if( ! empty( $data ) ){
					$params[ $this->wishlist_param ] = $view;

					if( $view == 'view' ){
						$params['wishlist_id'] = $data;
					}
					elseif( $view == 'user' ){
						$params['user_id'] = $data;
					}
				}
				else{
					$params[ $this->wishlist_param ] = $view;
				}

				$base_url = add_query_arg( $params, $base_url );
			}

			if( defined( 'ICL_PLUGIN_PATH' ) && $sitepress->get_current_language() != $sitepress->get_default_language() ){
				$base_url = add_query_arg( 'lang', $sitepress->get_current_language(), $base_url );
			}

			return apply_filters( 'yith_wcwl_wishlist_page_url', esc_url_raw( $base_url ), $action );
		}

		/**
		 * Retrieve url for the wishlist that was affected by last operation
		 *
		 * @return string Url to view last operation wishlist
		 */
		public function get_last_operation_url() {
			$action = 'view';

			if( ! empty( $this->last_operation_token ) ){
				$action .= "/{$this->last_operation_token}";
			}

			return $this->get_wishlist_url( $action );
		}

		/**
		 * Generates Add to Wishlist url, to use when customer do not have js enabled
		 *
		 * @param $product_id int Product id to add to wishlist
		 * @param $args array Any of the following parameters
		 * [
		 *     'base_url' => ''
		 *     'wishlist_id' => 0,
		 *     'quantity' => 1,
		 *     'user_id' => false,
		 *     'dateadded' => '',
		 *     'wishlist_name' => '',
		 *     'wishlist_visibility' => 0
		 * ]
		 * @return string Add to wishlist url
		 */
		public function get_add_to_wishlist_url( $product_id, $args = array() ) {
			$args = array_merge(
				array(
					'add_to_wishlist' => $product_id
				),
				$args
			);

			if( isset( $args['base_url'] ) ){
				$base_url = $args['base_url'];
				unset( $args['base_url'] );

				$url = add_query_arg( $args, $base_url );
			}
			else{
				$url = add_query_arg( $args );
			}

			return apply_filters( 'yith_wcwl_add_to_wishlist_url', esc_url_raw( $url ), $product_id, $args );
		}

		/**
		 * Build the URL used to remove an item from the wishlist.
		 *
		 * @param int $item_id
		 * @return string
		 * @since 1.0.0
		 */
		public function get_remove_url( $item_id ) {
			return esc_url( add_query_arg( 'remove_from_wishlist', $item_id ) );
		}

		/**
		 * Returns available views for wishlist page
		 *
		 * @return string[]
		 * @since 3.0.0
		 */
		public function get_available_views() {
			$available_views = apply_filters( 'yith_wcwl_available_wishlist_views', array( 'view', 'user' ) );
			return $available_views;
		}

		/**
		 * Checks whether multi-wishlist feature is enabled for current user
		 *
		 * @return bool Whether feature is enabled or not
		 */
		public function is_multi_wishlist_enabled() {
			return false;
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL class
 *
 * @return \YITH_WCWL|\YITH_WCWL_Premium
 * @since 2.0.0
 */
function YITH_WCWL(){
	return defined( 'YITH_WCWL_PREMIUM' ) ? YITH_WCWL_Premium::get_instance() : YITH_WCWL::get_instance();
}
