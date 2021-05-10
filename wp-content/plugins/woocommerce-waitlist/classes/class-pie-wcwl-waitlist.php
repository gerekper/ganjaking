<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Waitlist' ) ) {
	/**
	 * Pie_WCWL_Waitlist
	 *
	 * @package WooCommerce Waitlist
	 */
	class Pie_WCWL_Waitlist {

		/**
		 * Array of user IDs on the current waitlist
		 *
		 * @var array
		 */
		public $waitlist;
		/**
		 * An array of user objects
		 *
		 * @var array
		 */
		public $users;
		/**
		 * Current product object
		 *
		 * @var WC_Product
		 */
		public $product;
		/**
		 * Product unique ID
		 *
		 * @var int
		 * @access public
		 */
		public $product_id;
		/**
		 * Array of the products parents. This could be variable/grouped or both
		 *
		 * @var array
		 * @access public
		 */
		public $parent_ids;

		/**
		 * Constructor function to hook up actions and filters and class properties
		 *
		 * @param $product
		 *
		 * @access   public
		 */
		public function __construct( $product ) {
			$this->product = $product;
			$this->setup_product_ids( $product );
			$this->setup_waitlist();
		}

		/**
		 * Setup product class variables
		 *
		 * @param $product
		 *
		 * @access   public
		 */
		public function setup_product_ids( $product ) {
			$this->product_id = $product->get_id();
			$this->parent_ids = $this->get_parent_id( $product );
		}

		/**
		 * Retrieves an array of parent product IDs based on current WooCommerce version
		 *
		 * @param WC_Product $product product object
		 *
		 * @since 1.8.0
		 *
		 * @return array parent IDs
		 */
		public function get_parent_id( WC_Product $product ) {
			$parent_ids = array();
			if ( $parent_id = $product->get_parent_id() ) {
				$parent_ids[] = $parent_id;
			}
			$parent_ids = array_merge( $parent_ids, $this->get_grouped_parent_id( $product ) );

			return $parent_ids;
		}

		/**
		 * Check all grouped products to see if they have this product as a child product
		 *
		 * @param WC_Product $product
		 *
		 * @return array
		 */
		public function get_grouped_parent_id( WC_Product $product ) {
			$parent_products  = array();
			$args             = array(
				'type'  => 'grouped',
				'limit' => - 1,
			);
			$grouped_products = wc_get_products( $args );
			foreach ( $grouped_products as $grouped_product ) {
				foreach ( $grouped_product->get_children() as $child_id ) {
					if ( $child_id == $product->get_id() ) {
						$parent_products[] = $grouped_product->get_id();
					}
				}
			}

			return $parent_products;
		}

		/**
		 * Setup waitlist array
		 *
		 * Adjust old meta to new format ( $waitlist[user_id] = date_added )
		 *
		 * @access public
		 * @return void
		 */
		public function setup_waitlist() {
			$waitlist = get_post_meta( $this->product_id, WCWL_SLUG, true );
			if ( ! is_array( $waitlist ) || empty( $waitlist ) ) {
				$this->waitlist = array();
			} else {
				if ( $this->waitlist_has_new_meta() ) {
					$this->load_waitlist( $waitlist, 'new' );
				} else {
					$this->load_waitlist( $waitlist, 'old' );
				}
			}
			add_action( 'wcwl_after_add_email_to_waitlist', array( $this, 'email_customer_joined_waitlist' ), 10, 2 );
			add_action( 'wcwl_after_add_email_to_waitlist', array( $this, 'email_admin_user_joined_waitlist' ), 10, 2 );
		}

		/**
		 * Check if waitlist has been updated to the new meta format
		 *
		 * @return bool
		 */
		public function waitlist_has_new_meta() {
			$has_dates = get_post_meta( $this->product_id, WCWL_SLUG . '_has_dates', true );
			if ( $has_dates ) {
				return true;
			}

			return false;
		}

		/**
		 * Load up waitlist
		 *
		 * Meta has changed to incorporate the date added for each user so a check is required
		 * If waitlist has old meta we want to bring this up to speed
		 *
		 * @param $waitlist
		 * @param $type
		 */
		public function load_waitlist( $waitlist, $type ) {
			if ( 'old' == $type ) {
				foreach ( $waitlist as $user ) {
					$this->waitlist[ $user ] = 'unknown';
				}
			} else {
				$this->waitlist = $waitlist;
			}
		}

		/**
		 * For some bizarre reason around 1.2.0, this function has started emitting notices. It is caused by the original
		 * assignment of WCWL_Frontend_UI->User being set to false when a user is not logged in. All around the application,
		 * this is now being called on as an object.
		 *
		 * @param $email
		 *
		 * @return bool Whether or not the User is registered to this waitlist, if they are a valid user
		 *
		 * @access   public
		 */
		public function user_is_registered( $email ) {
			if ( array_key_exists( $email, $this->waitlist ) ) {
				return true;
			}
			if ( is_email( $email ) ) {
				$user = get_user_by( 'email', $email );
				if ( $user && array_key_exists( $user->ID, $this->waitlist ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Remove user from the current waitlist
		 * "wcwl_after_remove_user_from_waitlist" is now deprecated
		 * "wcwl_after_remove_email_from_waitlist" should be used instead
		 *
		 * @param $email
		 *
		 * @return WP_Error/string error or success message
		 *
		 * @access   public
		 */
		public function unregister_user( $email ) {
			if ( $email ) {
				if ( $this->user_is_registered( $email ) ) {
					$user = get_user_by( 'email', $email );
					if ( $user ) {
						if ( isset( $this->waitlist[ $user->ID ] ) ) {
							do_action( 'wcwl_before_remove_user_from_waitlist', $this->product_id, $user );
							unset( $this->waitlist[ $user->ID ] );
						}
					}
					if ( isset( $this->waitlist[ $email ] ) ) {
						do_action( 'wcwl_before_remove_email_from_waitlist', $this->product_id, $email );
						unset( $this->waitlist[ $email ] );
					}
					$this->save_waitlist();
					$this->update_waitlist_count( 'remove' );
					if ( $user ) {
						do_action( 'wcwl_after_remove_user_from_waitlist', $this->product_id, $user );
					}
					do_action( 'wcwl_after_remove_email_from_waitlist', $this->product_id, $email );

					return apply_filters( 'wcwl_leave_waitlist_success_message_text', __( 'You have been removed from the waitlist for this product', 'woocommerce-waitlist' ) );
				} else {
					return new WP_Error( 'wcwl_error', __( 'The provided email address is not registered on the waitlist for this product', 'woocommerce-waitlist' ) );
				}
			}

			return new WP_Error( 'wcwl_error', __( 'Invalid user', 'woocommerce-waitlist' ) );
		}

		/**
		 * For some bizarre reason around 1.2.0, this function has started emitting notices. It is caused by the original
		 * assignment of WCWL_Frontend_UI->User being set to false when a user is not logged in. All around the application,
		 * this is now being called on as an object.
		 * "wcwl_before_add_user_to_waitlist" and "wcwl_after_add_user_to_waitlist" are now deprecated
		 * "wcwl_before_add_email_to_waitlist" and "wcwl_after_add_email_to_waitlist" should be used instead
		 *
		 * @param $email
		 * @param string $lang
		 *
		 * @return bool
		 *
		 * @access   public
		 */
		public function register_user( $email, $lang = '' ) {
			if ( $email ) {
				if ( ! $this->user_is_registered( $email ) ) {
					do_action( 'wcwl_before_add_email_to_waitlist', $this->product_id, $email );
					$user_id = email_exists( $email );
					if ( ! $user_id && 'no' !== get_option( 'woocommerce_waitlist_create_account' ) ) {
						$user_id = $this->create_customer( $email );
						if ( is_wp_error( $user_id ) ) {
							return $user_id->get_error_message();
						}
					}
					if ( $user_id ) {
						  $user = get_user_by( 'id', $user_id );
						  do_action( 'wcwl_before_add_user_to_waitlist', $this->product_id, $user );
						  $this->waitlist[ $user_id ] = strtotime( 'now' );
					} else {
							$this->waitlist[ $email ] = strtotime( 'now' );
					}
					$this->update_user_chosen_language_for_product( $email, $lang );
					$this->save_waitlist();
					$this->update_waitlist_count( 'add' );
					if ( isset( $user ) && $user ) {
						do_action( 'wcwl_after_add_user_to_waitlist', $this->product_id, $user );
					}
					do_action( 'wcwl_after_add_email_to_waitlist', $this->product_id, $email );

					return apply_filters( 'wcwl_join_waitlist_success_message_text', __( 'You have been added to the waitlist for this product', 'woocommerce-waitlist' ) );
				} else {
					return apply_filters( 'wcwl_join_waitlist_already_joined_message_text', __( 'The email provided is already on the waitlist for this product', 'woocommerce-waitlist' ) );
				}
			}

			return new WP_Error( 'wcwl_error', __( 'Invalid user', 'woocommerce-waitlist' ) );
		}

		/**
		 * Create a new customer if required
		 *
		 * @param string $email
		 * @return void/int
		 */
		public function create_customer( $email ) {
			add_filter( 'pre_option_woocommerce_registration_generate_password', array( WooCommerce_Waitlist_Plugin::instance(), 'return_option_setting_yes' ), 10 );
			add_filter( 'pre_option_woocommerce_registration_generate_username', array( WooCommerce_Waitlist_Plugin::instance(), 'return_option_setting_yes' ), 10 );
			$user_id = wc_create_new_customer( $email );
			remove_filter( 'pre_option_woocommerce_registration_generate_password', array( WooCommerce_Waitlist_Plugin::instance(), 'return_option_setting_yes' ), 10 );
			remove_filter( 'pre_option_woocommerce_registration_generate_username', array( WooCommerce_Waitlist_Plugin::instance(), 'return_option_setting_yes' ), 10 );
			if ( is_wp_error( $user_id ) ) {
				wcwl_add_log( $user_id->get_error_message(), '', $email );
			} else {
				do_action( 'wcwl_customer_created', $user_id );
				$this->maybe_login_customer( $user_id );
			}
			return $user_id;
		}

		/**
		 * Login customer automatically after creation if option is selected
		 *
		 * @param int $user_id
		 * @return void
		 */
		public function maybe_login_customer( $user_id ) {
			if ( 'wcwl_process_user_waitlist_request' === $_POST['action'] && 'yes' === get_option( 'woocommerce_waitlist_auto_login' ) ) {
				wp_set_auth_cookie( $user_id );
			}
		}

		/**
		 * Email customer when they sign up to a waitlist
		 *
		 * @param int $product_id
		 * @param string $email
		 * @return void
		 */
		public function email_customer_joined_waitlist( $product_id, $email ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				WC_Emails::instance();
				do_action( 'wcwl_joined_mailout_send_email', $email, $product_id );
			}
		}

		/**
		 * Email customer when they leave a waitlist
		 *
		 * @param int $product_id
		 * @param string $email
		 * @return void
		 */
		public function email_customer_left_waitlist( $product_id, $email ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				WC_Emails::instance();
				do_action( 'wcwl_leave_mailout_send_email', $email, $product_id );
			}
		}

		/**
		 * Email the site admin when a user joins a waitlist
		 * Action hook "wcwl_new_signup_send_email" is now deprecated
		 * "wcwl_new_signup_send_admin_email" should be used instead
		 *
		 * @param $product_id
		 * @param $email
		 *
		 * @since 1.8.0
		 */
		public function email_admin_user_joined_waitlist( $product_id, $email ) {
			if ( $this->product_id !== $product_id ) {
				return;
			}
			$product = wc_get_product( $product_id );
			if ( $product ) {
				WC_Emails::instance();
				$user = get_user_by( 'email', $email );
				if ( $user ) {
					do_action( 'wcwl_new_signup_send_email', $user->ID, $product_id );
				}
				do_action( 'wcwl_new_signup_send_admin_email', $email, $product_id );
			}
		}

		/**
		 * Update the usermeta for the current user to show which language they joined this products waitlist in
		 *
		 * This is used to show the language of the user on the waitlist in the admin and to determine which language the waitlist email should be
		 *
		 * @param $user
		 * @param $lang
		 */
		protected function update_user_chosen_language_for_product( $user, $lang ) {
			if ( function_exists( 'wpml_get_current_language' ) ) {
				$lang_option           = get_option( '_' . WCWL_SLUG . '_languages' ) ? get_option( '_' . WCWL_SLUG . '_languages' ) : array();
				$lang_option[ $user ] = $lang;
				update_option( '_' . WCWL_SLUG . '_languages', $lang_option );
			}
		}

		/**
		 * Save the current waitlist into the database
		 *
		 * Update meta to notify us that meta format has been updated
		 *
		 * @return void
		 */
		public function save_waitlist() {
			update_post_meta( $this->product_id, WCWL_SLUG, $this->waitlist );
			update_post_meta( $this->product_id, WCWL_SLUG . '_has_dates', true );
		}

		/**
		 * Adjust waitlist count in database when a user is registered/unregistered
		 *
		 * @param $type
		 */
		protected function update_waitlist_count( $type ) {
			update_post_meta( $this->product_id, '_' . WCWL_SLUG . '_count', count( $this->waitlist ) );
			if ( ! empty( $this->parent_ids ) ) {
				$this->update_parent_count( $type );
			}
		}

		/**
		 * Update waitlist counts for all parents of current product
		 */
		protected function update_parent_count( $type ) {
			foreach ( $this->parent_ids as $parent_id ) {
				$count = get_post_meta( $parent_id, '_' . WCWL_SLUG . '_count', true );
				if ( 'add' == $type ) {
					$new_count = intval( $count ) + 1;
				} else {
					if ( $count < 1 ) {
						$new_count = 0;
					} else {
						$new_count = intval( $count ) - 1;
					}
				}
				update_post_meta( $parent_id, '_' . WCWL_SLUG . '_count', $new_count );
			}
		}

		/**
		 * Triggers instock notification email to each user on the waitlist for a product
		 *
		 * @access public
		 * @return void
		 */
		public function waitlist_mailout() {
			if ( ! empty( $this->waitlist ) ) {
				do_action( 'wcwl_before_waitlist_notification_emails', $this->waitlist );
				global $woocommerce, $sitepress;
				if ( isset( $sitepress ) ) {
					$this->check_translations_for_waitlist_entries( $this->product_id );
				}
				$woocommerce->mailer();
				foreach ( $this->waitlist as $user => $date_added ) {
					if ( ! is_email( $user ) ) {
						$user_object = get_user_by( 'id', $user );
						if ( $user_object ) {
							$user = $user_object->user_email;
						}
					}
					$response = $this->maybe_do_mailout( $user );
					if ( is_wp_error( $response ) ) {
						$this->add_error_to_waitlist_data( $response, $user );
					} elseif ( $response ) {
						$this->clear_errors_from_waitlist_data( $this->product_id, $user );
						$this->maybe_remove_user( $user );
					}
				}
				do_action( 'wcwl_after_waitlist_notification_emails', $this->waitlist );
			}
		}

		/**
		 * Check that no translation products contain waitlist entries and log a notice if they do
		 *
		 * @param $product_id
		 */
		protected function check_translations_for_waitlist_entries( $product_id ) {
			global $sitepress;
			$translated_products = $sitepress->get_element_translations( $product_id, 'post_product' );
			foreach ( $translated_products as $translated_product ) {
				if ( $product_id == $translated_product->element_id ) {
					continue;
				} else {
					$waitlist = get_post_meta( $translated_product->element_id, WCWL_SLUG, true );
					if ( is_array( $waitlist ) && ! empty( $waitlist ) ) {
						$logger  = wc_get_logger();
		        $context = array( 'source' => 'woocommerce-waitlist' );
						$logger->debug( sprintf( __( 'Woocommerce Waitlist data found for translated product %1$d (main product ID = %2$d)' ), $translated_product->element_id, $product_id ), array( 'source' => 'woocommerce-waitlist' ) );
						update_option( '_' . WCWL_SLUG . '_corrupt_data', true );
					}
				}
			}
		}

		/**
		 * Add the mailout error to the product metadata to show on the waitlist tab
		 *
		 * @param WP_Error $error
		 * @param          $user
		 */
		public function add_error_to_waitlist_data( WP_Error $error, $user ) {
			$errors = get_post_meta( $this->product_id, 'wcwl_mailout_errors', true );
			if ( ! $errors ) {
				$errors = array();
			}
			$errors[ $user ] = $error->get_error_message();
			update_post_meta( $this->product_id, 'wcwl_mailout_errors', $errors );
		}

		/**
		 * If user is emailed successfully, remove any errors for given user
		 *
		 * @param $product_id
		 * @param $user
		 */
		public function clear_errors_from_waitlist_data( $product_id, $user ) {
			$errors = get_post_meta( $product_id, 'wcwl_mailout_errors', true );
			if ( ! $errors || ! isset( $errors[ $user ] ) ) {
				return;
			}
			unset( $errors[ $user ] );
			update_post_meta( $product_id, 'wcwl_mailout_errors', $errors );
		}

		/**
		 * If required, remove the given user from the current waitlist
		 *
		 * @param $email
		 */
		protected function maybe_remove_user( $email ) {
			if ( WooCommerce_Waitlist_Plugin::persistent_waitlists_are_disabled( $this->product_id ) ) {
				$this->unregister_user( $email );
				$this->maybe_add_user_to_archive( $email );
			}
		}

		/**
		 * Add a user to the archive for the current product
		 * This occurs when the user has been emailed and appends their ID to the list of users emailed today
		 *
		 * @param $email
		 *
		 * @return bool|int
		 */
		public function maybe_add_user_to_archive( $email ) {
			if ( 'yes' !== get_option( 'woocommerce_waitlist_archive_on' ) ) {
				return false;
			}
			$existing_archives = get_post_meta( $this->product_id, 'wcwl_waitlist_archive', true );
			if ( ! is_array( $existing_archives ) ) {
				$existing_archives = array();
			}
			$today = strtotime( date( 'Ymd' ) );
			if ( ! isset( $existing_archives[ $today ] ) ) {
				$existing_archives[ $today ] = array();
			}
			$existing_archives[ $today ][ $email ] = $email;

			return update_post_meta( $this->product_id, 'wcwl_waitlist_archive', $existing_archives );
		}

		/**
		 * If required, perform the waitlist mailout for the given user
		 *
		 * @param $email
		 *
		 * @return bool | WP_Error
		 */
		protected function maybe_do_mailout( $email ) {
			if ( WooCommerce_Waitlist_Plugin::automatic_mailouts_are_disabled( $this->product_id ) ) {
				return false;
			}
			if ( $this->user_has_been_emailed( $email ) ) {
				return false;
			}
			if ( 'publish' !== get_post_status( $this->product_id ) ) {
				return false;
			}
			$timeout = apply_filters( 'wcwl_notification_limit_time', 10 );
			set_transient( 'wcwl_done_mailout_' . $email . '_' . $this->product_id, 'yes', $timeout );
			require_once 'class-pie-wcwl-waitlist-mailout.php';
			$mailer = new Pie_WCWL_Waitlist_Mailout();

			return $mailer->trigger( $email, $this->product_id );
		}

		/**
		 * Check whether the user has just been mailed for this product
		 *
		 * @param $email
		 *
		 * @return mixed
		 */
		public function user_has_been_emailed( $email ) {
			return get_transient( 'wcwl_done_mailout_' . $email . '_' . $this->product_id );
		}
	}
}
