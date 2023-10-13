<?php
/**
 * Class YITH_WCBK_Multi_Vendor_Integration
 * Multi Vendor integration
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Multi_Vendor_Integration
 *
 * @since   1.0.7
 * @since   3.6.0 Support to Multi Vendor 4.0
 */
class YITH_WCBK_Multi_Vendor_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Meta to store vendor ID in services.
	 */
	const VENDOR_SERVICE_META = 'yith_shop_vendor';

	/**
	 * The vendor panel page.
	 */
	const PANEL_PAGE = 'yith_wcbk_vendor_panel';

	/**
	 * The option in Multi Vendor plugin.
	 */
	const ACTIVATED_OPTION = 'yith_wpv_vendors_option_booking_management';

	/**
	 * Filter vendor services enabled flag.
	 *
	 * @var bool
	 */
	protected $filter_vendor_services_enabled = true;

	/**
	 * The vendor panel
	 *
	 * @var $panel YIT_Plugin_Panel_WooCommerce
	 */
	protected $panel;

	/**
	 * Init
	 */
	protected function init() {
		// Booking post type.
		add_filter( 'manage_' . YITH_WCBK_Post_Types::BOOKING . '_posts_columns', array( $this, 'remove_vendor_column_in_booking_for_vendors' ), 20 );
		add_filter( 'yith_wcbk_booking_helper_count_booked_bookings_in_period_query_args', array( $this, 'suppress_vendor_filter' ), 10, 1 );

		add_action( 'yith_wcmv_vendor_additional_capabilities', array( $this, 'add_booking_capabilities_for_vendor' ) );

		if ( $this->is_enabled() ) {
			// Admin panel.
			add_action( 'admin_menu', array( $this, 'register_vendor_panel' ), 5 );
			add_filter( 'yith_wcmv_admin_vendor_menu_items', array( $this, 'add_allowed_menu_items' ), 10, 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 99 );
			add_filter( 'yith_wcbk_booking_admin_screen_ids', array( $this, 'add_vendor_panel_to_admin_screen_ids' ), 10, 1 );

			// Bookings.
			add_action( 'yith_wcbk_booking_created', array( $this, 'add_vendor_taxonomy_to_booking' ), 10, 3 );
			add_filter( 'yith_wcmv_vendors_factory_read_vendor_id', array( $this, 'filter_read_vendor_id' ), 10, 4 );

			// Orders.
			add_filter( 'yith_wcbk_order_check_order_for_booking', array( $this, 'not_check_for_booking_in_parent_orders_with_suborders' ), 10, 3 );
			add_action( 'yith_wcmv_checkout_order_processed', array( yith_wcbk()->orders, 'check_order_for_booking' ), 999, 1 ); // check (sub)orders for booking.
			add_filter( 'yith_wcbk_order_bookings_related_to_order', array( $this, 'add_bookings_related_to_suborders' ), 10, 2 );
			add_filter( 'yith_wcbk_booking_details_order_id', array( $this, 'show_parent_order_id' ) );
			add_filter( 'yith_wcbk_email_booking_details_order_id', array( $this, 'show_parent_order_id_in_emails' ), 10, 5 );
			add_filter( 'yith_wcbk_pdf_booking_details_order_id', array( $this, 'show_parent_order_id_in_pdf' ), 10, 3 );

			// Services.
			if ( yith_wcbk_is_services_module_active() ) {
				if ( is_admin() ) {
					add_action( 'pre_get_terms', array( $this, 'filter_vendor_services' ) );
					add_filter( 'wp_unique_term_slug', array( $this, 'unique_term_slug_for_vendors' ), 10, 3 );
					add_filter( 'pre_get_terms', array( $this, 'filter_services_by_vendor_or_admin_when_creating_services' ) );
				}
				add_action( 'yith_wcbk_process_service_meta', array( $this, 'set_vendor_in_services' ), 10, 1 );
				add_filter( 'yith_wcbk_service_tax_get_service_taxonomy_fields', array( $this, 'add_vendor_info_in_services' ) );
				add_action( 'after-' . YITH_WCBK_Post_Types::SERVICE_TAX . '-table', array( $this, 'add_vendor_filter_in_services' ) );
				add_filter( 'yith_wcbk_booking_services_list_additional_columns', array( $this, 'add_vendor_column_in_services' ) );
				add_filter( 'manage_' . YITH_WCBK_Post_Types::SERVICE_TAX . '_custom_column', array( $this, 'print_vendor_column_in_services' ), 10, 3 );
				add_filter( 'yith_wcmv_disable_post', array( $this, 'allow_editing_services' ), 20 );
				add_filter( 'yith_plugin_fw_panel_url', array( $this, 'add_post_type_to_services_url_in_panel_nav' ), 10, 3 );
			}

			// Calendar.
			add_filter( 'yith_wcbk_json_search_booking_products_args', array( $this, 'filter_args_to_return_vendor_booking_products_only' ), 10, 1 );
			add_filter( 'yith_wcbk_calendar_url_query_args', array( $this, 'calendar_url_query_args' ), 10, 1 );
			add_filter( 'yith_wcbk_admin_js_disable_wc_check_for_changes', array( $this, 'admin_js_disable_wc_check_for_changes' ), 10, 1 );
			$show_externals = yith_wcbk()->settings->show_externals_in_calendar();
			if ( $show_externals ) {
				add_filter( 'yith_wcbk_calendar_booking_list_bookings', array( $this, 'filter_external_bookings_in_calendar' ) );
			}

			if ( is_admin() && ! ( YITH_WCBK()->is_request( 'ajax' ) ) ) {
				add_filter( 'yith_wcbk_pre_get_bookings_args', array( $this, 'filter_args_to_return_vendor_bookings_only' ), 10, 1 );
			}

			// Emails.
			add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ), 20 );

			// Product.
			add_filter( 'yith_wcbk_booking_product_sub_tabs', array( $this, 'hide_resources_tab_for_vendors' ), 999 );
		} else {
			// Hide Booking Products in Admin, if integration is not active.
			add_filter( 'product_type_selector', array( $this, 'remove_booking_in_product_type_selector_for_vendors' ), 999 );
			add_action( 'init', array( $this, 'remove_booking_data_panels_for_vendors' ), 999 );

		}
	}

	/**
	 * Add allowed menu items for vendor.
	 *
	 * @param array $items Allowed menu items.
	 *
	 * @return array
	 * @since 3.6.0
	 */
	public function add_allowed_menu_items( $items ) {
		$items[] = self::PANEL_PAGE;

		return $items;
	}

	/**
	 * Filter read vendor id in factory.
	 * To allow retrieving the vendor by booking objects through the `yith_wcmv_get_vendor` function.
	 *
	 * @param int                                 $vendor_id   The vendor ID.
	 * @param int|WP_Post|YITH_WCBK_Booking|false $object      The object.
	 * @param string                              $object_type The object type.
	 *
	 * @return int
	 * @see   YITH_Vendors_Factory::read
	 * @since 3.6.0
	 */
	public function filter_read_vendor_id( $vendor_id, $object, $object_type ) {
		$type_class_map = array(
			YITH_WCBK_Post_Types::BOOKING => 'YITH_WCBK_Booking',
			'booking'                     => 'YITH_WCBK_Booking',
		);
		$object_class   = $type_class_map[ $object_type ] ?? '';

		if ( $object_class ) {
			if ( false === $object ) {
				global $post;
				$post_id = isset( $post ) ? $post->ID : 0;
			} elseif ( $object instanceof WP_Post ) {
				$post_id = $object->ID;
			} elseif ( is_a( $object, $object_class ) && is_callable( array( $object, 'get_id' ) ) ) {
				$post_id = $object->get_id();
			} elseif ( is_numeric( $object ) ) {
				$post_id = absint( $object );
			}

			$terms = ! empty( $post_id ) ? wp_get_post_terms( $post_id, YITH_Vendors_Taxonomy::TAXONOMY_NAME ) : array();

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$vendor_term = array_shift( $terms );
				$vendor_id   = $vendor_term->term_id;
			}
		}

		return $vendor_id;
	}

	/**
	 * Retrieve the current Vendor.
	 *
	 * @return YITH_Vendor|false
	 * @since 2.1.28
	 */
	public function get_current_vendor() {
		if ( function_exists( 'yith_wcmv_get_vendor' ) ) {
			$vendor = yith_wcmv_get_vendor( 'current', 'user' );
			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				return $vendor;
			}
		}

		return false;
	}

	/**
	 * Register specific panel for vendor.
	 */
	public function register_vendor_panel() {
		$vendor = $this->get_current_vendor();
		if ( $vendor ) {
			if ( ! empty( $this->panel ) ) {
				return;
			}

			$tabs = array(
				'vendor-all-bookings' => array(
					'title'       => _x( 'Bookings', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
					'icon'        => 'dashboard',
					'description' => __( 'Here you can see all your bookings.', 'yith-booking-for-woocommerce' ),
				),
				'vendor-calendar'     => array(
					'title'       => _x( 'Calendar', 'Tab title in vendor plugin settings panel', 'yith-booking-for-woocommerce' ),
					'icon'        => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"></path></svg>',
					'description' => __( 'Here you can see the calendar containing all your bookings.', 'yith-booking-for-woocommerce' ),
				),
				'vendor-services'     => array(
					'title'       => _x( 'Services', 'Tab title in vendor plugin settings panel', 'yith-booking-for-woocommerce' ),
					'icon'        => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"></path></svg>',
					'description' => __( 'A service is anything you can think of that you may want to provide with your bookable products; it can be optional or not. Example: breakfast, WiFi, air-conditioning.', 'yith-booking-for-woocommerce' ),
				),
			);

			if ( ! yith_wcbk_is_services_module_active() ) {
				unset( $tabs['vendor-services'] );
			}

			$args = array(
				'ui_version'       => 2,
				'create_menu_page' => true,
				'parent_slug'      => '',
				'class'            => yith_set_wrapper_class(),
				'page_title'       => 'Booking and Appointment for WooCommerce',
				'menu_title'       => 'Booking',
				'capability'       => 'edit_yith_bookings',
				'parent'           => '',
				'parent_page'      => '',
				'page'             => self::PANEL_PAGE,
				'admin-tabs'       => $tabs,
				'icon_url'         => 'dashicons-calendar',
				'position'         => 30,
				'options-path'     => YITH_WCBK_DIR . '/includes/integrations/plugins/multi-vendor/panel',
			);

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}
	}

	/**
	 * Is this the Vendor Membership panel?
	 *
	 * @param string $tab The tab.
	 *
	 * @return bool
	 */
	public function is_panel( $tab = '' ) {
		$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		$screen_id = $screen ? $screen->id : false;

		if ( $screen_id && strpos( $screen_id, self::PANEL_PAGE ) !== false ) {
			if ( ! $tab ) {
				return true;
			} elseif ( isset( $_GET['tab'] ) && $tab === $_GET['tab'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return true;
			}
		}

		return false;
	}

	/**
	 * Add Vendor panel to admin screen IDs to allow enqueuing admin styles and scripts.
	 *
	 * @param array $screen_ids Screen IDs.
	 *
	 * @return array
	 */
	public function add_vendor_panel_to_admin_screen_ids( $screen_ids ) {
		$screen_ids[] = 'toplevel_page_' . self::PANEL_PAGE;

		return $screen_ids;
	}

	/**
	 * Enqueue Admin Scripts and Styles
	 */
	public function admin_enqueue_scripts() {
		if ( $this->is_panel( 'vendor-calendar' ) ) {
			wp_enqueue_script( 'yith-wcbk-admin-booking-calendar' );
			wp_enqueue_style( 'yith-wcbk-admin-booking-calendar' );
		}

		if ( $this->get_current_vendor() ) {
			wp_enqueue_style( 'yith-wcbk-integrations-vendor-admin', YITH_WCBK_ASSETS_URL . '/css/integrations/multi-vendor/vendor-admin.css', array(), YITH_WCBK_VERSION );
		}
	}

	/**
	 * Filter 'get_post' args to return vendor booking products only.
	 *
	 * @param array $args The 'get_post' args.
	 *
	 * @return array
	 * @since 2.1.28
	 */
	public function filter_args_to_return_vendor_booking_products_only( $args ) {
		$vendor = $this->get_current_vendor();
		if ( $vendor ) {
			$args['tax_query']   = $args['tax_query'] ?? array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			$args['tax_query'][] = array(
				'taxonomy' => YITH_Vendors_Taxonomy::TAXONOMY_NAME,
				'field'    => 'id',
				'terms'    => $vendor->get_id(),
			);
		}

		return $args;
	}

	/**
	 * Filter Calendar URL query args.
	 *
	 * @param array $args The  args.
	 *
	 * @return array
	 * @since 3.0.2
	 */
	public function calendar_url_query_args( $args ) {
		$vendor = $this->get_current_vendor();
		if ( $vendor ) {
			$args = array(
				'page' => self::PANEL_PAGE,
				'tab'  => 'vendor-calendar',
			);
		}

		return $args;
	}

	/**
	 * Disable WC check for changes in JS.
	 *
	 * @param bool $disable Disable flag.
	 *
	 * @return bool
	 * @since 3.0.2
	 */
	public function admin_js_disable_wc_check_for_changes( $disable ) {
		if ( $this->is_panel( 'vendor-calendar' ) ) {
			$disable = true;
		}

		return $disable;
	}

	/**
	 * Filter args to return vendor bookings only in calendar
	 *
	 * @param array $args The args.
	 *
	 * @return array
	 * @since 3.0.0
	 */
	public function filter_args_to_return_vendor_bookings_only( $args ) {
		$vendor = $this->get_current_vendor();
		if ( $vendor ) {
			$args['data_query']   = $args['data_query'] ?? array();
			$args['data_query'][] = array(
				'data-type' => 'term',
				'taxonomy'  => YITH_Vendors_Taxonomy::TAXONOMY_NAME,
				'terms'     => $vendor->get_id(),
				'operator'  => 'IN',
				'field'     => 'id',
			);
		}

		return $args;
	}

	/**
	 * Suppress filters for booking post type to avoid issues when retrieving booking product availability through AJAX.
	 * This way when the plugin search for "bookings" it'll retrieve all bookings regardless the vendor
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 * @see   YITH_WCMV_Addons_Compatibility::filter_vendor_post_types (since 3.3.7)
	 * @since 2.1.4
	 */
	public function suppress_vendor_filter( $args ) {
		$args['yith_wcmv_addons_suppress_filter'] = true;

		return $args;
	}

	/**
	 * Filter externals in calendar to show the vendor ones only
	 *
	 * @param YITH_WCBK_Booking[]|YITH_WCBK_Booking_External[] $bookings Bookings.
	 *
	 * @return YITH_WCBK_Booking[]|YITH_WCBK_Booking_External[]
	 */
	public function filter_external_bookings_in_calendar( $bookings ) {
		$vendor = $this->get_current_vendor();
		if ( $vendor ) {
			$vendor_product_ids = array_map( 'absint', $vendor->get_products() );
			foreach ( $bookings as $key => $booking ) {
				if ( $booking->is_external() && ! in_array( $booking->get_product_id(), $vendor_product_ids, true ) ) {
					unset( $bookings[ $key ] );
				}
			}
		}

		return $bookings;
	}

	/**
	 * Remove booking data panels in product for vendors if the integration is not active
	 */
	public function remove_booking_data_panels_for_vendors() {
		$vendor = $this->get_current_vendor();
		if ( $vendor ) {
			$product_cpt = YITH_WCBK_Product_Post_Type_Admin::get_instance();
			$priority    = has_filter( 'woocommerce_product_data_tabs', array( $product_cpt, 'product_booking_tabs' ) );
			remove_filter( 'woocommerce_product_data_tabs', array( $product_cpt, 'product_booking_tabs' ), $priority );
		}
	}

	/**
	 * Remove vendor column in bookings for vendors.
	 *
	 * @param array $columns The columns.
	 *
	 * @return array
	 */
	public function remove_vendor_column_in_booking_for_vendors( $columns ) {
		$vendor = $this->get_current_vendor();
		if ( $vendor && isset( $columns[ 'taxonomy-' . YITH_Vendors_Taxonomy::TAXONOMY_NAME ] ) ) {
			unset( $columns[ 'taxonomy-' . YITH_Vendors_Taxonomy::TAXONOMY_NAME ] );
		}

		return $columns;
	}

	/**
	 * Show parent order ID in emails.
	 *
	 * @param int               $order_id      Order ID.
	 * @param YITH_WCBK_Booking $booking       The Booking.
	 * @param bool              $sent_to_admin Sent to admin flag.
	 * @param string            $plain_text    Plain text.
	 * @param WC_Email          $email         The email.
	 *
	 * @return mixed
	 */
	public function show_parent_order_id_in_emails( $order_id, $booking, $sent_to_admin, $plain_text, $email ) {
		if ( ! $email instanceof YITH_WCBK_Email_Booking_Status ) {
			return $this->show_parent_order_id( $order_id );
		}

		return $order_id;
	}

	/**
	 * Show parent order ID in PDF.
	 *
	 * @param int               $order_id Order ID.
	 * @param YITH_WCBK_Booking $booking  The booking.
	 * @param bool              $is_admin Is-admin flag.
	 *
	 * @return mixed
	 */
	public function show_parent_order_id_in_pdf( $order_id, $booking, $is_admin ) {
		if ( ! $is_admin ) {
			return $this->show_parent_order_id( $order_id );
		}

		return $order_id;
	}

	/**
	 * Retrieve the parent order ID.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return int
	 */
	public function show_parent_order_id( $order_id ) {
		$parent_id = wp_get_post_parent_id( $order_id );

		return ! ! $parent_id ? $parent_id : $order_id;
	}

	/**
	 * Add booking related to suborders to display them in parent order details
	 *
	 * @param YITH_WCBK_Booking[] $bookings Bookings.
	 * @param WC_Order            $order    The order.
	 *
	 * @return array
	 */
	public function add_bookings_related_to_suborders( $bookings, $order ) {
		$bookings     = ! ! $bookings && is_array( $bookings ) ? $bookings : array();
		$suborder_ids = YITH_Vendors_Orders::get_suborders( $order->get_id() );
		$suborder_ids = ! ! $suborder_ids && is_array( $suborder_ids ) ? $suborder_ids : array();

		foreach ( $suborder_ids as $suborder_id ) {
			$suborder_bookings = yith_wcbk()->booking_helper->get_bookings_by_order( $suborder_id );
			if ( ! ! $suborder_bookings && is_array( $suborder_bookings ) ) {
				$bookings = array_merge( $bookings, $suborder_bookings );
			}
		}

		return $bookings;
	}

	/**
	 * Add email classes to WooCommerce ones.
	 *
	 * @param WC_Email[] $emails Emails.
	 *
	 * @return WC_Email[]
	 */
	public function add_email_classes( $emails ) {
		$emails['YITH_WCBK_Email_Vendor_New_Booking']    = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-vendor-new-booking.php';
		$emails['YITH_WCBK_Email_Booking_Status_Vendor'] = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-booking-status-vendor.php';

		return $emails;
	}

	/**
	 * Remove booking product type in product type selector for vendors
	 *
	 * @param array $types Types.
	 *
	 * @return array
	 */
	public function remove_booking_in_product_type_selector_for_vendors( $types ) {
		$vendor = $this->get_current_vendor();
		if ( $vendor && isset( $types[ YITH_WCBK_Product_Post_Type_Admin::$prod_type ] ) ) {
			unset( $types[ YITH_WCBK_Product_Post_Type_Admin::$prod_type ] );
		}

		return $types;
	}

	/**
	 * Disable check for bookings in orders with suborders
	 *
	 * @param bool  $check    Check flag.
	 * @param int   $order_id Order ID.
	 * @param array $posted   Posted arguments.
	 *
	 * @return bool
	 */
	public function not_check_for_booking_in_parent_orders_with_suborders( $check, $order_id, $posted ) {
		$has_suborders = ! ! get_post_meta( $order_id, 'has_sub_order', true );
		if ( $has_suborders ) {
			// parent order.
			return false;
		}

		return $check;
	}

	/**
	 * Add vendor taxonomy to booking when it's created
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 */
	public function add_vendor_taxonomy_to_booking( $booking ) {
		if ( $booking->get_product_id() ) {
			$vendor = yith_wcmv_get_vendor( $booking->get_product_id(), 'product' );

			if ( $vendor->is_valid() ) {
				wp_set_object_terms( $booking->get_id(), $vendor->get_slug(), YITH_Vendors_Taxonomy::TAXONOMY_NAME, false );
			}
		}
	}

	/**
	 * Add booking capabilities to Vendor.
	 *
	 * @param array $caps Capabilities.
	 *
	 * @return array
	 */
	public function add_booking_capabilities_for_vendor( $caps ) {
		if ( $this->is_enabled() ) {
			$booking_post_type = YITH_WCBK_Post_Types::BOOKING;

			$caps['bookings'] = array(
				"edit_{$booking_post_type}"            => true,
				"edit_{$booking_post_type}s"           => true,
				"edit_others_{$booking_post_type}s"    => true,
				"read_private_{$booking_post_type}s"   => true,
				"edit_private_{$booking_post_type}s"   => true,
				"edit_published_{$booking_post_type}s" => true,
			);

			$caps['booking_services'] = array(
				'manage_' . YITH_WCBK_Post_Types::SERVICE_TAX . 's' => true,
				'edit_' . YITH_WCBK_Post_Types::SERVICE_TAX . 's'   => true,
				'delete' . YITH_WCBK_Post_Types::SERVICE_TAX . 's'  => true,
				'assign' . YITH_WCBK_Post_Types::SERVICE_TAX . 's'  => true,
			);
		}

		return $caps;
	}

	/**
	 * Filter services by vendor or admin to allow creating vendor services with the same name of admin services
	 *
	 * @param WP_Term_Query $term_query Term query.
	 */
	public function filter_services_by_vendor_or_admin_when_creating_services( $term_query ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if (
			isset( $_REQUEST['yith_booking_service_data'], $_REQUEST['yith_booking_service_data']['yith_shop_vendor'] )
			&& $this->filter_vendor_services_enabled && function_exists( 'yith_wcmv_get_vendor' )
			&& isset( $term_query->query_vars['taxonomy'] ) && array( YITH_WCBK_Post_Types::SERVICE_TAX ) === $term_query->query_vars['taxonomy']
		) {
			$vendor_id = absint( $_REQUEST['yith_booking_service_data']['yith_shop_vendor'] );
			$vendor    = $vendor_id ? yith_wcmv_get_vendor( $vendor_id ) : false;
			if ( $vendor && $vendor->is_valid() ) {
				$meta_query = array(
					array(
						'key'   => self::VENDOR_SERVICE_META,
						'value' => $vendor->get_id(),
					),
				);
			} else {
				$meta_query = array(
					array(
						'relation' => 'OR',
						array(
							'key'   => self::VENDOR_SERVICE_META,
							'value' => '',
						),
						array(
							'key'     => self::VENDOR_SERVICE_META,
							'compare' => 'NOT EXISTS',
						),
					),
				);
			}
			if ( ! empty( $term_query->query_vars['meta_query'] ) && is_array( $term_query->query_vars['meta_query'] ) ) {
				$meta_query = array_merge( $meta_query, $term_query->query_vars['meta_query'] );
			}

			$term_query->query_vars['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}
		// phpcs:enable
	}

	/**
	 * Filter the vendor services.
	 *
	 * @param WP_Term_Query $term_query Term query.
	 */
	public function filter_vendor_services( $term_query ) {
		global $pagenow;
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if (
			$this->filter_vendor_services_enabled && function_exists( 'yith_wcmv_get_vendor' )
			&& isset( $term_query->query_vars['taxonomy'] ) && array( YITH_WCBK_Post_Types::SERVICE_TAX ) === $term_query->query_vars['taxonomy']
		) {
			$vendor                             = yith_wcmv_get_vendor( 'current', 'user' );
			$is_vendor                          = $vendor->is_valid() && $vendor->has_limited_access();
			$is_service_edit_page_filter_vendor = 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && YITH_WCBK_Post_Types::SERVICE_TAX === $_GET['taxonomy'] && ! empty( $_GET[ self::VENDOR_SERVICE_META ] );

			if ( $is_vendor || $is_service_edit_page_filter_vendor ) {
				if ( $is_vendor ) {
					$vendor_id = $vendor->get_id();
				} else {
					// $is_service_edit_page_filter_vendor
					$vendor_id = wc_clean( wp_unslash( $_GET[ self::VENDOR_SERVICE_META ] ) );
				}

				if ( 'mine' !== $vendor_id ) {
					$meta_query = array(
						array(
							'key'   => self::VENDOR_SERVICE_META,
							'value' => $vendor_id,
						),
					);
				} else {
					$meta_query = array(
						array(
							'relation' => 'OR',
							array(
								'key'   => self::VENDOR_SERVICE_META,
								'value' => '',
							),
							array(
								'key'     => self::VENDOR_SERVICE_META,
								'compare' => 'NOT EXISTS',
							),
						),
					);
				}

				if ( ! empty( $term_query->query_vars['meta_query'] ) && is_array( $term_query->query_vars['meta_query'] ) ) {
					$meta_query = array_merge( $meta_query, $term_query->query_vars['meta_query'] );
				}

				$term_query->query_vars['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			}
		}
		// phpcs:enable
	}

	/**
	 * Filter unique term slug to allow Vendor to add services with the same name of the admin services
	 *
	 * @param string  $slug          Slug.
	 * @param WP_Term $term          The term.
	 * @param string  $original_slug Original slug.
	 *
	 * @return string
	 * @since 1.0.14
	 */
	public function unique_term_slug_for_vendors( $slug, $term, $original_slug ) {
		if ( isset( $term->taxonomy ) && YITH_WCBK_Post_Types::SERVICE_TAX === $term->taxonomy ) {
			remove_filter( 'wp_unique_term_slug', array( $this, __FUNCTION__ ), 10 );
			$this->filter_vendor_services_enabled = false;

			$slug = wp_unique_term_slug( $original_slug, $term );

			add_filter( 'wp_unique_term_slug', array( $this, __FUNCTION__ ), 10, 3 );
			$this->filter_vendor_services_enabled = true;
		}

		return $slug;
	}

	/**
	 * Add Vendor ID in services
	 *
	 * @param YITH_WCBK_Service $service The service.
	 */
	public function set_vendor_in_services( $service ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$vendor = $this->get_current_vendor();
		if ( $vendor ) {
			$service->update_meta_data( self::VENDOR_SERVICE_META, $vendor->get_id() );
		} elseif ( isset( $_POST['yith_booking_service_data']['yith_shop_vendor'] ) ) {
			$vendor_id = absint( $_POST['yith_booking_service_data']['yith_shop_vendor'] );
			if ( $vendor_id ) {
				$service->update_meta_data( self::VENDOR_SERVICE_META, $vendor_id );
			} else {
				$service->delete_meta_data( self::VENDOR_SERVICE_META );
			}
		}
	}

	/**
	 * Allow editing and seeing services for vendors
	 *
	 * @param bool $disable_post Disable post flag.
	 *
	 * @return bool
	 * @since 2.0.9
	 */
	public function allow_editing_services( $disable_post ) {
		global $pagenow;

		// phpcs:disable WordPress.Security.NonceVerification.Missing

		$is_edit_tag         = 'edit-tags.php' === $pagenow;
		$is_edit_action      = ! empty( $_POST['action'] ) && 'editedtag' === $_POST['action'];
		$is_booking_taxonomy = ! empty( $_POST['taxonomy'] ) && YITH_WCBK_Post_Types::SERVICE_TAX === $_POST['taxonomy'];

		if ( $is_edit_tag && $is_edit_action && $is_booking_taxonomy ) {
			$disable_post = false;
		}

		// phpcs:enable

		return $disable_post;
	}

	/**
	 * Add Post type param to Services URL, to allow vendors seeing that page.
	 *
	 * @param string $url  The Tab URL.
	 * @param string $page The page.
	 * @param string $tab  The tab.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	public function add_post_type_to_services_url_in_panel_nav( $url, $page, $tab ) {
		if ( self::PANEL_PAGE === $page && 'vendor-services' === $tab ) {
			$url = add_query_arg(
				array(
					'post_type' => YITH_WCBK_Post_Types::BOOKING,
				),
				$url
			);
		}

		return $url;
	}

	/**
	 * Add Vendor info in sevices to show vendor dropdown
	 *
	 * @param array $info Service info.
	 *
	 * @return array
	 */
	public function add_vendor_info_in_services( $info ) {
		$vendor = yith_wcmv_get_vendor( 'current', 'user' );
		if ( ! $vendor->is_valid() || ! $vendor->has_limited_access() ) {
			$vendors = self::get_vendors( array( 'fields' => 'id=>name' ) );

			if ( ! $vendors || ! is_array( $vendors ) ) {
				$vendors = array();
			}

			$vendors[''] = __( 'None', 'yith-booking-for-woocommerce' );
			asort( $vendors );

			$info[ self::VENDOR_SERVICE_META ] = array(
				'title'   => __( 'Vendor', 'yith-booking-for-woocommerce' ),
				'type'    => 'select',
				'default' => '',
				'options' => $vendors,
				'desc'    => '',
			);
		}

		return $info;
	}

	/**
	 * Add vendor filter form and dropdown in services
	 */
	public function add_vendor_filter_in_services() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$vendor = yith_wcmv_get_vendor( 'current', 'user' );
		if ( ! $vendor->is_valid() || ! $vendor->has_limited_access() ) {
			$vendors = self::get_vendors( array( 'fields' => 'id=>name' ) );
			if ( ! $vendors || ! is_array( $vendors ) ) {
				$vendors = array();
			}

			$vendors['']     = __( 'All', 'yith-booking-for-woocommerce' );
			$vendors['mine'] = __( 'Mine', 'yith-booking-for-woocommerce' );

			asort( $vendors );

			$get_params = ! empty( $_GET ) ? $_GET : array();

			echo '<div class="yith-wcbk-services-filter-by-vendor-form yith-wcbk-move alignleft actions" data-after=".tablenav.top > .bulkactions">';
			echo '<form method="get">';
			foreach ( $get_params as $key => $value ) {
				if ( self::VENDOR_SERVICE_META === $key ) {
					continue;
				}
				echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';
			}

			$selected_vendor = wc_clean( wp_unslash( $_REQUEST[ self::VENDOR_SERVICE_META ] ?? '' ) );
			$selected_vendor = ! ! $selected_vendor && 'mine' !== $selected_vendor ? absint( $selected_vendor ) : $selected_vendor;

			echo '<select name="' . esc_attr( self::VENDOR_SERVICE_META ) . '">';
			foreach ( $vendors as $vendor_id => $vendor_name ) {
				$vendor_id = ! ! $vendor_id && 'mine' !== $vendor_id ? absint( $vendor_id ) : $vendor_id;
				echo '<option value="' . esc_attr( $vendor_id ) . '" ' . selected( $selected_vendor, $vendor_id ) . '>' . esc_html( $vendor_name ) . '</option>';
			}
			echo '</select>';

			echo '<input type="submit" class="button" value="' . esc_html__( 'Filter by Vendor', 'yith-booking-for-woocommerce' ) . '">';

			echo '</form>';
			echo '</div>';
		}
		// phpcs:enable
	}

	/**
	 * Add Vendor column in services
	 *
	 * @param array $columns The columns.
	 *
	 * @return array The columns list
	 */
	public function add_vendor_column_in_services( $columns ) {
		$vendor = yith_wcmv_get_vendor( 'current', 'user' );
		if ( ! $vendor->is_valid() || ! $vendor->has_limited_access() ) {
			$columns['service_vendor'] = __( 'Vendor', 'yith-booking-for-woocommerce' );
		}

		return $columns;
	}

	/**
	 * Print Vendor column in services
	 *
	 * @param string $custom_column Filtered value.
	 * @param string $column_name   Column name.
	 * @param int    $term_id       The term ID.
	 *
	 * @return string The column value.
	 */
	public function print_vendor_column_in_services( $custom_column, $column_name, $term_id ) {
		$service = yith_wcbk_get_service( $term_id );
		if ( 'service_vendor' === $column_name ) {
			$vendor_id = absint( $service->get_meta( self::VENDOR_SERVICE_META ) );
			if ( ! ! $vendor_id ) {
				$vendor = yith_wcmv_get_vendor( $vendor_id );
				if ( $vendor->is_valid() ) {
					$link        = add_query_arg(
						array(
							self::VENDOR_SERVICE_META => $vendor->get_id(),
						)
					);
					$vendor_name = $vendor->get_name();
					// translators: %s is the vendor name.
					$title = sprintf( _x( 'Filter by %s', 'Filter by Vendor name', 'yith-booking-for-woocommerce' ), $vendor_name );

					$custom_column .= sprintf(
						'<a href="%s" title="%s">%s</a>',
						esc_url( $link ),
						esc_attr( $title ),
						esc_html( $vendor_name )
					);
				}
			}
		}

		return $custom_column;
	}

	/**
	 * Get Vendors.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array|int|WP_Error
	 */
	public static function get_vendors( $args = array() ) {
		static $vendors_data = array();

		$hash = ! ! $args ? md5( implode( ' ', $args ) ) : 0;

		if ( ! isset( $vendors_data[ $hash ] ) ) {
			$default_args = array(
				'fields'     => 'id',
				'hide_empty' => false,
			);

			$args             = wp_parse_args( $args, $default_args );
			$args['taxonomy'] = YITH_Vendors_Taxonomy::TAXONOMY_NAME;

			$vendors_data[ $hash ] = yith_wcbk()->wp->get_terms( $args );
		}

		return $vendors_data[ $hash ];
	}

	/**
	 * Hide resources tab for vendors.
	 *
	 * @param array $sub_tabs The sub-tabs shown in product edit page.
	 *
	 * @return array
	 * @since 4.0.0
	 */
	public function hide_resources_tab_for_vendors( $sub_tabs ) {
		if ( $this->get_current_vendor() && isset( $sub_tabs['yith_booking_resources'] ) ) {
			unset( $sub_tabs['yith_booking_resources'] );
		}

		return $sub_tabs;
	}

	/**
	 * Return true if the related option is enabled on Multi Vendor.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return $this->is_component_active() && get_option( self::ACTIVATED_OPTION, 'no' ) === 'yes';
	}
}
