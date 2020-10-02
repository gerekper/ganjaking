<?php

/**
 * Initializes bookings.
 *
 * @since 1.13.0
 */
class WC_Bookings_Init {
	/**
	 * Constructor.
	 *
	 * @since 1.13.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init_post_types' ), 9 );
		add_action( 'wp_enqueue_scripts', array( $this, 'booking_form_styles' ) );
		add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ) );

		// Load payment gateway name.
		add_filter( 'woocommerce_payment_gateways', array( $this, 'include_gateway' ) );

		// Dynamically add new bookings' capabilities for roles with deprecated manage_bookings cap.
		add_filter( 'user_has_cap', array( $this, 'add_new_booking_caps' ), 10, 4 );
	}

	/**
	 * Init post types
	 */
	public function init_post_types() {
		register_post_type( 'bookable_person',
			apply_filters( 'woocommerce_register_post_type_bookable_person',
				array(
					'label'           => __( 'Person Type', 'woocommerce-bookings' ),
					'public'          => false,
					'hierarchical'    => false,
					'supports'        => false,
					'capability_type' => 'bookable_person',
					'map_meta_cap'    => true,
				)
			)
		);

		register_post_type( 'bookable_resource',
			apply_filters( 'woocommerce_register_post_type_bookable_resource',
				array(
					'label'  => __( 'Resources', 'woocommerce-bookings' ),
					'labels' => array(
						'name'               => __( 'Bookable resources', 'woocommerce-bookings' ),
						'singular_name'      => __( 'Bookable resource', 'woocommerce-bookings' ),
						'add_new'            => __( 'Add Resource', 'woocommerce-bookings' ),
						'add_new_item'       => __( 'Add New Resource', 'woocommerce-bookings' ),
						'edit'               => __( 'Edit', 'woocommerce-bookings' ),
						'edit_item'          => __( 'Edit Resource', 'woocommerce-bookings' ),
						'new_item'           => __( 'New Resource', 'woocommerce-bookings' ),
						'view'               => __( 'View Resource', 'woocommerce-bookings' ),
						'view_item'          => __( 'View Resource', 'woocommerce-bookings' ),
						'search_items'       => __( 'Search Resource', 'woocommerce-bookings' ),
						'not_found'          => __( 'No Resource found', 'woocommerce-bookings' ),
						'not_found_in_trash' => __( 'No Resource found in trash', 'woocommerce-bookings' ),
						'parent'             => __( 'Parent Resources', 'woocommerce-bookings' ),
						'menu_name'          => _x( 'Resources', 'Admin menu name', 'woocommerce-bookings' ),
						'all_items'          => __( 'Resources', 'woocommerce-bookings' ),
					),
					'description'                        => __( 'Bookable resources are bookable within a bookings product.', 'woocommerce-bookings' ),
					'public'                             => false,
					'show_ui'                            => true,
					'capability_type'                    => 'bookable_resource',
					'map_meta_cap'                       => true,
					'publicly_queryable'                 => false,
					'exclude_from_search'                => true,
					'show_in_menu'                       => true,
					'hierarchical'                       => false,
					'show_in_nav_menus'                  => false,
					'rewrite'                            => false,
					'query_var'                          => false,
					'supports'                           => array( 'title' ),
					'has_archive'                        => false,
					'show_in_menu'                       => 'edit.php?post_type=wc_booking',
				)
			)
		);

		register_post_type( 'wc_booking',
			apply_filters( 'woocommerce_register_post_type_wc_booking',
				array(
					'label'                      => __( 'Booking', 'woocommerce-bookings' ),
					'labels'                     => array(
						'name'               => __( 'Bookings', 'woocommerce-bookings' ),
						'singular_name'      => __( 'Booking', 'woocommerce-bookings' ),
						'add_new'            => __( 'Add Booking', 'woocommerce-bookings' ),
						'add_new_item'       => __( 'Add New Booking', 'woocommerce-bookings' ),
						'edit'               => __( 'Edit', 'woocommerce-bookings' ),
						'edit_item'          => __( 'Edit Booking', 'woocommerce-bookings' ),
						'new_item'           => __( 'New Booking', 'woocommerce-bookings' ),
						'view'               => __( 'View Booking', 'woocommerce-bookings' ),
						'view_item'          => __( 'View Booking', 'woocommerce-bookings' ),
						'search_items'       => __( 'Search Bookings', 'woocommerce-bookings' ),
						'not_found'          => __( 'No Bookings found', 'woocommerce-bookings' ),
						'not_found_in_trash' => __( 'No Bookings found in trash', 'woocommerce-bookings' ),
						'parent'             => __( 'Parent Bookings', 'woocommerce-bookings' ),
						'menu_name'          => _x( 'Bookings', 'Admin menu name', 'woocommerce-bookings' ),
						'all_items'          => __( 'All Bookings', 'woocommerce-bookings' ),
					),
					'description'                => __( 'This is where bookings are stored.', 'woocommerce-bookings' ),
					'public'                     => false,
					'show_ui'                    => true,
					'capability_type'            => 'wc_booking',
					'map_meta_cap'               => true,
					'publicly_queryable'         => false,
					'exclude_from_search'        => true,
					'show_in_menu'               => true,
					'hierarchical'               => false,
					'show_in_nav_menus'          => false,
					'rewrite'                    => false,
					'query_var'                  => false,
					'supports'                   => array( '' ),
					'has_archive'                => false,
					'menu_icon'                  => 'dashicons-calendar-alt',
				)
			)
		);

		/**
		 * Post status
		 */
		register_post_status( 'complete', array(
			'label'                     => '<span class="status-complete tips" data-tip="' . wc_sanitize_tooltip( _x( 'Complete', 'woocommerce-bookings', 'woocommerce-bookings' ) ) . '">' . _x( 'Complete', 'woocommerce-bookings', 'woocommerce-bookings' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: 1: count, 2: count */
			'label_count'               => _n_noop( 'Complete <span class="count">(%s)</span>', 'Complete <span class="count">(%s)</span>', 'woocommerce-bookings' ),
		) );
		register_post_status( 'paid', array(
			'label'                     => '<span class="status-paid tips" data-tip="' . wc_sanitize_tooltip( _x( 'Paid &amp; Confirmed', 'woocommerce-bookings', 'woocommerce-bookings' ) ) . '">' . _x( 'Paid &amp; Confirmed', 'woocommerce-bookings', 'woocommerce-bookings' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: 1: count, 2: count */
			'label_count'               => _n_noop( 'Paid &amp; Confirmed <span class="count">(%s)</span>', 'Paid &amp; Confirmed <span class="count">(%s)</span>', 'woocommerce-bookings' ),
		) );
		register_post_status( 'confirmed', array(
			'label'                     => '<span class="status-confirmed tips" data-tip="' . wc_sanitize_tooltip( _x( 'Confirmed', 'woocommerce-bookings', 'woocommerce-bookings' ) ) . '">' . _x( 'Confirmed', 'woocommerce-bookings', 'woocommerce-bookings' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: 1: count, 2: count */
			'label_count'               => _n_noop( 'Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>', 'woocommerce-bookings' ),
		) );
		register_post_status( 'unpaid', array(
			'label'                     => '<span class="status-unpaid tips" data-tip="' . wc_sanitize_tooltip( _x( 'Un-paid', 'woocommerce-bookings', 'woocommerce-bookings' ) ) . '">' . _x( 'Un-paid', 'woocommerce-bookings', 'woocommerce-bookings' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: 1: count, 2: count */
			'label_count'               => _n_noop( 'Un-paid <span class="count">(%s)</span>', 'Un-paid <span class="count">(%s)</span>', 'woocommerce-bookings' ),
		) );
		register_post_status( 'pending-confirmation', array(
			'label'                     => '<span class="status-pending tips" data-tip="' . wc_sanitize_tooltip( _x( 'Pending Confirmation', 'woocommerce-bookings', 'woocommerce-bookings' ) ) . '">' . _x( 'Pending Confirmation', 'woocommerce-bookings', 'woocommerce-bookings' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: 1: count, 2: count */
			'label_count'               => _n_noop( 'Pending Confirmation <span class="count">(%s)</span>', 'Pending Confirmation <span class="count">(%s)</span>', 'woocommerce-bookings' ),
		) );
		register_post_status( 'cancelled', array(
			'label'                     => '<span class="status-cancelled tips" data-tip="' . wc_sanitize_tooltip( _x( 'Cancelled', 'woocommerce-bookings', 'woocommerce-bookings' ) ) . '">' . _x( 'Cancelled', 'woocommerce-bookings', 'woocommerce-bookings' ) . '</span>',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: 1: count, 2: count */
			'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'woocommerce-bookings' ),
		) );
		register_post_status( 'in-cart', array(
			'label'                     => '<span class="status-incart tips" data-tip="' . wc_sanitize_tooltip( _x( 'In Cart', 'woocommerce-bookings', 'woocommerce-bookings' ) ) . '">' . _x( 'In Cart', 'woocommerce-bookings', 'woocommerce-bookings' ) . '</span>',
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			/* translators: 1: count, 2: count */
			'label_count'               => _n_noop( 'In Cart <span class="count">(%s)</span>', 'In Cart <span class="count">(%s)</span>', 'woocommerce-bookings' ),
		) );
		register_post_status( 'was-in-cart', array(
			'label'                     => false,
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => false,
			'label_count'               => false,
		) );

	}

	/**
	 * Register data stores for bookings.
	 *
	 * @param  array  $data_stores
	 * @return array
	 */
	public function register_data_stores( $data_stores = array() ) {
		$data_stores['booking']                     = 'WC_Booking_Data_Store';
		$data_stores['product-booking']             = 'WC_Product_Booking_Data_Store_CPT';
		$data_stores['product-booking-resource']    = 'WC_Product_Booking_Resource_Data_Store_CPT';
		$data_stores['product-booking-person-type'] = 'WC_Product_Booking_Person_Type_Data_Store_CPT';
		$data_stores['booking-global-availability'] = 'WC_Global_Availability_Data_Store';
		return $data_stores;
	}

	/**
	 * Frontend booking form scripts
	 */
	public function booking_form_styles() {
		global $wp_scripts;

		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';

		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.min.css' );
		wp_enqueue_style( 'wc-bookings-styles', WC_BOOKINGS_PLUGIN_URL . '/dist/css/frontend.css', null, WC_BOOKINGS_VERSION );
	}

	/**
	 * Add a custom payment gateway
	 * This gateway works with booking that requires confirmation
	 */
	public function include_gateway( $gateways ) {
		$gateways[] = 'WC_Bookings_Gateway';

		return $gateways;
	}

	/**
	 * Adds all bookings capabilities to roles with deprecated manage_bookings cap.
	 *
	 * @param array        $allcaps An array of all the user's capabilities.
	 * @param array|string $caps    Actual capabilities for meta capability.
	 * @param array        $args    Optional parameters passed to has_cap(), typically object ID.
	 * @param WP_User      $user    The user object.
	 *
	 * @return array
	 */
	public function add_new_booking_caps( array $allcaps, $caps, $args, WP_User $user ) {
		if ( empty( $allcaps['manage_bookings'] ) ) {
			return $allcaps;
		}

		$bookings_capabilities = WC_Bookings_Install::get_core_capabilities();
		// Remove core capabilities as they are new caps previously restricted by the manage_woocommerce cap.
		unset( $bookings_capabilities['core'] );

		foreach ( $bookings_capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$allcaps[ $cap ] = true;
			}
		}

		return $allcaps;
	}
}
