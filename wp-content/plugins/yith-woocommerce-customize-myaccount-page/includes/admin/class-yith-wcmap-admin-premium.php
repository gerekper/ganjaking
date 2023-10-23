<?php
/**
 * Admin class premium
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Admin_Premium', false ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Admin_Premium extends YITH_WCMAP_Admin_Extended {

		/**
		 * Class construct
		 *
		 * @since  3.12.0
		 * @return void
		 */
		public function __construct() {
			parent::__construct();

			remove_filter( 'yith_plugin_row_meta_documentation_url', array( $this, 'filter_extended_documentation_link' ), 10 );
			remove_filter( 'yith_wcmap_admin_panel_args', array( $this, 'filter_doc_url' ), 10 );

			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCMAP_DIR . '/' . basename( YITH_WCMAP_FILE ) ), array( $this, 'action_links' ) );
			// Filter endpoint template args.
			add_filter( 'yith_wcmap_admin_endpoints_template', array( $this, 'filter_endpoint_template_args' ), 10, 1 );
			add_action( 'yith_wcmap_admin_after_single_item_content', array( $this, 'print_grouped_endpoint' ), 10, 3 );
		}

		/**
		 * Retrieve the documentation URL.
		 *
		 * @return string
		 */
		protected function get_doc_url(): string {
			return 'https://docs.yithemes.com/yith-woocommerce-customize-myaccount-page/';
		}

		/**
		 * Get admin panel tabs
		 *
		 * @since  3.12.0
		 * @return array
		 */
		public function get_admin_tabs() {
			/**
			 * APPLY_FILTERS: yith_wcmap_admin_tabs
			 *
			 * Filter the available tabs in the plugin panel.
			 *
			 * @param array $tabs Admin tabs.
			 *
			 * @return array
			 */
			return apply_filters(
				'yith_wcmap_admin_tabs',
				array(
					'endpoints' => array(
						'title'       => _x( 'Endpoints', 'Endpoints tab name', 'yith-woocommerce-customize-myaccount-page' ),
						'icon'        => '
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
								<path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3" />
							</svg>
					 	',
						'description' => _x( 'An “Endpoint” is the content shown as a subtab on your customers\' My Account page. With this plugin, you can disable WooCommerce default endpoints (Dashboard, Orders, etc.), edit their content, and change the order in which they’re displayed. You can add new endpoints using the dedicated button.', 'Admin endpoints tab description ', 'yith-woocommerce-customize-myaccount-page' ),
					),
					'banners'   => array(
						'title'       => _x( 'Banners', 'Banners tab name', 'yith-woocommerce-customize-myaccount-page' ),
						'icon'        => '
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
								<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z" />
							</svg>
						',
						'description' => _x( 'Create custom banners to show advanced content in the endpoints.', 'Admin banners tab description ', 'yith-woocommerce-customize-myaccount-page' ),
					),
					'settings'  => array(
						'title'       => _x( 'Settings', 'Settings tab name', 'yith-woocommerce-customize-myaccount-page' ),
						'icon'        => 'settings',
					),
				)
			);
		}

		/**
		 * Get items to be shown in "Your store tools" tab.
		 *
		 * @return array
		 */
		protected function get_your_store_tools_items() {
			$items = array(
				'gift-cards'                    => array(
					'name'           => 'Gift Cards',
					'icon_url'       => YITH_WCMAP_URL . 'assets/images/plugins/gift-cards.svg',
					'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-gift-cards/',
					'description'    => _x(
						'Sell gift cards in your shop to increase your earnings and attract new customers.',
						'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Gift Cards',
						'yith-woocommerce-waiting-list'
					),
					'is_active'      => defined( 'YITH_YWGC_PREMIUM' ),
					'is_recommended' => true,
				),
				'ajax-product-filter'           => array(
					'name'           => 'Ajax Product Filter',
					'icon_url'       => YITH_WCMAP_URL . 'assets/images/plugins/ajax-product-filter.svg',
					'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-ajax-product-filter/',
					'description'    => _x(
						'Help your customers to easily find the products they are looking for and improve the user experience of your shop.',
						'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Ajax Product Filter',
						'yith-woocommerce-wishlist'
					),
					'is_active'      => defined( 'YITH_WCAN_PREMIUM' ),
					'is_recommended' => true,
				),
				'wishlist'                      => array(
					'name'           => 'Wishlist',
					'icon_url'       => YITH_WCMAP_URL . '/images/plugins/wishlist.svg',
					'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-wishlist/',
					'description'    => _x( 'Allow your customers to create lists of products they want and share them with family and friends.', '[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Wishlist', 'yith-woocommerce-request-a-quote' ),
					'is_active'      => defined( 'YITH_WCWL_PREMIUM' ),
					'is_recommended' => true,
				),
				'booking'                       => array(
					'name'        => 'Booking and Appointment',
					'icon_url'    => YITH_WCMAP_URL . 'assets/images/plugins/booking.svg',
					'url'         => '//yithemes.com/themes/plugins/yith-woocommerce-booking/',
					'description' => _x(
						'Enable a booking/appointment system to manage renting or booking of services, rooms, houses, cars, accommodation facilities and so on.',
						'[YOUR STORE TOOLS TAB] Description for plugin YITH Bookings',
						'yith-woocommerce-wishlist'
					),
					'is_active'   => defined( 'YITH_WCBK_PREMIUM' ),
					'is_recommended' => false,

				),
				'request-a-quote'               => array(
					'name'           => 'Request a Quote',
					'icon_url'       => YITH_WCMAP_URL . 'assets/images/plugins/request-a-quote.svg',
					'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-request-a-quote/',
					'description'    => _x(
						'Hide prices and/or the "Add to cart" button and let your customers request a custom quote for every product.',
						'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Request a Quote',
						'yith-woocommerce-wishlist'
					),
					'is_active'      => defined( 'YITH_YWRAQ_PREMIUM' ),
					'is_recommended' => false,
				),
				'product-add-ons'               => array(
					'name'           => 'Product Add-Ons & Extra Options',
					'icon_url'       => YITH_WCMAP_URL . 'assets/images/plugins/product-add-ons.svg',
					'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-product-add-ons/',
					'description'    => _x(
						'Add paid or free advanced options to your product pages using fields like radio buttons, checkboxes, drop-downs, custom text inputs, and more.',
						'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Product Add-Ons',
						'yith-woocommerce-wishlist'
					),
					'is_active'      => defined( 'YITH_WAPO_PREMIUM' ),
					'is_recommended' => false,
				),
				'dynamic-pricing-and-discounts' => array(
					'name'           => 'Dynamic Pricing and Discounts',
					'icon_url'       => YITH_WCMAP_URL . 'assets/images/plugins/dynamic-pricing-and-discounts.svg',
					'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-dynamic-pricing-and-discounts/',
					'description'    => _x(
						'Increase conversions through dynamic discounts and price rules, and build powerful and targeted offers.',
						'[YOUR STORE TOOLS TAB] Description for plugin YITH WooCommerce Dynamic Pricing and Discounts',
						'yith-woocommerce-wishlist'
					),
					'is_active'      => defined( 'YITH_YWDPD_PREMIUM' ),
					'is_recommended' => false,
				),
				'recover-abandoned-cart'        => array(
					'name'           => 'Recover Abandoned Cart',
					'icon_url'       => YITH_WCMAP_URL . 'assets/images/plugins/recover-abandoned-cart.svg',
					'url'            => '//yithemes.com/themes/plugins/yith-woocommerce-recover-abandoned-cart/',
					'description'    => _x(
						'Contact users who have added products to the cart without completing the order and try to recover lost sales.',
						'[YOUR STORE TOOLS TAB] Description for plugin Recover Abandoned Cart',
						'yith-woocommerce-wishlist'
					),
					'is_active'      => defined( 'YITH_YWRAC_PREMIUM' ),
					'is_recommended' => false,
				),
			);
			
			foreach ( $items as $key => $item ) {
				if ( is_array( $item['description'] ) ) {
					$items[ $key ]['description'] = implode( '<br />', $item['description'] );
				}

				$items[ $key ]['icon_url'] = YITH_WCMAP_ASSETS_URL . '/images/plugins/' . $key . '.svg';
			}

			return $items;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @return   void
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			/**
			 * APPLY_FILTERS: yith_wcmap_admin_panel_args
			 *
			 * Filters the array with the arguments to build the plugin panel.
			 *
			 * @param array $args Array of arguments.
			 *
			 * @return array
			 */
			$args = apply_filters(
				'yith_wcmap_admin_panel_args',
				array(
					'ui_version'       => 2,
					'create_menu_page' => true,
					'parent_slug'      => '',
					'plugin_slug'      => YITH_WCMAP_SLUG,
					'page_title'       => 'YITH WooCommerce Customize My Account Page',
					'menu_title'       => 'Customize My Account Page',
					'capability'       => 'manage_options',
					'parent'           => '',
					'class'            => yith_set_wrapper_class(),
					'parent_page'      => 'yith_plugin_panel',
					'page'             => self::PANEL_PAGE,
					'admin-tabs'       => $this->get_admin_tabs(),
					'options-path'     => YITH_WCMAP_DIR . '/plugin-options',
					'is_premium'       => defined( 'YITH_WCMAP_PREMIUM' ),
					'is_extended'      => defined( 'YITH_WCMAP_EXTENDED' ),
					'help_tab'         => array(
						'main_video' => array(
							'desc' => _x( 'Check this video to learn how to configure the plugin YITH WooCommerce Customize My Account Page and why you should install it in your shop:', '[HELP TAB] Video title', 'yith-woocommerce-customize-myaccount-page' ),
							'url'  => array(
								'en' => 'https://www.youtube.com/embed/ETTEuWRp00o',
								'it' => 'https://www.youtube.com/embed/omm1WK_AEzI',
								'es' => 'https://www.youtube.com/embed/5RTuT-thEjE',
							),
						),
						'playlists'  => array(
							'en' => 'https://www.youtube.com/playlist?list=PLDriKG-6905npKYDuuPK_bma2b800SfcA',
							'it' => 'https://www.youtube.com/playlist?list=PL9c19edGMs0-qC6DNHxflJ2w-3XWptwK1',
							'es' => 'https://www.youtube.com/playlist?list=PL9Ka3j92PYJOsiu5_wN2SactHEeGcojYY',
						),
						'hc_url'     => 'https://support.yithemes.com/hc/en-us/categories/360003468777-YITH-WOOCOMMERCE-CUSTOMIZE-MY-ACCOUNT-PAGE',
						'doc_url'    => $this->get_doc_url(),
					),'your_store_tools' => array(
						'items' => $this->get_your_store_tools_items(),
					),
				)
			);

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_WCMAP_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}


		/**
		 * Filter endpoint template args.
		 * Add buttons link and group.
		 *
		 * @since  3.12.0
		 * @param array $args An array of template arguments.
		 * @return array
		 */
		public function filter_endpoint_template_args( $args ) {
			$args['actions'] = array_merge(
				$args['actions'],
				array(
					'link'  => array(
						'label'     => __( 'Add link', 'yith-woocommerce-customize-myaccount-page' ),
						'alt-label' => __( 'Close new link', 'yith-woocommerce-customize-myaccount-page' ),
					),
					'group' => array(
						'label'     => __( 'Add group', 'yith-woocommerce-customize-myaccount-page' ),
						'alt-label' => __( 'Close new group', 'yith-woocommerce-customize-myaccount-page' ),
					),
				)
			);

			return $args;
		}

		/**
		 * Print grouped endpoint in admin endpoint list
		 *
		 * @since  3.12.0
		 * @param string $item_key The parent item key.
		 * @param array  $options  The parent item options array.
		 * @param string $type     The parent type.
		 * @return void
		 */
		public function print_grouped_endpoint( $item_key, $options, $type ) {
			if ( empty( $options['children'] ) ) {
				return;
			}

			echo '<ol class="dd-list items">';
			foreach ( (array) $options['children'] as $key => $single_options ) {
				$args = array(
					'item_key' => $key,
					'options'  => $single_options,
					'type'     => isset( $single_options['url'] ) ? 'link' : 'endpoint',
				);

				call_user_func( 'yith_wcmap_admin_print_single_item', $args );
			}
			echo '</ol>';
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @param array $links Links plugin array.
		 * @return mixed
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, self::PANEL_PAGE, true, YITH_WCMAP_SLUG );
			return $links;
		}

		/**
		 * Plugin row meta. Add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @use      plugin_row_meta
		 * @param array    $new_row_meta_args An array of plugin row meta.
		 * @param string[] $plugin_meta       An array of the plugin's metadata,
		 *                                    including the version, author,
		 *                                    author URI, and plugin URI.
		 * @param string   $plugin_file       Path to the plugin file relative to the plugins directory.
		 * @param array    $plugin_data       An array of plugin data.
		 * @param string   $status            Status of the plugin. Defaults are 'All', 'Active',
		 *                                    'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                                    'Drop-ins', 'Search', 'Paused'.
		 * @return   Array
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( defined( 'YITH_WCMAP_INIT' ) && YITH_WCMAP_INIT === $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_WCMAP_SLUG;
				$new_row_meta_args['live_demo']  = array( 'url' => 'https://plugins.yithemes.com/yith-woocommerce-customize-my-account-page/' );
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Enqueue scripts
		 * Premium add icons list as script data.
		 *
		 * @since  1.0.0
		 */
		public function enqueue_scripts() {
			// phpcs:disable WordPress.Security.NonceVerification
			parent::enqueue_scripts();
			if ( isset( $_GET['page'] ) && sanitize_text_field( wp_unslash( $_GET['page'] ) ) === self::PANEL_PAGE ) {
				// font awesome.
				wp_enqueue_style( 'font-awesome', YITH_WCMAP_ASSETS_URL . '/css/font-awesome.min.css', array(), YITH_WCMAP_VERSION );

				if ( empty( $_GET['tab'] ) || in_array( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), array( 'banners', 'endpoints' ), true ) ) {
					wp_localize_script( 'yith_wcmap', 'ywcmap_icons', yith_wcmap_get_icon_list() );
				}
			}
			// phpcs:enable WordPress.Security.NonceVerification
		}
	}
}
