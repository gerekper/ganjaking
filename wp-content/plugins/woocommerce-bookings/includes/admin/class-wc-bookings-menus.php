<?php

/**
 * WC_Bookings_Menus.
 */
class WC_Bookings_Menus {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'current_screen', array( $this, 'buffer' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'woocommerce_screen_ids' ) );
		add_action( 'admin_menu', array( $this, 'remove_default_add_booking_url' ), 10 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 49 );
		add_filter( 'menu_order', array( $this, 'menu_order' ), 20 );
		add_filter( 'admin_url', array( $this, 'add_new_booking_url' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'booking_form_styles' ) );
	}

	/**
	 * output buffer.
	 */
	public function buffer() {
		$screen = get_current_screen();

		if ( 'wc_booking_page_create_booking' === $screen->id ) {
			ob_start();
		}
	}

	/**
	 * Screen IDS.
	 *
	 * @param  array  $ids
	 * @return array
	 */
	public function woocommerce_screen_ids( $ids ) {
		return array_merge( $ids, array(
			'edit-wc_booking',
			'edit-bookable_resource',
			'bookable_resource',
			'wc_booking',
			'wc_booking_page_booking_calendar',
			'wc_booking_page_booking_notification',
			'wc_booking_page_create_booking',
			'wc_booking_page_wc_bookings_settings',
		) );
	}

	/**
	 * Removes the default add new booking link from the main admin menu.
	 */
	public function remove_default_add_booking_url() {
		global $submenu;

		if ( isset( $submenu['edit.php?post_type=wc_booking'] ) ) {
			foreach ( $submenu['edit.php?post_type=wc_booking'] as $key => $value ) {
				if ( 'post-new.php?post_type=wc_booking' == $value[2] ) {
					unset( $submenu['edit.php?post_type=wc_booking'][ $key ] );
					return;
				}
			}
		}
	}

	/**
	 * Add a submenu for managing bookings pages.
	 */
	public function admin_menu() {
		$create_booking_page = add_submenu_page( 'edit.php?post_type=wc_booking', __( 'Add Booking', 'woocommerce-bookings' ), __( 'Add Booking', 'woocommerce-bookings' ), 'edit_wc_bookings', 'create_booking', array( $this, 'create_booking_page' ) );
		$calendar_page       = add_submenu_page( 'edit.php?post_type=wc_booking', __( 'Calendar', 'woocommerce-bookings' ), __( 'Calendar', 'woocommerce-bookings' ), 'edit_wc_bookings', 'booking_calendar', array( $this, 'calendar_page' ) );
		$notification_page   = add_submenu_page( 'edit.php?post_type=wc_booking', __( 'Send Notification', 'woocommerce-bookings' ), __( 'Send Notification', 'woocommerce-bookings' ), 'edit_wc_bookings', 'booking_notification', array( $this, 'notifications_page' ) );
		$settings_page       = add_submenu_page( 'edit.php?post_type=wc_booking', __( 'Settings', 'woocommerce-bookings' ), __( 'Settings', 'woocommerce-bookings' ), 'manage_bookings_settings', 'wc_bookings_settings', array( $this, 'settings_page' ) );

		// Add action for screen options on this new page
		add_action( 'admin_print_scripts-' . $create_booking_page, array( $this, 'create_booking_page_scripts' ) );
		add_action( 'admin_print_scripts-' . $calendar_page, array( $this, 'calendar_page_scripts' ) );
		add_action( 'admin_print_scripts-' . $settings_page, array( $this, 'settings_page_scripts' ) );
	}

	/**
	 * Create booking scripts.
	 */
	public function create_booking_page_scripts() {
		global $wp_scripts;

		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';

		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.min.css' );
		wp_enqueue_style( 'wc-bookings-styles', WC_BOOKINGS_PLUGIN_URL . '/dist/css/frontend.css', null, WC_BOOKINGS_VERSION );
	}

	/**
	 * Create booking page.
	 */
	public function create_booking_page() {
		require_once( 'class-wc-bookings-create.php' );
		$page = new WC_Bookings_Create();
		$page->output();
	}

	/**
	 * calendar_page_scripts.
	 */
	public function calendar_page_scripts() {
		global $wp_version;

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'wc_bookings_admin_calendar_css' );
		wp_enqueue_script( 'wc_bookings_admin_calendar_js' );

		if ( WC_BOOKINGS_GUTENBERG_EXISTS ) {
			wp_enqueue_script( 'wc_bookings_admin_calendar_gutenberg_js' );
		}
	}

	/**
	 * Output the calendar page.
	 */
	public function calendar_page() {
		require_once 'class-wc-bookings-calendar.php';
		$page = new WC_Bookings_Calendar();
		$page->output();
	}

	/**
	 * Provides an email notification form.
	 */
	public function notifications_page() {
		if ( ! empty( $_POST ) && check_admin_referer( 'send_booking_notification' ) ) {
			$notification_product_id = absint( $_POST['notification_product_id'] );
			$notification_subject    = wc_clean( stripslashes( $_POST['notification_subject'] ) );
			$notification_message    = wp_kses_post( stripslashes( $_POST['notification_message'] ) );

			try {
				if ( ! $notification_product_id ) {
					throw new Exception( __( 'Please choose a product', 'woocommerce-bookings' ) );
				}

				if ( ! $notification_message ) {
					throw new Exception( __( 'Please enter a message', 'woocommerce-bookings' ) );
				}

				if ( ! $notification_subject ) {
					throw new Exception( __( 'Please enter a subject', 'woocommerce-bookings' ) );
				}

				$bookings     = WC_Booking_Data_Store::get_bookings_for_product( $notification_product_id );
				$mailer       = WC()->mailer();
				$notification = $mailer->emails['WC_Email_Booking_Notification'];

				foreach ( $bookings as $booking ) {
					$attachments = array();

					// Add .ics file
					if ( isset( $_POST['notification_ics'] ) ) {
						$generate = new WC_Bookings_ICS_Exporter;
						$attachments[] = $generate->get_booking_ics( $booking );
					}

					$notification->reset_tags();
					$notification->trigger( $booking->get_id(), $notification_subject, $notification_message, $attachments );
				}

				do_action( 'wc_bookings_notification_sent', $bookings, $notification );

				echo '<div class="updated fade"><p>' . esc_html__( 'Notification sent successfully', 'woocommerce-bookings' ) . '</p></div>';

			} catch ( Exception $e ) {
				echo '<div class="error"><p>' . esc_html( $e->getMessage() ) . '</p></div>';
			}
		}

		$booking_products = WC_Bookings_Admin::get_booking_products();

		include( 'views/html-notifications-page.php' );
	}

	/**
	 * settings_page_scripts.
	 */
	public function settings_page_scripts() {
		global $wp_version;

		if ( WC_BOOKINGS_GUTENBERG_EXISTS ) {
			wp_enqueue_script( 'wc_bookings_admin_store_availability_js' );
			wp_enqueue_style( 'wc_bookings_admin_store_availability_css' );
		}
	}

	/**
	 * Output the store availability page.
	 *
	 * @since 1.16.0
	 */
	public function settings_page() {
		wp_enqueue_script( 'wc_bookings_admin_js' );

		$tabs_metadata = apply_filters( 'woocommerce_bookings_settings_page', array(
			'availability' => array(
				'name'          => __( 'Store Availability', 'woocommerce-bookings' ),
				'href'          => admin_url( 'edit.php?post_type=wc_booking&page=wc_bookings_settings&tab=availability' ),
				'capability'    => 'read_global_availability',
				'generate_html' => function() {

					if ( defined( 'WC_BOOKINGS_ENABLE_STORE_AVAILABILITY_CALENDAR' ) && WC_BOOKINGS_ENABLE_STORE_AVAILABILITY_CALENDAR ) {
						$saved_view = get_option( 'wc_bookings_store_availability_view_setting', 'calendar' );
						$view = isset( $_GET['view'] ) ? wc_clean( $_GET['view'] ) : $saved_view;

						if ( 'classic' === $view ) {
							// Save chosen view to db.
							update_option( 'wc_bookings_store_availability_view_setting', 'classic' );
							include 'views/html-classic-availability-settings.php';
						} else {
							// Save chosen view to db.
							update_option( 'wc_bookings_store_availability_view_setting', 'calendar' );
							include 'views/html-store-availability-settings.php';
						}
					} else {
						include 'views/html-classic-availability-settings.php';
					}
				},
			),
			'timezones'    => array(
				'name'          => __( 'Timezones', 'woocommerce-bookings' ),
				'href'          => admin_url( 'edit.php?post_type=wc_booking&page=wc_bookings_settings&tab=timezones' ),
				'capability'    => 'manage_bookings_timezones',
				'generate_html' => 'WC_Bookings_Timezone_Settings::generate_form_html',
			),
			'connection'   => array(
				'name'          => __( 'Calendar Connection', 'woocommerce-bookings' ),
				'href'          => admin_url( 'edit.php?post_type=wc_booking&page=wc_bookings_settings&tab=connection' ),
				'capability'    => 'manage_bookings_connection',
				'generate_html' => 'WC_Bookings_Google_Calendar_Connection::generate_form_html',
			),
		) );

		include 'views/html-settings-page.php';
	}

	/**
	 * Reorder the WC menu items in admin.
	 *
	 * @param mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$new_menu_order = array();

		// Get index of product menu
		$booking_menu = array_search( 'edit.php?post_type=wc_booking', $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ) :
			if ( ( ( 'edit.php?post_type=product' ) == $item ) ) :
				$new_menu_order[] = $item;
				$new_menu_order[] = 'edit.php?post_type=wc_booking';
				unset( $menu_order[ $booking_menu ] );
			else :
				$new_menu_order[] = $item;
			endif;
		endforeach;

		// Return order
		return $new_menu_order;
	}

	/**
	 * Filters the add new booking url to point to our custom page
	 * @param string $url original url
	 * @param string $path requested path that we can match against
	 * @return string new url
	 */
	public function add_new_booking_url( $url, $path ) {
		if ( 'post-new.php?post_type=wc_booking' == $path ) {
			return admin_url( 'edit.php?post_type=wc_booking&page=create_booking' );
		}
		return $url;
	}
}
