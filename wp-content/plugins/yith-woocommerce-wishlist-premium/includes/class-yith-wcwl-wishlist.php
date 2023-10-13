<?php
/**
 * Wishlist class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes\Wishlists
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Wishlist' ) ) {
	/**
	 * This class describes Wishlist object, and it is meant to be used whenever a wishlist needs to be retrieved
	 * It is an abstract class to account for different type of wishlists; system will determine wishlist type basing
	 * on user visiting the site
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Wishlist extends WC_Data implements ArrayAccess {

		/**
		 * Wishlist token (Unique identifier)
		 *
		 * @var string
		 */
		protected $token = '';

		/**
		 * Wishlist Data array
		 *
		 * @var array
		 */
		protected $data;

		/**
		 * Wishlist items will be stored here, sometimes before they persist in the DB.
		 *
		 * @since 3.0.0
		 * @var array
		 */
		protected $items = array();

		/**
		 * Wishlist items that need deleting are stored here.
		 *
		 * @since 3.0.0
		 * @var array
		 */
		protected $items_to_delete = array();

		/**
		 * Stores meta in cache for future reads.
		 *
		 * A group must be set to to enable caching.
		 *
		 * @var string
		 */
		protected $cache_group = 'wishlists';

		/* === MAGIC METHODS === */

		/**
		 * Constructor
		 *
		 * @param int|string|\YITH_WCWL_Wishlist $wishlist Wishlist identifier.
		 *
		 * @throws Exception When not able to load Data Store class.
		 */
		public function __construct( $wishlist = 0 ) {
			// set default values.
			$this->data = array(
				/**
				 * APPLY_FILTERS: yith_wcwl_default_wishlist_privacy
				 *
				 * Filter the default Wishlist privacy.
				 *
				 * @param string $wishlist_privacy Default wishlist privacy. Possible values are 0 (Public), 1 (Shared) or 2 (Private)
				 *
				 * @return string
				 */
				'privacy'    => apply_filters( 'yith_wcwl_default_wishlist_privacy', 0 ),
				'user_id'    => 0,
				'session_id' => '',
				/**
				 * APPLY_FILTERS: yith_wcwl_default_wishlist_name
				 *
				 * Filter the default Wishlist name.
				 *
				 * @param string $wishlist_name Default wishlist name
				 *
				 * @return string
				 */
				'name'       => apply_filters( 'yith_wcwl_default_wishlist_name', '' ),
				/**
				 * APPLY_FILTERS: yith_wcwl_default_wishlist_slug
				 *
				 * Filter the default Wishlist slug.
				 *
				 * @param string $wishlist_slug Default wishlist slug
				 *
				 * @return string
				 */
				'slug'       => apply_filters( 'yith_wcwl_default_wishlist_slug', '' ),
				'token'      => '',
				'is_default' => 0,
				'date_added' => '',
				'expiration' => '',
			);

			parent::__construct();

			if ( is_numeric( $wishlist ) && $wishlist > 0 ) {
				$this->set_id( $wishlist );
			} elseif ( $wishlist instanceof self ) {
				$this->set_id( $wishlist->get_id() );
			} elseif ( is_string( $wishlist ) ) {
				$this->set_token( $wishlist );
			} else {
				$this->set_object_read( true );
			}

			$this->data_store = WC_Data_Store::load( 'wishlist' );

			if ( $this->get_id() > 0 || ! empty( $this->get_token() ) ) {
				$this->data_store->read( $this );
			}
		}

		/* === HELPERS === */

		/**
		 * Return number of items for current wishlist
		 *
		 * @return int Count of items
		 */
		public function count_items() {
			return count( $this->get_items() );
		}

		/**
		 * Check whether wishlist was created for unautheticated user
		 *
		 * @return bool
		 */
		public function is_session_based() {
			return (bool) $this->get_session_id();
		}

		/**
		 * Returns true when wishlist is default
		 *
		 * @return bool Whether wishlist is default or not
		 */
		public function is_default() {
			return $this->get_is_default();
		}

		/**
		 * Check whether wishlist was created for authenticated user
		 *
		 * @return bool
		 */
		public function has_owner() {
			return (bool) $this->get_user_id();
		}

		/**
		 * Check if current user is owner of this wishlist (works both for authenticated users & guests)
		 *
		 * @param string|int|bool $current_user Optional user identifier, in the form of a User ID or session id; false for default.
		 * @return bool
		 */
		public function is_current_user_owner( $current_user = false ) {
			$user_id    = $this->get_user_id();
			$session_id = $this->get_session_id();
			$is_owner   = false;

			if ( $current_user && ( (int) $current_user === $user_id || $current_user === $session_id ) ) {
				$is_owner = true;
			}

			if ( $this->has_owner() && is_user_logged_in() && get_current_user_id() === $user_id ) {
				$is_owner = true;
			}

			if ( $this->is_session_based() && YITH_WCWL_Session()->maybe_get_session_id() === $session_id ) {
				$is_owner = true;
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_is_current_user_owner
			 *
			 * Filters whether the current user is the wishlist owner.
			 *
			 * @param bool $is_owner Whether the current user is the wishlist owner
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_wcwl_is_current_user_owner', $is_owner );
		}

		/**
		 * Check whether current user can perform a specific action on wishlist
		 *
		 * Accepted capabilities:
		 * * view
		 * * update_wishlist
		 * * add_to_wishlist
		 * * remove_from_wishlist
		 * * move_to_another_wishlist
		 * * ask_an_estimate
		 * * sort_items
		 * * update_quantity
		 * * download_pdf
		 *
		 * @param string          $capability Capability to check; default "view".
		 * @param string|int|bool $current_user Optional user identifier, in the form of a User ID or session id; false for default.
		 * @return bool
		 */
		public function current_user_can( $capability = 'view', $current_user = false ) {
			// admin can do anything by default.
			/**
			 * APPLY_FILTERS: yith_wcwl_admin_can
			 *
			 * Filter whether admin can perform a specific action on wishlist.
			 *
			 * @param bool               $user_can     Whether admin can perform some actions on the wishlist
			 * @param string             $capability   Capability
			 * @param WP_User            $current_user Current user
			 * @param YITH_WCWL_Wishlist $wishlist     Wishlist object
			 *
			 * @return bool
			 */
			if ( is_user_logged_in() && current_user_can( 'manage_woocommerce' ) && apply_filters( 'yith_wcwl_admin_can', true, $capability, $current_user, $this ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
				return true;
			}

			// for other users, perform checks over capability required.
			switch ( $capability ) {
				case 'view':
					$can = $this->is_current_user_owner( $current_user );

					if ( ! $can && $this->has_privacy( array( 'public', 'shared' ) ) ) {
						$can = true;
					}
					break;
				default:
					$can = $this->is_current_user_owner( $current_user );
					break;
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_current_user_can
			 *
			 * Filter whether current user can perform a specific action on wishlist.
			 *
			 * @param bool               $user_can     Whether current user can perform some actions on the wishlist
			 * @param string             $capability   Capability
			 * @param WP_User            $current_user Current user
			 * @param YITH_WCWL_Wishlist $wishlist     Wishlist object
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_wcwl_current_user_can', $can, $capability, $current_user, $this );
		}

		/* === GETTERS === */

		/**
		 * Get wishlist token
		 *
		 * @return string Wishlist unique token
		 */
		public function get_token() {
			return $this->token;
		}

		/**
		 * Get privacy visibility
		 *
		 * @param string $context Context.
		 * @return int Wishlist visibility (0 => public, 1 => shared, 2 => private)
		 */
		public function get_privacy( $context = 'view' ) {
			return (int) $this->get_prop( 'privacy', $context );
		}

		/**
		 * Get formatted privacy name
		 *
		 * @param string $context Context.
		 * @return string Formatted privacy value
		 */
		public function get_formatted_privacy( $context = 'view' ) {
			$privacy           = $this->get_privacy( $context );
			$formatted_privacy = yith_wcwl_get_privacy_label( $privacy );

			/**
			 * APPLY_FILTERS: yith_wcwl_wishlist_formatted_privacy
			 *
			 * Filter the formatted wishlist privacy label.
			 *
			 * @param string             $formatted_privacy Formatted privacy label
			 * @param int                $privacy           Privacy option
			 * @param YITH_WCWL_Wishlist $wishlist          Wishlist object
			 * @param string             $context           Context
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_wishlist_formatted_privacy', $formatted_privacy, $privacy, $this, $context );
		}

		/**
		 * Checks if current wishlist has a specific privacy value
		 * Method will accept both numeric privacy values and privacy labels
		 *
		 * @param int|string|array $privacy Privacy value (0|1|2) or label (public|shared|private), or array of acceptable values.
		 * @return bool Whether wishlist matched privacy test
		 */
		public function has_privacy( $privacy ) {
			$wishlist_privacy = $this->get_privacy( 'edit' );
			$has_privacy      = false;

			if ( is_array( $privacy ) && ! empty( $privacy ) ) {
				foreach ( $privacy as $test_value ) {
					// return true if wishlist has any of the privacy value submitted.
					if ( $this->has_privacy( $test_value ) ) {
						return true;
					}
				}
			} elseif ( is_string( $privacy ) ) {
				$has_privacy = yith_wcwl_get_privacy_value( $privacy ) === $wishlist_privacy;
			} else {
				$has_privacy = $privacy === $wishlist_privacy;
			}

			return $has_privacy;
		}

		/**
		 * Get owner id
		 *
		 * @param string $context Context.
		 * @return int Wishlist owner id
		 */
		public function get_user_id( $context = 'view' ) {
			return (int) $this->get_prop( 'user_id', $context );
		}

		/**
		 * Get session id
		 *
		 * @param string $context Context.
		 * @return int Wishlist owner id
		 */
		public function get_session_id( $context = 'view' ) {
			return $this->get_prop( 'session_id', $context );
		}

		/**
		 * Get wishlist name
		 *
		 * @param string $context Context.
		 * @return string Wishlist name
		 */
		public function get_name( $context = 'view' ) {
			return wc_clean( stripslashes( $this->get_prop( 'name', $context ) ) );
		}

		/**
		 * Get wishlist formatted name
		 *
		 * @param string $context Context.
		 * @return string Formatted name
		 */
		public function get_formatted_name( $context = 'view' ) {
			$name = $this->get_name( $context );

			if ( $this->is_default() && ! $name ) {
				/**
				 * APPLY_FILTERS: yith_wcwl_default_wishlist_formatted_title
				 *
				 * Filter the default wishlist title.
				 *
				 * @param string $title Default wishlist title
				 *
				 * @return string
				 */
				$name = apply_filters( 'yith_wcwl_default_wishlist_formatted_title', get_option( 'yith_wcwl_wishlist_title' ) );
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_wishlist_formatted_title
			 *
			 * Filter the formatted wishlist title.
			 *
			 * @param string $title Formatted wishlist title
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_wishlist_formatted_title', $name );
		}

		/**
		 * Get wishlist slug
		 *
		 * @param string $context Context.
		 * @return string Wishlist slug
		 */
		public function get_slug( $context = 'view' ) {
			return $this->get_prop( 'slug', $context );
		}

		/**
		 * Check if wishlist is default one for the user
		 *
		 * @param string $context Context.
		 * @return bool Whether wishlist is default one or not
		 */
		public function get_is_default( $context = 'view' ) {
			return (bool) $this->get_prop( 'is_default', $context );
		}

		/**
		 * Get wishlist date added
		 *
		 * @param string $context Context.
		 * @return \WC_DateTime|string Wishlist date of creation
		 */
		public function get_date_added( $context = 'view' ) {
			$date_added = $this->get_prop( 'date_added', $context );

			if ( $date_added && 'view' === $context ) {
				return $date_added->date_i18n( 'Y-m-d H:i:s' );
			}

			return $date_added;
		}

		/**
		 * Get formatted wishlist date added
		 *
		 * @param string $format Date format (if empty, WP date format will be applied).
		 * @return string Wishlist date of creation
		 */
		public function get_date_added_formatted( $format = '' ) {
			$date_added = $this->get_date_added( 'edit' );

			if ( $date_added ) {
				$format = $format ? $format : get_option( 'date_format' );
				return $date_added->date_i18n( $format );
			}

			return '';
		}

		/**
		 * Get wishlist date added
		 *
		 * @param string $context Context.
		 * @return \WC_DateTime|string Wishlist date of creation
		 */
		public function get_expiration( $context = 'view' ) {
			$expiration = $this->get_prop( 'expiration', $context );

			if ( $expiration && 'view' === $context ) {
				return $expiration->date_i18n( 'Y-m-d H:i:s' );
			}

			return $expiration;
		}

		/**
		 * Get formatted wishlist expiration added
		 *
		 * @param string $format Date format (if empty, WP date format will be applied).
		 * @return string Wishlist date of expiration
		 */
		public function get_expiration_formatted( $format = '' ) {
			$expiration = $this->get_expiration( 'edit' );

			if ( $expiration ) {
				$format = $format ? $format : get_option( 'date_format' );
				return $expiration->date_i18n( $format );
			}

			return '';
		}

		/**
		 * Retrieve email of the owner of the wishlist (if any)
		 *
		 * @return string|bool Email of wishlist owner (or false, if no registered owner)
		 */
		public function get_user_email() {
			$user_id = $this->get_user_id();

			if ( ! $user_id ) {
				return false;
			}

			$user = get_userdata( $user_id );
			return $user->user_email;
		}

		/**
		 * Retrieve first name of the owner of the wishlist (if any)
		 *
		 * @return string|bool First name of wishlist owner (or false, if no registered owner)
		 */
		public function get_user_first_name() {
			$user_id = $this->get_user_id();

			if ( ! $user_id ) {
				return false;
			}

			$user = get_userdata( $user_id );
			return $user->first_name;
		}

		/**
		 * Retrieve last name of the owner of the wishlist (if any)
		 *
		 * @return string|bool Last name of wishlist owner (or false, if no registered owner)
		 */
		public function get_user_last_name() {
			$user_id = $this->get_user_id();

			if ( ! $user_id ) {
				return false;
			}

			$user = get_userdata( $user_id );
			return $user->last_name;
		}

		/**
		 * Return owner formatted name
		 *
		 * @return string User formatted name
		 */
		public function get_user_formatted_name() {
			$user_id = $this->get_user_id();

			if ( ! $user_id ) {
				return false;
			}

			$user       = get_userdata( $user_id );
			$first_name = $user->first_name;
			$last_name  = $user->last_name;
			$email      = $user->user_email;

			$formatted_name = $email;

			if ( ! empty( $first_name ) || ! empty( $last_name ) ) {
				$formatted_name .= " <{$first_name} {$last_name}>";
			}

			return $formatted_name;
		}

		/**
		 * Return url to visit wishlist
		 *
		 * @return string Url to the wishlist
		 */
		public function get_url() {
			return YITH_WCWL()->get_wishlist_url( 'view/' . $this->get_token() );
		}

		/**
		 * Get url to download the wishlist as PDF file
		 *
		 * @return string Url to download
		 */
		public function get_download_url() {
			/**
			 * APPLY_FILTERS: yith_wcwl_wishlist_download_url
			 *
			 * Filter the URL to download the wishlist PDF.
			 *
			 * @param string             $download_url PDF download URL
			 * @param YITH_WCWL_Wishlist $wishlist     Wishlist object
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_wishlist_download_url', wp_nonce_url( add_query_arg( 'download_wishlist', $this->get_id() ), 'download_wishlist', 'download_nonce' ), $this );
		}

		/**
		 * Get url to delete the wishlist
		 *
		 * @return string Url to delete the wishlist
		 */
		public function get_delete_url() {
			/**
			 * APPLY_FILTERS: yith_wcwl_wishlist_delete_url
			 *
			 * Filter the URL to delete the wishlist.
			 *
			 * @param string             $delete_url Delete wishlist URL
			 * @param YITH_WCWL_Wishlist $wishlist   Wishlist object
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_wishlist_delete_url', wp_nonce_url( add_query_arg( 'wishlist_id', $this->get_id(), YITH_WCWL()->get_wishlist_url( 'manage' ) ), 'yith_wcwl_delete_action', 'yith_wcwl_delete' ), $this );
		}

		/* === SETTERS === */

		/**
		 * Set wishlist token
		 *
		 * @param string $token Wishlist unique token.
		 */
		public function set_token( $token ) {
			$this->token = (string) $token;
		}

		/**
		 * Set privacy visibility
		 *
		 * @param int $privacy Wishlist visibility (0 => public, 1 => shared, 2 => private).
		 */
		public function set_privacy( $privacy ) {
			$this->set_prop( 'privacy', $privacy );
		}

		/**
		 * Set owner id
		 *
		 * @param int $user_id Wishlist owner id.
		 */
		public function set_user_id( $user_id ) {
			$this->set_prop( 'user_id', $user_id );
		}

		/**
		 * Set session id
		 *
		 * @param int $session_id Wishlist session.
		 */
		public function set_session_id( $session_id ) {
			$this->set_prop( 'session_id', $session_id );
		}

		/**
		 * Set wishlist name
		 *
		 * @param string $name Wishlist name.
		 */
		public function set_name( $name ) {
			$this->set_prop( 'name', $name );
		}

		/**
		 * Set wishlist slug
		 *
		 * @param string $slug Wishlist slug.
		 */
		public function set_slug( $slug ) {
			$this->set_prop( 'slug', substr( $slug, 0, 200 ) );
		}

		/**
		 * Set if wishlist is default one for the user
		 *
		 * @param bool $is_default Whether wishlist is default one or not.
		 */
		public function set_is_default( $is_default ) {
			$this->set_prop( 'is_default', $is_default );
		}

		/**
		 * Set wishlist date added
		 *
		 * @param int|string $date_added Wishlist date of creation (timestamp or date).
		 */
		public function set_date_added( $date_added ) {
			$this->set_date_prop( 'date_added', $date_added );
		}

		/**
		 * Set wishlist date added
		 *
		 * @param int|string $expiration Wishlist date of creation (timestamp or date).
		 */
		public function set_expiration( $expiration ) {
			$this->set_date_prop( 'expiration', $expiration );
		}

		/**
		 * Sets a prop for a setter method.
		 *
		 * This stores changes in a special array so we can track what needs saving
		 * the the DB later.
		 *
		 * @since 3.0.0
		 * @param string $prop Name of prop to set.
		 * @param mixed  $value Value of the prop.
		 */
		protected function set_prop( $prop, $value ) {
			parent::set_prop( $prop, $value );

			if ( 'name' === $prop ) {
				$this->set_slug( sanitize_title_with_dashes( $this->get_name() ) );
			}
		}

		/* === CRUD METHODS === */

		/**
		 * Save data to the database.
		 *
		 * @since 3.0.0
		 * @return int order ID
		 */
		public function save() {
			if ( $this->data_store ) {
				// Trigger action before saving to the DB. Allows you to adjust object props before save.
				do_action( 'woocommerce_before_' . $this->object_type . '_object_save', $this, $this->data_store );

				if ( $this->get_id() ) {
					$this->data_store->update( $this );
				} else {
					$this->data_store->create( $this );
				}
			}
			$this->save_items();
			return $this->get_id();
		}

		/* === ITEM METHODS === */

		/**
		 * Returns true when wishlist is non-empty
		 *
		 * @return bool Whether wishlist is empty or not
		 */
		public function has_items() {
			$items = $this->get_items();

			return ! empty( $items );
		}

		/**
		 * Return an array of items/products within this wishlist.
		 *
		 * @param int $limit  When differs from 0, method will return at most this number of items.
		 * @param int $offset When @see $limit is set, this will be used as offset to retrieve items.
		 *
		 * @return YITH_WCWL_Wishlist_Item[]
		 */
		public function get_items( $limit = 0, $offset = 0 ) {
			if ( ! $this->items ) {
				$this->items = array_filter( $this->data_store->read_items( $this ) );
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_wishlist_get_items
			 *
			 * Filter the wishlist items.
			 *
			 * @param array              $wishlist_items Wishlist items
			 * @param YITH_WCWL_Wishlist $wishlist       Wishlist object
			 *
			 * @return string
			 */
			$items = apply_filters( 'yith_wcwl_wishlist_get_items', $this->items, $this );

			if ( $limit ) {
				$items = array_slice( $items, $offset, $limit );
			}

			return $items;
		}

		/**
		 * Save all wishlist items which are part of this wishlist.
		 *
		 * @return void
		 */
		protected function save_items() {
			foreach ( $this->items_to_delete as $item ) {
				$item->delete();
			}
			$this->items_to_delete = array();

			// Add/save items.
			foreach ( $this->items as $product_id => $item ) {
				if ( $item->get_wishlist_id() !== $this->get_id() ) {
					$item->set_wishlist_id( $this->get_id() );
				}

				$item->save();
			}
		}

		/**
		 * Check whether a product is already in list
		 *
		 * @param int $product_id Product id.
		 * @return bool Whether product is already in list
		 */
		public function has_product( $product_id ) {
			$product_id = yith_wcwl_object_id( $product_id, 'product', true, 'default' );

			return array_key_exists( $product_id, $this->get_items() );
		}

		/**
		 * Retrieves a product from the list (if set)
		 *
		 * @param int $product_id Product ID.
		 * @return \YITH_WCWL_Wishlist_Item|bool Item on success, false on failure
		 */
		public function get_product( $product_id ) {
			$product_id = yith_wcwl_object_id( $product_id, 'product', true, 'default' );

			if ( ! $this->has_product( $product_id ) ) {
				return false;
			}

			$items = $this->get_items();
			return $items[ $product_id ];
		}

		/**
		 * Add a product to the list
		 *
		 * @param int $product_id Product id.
		 *
		 * @return \YITH_WCWL_Wishlist_Item|bool Item on success; false on failure
		 */
		public function add_product( $product_id ) {
			$product_id = yith_wcwl_object_id( $product_id, 'product', true, 'default' );

			$product = wc_get_product( $product_id );

			if ( ! $product || $this->has_product( $product_id ) ) {
				return false;
			}

			try {
				$item = new YITH_WCWL_Wishlist_Item();
				$item->set_product_id( $product_id );
				$item->set_wishlist_id( $this->get_id() );
				$item->set_user_id( $this->get_user_id() );

				$this->items[ $product_id ] = $item;

				return $item;
			} catch ( Exception $e ) {
				return false;
			}
		}

		/**
		 * Remove product from the list
		 *
		 * @param int $product_id Product id.
		 * @return bool Status of the operation
		 */
		public function remove_product( $product_id ) {
			$product_id = yith_wcwl_object_id( $product_id, 'product', true, 'default' );

			if ( ! $this->has_product( $product_id ) ) {
				return false;
			}

			$this->items_to_delete[] = $this->items[ $product_id ];
			unset( $this->items[ $product_id ] );

			return true;
		}

		/**
		 * Check whether an item is already in list (by item id)
		 *
		 * @param int $item_id Item id.
		 * @return bool Whether item is already in list
		 */
		public function has_item( $item_id ) {
			return in_array( (int) $item_id, array_column( $this->get_items(), 'id' ), true );
		}

		/**
		 * Retrieve a specific item of the list, by item id
		 *
		 * @param int $item_id Item id.
		 * @return \YITH_WCWL_Wishlist_Item|bool Item to retrieve, or false on error
		 */
		public function get_item( $item_id ) {
			if ( ! $this->has_item( $item_id ) ) {
				return false;
			}

			$items = array_combine( array_column( $this->get_items(), 'id' ), $this->get_items() );
			return $items[ $item_id ];
		}

		/**
		 * Add new item to the list
		 *
		 * @param \YITH_WCWL_Wishlist_Item $item Wishlist item.
		 * @return \YITH_WCWL_Wishlist_Item|bool Item on success; false on failure
		 */
		public function add_item( $item ) {
			if ( ! $item->get_product_id() || $this->has_item( $item->get_id() ) ) {
				return false;
			}

			$item->set_wishlist_id( $this->get_id() );
			$item->set_user_id( $this->get_user_id() );

			$this->items[ $item->get_product_id() ] = $item;
			return $item;
		}

		/**
		 * Remove item from the list
		 *
		 * @param int $item_id Item id.
		 * @return bool status of the operation
		 */
		public function remove_item( $item_id ) {
			if ( ! $this->has_item( $item_id ) ) {
				return false;
			}

			$item = $this->get_item( $item_id );

			$this->items_to_delete[] = $item;
			unset( $this->items[ $item->get_product_id() ] );

			return true;
		}

		/* === ARRAY ACCESS METHODS === */

		/**
		 * OffsetSet for ArrayAccess.
		 *
		 * @param string $offset Offset.
		 * @param mixed  $value  Value.
		 */
		#[\ReturnTypeWillChange]
		public function offsetSet( $offset, $value ) {
			$offset = $this->map_legacy_offsets( $offset );

			if ( array_key_exists( $offset, $this->data ) ) {
				$setter = "set_$offset";
				if ( is_callable( array( $this, $setter ) ) ) {
					$this->$setter( $value );
				}
			}
		}

		/**
		 * OffsetUnset for ArrayAccess.
		 *
		 * @param string $offset Offset.
		 */
		#[\ReturnTypeWillChange]
		public function offsetUnset( $offset ) {
			$offset = $this->map_legacy_offsets( $offset );

			if ( array_key_exists( $offset, $this->data ) ) {
				unset( $this->data[ $offset ] );
			}

			if ( array_key_exists( $offset, $this->changes ) ) {
				unset( $this->changes[ $offset ] );
			}
		}

		/**
		 * OffsetExists for ArrayAccess.
		 *
		 * @param string $offset Offset.
		 * @return bool
		 */
		#[\ReturnTypeWillChange]
		public function offsetExists( $offset ) {
			$offset = $this->map_legacy_offsets( $offset );

			$getter = "get_$offset";
			if ( is_callable( array( $this, $getter ) ) ) {
				return true;
			}

			return false;
		}

		/**
		 * OffsetGet for ArrayAccess.
		 *
		 * @param string $offset Offset.
		 * @return mixed
		 */
		#[\ReturnTypeWillChange]
		public function offsetGet( $offset ) {
			$offset = $this->map_legacy_offsets( $offset );

			$getter = "get_$offset";
			if ( is_callable( array( $this, $getter ) ) ) {
				return $this->$getter();
			}

			return null;
		}

		/**
		 * Map legacy indexes to new properties, for ArrayAccess
		 *
		 * @param string $offset Offset to search.
		 * @return string Mapped offset
		 */
		protected function map_legacy_offsets( $offset ) {
			$legacy_offset = $offset;

			if ( false !== strpos( $offset, 'wishlist_' ) ) {
				$offset = str_replace( 'wishlist_', '', $offset );
			}

			if ( 'dateadded' === $offset ) {
				$offset = 'date_added';
			}

			if ( 'first_name' === $offset ) {
				$offset = 'user_first_name';
			}

			if ( 'last_name' === $offset ) {
				$offset = 'user_last_name';
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_wishlist_map_legacy_offsets
			 *
			 * Filter the wishlist legacy offsets to index to new properties.
			 *
			 * @param string $offset        Offset to search
			 * @param string $legacy_offset Legacy offset
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_wishlist_map_legacy_offsets', $offset, $legacy_offset );
		}
	}
}
