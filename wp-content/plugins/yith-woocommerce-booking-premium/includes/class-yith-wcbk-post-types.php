<?php
/**
 * Class YITH_WCBK_Post_Types
 * Post Types handler.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Post_Types' ) ) {
	/**
	 * YITH_WCBK_Post_Types class.
	 */
	class YITH_WCBK_Post_Types {
		const BOOKING     = 'yith_booking';
		const PERSON_TYPE = 'ywcbk-person-type';
		const SEARCH_FORM = 'ywcbk-search-form';
		const EXTRA_COST  = 'ywcbk-extra-cost';
		const RESOURCE    = 'ywcbk-resource';
		const SERVICE_TAX = 'yith_booking_service';

		/**
		 * Booking Post Type
		 *
		 * @var string
		 * @deprecated 3.0.0 | use YITH_WCBK_Post_Types::BOOKING instead
		 */
		public static $booking = self::BOOKING;

		/**
		 * Person Type Post Type
		 *
		 * @var string
		 * @deprecated 3.0.0 | use YITH_WCBK_Post_Types::PERSON_TYPE instead
		 */
		public static $person_type = self::PERSON_TYPE;

		/**
		 * Search Form Post Type
		 *
		 * @var string
		 * @deprecated 3.0.0 | use YITH_WCBK_Post_Types::SEARCH_FORM instead
		 */
		public static $search_form = self::SEARCH_FORM;

		/**
		 * Extra Cost Post Type
		 *
		 * @var string
		 * @deprecated 3.0.0 | use YITH_WCBK_Post_Types::EXTRA_COST instead
		 */
		public static $extra_cost = self::EXTRA_COST;

		/**
		 * Service Tax
		 *
		 * @var string
		 * @deprecated 3.0.0 | use YITH_WCBK_Post_Types::SERVICE_TAX instead
		 */
		public static $service_tax = self::SERVICE_TAX;

		/**
		 * Let's init the post types, post statuses, taxonomies and data stores.
		 */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
			add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );

			add_filter( 'woocommerce_data_stores', array( __CLASS__, 'register_data_stores' ), 10, 1 );

			add_action( 'yith_plugin_fw_loaded', array( __CLASS__, 'include_admin_handlers' ) );

			add_filter( 'wp_untrash_post_status', array( __CLASS__, 'untrash_post_status' ), 10, 3 );
			add_action( 'before_delete_post', array( __CLASS__, 'before_delete_post' ), 10, 1 );
			add_action( 'deleted_post', array( __CLASS__, 'deleted_post' ), 10, 2 );
			add_action( 'trashed_post', array( __CLASS__, 'updated_trashed_post_status' ) );
			add_action( 'untrashed_post', array( __CLASS__, 'updated_trashed_post_status' ) );

			add_action( 'admin_action_yith-wcbk-add-new-post', array( __CLASS__, 'handle_new_post_creation' ) );

			add_action( 'save_post', array( __CLASS__, 'save_meta_boxes' ), 1, 2 );
			add_action( 'dbx_post_sidebar', array( __CLASS__, 'add_nonce_in_edit_page' ), 10, 1 );
		}

		/**
		 * Include Admin Post Type and Taxonomy handlers.
		 */
		public static function include_admin_handlers() {
			require_once trailingslashit( YITH_WCBK_INCLUDES_PATH ) . 'admin/post-types/abstract-yith-wcbk-post-type-admin.php';

			require_once trailingslashit( YITH_WCBK_INCLUDES_PATH ) . 'admin/post-types/class-yith-wcbk-booking-post-type-admin.php';

			do_action( 'yith_wcbk_admin_post_type_handlers_loaded' );
		}

		/**
		 * Register core post types.
		 */
		public static function register_post_types() {
			if ( post_type_exists( self::BOOKING ) ) {
				return;
			}

			do_action( 'yith_wcbk_register_post_type' );

			// Booking -----------------------------------------------------------.
			$labels = array(
				'name'               => __( 'All Bookings', 'yith-booking-for-woocommerce' ),
				'singular_name'      => __( 'Booking', 'yith-booking-for-woocommerce' ),
				'add_new'            => __( 'Add Booking', 'yith-booking-for-woocommerce' ),
				'add_new_item'       => __( 'Add New Booking', 'yith-booking-for-woocommerce' ),
				'edit'               => __( 'Edit', 'yith-booking-for-woocommerce' ),
				'edit_item'          => __( 'Edit Booking', 'yith-booking-for-woocommerce' ),
				'new_item'           => __( 'New Booking', 'yith-booking-for-woocommerce' ),
				'view'               => __( 'View Booking', 'yith-booking-for-woocommerce' ),
				'view_item'          => __( 'View Booking', 'yith-booking-for-woocommerce' ),
				'search_items'       => __( 'Search Bookings', 'yith-booking-for-woocommerce' ),
				'not_found'          => __( 'No bookings found', 'yith-booking-for-woocommerce' ),
				'not_found_in_trash' => __( 'No bookings found in trash', 'yith-booking-for-woocommerce' ),
				'parent'             => __( 'Parent Bookings', 'yith-booking-for-woocommerce' ),
				'menu_name'          => _x( 'Bookings', 'Admin menu name', 'yith-booking-for-woocommerce' ),
				'all_items'          => __( 'All Bookings', 'yith-booking-for-woocommerce' ),
			);

			$booking_post_type_args = array(
				'label'               => __( 'Booking', 'yith-booking-for-woocommerce' ),
				'labels'              => $labels,
				'description'         => __( 'This is where bookings are stored.', 'yith-booking-for-woocommerce' ),
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => self::BOOKING,
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_in_menu'        => false,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( '' ),
				'has_archive'         => false,
				'menu_icon'           => 'dashicons-calendar',
			);

			register_post_type( self::BOOKING, $booking_post_type_args );
		}

		/**
		 * Register our custom post statuses, used for order status.
		 */
		public static function register_post_status() {
			$singulars = array(
				'unpaid'          => _nx( 'Unpaid', 'Unpaid', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
				'paid'            => _nx( 'Paid', 'Paid', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
				'completed'       => _nx( 'Completed', 'Completed', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
				'cancelled'       => _nx( 'Cancelled', 'Cancelled', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
				'pending-confirm' => _nx( 'Pending', 'Pending', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
				'confirmed'       => _nx( 'Confirmed', 'Confirmed', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
				'unconfirmed'     => _nx( 'Rejected', 'Rejected', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
			);
			$plurals   = array(
				'unpaid'          => _nx( 'Unpaid', 'Unpaid', 2, 'Booking status', 'yith-booking-for-woocommerce' ),
				'paid'            => _nx( 'Paid', 'Paid', 2, 'Booking status', 'yith-booking-for-woocommerce' ),
				'completed'       => _nx( 'Completed', 'Completed', 2, 'Booking status', 'yith-booking-for-woocommerce' ),
				'cancelled'       => _nx( 'Cancelled', 'Cancelled', 2, 'Booking status', 'yith-booking-for-woocommerce' ),
				'pending-confirm' => _nx( 'Pending', 'Pending', 2, 'Booking status', 'yith-booking-for-woocommerce' ),
				'confirmed'       => _nx( 'Confirmed', 'Confirmed', 2, 'Booking status', 'yith-booking-for-woocommerce' ),
				'unconfirmed'     => _nx( 'Rejected', 'Rejected', 2, 'Booking status', 'yith-booking-for-woocommerce' ),
			);

			foreach ( yith_wcbk_get_booking_statuses() as $status_slug => $status_label ) {
				$count    = ' <span class="count">(%s)</span>';
				$singular = $singulars[ $status_slug ] ?? $status_label;
				$plural   = $plurals[ $status_slug ] ?? $status_label;

				$singular .= $count;
				$plural   .= $count;

				$label_count = array(
					0          => $singular,
					1          => $plural,
					'singular' => $singular,
					'plural'   => $plural,
					'context'  => 'No translate',
					'domain'   => 'yith-booking-for-woocommerce',
				);

				$status_slug = 'bk-' . $status_slug;
				$options     = array(
					'label'                     => $status_label,
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => $label_count,
				);

				register_post_status( $status_slug, $options );
			}
		}

		/**
		 * Register core taxonomies.
		 *
		 * @deprecated 4.0.0
		 */
		public static function register_taxonomies() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Post_Types::register_taxonomies', '4.0.0' );
		}

		/**
		 * Add capabilities to Admin and Shop Manager
		 */
		public static function add_capabilities() {
			$capability_types = array(
				self::BOOKING          => 'post',
				'yith_create_booking'  => 'single',
				'yith_manage_bookings' => 'single',
			);

			foreach ( $capability_types as $object_type => $type ) {
				$caps = yith_wcbk_get_capabilities( $type, $object_type );
				if ( self::BOOKING === $object_type ) {
					unset( $caps['create_posts'] );
				}

				yith_wcbk_add_capabilities( $caps );
			}
		}

		/**
		 * Register data stores
		 *
		 * @param array $data_stores WooCommerce Data Stores.
		 *
		 * @return array
		 */
		public static function register_data_stores( $data_stores ) {
			$data_stores['product-booking']                    = 'YITH_WCBK_Product_Booking_Data_Store_CPT';
			$data_stores['yith-booking']                       = 'YITH_WCBK_Booking_Data_Store';
			$data_stores['yith-wcbk-global-availability-rule'] = 'YITH_WCBK_Global_Availability_Rule_Data_Store';
			$data_stores['yith-wcbk-global-price-rule']        = 'YITH_WCBK_Global_Price_Rule_Data_Store';

			return $data_stores;
		}

		/**
		 * Ensure statuses are correctly reassigned when restoring CPT.
		 *
		 * @param string $new_status      The new status of the post being restored.
		 * @param int    $post_id         The ID of the post being restored.
		 * @param string $previous_status The status of the post at the point where it was trashed.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public static function untrash_post_status( $new_status, $post_id, $previous_status ) {
			$post_types = array( self::BOOKING, self::SEARCH_FORM, self::EXTRA_COST, self::PERSON_TYPE );

			if ( in_array( get_post_type( $post_id ), $post_types, true ) ) {
				$new_status = $previous_status;
			}

			return $new_status;
		}

		/**
		 * Clean product cache when deleting booking object.
		 *
		 * @param int $id ID of post being deleted.
		 *
		 * @since 5.0.0
		 */
		public static function before_delete_post( $id ) {
			if ( ! $id ) {
				return;
			}

			$post_type = get_post_type( $id );

			switch ( $post_type ) {
				case self::BOOKING:
					$booking = yith_get_booking( $id );
					if ( $booking ) {
						yith_wcbk_regenerate_product_data( $booking->get_product_id() );
					}
					break;
			}
		}

		/**
		 * Removes deleted bookings from lookup table.
		 *
		 * @param int     $id   ID of post being deleted.
		 * @param WP_Post $post The post being deleted.
		 *
		 * @throws Exception If the data store loading fails.
		 * @since 3.0.0
		 */
		public static function deleted_post( $id, $post = false ) {
			if ( ! $id ) {
				return;
			}

			if ( ! $post ) {
				// The $post arg was added to 'deleted_post' action in WordPress 5.5.
				// TODO: remove this line when removing support for WordPress < 5.5.
				$post = get_post( $id );
			}

			if ( ! $post ) {
				return;
			}

			$post_type = $post->post_type;

			switch ( $post_type ) {
				case self::BOOKING:
					/**
					 * The Booking Data Store
					 *
					 * @var YITH_WCBK_Booking_Data_Store $data_store
					 */
					$data_store = WC_Data_Store::load( 'yith-booking' );
					$data_store->delete_from_lookup_table( $id, YITH_WCBK_DB::BOOKING_META_LOOKUP_TABLE );
					break;
			}
		}

		/**
		 * Update status for trashed/un-trashed bookings in lookup table.
		 *
		 * @param mixed $id ID of post being trashed/un-trashed.
		 *
		 * @throws Exception If the data store loading fails.
		 * @since 3.0.0
		 */
		public static function updated_trashed_post_status( $id ) {
			if ( ! $id ) {
				return;
			}

			$post_type = get_post_type( $id );

			switch ( $post_type ) {
				case self::BOOKING:
					/**
					 * The Booking Data Store
					 *
					 * @var YITH_WCBK_Booking_Data_Store $data_store
					 */
					$data_store = WC_Data_Store::load( 'yith-booking' );
					$data_store->update_booking_meta_lookup_table( $id );

					$booking = yith_get_booking( $id );
					if ( $booking ) {
						yith_wcbk_regenerate_product_data( $booking->get_product_id() );
					}
					break;
			}
		}

		/**
		 * Handle new post creation.
		 *
		 * @since 3.0.0
		 */
		public static function handle_new_post_creation() {
			if (
				isset( $_REQUEST['yith-wcbk-add-new-post-nonce'], $_REQUEST['post_type'] ) &&
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['yith-wcbk-add-new-post-nonce'] ) ), 'yith-wcbk-add-new-post' )
			) {
				$post_type  = sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) );
				$post_types = array( self::EXTRA_COST, self::PERSON_TYPE );

				if ( in_array( $post_type, $post_types, true ) ) {
					$post_type_object = get_post_type_object( $post_type );

					if ( $post_type_object && current_user_can( $post_type_object->cap->create_posts ) ) {
						$name        = sanitize_text_field( wp_unslash( $_REQUEST['name'] ?? '' ) );
						$description = sanitize_textarea_field( wp_unslash( $_REQUEST['description'] ?? '' ) );

						if ( $name ) {
							$post_id = wp_insert_post(
								array(
									'post_title'   => wp_slash( $name ),
									'post_type'    => $post_type,
									'post_status'  => 'publish',
									'post_content' => wp_slash( $description ),
								)
							);

							if ( $post_id ) {
								$redirect_url = add_query_arg( array( 'post_type' => $post_type ), admin_url( 'edit.php' ) );
								wp_safe_redirect( $redirect_url );
								exit;
							}
						}
					}
				}
			}

			wp_die( esc_html__( 'Something went wrong. Try again!', 'yith-booking-for-woocommerce' ) );
		}

		/**
		 * Check if we're saving, the trigger an action based on the post type.
		 *
		 * @param int    $post_id Post ID.
		 * @param object $post    Post object.
		 */
		public static function save_meta_boxes( $post_id, $post ) {
			static $saved = false;

			$post_id = absint( $post_id );

			// $post_id and $post are required
			if ( empty( $post_id ) || empty( $post ) || $saved ) {
				return;
			}

			// Dont' save meta boxes for revisions or auto-saves.
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}

			// Check the nonce.
			if ( empty( $_POST['yith_wcbk_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yith_wcbk_meta_nonce'] ) ), 'yith_wcbk_save_data' ) ) {
				return;
			}

			// Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
			if ( empty( $_POST['post_ID'] ) || absint( $_POST['post_ID'] ) !== $post_id ) {
				return;
			}

			// Check user has permission to edit.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// We need this save event to run once to avoid potential endless loops.
			$saved = true;

			// Check the post type.
			if ( in_array( $post->post_type, self::get_post_types(), true ) ) {
				$key = array_flip( self::get_post_types() )[ $post->post_type ];
				do_action( 'yith_wcbk_post_process_' . $key . '_meta', $post_id, $post );
			}
		}

		/**
		 * Print save button in edit page.
		 *
		 * @param WP_Post $post The post.
		 */
		public static function add_nonce_in_edit_page( $post ) {
			if ( ! ! $post && isset( $post->post_type ) && in_array( $post->post_type, self::get_post_types(), true ) ) {
				self::meta_box_nonce_field();
			}
		}

		/**
		 * Print the meta-box nonce field for saving meta.
		 */
		public static function meta_box_nonce_field() {
			wp_nonce_field( 'yith_wcbk_save_data', 'yith_wcbk_meta_nonce' );
		}

		/**
		 * Retrieve post types handled by the plugin.
		 *
		 * @return mixed|void
		 */
		public static function get_post_types() {
			return apply_filters(
				'yith_wcbk_post_types',
				array(
					'booking' => self::BOOKING,
				)
			);
		}
	}
}
