<?php
/**
 * Init premium admin features of the plugin
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Admin_Premium' ) ) {
	/**
	 * WooCommerce Wishlist admin Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Admin_Premium extends YITH_WCWL_Admin_Extended {

		/**
		 * Various links
		 *
		 * @var string
		 * @access public
		 * @since 1.0.0
		 */
		public $showcase_images = array();

		/**
		 * Constructor of the class
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			parent::__construct();

			// register admin notices.
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );

			// add premium settings.
			add_filter( 'yith_wcwl_settings_options', array( $this, 'add_settings_options' ) );
			add_filter( 'yith_wcwl_add_to_wishlist_options', array( $this, 'add_add_to_wishlist_options' ) );
			add_filter( 'yith_wcwl_promotion_email_options', array( $this, 'add_promotion_email_options' ) );

			// register custom panel handling.
			add_action( 'yith_wcwl_after_popular_table', array( $this, 'print_promotion_wizard' ) );

			// register admin actions.
			add_action( 'admin_action_export_users', array( $this, 'export_users_via_csv' ) );
			add_action( 'admin_action_delete_wishlist', array( $this, 'delete_wishlist_from_actions' ) );
			add_action( 'admin_action_send_promotion', array( $this, 'trigger_promotion_email' ) );

			// adds column to product page.
			add_filter( 'manage_edit-product_columns', array( $this, 'add_product_columns' ) );
			add_filter( 'manage_edit-product_sortable_columns', array( $this, 'product_sortable_columns' ) );
			add_action( 'manage_product_posts_custom_column', array( $this, 'render_product_columns' ) );
			add_filter( 'request', array( $this, 'product_request_query' ) );

			// send promotion email.
			add_action( 'wp_ajax_preview_promotion_email', array( $this, 'ajax_preview_promotion_email' ) );
			add_action( 'wp_ajax_calculate_promotion_email_receivers', array( $this, 'ajax_calculate_promotion_email_receivers' ) );

			// compatibility with email templates.
			add_filter( 'yith_wcet_email_template_types', array( $this, 'register_emails_for_custom_templates' ) );

			// WPML compatibility.
			add_action( 'update_option_yith_wcwl_ask_an_estimate_fields', array( $this, 'register_ask_an_estimate_fields_for_translation' ), 10, 2 );

			// admin only ajax.
			add_action( 'wp_ajax_json_search_coupons', array( $this, 'json_search_coupons' ) );
		}

		/* === INITIALIZATION SECTION === */

		/**
		 * Initiator method. Initiate properties.
		 *
		 * @return void
		 * @access private
		 * @since 1.0.0
		 */
		public function init() {
			parent::init();

			// init scripts needed for the promotion wizard.
			$this->register_promotion_wizard_scripts();
		}

		/**
		 * Retrieve the admin panel tabs.
		 *
		 * @return array
		 */
		protected function get_admin_panel_tabs(): array {
			return apply_filters(
				'yith_wcwl_admin_panel_tabs',
				array(
					'dashboard' => array(
						'title' => _x( 'Dashboard', 'Settings tab name', 'yith-woocommerce-wishlist' ),
						'icon'  => 'dashboard',
					),
					'settings'  => array(
						'title' => _x( 'Settings', 'Settings tab name', 'yith-woocommerce-wishlist' ),
						'icon'  => 'settings',
					),
					'email'     => array(
						'title' => __( 'Email Settings', 'yith-woocommerce-wishlist' ),
						'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>',
					),
				)
			);
		}


		/**
		 * Add new options to general settings tab
		 *
		 * @param array $options Array of available options.
		 * @return array Filtered array of options
		 */
		public function add_settings_options( $options ) {
			$settings = $options['settings-general'];

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'disable_wishlist_for_unauthenticated_users' => array(
						'name'      => __( 'Enable wishlist for', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Choose whether to enable the wishlist feature for all users or only for logged-in users', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_disable_wishlist_for_unauthenticated_users',
						'options'   => array(
							'no'  => __( 'All users', 'yith-woocommerce-wishlist' ),
							'yes' => __( 'Only authenticated users', 'yith-woocommerce-wishlist' ),
						),
						'default'   => 'no',
						'type'      => 'yith-field',
						'yith-type' => 'radio',
					),

					'enable_add_to_wishlist_notices' => array(
						'name'      => __( 'Enable Added/Removed notices', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Enable popup notices when the product is added or removed from the wishlist', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_notices_enable',
						'default'   => 'yes',
						'type'      => 'yith-field',
						'yith-type' => 'onoff',
					),

					'enable_add_to_wishlist_tooltip' => array(
						'name'      => __( 'Enable "Add to wishlist" tooltip', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Choose whether to display a tooltip when hovering over Add to wishlist link', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_tooltip_enable',
						'default'   => 'no',
						'type'      => 'yith-field',
						'yith-type' => 'onoff',
					),

					'add_to_wishlist_tooltip_style'  => array(
						'name'         => __( 'Add to wishlist tooltip style', 'yith-woocommerce-wishlist' ),
						'desc'         => __( 'Choose colors for Add to wishlist tooltip', 'yith-woocommerce-wishlist' ),
						'id'           => 'yith_wcwl_tooltip_color',
						'type'         => 'yith-field',
						'yith-type'    => 'multi-colorpicker',
						'colorpickers' => array(
							array(
								'name'    => __( 'Background', 'yith-woocommerce-wishlist' ),
								'id'      => 'background',
								'default' => '#333',
							),
							array(
								'name'    => __( 'Text', 'yith-woocommerce-wishlist' ),
								'id'      => 'text',
								'default' => '#fff',
							),
						),
						'deps'         => array(
							'id'    => 'yith_wcwl_tooltip_enable',
							'value' => 'yes',
						),
					),
				),
				'general_section_start'
			);

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'multi_wishlist_section_start' => array(
						'name' => __( 'Multi-wishlist settings', 'yith-woocommerce-wishlist' ),
						'type' => 'title',
						'desc' => '',
						'id'   => 'yith_wcwl_multi_wishlist_settings',
					),

					'enable_multi_wishlist'        => array(
						'name'      => __( 'Enable multi-wishlist feature', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Allow customers to create and manage multiple wishlists', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_multi_wishlist_enable',
						'default'   => 'no',
						'type'      => 'yith-field',
						'yith-type' => 'onoff',
					),

					'enable_multi_wishlist_for_unauthenticated_users' => array(
						'name'      => __( 'Enable multiple wishlists for', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Choose whether to enable the multi-wishlist feature for all users or just for logged-in users', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_enable_multi_wishlist_for_unauthenticated_users',
						'options'   => array(
							'yes' => __( 'All users', 'yith-woocommerce-wishlist' ),
							'no'  => __( 'Only authenticated users', 'yith-woocommerce-wishlist' ),
						),
						'default'   => 'no',
						'type'      => 'yith-field',
						'yith-type' => 'radio',
						'deps'      => array(
							'id'    => 'yith_wcwl_multi_wishlist_enable',
							'value' => 'yes',
						),
					),

					'show_login_notice'            => array(
						'name'      => __( 'Login message for non-authenticated users', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_show_login_notice',
						'desc'      => __( 'Enter the message to ask unauthenticated users to login so they will be able to use the multi-wishlist feature.<br/>Use the placeholder %login_anchor% (set up the text in the following option) to add an anchor and redirect users to the Login page.', 'yith-woocommerce-wishlist' ),
						'default'   => __( 'Please %login_anchor% to use all the wishlist features', 'yith-woocommerce-wishlist' ),
						'type'      => 'yith-field',
						'yith-type' => 'text',
						'deps'      => array(
							'id'    => 'yith_wcwl_enable_multi_wishlist_for_unauthenticated_users',
							'value' => 'no',
						),
					),

					'login_anchor_text'            => array(
						'name'      => __( 'Login anchor text', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_login_anchor_text',
						'desc'      => __( 'Set up here the text of the Login link that replace %login_anchor%', 'yith-woocommerce-wishlist' ),
						'default'   => __( 'login', 'yith-woocommerce-wishlist' ),
						'type'      => 'yith-field',
						'yith-type' => 'text',
						'deps'      => array(
							'id'    => 'yith_wcwl_enable_multi_wishlist_for_unauthenticated_users',
							'value' => 'no',
						),
					),

					'multi_wishlist_section_end'   => array(
						'type' => 'sectionend',
						'id'   => 'yith_wcwl_multi_wishlist_settings',
					),
				),
				'general_section_end'
			);

			$options['settings-general'] = $settings;

			return $options;
		}

		/**
		 * Add new options to Add to Wishlist settings tab
		 *
		 * @param array $options Array of available options.
		 * @return array Filtered array of options
		 */
		public function add_add_to_wishlist_options( $options ) {
			$settings = $options['settings-add_to_wishlist'];

			$multi_wishlist_enabled = 'yes' === get_option( 'yith_wcwl_multi_wishlist_enable', 'yes' );

			if ( $multi_wishlist_enabled ) {
				$settings = yith_wcwl_merge_in_array(
					$settings,
					array(
						'enable_add_to_wishlist_modal' => array(
							'name'      => __( 'When clicking on Add to wishlist', 'yith-woocommerce-wishlist' ),
							'desc'      => __( 'Choose the default action for new products added to the wishlist.', 'yith-woocommerce-wishlist' ),
							'id'        => 'yith_wcwl_modal_enable',
							'default'   => 'yes',
							'type'      => 'yith-field',
							'yith-type' => 'radio',
							'options'   => array(
								'default' => __( 'Automatically add to the default list', 'yith-woocommerce-wishlist' ),
								'yes'     => __( 'Show a modal window to allow users to choose a wishlist', 'yith-woocommerce-wishlist' ),
								'no'      => __( 'Show a dropdown to allow users to choose a wishlist', 'yith-woocommerce-wishlist' ),
							),
						),
						'add_to_wishlist_modal_closing_behaviour' => array(
							'name'      => __( 'When product is added to wishlist', 'yith-woocommerce-wishlist' ),
							'desc'      => __( 'Choose what should happen to the modal, when a product is added to the list.', 'yith-woocommerce-wishlist' ),
							'id'        => 'yith_wcwl_modal_close_behaviour',
							'default'   => 'close',
							'type'      => 'yith-field',
							'yith-type' => 'radio',
							'deps'      => array(
								'id'    => 'yith_wcwl_modal_enable',
								'value' => 'yes',
							),
							'options'   => array(
								'close' => __( 'Automatically close the modal', 'yith-woocommerce-wishlist' ),
								'open'  => __( 'Leave the modal open', 'yith-woocommerce-wishlist' ),
							),
						),
					),
					'general_section_start'
				);

				$settings['after_add_to_wishlist_behaviour']['options']['modal'] = __( 'Add to wishlist button now opens a modal to move or remove items (available only with multi-wishlist option enabled)', 'yith-woocommerce-wishlist' );
			}

			// add options for product page.
			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'show_times_in_wishlist' => array(
						'name'      => __( 'Show a count of users with a specific product in wishlist', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Show a counter on the product page that allows your customers to know how many times the product has been added to a wishlist', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_show_counter',
						'default'   => 'no',
						'type'      => 'yith-field',
						'yith-type' => 'onoff',
					),
				),
				'add_to_wishlist_position'
			);

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'add_to_wishlist_popup_text' => array(
						'name'    => __( '"Add to wishlist" popup button text', 'yith-woocommerce-wishlist' ),
						'id'      => 'yith_wcwl_add_to_wishlist_popup_text',
						'desc'    => __( 'Text of the "Add to wishlist" button in the popup', 'yith-woocommerce-wishlist' ),
						'default' => __( 'Add to wishlist', 'yith-woocommerce-wishlist' ),
						'type'    => 'text',
					),
				),
				'already_in_wishlist_text'
			);

			$options['settings-add_to_wishlist'] = $settings;

			return $options;
		}

		/**
		 * Add new options to wishlist settings tab
		 *
		 * @param array $options Array of available options.
		 * @return array Filtered array of options
		 */
		public function add_wishlist_options( $options ) {
			$options = parent::add_wishlist_options( $options );

			$settings = $options['settings-wishlist_page'];

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'wishlist_manage_layout'      => array(
						'name'      => __( 'Layout for wishlist view', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Select a style for your "Manage wishlists" page', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_wishlist_manage_layout',
						'type'      => 'yith-field',
						'yith-type' => 'radio',
						'options'   => array(
							'traditional' => __( 'Traditional', 'yith-woocommerce-wishlist' ),
							'modern'      => __( 'Modern grid', 'yith-woocommerce-wishlist' ),
						),
						'default'   => 'traditional',
					),
					'show_manage_num_of_items'    => array(
						'name'          => __( 'Show wishlist info', 'yith-woocommerce-wishlist' ),
						'desc'          => __( 'Number of items in wishlist', 'yith-woocommerce-wishlist' ),
						'id'            => 'yith_wcwl_manage_num_of_items_show',
						'type'          => 'checkbox',
						'default'       => '',
						'checkboxgroup' => 'start',
					),
					'show_manage_creation_date'   => array(
						'name'          => __( 'Show wishlist info', 'yith-woocommerce-wishlist' ),
						'desc'          => __( 'Date of creation of the wishlist', 'yith-woocommerce-wishlist' ),
						'id'            => 'yith_wcwl_manage_creation_date_show',
						'type'          => 'checkbox',
						'default'       => '',
						'checkboxgroup' => 'manage_info',
					),
					'show_manage_download_pdf'    => array(
						'name'          => __( 'Show wishlist info', 'yith-woocommerce-wishlist' ),
						'desc'          => __( 'Download a PDF version of the wishlist', 'yith-woocommerce-wishlist' ),
						'id'            => 'yith_wcwl_manage_download_pdf_show',
						'type'          => 'checkbox',
						'default'       => '',
						'checkboxgroup' => 'manage_info',
					),
					'show_manage_rename_wishlist' => array(
						'name'          => __( 'Show wishlist info', 'yith-woocommerce-wishlist' ),
						'desc'          => __( 'Rename wishlist button', 'yith-woocommerce-wishlist' ),
						'id'            => 'yith_wcwl_manage_rename_wishlist_show',
						'type'          => 'checkbox',
						'default'       => 'no',
						'checkboxgroup' => 'manage_info',
					),
					'show_manage_delete_wishlist' => array(
						'name'          => __( 'Show wishlist info', 'yith-woocommerce-wishlist' ),
						'desc'          => __( 'Delete wishlist button', 'yith-woocommerce-wishlist' ),
						'id'            => 'yith_wcwl_manage_delete_wishlist_show',
						'type'          => 'checkbox',
						'default'       => 'yes',
						'checkboxgroup' => 'end',
					),
					'new_wishlist_as_popup'       => array(
						'name'      => __( '"Create wishlist" in popup', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Create a new wishlist in the popup instead of using the endpoint', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_create_wishlist_popup',
						'type'      => 'yith-field',
						'yith-type' => 'onoff',
						'default'   => 'no',
					),
				),
				'wishlist_page'
			);

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'wishlist_layout' => array(
						'name'      => __( 'Layout for product list', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Select a style for displaying your wishlist page', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_wishlist_layout',
						'type'      => 'yith-field',
						'yith-type' => 'radio',
						'default'   => 'traditional',
						'options'   => array(
							'traditional' => __( 'Traditional', 'yith-woocommerce-wishlist' ),
							'modern'      => __( 'Modern grid', 'yith-woocommerce-wishlist' ),
							'images'      => __( 'Only images with info at click', 'yith-woocommerce-wishlist' ),
						),
					),
				),
				'wishlist_section_start'
			);

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'show_price_changes' => array(
						'name'          => __( 'In wishlist table show', 'yith-woocommerce-wishlist' ),
						'desc'          => __( 'Price variation info (show the price difference compared to when the product was added to the list)', 'yith-woocommerce-wishlist' ),
						'id'            => 'yith_wcwl_price_changes_show',
						'type'          => 'checkbox',
						'default'       => '',
						'checkboxgroup' => 'wishlist_info',
					),
				),
				'show_unit_price'
			);

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'show_cb' => array(
						'name'          => __( 'In wishlist table show', 'yith-woocommerce-wishlist' ),
						'desc'          => __( 'Checkbox to select multiple items, add them to the cart or delete them with one click', 'yith-woocommerce-wishlist' ),
						'id'            => 'yith_wcwl_cb_show',
						'type'          => 'checkbox',
						'default'       => '',
						'checkboxgroup' => 'wishlist_info',
					),
				),
				'show_remove_button'
			);

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'show_move_to_another_wishlist' => array(
						'name'      => __( 'Show Move to another wishlist', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Enable the option to move the product to another wishlist', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_show_move_to_another_wishlist',
						'type'      => 'yith-field',
						'yith-type' => 'onoff',
						'default'   => '',
					),
					'move_to_another_wishlist_type' => array(
						'name'      => __( 'Move to another wishlist - style', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Choose the look and feel of the "Move to another wishlist" option', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_move_to_another_wishlist_type',
						'type'      => 'yith-field',
						'yith-type' => 'radio',
						'default'   => 'popup',
						'options'   => array(
							'select' => __( 'Select dropdown with all wishlists', 'yith-woocommerce-wishlist' ),
							'popup'  => __( 'Link to a popup', 'yith-woocommerce-wishlist' ),
						),
						'deps'      => array(
							'id'    => 'yith_wcwl_show_move_to_another_wishlist',
							'value' => 'yes',
						),
					),
				),
				'repeat_remove_button'
			);

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'enable_add_all_to_cart' => array(
						'name'      => __( 'Enable "Add all to cart"', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Enable "Add all to cart" button to let customers add all the products in the wishlist to the cart', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_enable_add_all_to_cart',
						'type'      => 'yith-field',
						'yith-type' => 'onoff',
						'default'   => 'no',
					),
					'enable_drag_n_drop'     => array(
						'name'      => __( 'Enable drag and drop option', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Enable drag and drop option so users can arrange the order of products in the wishlist', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_enable_drag_and_drop',
						'type'      => 'yith-field',
						'yith-type' => 'onoff',
						'default'   => 'no',
					),
					'enable_wishlist_links'  => array(
						'name'      => __( 'Show links to pages', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Show the links to "Manage", "Create" and "Search" pages after the wishlist table', 'yith-woocommerce-wishlist' ),
						'id'        => 'yith_wcwl_enable_wishlist_links',
						'type'      => 'yith-field',
						'yith-type' => 'onoff',
						'default'   => 'yes',
					),
				),
				'remove_after_add_to_cart'
			);

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'create_wishlist_page_title' => array(
						'name'    => __( '"Create wishlist" page name', 'yith-woocommerce-wishlist' ),
						'id'      => 'yith_wcwl_wishlist_create_title',
						'desc'    => __( 'Enter the title for the "Create wishlist" page', 'yith-woocommerce-wishlist' ),
						'default' => __( 'Create a new wishlist', 'yith-woocommerce-wishlist' ),
						'type'    => 'text',
					),
					'manage_wishlist_page_title' => array(
						'name'    => __( '"Manage wishlist" page name', 'yith-woocommerce-wishlist' ),
						'id'      => 'yith_wcwl_wishlist_manage_title',
						'desc'    => __( 'Enter the title for "Manage wishlists" page', 'yith-woocommerce-wishlist' ),
						'default' => __( 'Your wishlists', 'yith-woocommerce-wishlist' ),
						'type'    => 'text',
					),
					'search_wishlist_page_title' => array(
						'name'    => __( '"Search wishlist" page name', 'yith-woocommerce-wishlist' ),
						'id'      => 'yith_wcwl_wishlist_search_title',
						'desc'    => __( 'Enter the title for "Search wishlists" page', 'yith-woocommerce-wishlist' ),
						'default' => __( 'Search a wishlist', 'yith-woocommerce-wishlist' ),
						'type'    => 'text',
					),
				),
				'default_wishlist_title'
			);

			$options['settings-wishlist_page'] = $settings;

			return $options;
		}

		/**
		 * Add new options to Promotional settings tab
		 *
		 * @param array $options Array of available options.
		 * @return array Filtered array of options
		 */
		public function add_promotion_email_options( $options ) {
			$settings = $options['promotion_email'];

			// retrieve available categories.
			$product_categories = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
					'number'     => 0,
					'fields'     => 'id=>name',
				)
			);

			// retrieve on-sale item email configuration.
			$on_sale_item_saved_options      = get_option( 'woocommerce_yith_wcwl_on_sale_item_settings', array() );
			$on_sale_item_exclusions_options = array();

			if ( ! empty( $on_sale_item_saved_options['product_exclusions'] ) ) {
				foreach ( $on_sale_item_saved_options['product_exclusions'] as $product_id ) {
					$product = wc_get_product( $product_id );

					if ( ! $product ) {
						continue;
					}

					$on_sale_item_exclusions_options[ $product_id ] = $product->get_formatted_name();
				}
			}

			$settings = array_merge(
				array(
					'promotion_email_start'        => array(
						'name' => __( '"Promotional" email', 'yith-woocommerce-wishlist' ),
						'type' => 'title',
						'desc' => '',
						'id'   => 'yith_wcwl_promotional_email',
					),

					'promotion_email_mail_type'    => array(
						'name'      => __( 'Email type', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Choose which type of email to send', 'yith-woocommerce-wishlist' ),
						'id'        => 'woocommerce_yith_wcwl_promotion_mail_settings[email_type]',
						'default'   => 'html',
						'type'      => 'yith-field',
						'yith-type' => 'select',
						'class'     => 'wc-enhanced-select',
						'options'   => array(
							'plain'     => __( 'Plain', 'yith-woocommerce-wishlist' ),
							'html'      => __( 'HTML', 'yith-woocommerce-wishlist' ),
							'multipart' => __( 'Multipart', 'yith-woocommerce-wishlist' ),
						),
					),

					'promotion_email_mail_heading' => array(
						'name'    => __( 'Email heading', 'yith-woocommerce-wishlist' ),
						'desc'    => __( 'Enter the title for the email notification. Leave blank to use the default heading: "<i>There is a deal for you!</i>"', 'yith-woocommerce-wishlist' ),
						'id'      => 'woocommerce_yith_wcwl_promotion_mail_settings[heading]',
						'default' => '',
						'type'    => 'text',
					),

					'promotion_email_mail_subject' => array(
						'name'    => __( 'Email subject', 'yith-woocommerce-wishlist' ),
						'desc'    => __( 'Enter the mail subject line. Leave blank to use the default subject: "<i>A product of your wishlist is on sale</i>"', 'yith-woocommerce-wishlist' ),
						'id'      => 'woocommerce_yith_wcwl_promotion_mail_settings[subject]',
						'default' => '',
						'type'    => 'text',
					),

					'promotion_email_html_content' => array(
						'name'      => __( 'Email HTML content', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'This field lets you modify the main content of the HTML email. You can use the following placeholders: <code>{user_name}</code> <code>{user_email}</code> <code>{user_first_name}</code> <code>{user_last_name}</code> <code>{product_image}</code> <code>{product_name}</code> <code>{product_price}</code> <code>{coupon_code}</code> <code>{coupon_amount}</code> <code>{product_url}</code> <code>{add_to_cart_url}</code>', 'yith-woocommerce-wishlist' ),
						'id'        => 'woocommerce_yith_wcwl_promotion_mail_settings[content_html]',
						'default'   => class_exists( 'YITH_WCWL_Promotion_Email' ) ? YITH_WCWL_Promotion_Email::get_default_content( 'html' ) : '',
						'type'      => 'yith-field',
						'yith-type' => 'textarea',
						'deps'      => array(
							'id'    => 'woocommerce_yith_wcwl_promotion_mail_settings[email_type]',
							'value' => 'html,multipart',
						),
					),

					'promotion_email_text_content' => array(
						'name'      => __( 'Email plain content', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'This field lets you modify the main content of the text email. You can use the following placeholders: <code>{user_name}</code> <code>{user_email}</code> <code>{user_first_name}</code> <code>{user_last_name}</code> <code>{product_name}</code> <code>{product_price}</code> <code>{coupon_code}</code> <code>{coupon_amount}</code> <code>{coupon_value}</code> <code>{product_url}</code> <code>{add_to_cart_url}</code>', 'yith-woocommerce-wishlist' ),
						'id'        => 'woocommerce_yith_wcwl_promotion_mail_settings[content_text]',
						'default'   => class_exists( 'YITH_WCWL_Promotion_Email' ) ? YITH_WCWL_Promotion_Email::get_default_content( 'plain' ) : '',
						'type'      => 'yith-field',
						'yith-type' => 'textarea',
					),

					'promotion_email_end'          => array(
						'type' => 'sectionend',
						'id'   => 'yith_wcwl_promotional_email',
					),
				),
				$settings
			);

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'on_sale_item_email_start'        => array(
						'name' => __( '"On sale item" email', 'yith-woocommerce-wishlist' ),
						'type' => 'title',
						'desc' => '',
						'id'   => 'yith_wcwl_on_sale_item_email',
					),

					'on_sale_item_email_enable'       => array(
						'name'      => __( 'Enable "On sale item" email', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Enable this email to send notifications to your customers whenever a product in their wishlist is on sale', 'yith-woocommerce-wishlist' ),
						'id'        => 'woocommerce_yith_wcwl_on_sale_item_settings[enabled]',
						'default'   => 'no',
						'type'      => 'yith-field',
						'yith-type' => 'onoff',
					),

					'on_sale_item_email_product_exclusions' => array(
						'name'      => __( 'Product exclusions', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Select products that should not trigger the "on sale item" notifications', 'yith-woocommerce-wishlist' ),
						'id'        => 'woocommerce_yith_wcwl_on_sale_item_settings[product_exclusions]',
						'type'      => 'yith-field',
						'yith-type' => 'select',
						'multiple'  => true,
						'class'     => 'wc-product-search',
						'options'   => $on_sale_item_exclusions_options,
					),

					'on_sale_item_email_category_exclusions' => array(
						'name'      => __( 'Category exclusions', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Select product categories that should not trigger the "on sale item" notification', 'yith-woocommerce-wishlist' ),
						'id'        => 'woocommerce_yith_wcwl_on_sale_item_settings[category_exclusions]',
						'type'      => 'yith-field',
						'yith-type' => 'select',
						'multiple'  => true,
						'class'     => 'wc-enhanced-select',
						'options'   => $product_categories,
					),

					'on_sale_item_email_mail_type'    => array(
						'name'      => __( 'Email type', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'Choose which type of email to send', 'yith-woocommerce-wishlist' ),
						'id'        => 'woocommerce_yith_wcwl_on_sale_item_settings[email_type]',
						'default'   => 'html',
						'type'      => 'yith-field',
						'yith-type' => 'select',
						'class'     => 'wc-enhanced-select',
						'options'   => array(
							'plain'     => __( 'Plain', 'yith-woocommerce-wishlist' ),
							'html'      => __( 'HTML', 'yith-woocommerce-wishlist' ),
							'multipart' => __( 'Multipart', 'yith-woocommerce-wishlist' ),
						),
					),

					'on_sale_item_email_mail_heading' => array(
						'name'    => __( 'Email heading', 'yith-woocommerce-wishlist' ),
						'desc'    => __( 'Enter the title for the email notification. Leave blank to use the default heading: "<i>An item of your wishlist is on sale!</i>"', 'yith-woocommerce-wishlist' ),
						'id'      => 'woocommerce_yith_wcwl_on_sale_item_settings[heading]',
						'default' => '',
						'type'    => 'text',
					),

					'on_sale_item_email_mail_subject' => array(
						'name'    => __( 'Email subject', 'yith-woocommerce-wishlist' ),
						'desc'    => __( 'Enter the mail subject line. Leave blank to use the default subject: "<i>An item of your wishlist is on sale!</i>"', 'yith-woocommerce-wishlist' ),
						'id'      => 'woocommerce_yith_wcwl_on_sale_item_settings[subject]',
						'default' => '',
						'type'    => 'text',
					),

					'on_sale_item_email_html_content' => array(
						'name'      => __( 'Email HTML content', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'This field lets you modify the main content of the HTML email. You can use the following placeholders: <code>{user_name}</code> <code>{user_email}</code> <code>{user_first_name}</code> <code>{user_last_name}</code> <code>{products_table}</code> <code>{unsubscribe_link}</code>', 'yith-woocommerce-wishlist' ),
						'id'        => 'woocommerce_yith_wcwl_on_sale_item_settings[content_html]',
						'default'   => class_exists( 'YITH_WCWL_On_Sale_Item_Email' ) ? YITH_WCWL_On_Sale_Item_Email::get_default_content( 'html' ) : '',
						'type'      => 'yith-field',
						'yith-type' => 'textarea',
					),

					'on_sale_item_email_text_subject' => array(
						'name'      => __( 'Email plain content', 'yith-woocommerce-wishlist' ),
						'desc'      => __( 'This field lets you modify the main content of the text email. You can use the following placeholders: <code>{user_name}</code> <code>{user_email}</code> <code>{user_first_name}</code> <code>{user_last_name}</code> <code>{products_list}</code> <code>{unsubscribe_url}</code>', 'yith-woocommerce-wishlist' ),
						'id'        => 'woocommerce_yith_wcwl_on_sale_item_settings[content_text]',
						'default'   => class_exists( 'YITH_WCWL_On_Sale_Item_Email' ) ? YITH_WCWL_On_Sale_Item_Email::get_default_content( 'plain' ) : '',
						'type'      => 'yith-field',
						'yith-type' => 'textarea',
					),

					'on_sale_item_email_end'          => array(
						'type' => 'sectionend',
						'id'   => 'yith_wcwl_on_sale_item_email',
					),
				),
				'back_in_stock_email_end'
			);

			$options['promotion_email'] = $settings;

			return $options;
		}

		/**
		 * Register promotion wizard scripts
		 *
		 * @since 3.18.0
		 */
		public function register_promotion_wizard_scripts() {
			$prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'unminified/' : '';
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'yith-wcwl-promotion-wizard', YITH_WCWL_URL . 'assets/js/' . $prefix . 'admin/yith-wcwl-promotion-wizard' . $suffix . '.js', array( 'jquery', 'wc-backbone-modal', 'jquery-blockui' ), YITH_WCWL_Frontend()->version, true );

			$emails    = WC()->mailer()->get_emails();
			$email_obj = isset( $emails['yith_wcwl_promotion_mail'] ) ? $emails['yith_wcwl_promotion_mail'] : false;

			if ( $email_obj ) {
				$email_obj->init_settings();
				$promotion_settings = $email_obj->settings;
			} else {
				$promotion_settings = get_option( 'woocommerce_yith_wcwl_promotion_mail_settings', array() );
			}

			wp_localize_script(
				'yith-wcwl-admin',
				'yith_wcwl',
				array(
					'promotion' => $promotion_settings,
					'nonce'     => array(
						'preview_promotion_email' => wp_create_nonce( 'preview_promotion_email' ),
						'calculate_promotion_email_receivers' => wp_create_nonce( 'calculate_promotion_email_receivers' ),
					),
				)
			);
		}

		/* === PANEL HANDLING === */

		/**
		 * Print admin notices for wishlist settings page
		 *
		 * @return void
		 * @since 2.0.7
		 */
		public function admin_notices() {
			$email_sent = isset( $_GET['email_sent'] ) ? $_GET['email_sent'] : false; // phpcs:ignore WordPress.Security

			if ( $email_sent ) {
				$res = is_numeric( $email_sent ) ? intval( $email_sent ) : sanitize_text_field( wp_unslash( $email_sent ) );

				if ( $res ) {
					?>
					<div class="updated fade">
						<p><?php esc_html_e( 'Promotional email correctly scheduled', 'yith-woocommerce-wishlist' ); ?></p>
					</div>
					<?php
				} else {
					?>
					<div class="updated fade">
						<p><?php esc_html_e( 'There was an error while scheduling emails; please, try again later', 'yith-woocommerce-wishlist' ); ?></p>
					</div>
					<?php
				}
			}
		}

		/**
		 * Print template for Create Promotion wizard
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function print_promotion_wizard() {
			$emails    = WC()->mailer()->get_emails();
			$email_obj = isset( $emails['yith_wcwl_promotion_mail'] ) ? $emails['yith_wcwl_promotion_mail'] : false;

			if ( ! $email_obj ) {
				return;
			}

			include YITH_WCWL_DIR . 'templates/admin/promotion-wizard.php';
		}

		/* === REQUEST HANDLING === */

		/**
		 * Handle admin requests to delete a wishlist
		 *
		 * @return void
		 * @since 2.0.6
		 */
		public function delete_wishlist_from_actions() {
			if ( ! empty( $_REQUEST['wishlist_id'] ) ) {
				if ( isset( $_REQUEST['delete_wishlist'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['delete_wishlist'] ) ), 'delete_wishlist' ) ) {
					$wishlist_id = sanitize_text_field( wp_unslash( $_REQUEST['wishlist_id'] ) );
					try {
						YITH_WCWL_Premium()->remove_wishlist( $wishlist_id );
					} catch ( Exception $e ) { // phpcs:ignore
						// do nothing.
					}
				}
			}

			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'page' => 'yith_wcwl_panel',
							'tab'  => 'dashboard-lists',
						),
						admin_url( 'admin.php' )
					)
				)
			);
			die();
		}

		/**
		 * Export users that added a specific product to their wishlists
		 *
		 * @return void
		 * @since 2.1.3
		 */
		public function export_users_via_csv() {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'export_users' ) ) {
				wp_safe_redirect(
					add_query_arg(
						array(
							'page' => 'yith_wcwl_panel',
							'tab'  => 'dashboard-popular',
						),
						admin_url( 'admin.php' )
					)
				);
				die;
			}
			$product_id = isset( $_GET['product_id'] ) ? intval( $_GET['product_id'] ) : false;
			$product    = wc_get_product( $product_id );

			$items = YITH_WCWL_Wishlist_Factory::get_wishlist_items(
				array(
					'product_id'  => $product_id,
					'user_id'     => false,
					'session_id'  => false,
					'wishlist_id' => 'all',
				)
			);

			if ( ! empty( $items ) ) {

				$formatted_users = array();

				foreach ( $items as $item ) {
					$user_obj = $item->get_user();
					$user_id  = $item->get_user_id();

					if ( ! $user_obj || isset( $formatted_users[ $user_id ] ) ) {
						continue;
					}

					/**
					 * APPLY_FILTERS: yith_wcwl_csv_export_users_data
					 *
					 * Filter the user data to be exported into CSV.
					 *
					 * @param array   $user_data User data to be exported
					 * @param int     $user_id   User ID
					 * @param WP_User $user_obj User object
					 *
					 * @return array
					 */
					$formatted_users[ $user_id ] = apply_filters(
						'yith_wcwl_csv_export_users_data',
						array(
							$user_id,
							$user_obj->user_email,
							! empty( $user_obj->billing_first_name ) ? $user_obj->billing_first_name : $user_obj->first_name,
							! empty( $user_obj->billing_last_name ) ? $user_obj->billing_last_name : $user_obj->last_name,
						),
						$user_id,
						$user_obj
					);
				}

				if ( ! empty( $formatted_users ) ) {
					$sitename  = sanitize_key( get_bloginfo( 'name' ) );
					$sitename .= ( ! empty( $sitename ) ) ? '-' : '';
					$filename  = $sitename . 'wishlist-users-' . sanitize_title_with_dashes( $product->get_title() ) . '-' . gmdate( 'Y-m-d-H-i' ) . '.csv';

					// Add Labels to CSV.
					/**
					 * APPLY_FILTERS: yith_wcwl_csv_export_users_labels
					 *
					 * Filter the labels of the user data to be exported into CSV.
					 *
					 * @param array $labels Array of labels
					 *
					 * @return array
					 */
					$formatted_users_labels[] = apply_filters(
						'yith_wcwl_csv_export_users_labels',
						array(
							__( 'User ID', 'yith-woocommerce-wishlist' ),
							__( 'User Email', 'yith-woocommerce-wishlist' ),
							__( 'User First Name', 'yith-woocommerce-wishlist' ),
							__( 'User Last Name', 'yith-woocommerce-wishlist' ),
						)
					);

					$formatted_users = array_merge( $formatted_users_labels, $formatted_users );

					header( 'Content-Description: File Transfer' );
					header( 'Content-Disposition: attachment; filename=' . $filename );
					header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

					$df = fopen( 'php://output', 'w' );

					foreach ( $formatted_users as $row ) {
						fputcsv( $df, $row );
					}

					fclose( $df ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
				}
			}

			die();
		}

		/* === WISHLIST COUNT PRODUCT COLUMN === */

		/**
		 * Add column to product table, to show product occurrences in wishlists
		 *
		 * @param array $columns Array of columns for products table.
		 * @return array
		 * @since 2.0.0
		 */
		public function add_product_columns( $columns ) {
			$columns['wishlist_count'] = __( 'Wishlist Count', 'yith-woocommerce-wishlist' );
			return $columns;
		}

		/**
		 * Render column of occurrences in product table
		 *
		 * @param string $column Column to render.
		 * @return void
		 * @since 2.0.0
		 */
		public function render_product_columns( $column ) {
			global $post;

			if ( 'wishlist_count' === $column ) {
				echo (int) YITH_WCWL()->count_product_occurrences( $post->ID );
			}
		}

		/**
		 * Register column of occurrences in wishlist as sortable
		 *
		 * @param array $columns Columns that can be sorted in product list table.
		 * @return array
		 * @since 2.0.0
		 */
		public function product_sortable_columns( $columns ) {
			$columns['wishlist_count'] = 'wishlist_count';
			return $columns;
		}

		/**
		 * Alter post query when ordering for wishlist occurrences
		 *
		 * @param array $vars Arguments used to filter products for the table.
		 * @return array
		 * @since 2.0.0
		 */
		public function product_request_query( $vars ) {
			global $typenow, $wp_query;

			if ( 'product' === $typenow ) {
				// Sorting.
				if ( isset( $vars['orderby'] ) ) {
					if ( 'wishlist_count' === $vars['orderby'] ) {
						add_filter( 'posts_join', array( 'YITH_WCWL_Wishlist_Item_Data_Store', 'filter_join_for_wishlist_count' ) );
						add_filter( 'posts_orderby', array( 'YITH_WCWL_Wishlist_Item_Data_Store', 'filter_orderby_for_wishlist_count' ) );
					}
				}
			}

			return $vars;
		}

		/* === SEND PROMOTION EMAIL === */

		/**
		 * Preview promotional email template
		 *
		 * @param bool $return Whether to return or echo the result (@since 3.0.0).
		 *
		 * @return string
		 * @since 2.0.7
		 */
		public function preview_promotion_email( $return = false ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.returnFound
			if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'preview_promotion_email' ) ) {
				return '';
			}

			$product_id    = isset( $_REQUEST['product_id'] ) ? array_filter( array_map( 'intval', (array) $_REQUEST['product_id'] ) ) : false;
			$template      = isset( $_REQUEST['template'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['template'] ) ) : 'html';
			$content_html  = isset( $_REQUEST['content_html'] ) ? wp_kses_post( wp_unslash( $_REQUEST['content_html'] ) ) : false;
			$content_text  = isset( $_REQUEST['content_text'] ) ? sanitize_textarea_field( wp_unslash( $_REQUEST['content_text'] ) ) : false;
			$coupon        = isset( $_REQUEST['coupon'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['coupon'] ) ) : false;
			$template_path = '';

			if ( ! in_array( $template, array( 'html', 'plain' ), true ) ) {
				$template = 'html';
			}

			if ( is_array( $product_id ) ) {
				$product_id = array_shift( $product_id );
			}

			if ( 'plain' === $template ) {
				$template_path = 'plain/';
			}

			// load the mailer class.
			$mailer        = WC()->mailer();
			$email         = $mailer->emails['yith_wcwl_promotion_mail'];
			$email->user   = get_user_by( 'id', get_current_user_id() );
			$email->object = wc_get_product( $product_id );

			// set contents.
			if ( $content_html ) {
				$email->content_html = wpautop( $content_html );
			}
			if ( $content_text ) {
				$email->content_text = $content_text;
			}

			// set coupon.
			if ( $coupon ) {
				$email->coupon = new WC_Coupon( $coupon );
			}

			// get the preview email subject.
			$email_heading = $email->get_heading();
			$email_content = $email->{'get_custom_content_' . $template}();

			// get the preview email content.
			ob_start();
			include YITH_WCWL_DIR . 'templates/emails/' . $template_path . 'promotion.php';
			$message = ob_get_clean();

			if ( 'plain' === $template ) {
				$message = nl2br( $message );
			}

			$message = $email->style_inline( $message );

			// print the preview email.
			if ( $return ) {
				return $message;
			}

			echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Preview promotion email on ajax call
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function ajax_preview_promotion_email() {
			if ( ! current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
				die;
			}

			$this->preview_promotion_email();
			die;
		}

		/**
		 * Calculate the number of receivers for the current email and echo it as json content
		 *
		 * @return void
		 */
		public function ajax_calculate_promotion_email_receivers() {
			if ( ! current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
				die;
			}

			if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'calculate_promotion_email_receivers' ) ) {
				die;
			}

			$product_id = isset( $_REQUEST['product_id'] ) ? array_filter( array_map( 'intval', (array) $_REQUEST['product_id'] ) ) : false;
			$user_id    = isset( $_REQUEST['user_id'] ) ? array_filter( array_map( 'intval', (array) $_REQUEST['user_id'] ) ) : false;

			$count = 0;

			if ( $user_id ) {
				$count = is_array( $user_id ) ? count( $user_id ) : 1;
			} else {
				$receivers_ids = array();

				foreach ( $product_id as $id ) {
					$items = YITH_WCWL_Wishlist_Factory::get_wishlist_items(
						array(
							'wishlist_id' => 'all',
							'session_id'  => false,
							'user_id'     => false,
							'product_id'  => $id,
						)
					);

					if ( ! empty( $items ) ) {
						foreach ( $items as $item ) {
							$receivers_ids[] = $item->get_user_id();
						}
					}

					$receivers_ids = array_unique( $receivers_ids );
					$count        += count( $receivers_ids );
				}
			}

			wp_send_json(
				array(
					'count' => $count,
					'label' => sprintf( '%d %s', $count, _n( 'user', 'users', $count, 'yith-woocommerce-wishlist' ) ),
				)
			);
		}

		/**
		 * Trigger event to send the promotion email
		 *
		 * @return void
		 * @since 2.0.7
		 */
		public function trigger_promotion_email() {
			if ( ! isset( $_POST['send_promotion_email'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['send_promotion_email'] ) ), 'send_promotion_email_action' ) ) {
				return;
			}

			if ( ! isset( $_POST['product_id'] ) && ! isset( $_POST['user_id'] ) ) {
				return;
			}

			$product_id    = isset( $_POST['product_id'] ) ? $_POST['product_id'] : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$user_id       = isset( $_POST['user_id'] ) ? $_POST['user_id'] : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$html_content  = isset( $_POST['content_html'] ) ? wp_kses_post( wp_unslash( $_POST['content_html'] ) ) : false;
			$text_content  = isset( $_POST['content_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['content_text'] ) ) : false;
			$coupon_code   = isset( $_POST['coupon'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon'] ) ) : false;
			$receivers_ids = array();

			$product_id = is_array( $product_id ) ? $product_id : (array) $product_id;
			$product_id = array_filter( array_map( 'intval', $product_id ) );

			$user_id = is_array( $user_id ) ? $user_id : (array) $user_id;
			$user_id = array_filter( array_map( 'intval', $user_id ) );

			// retrieve data about drafts.
			$target = compact( 'product_id', 'user_id' );
			$hash   = md5( http_build_query( $target ) );
			$drafts = get_option( 'yith_wcwl_promotion_drafts', array() );

			// if we're saving draft, update option and skip.
			if ( isset( $_POST['save_draft'] ) ) {
				$drafts[ $hash ] = array_merge(
					$target,
					array(
						'content_html' => $html_content,
						'content_text' => $text_content,
						'coupon'       => $coupon_code,
					)
				);

				update_option( 'yith_wcwl_promotion_drafts', $drafts );

				wp_safe_redirect(
					esc_url_raw(
						add_query_arg(
							array(
								'page'       => 'yith_wcwl_panel',
								'tab'        => 'dashboard-popular',
								'action'     => $user_id ? 'show_users' : false,
								'product_id' => $user_id ? array_shift( $product_id ) : false,
							),
							admin_url( 'admin.php' )
						)
					)
				);
				exit;
			}

			if ( ! empty( $user_id ) ) {
				$receivers_ids = $user_id;
			} elseif ( ! empty( $product_id ) ) {
				foreach ( $product_id as $id ) {
					$items = YITH_WCWL_Wishlist_Factory::get_wishlist_items(
						array(
							'wishlist_id' => 'all',
							'session_id'  => false,
							'user_id'     => false,
							'product_id'  => $id,
						)
					);

					if ( ! empty( $items ) ) {
						foreach ( $items as $item ) {
							$receivers_ids[] = $item->get_user_id();
						}
					}
				}

				$receivers_ids = array_unique( $receivers_ids );
			}

			if ( ! empty( $receivers_ids ) ) {
				/**
				 * APPLY_FILTERS: yith_wcwl_promotional_email_additional_info
				 *
				 * Filter the additional data required for the Promotional email.
				 *
				 * @param array $data Array of data
				 *
				 * @return array
				 */
				$campaign_info = apply_filters(
					'yith_wcwl_promotional_email_additional_info',
					array(
						'html_content'  => $html_content,
						'text_content'  => $text_content,
						'coupon_code'   => $coupon_code,
						'product_id'    => $product_id,
						'user_id'       => $user_id,
						'receivers'     => $receivers_ids,
						'schedule_date' => time(),
						'counters'      => array(
							'sent'    => 0,
							'to_send' => count( $receivers_ids ),
						),
					)
				);
				// retrieve campaign queue.
				$queue   = get_option( 'yith_wcwl_promotion_campaign_queue', array() );
				$queue[] = $campaign_info;
				$res     = update_option( 'yith_wcwl_promotion_campaign_queue', $queue );
			} else {
				$res = false;
			}

			// finally remove item from drafts.
			if ( isset( $drafts[ $hash ] ) ) {
				unset( $drafts[ $hash ] );

				update_option( 'yith_wcwl_promotion_drafts', $drafts );
			}

			wp_safe_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'page'       => 'yith_wcwl_panel',
							'tab'        => 'dashboard-popular',
							'email_sent' => ! empty( $res ) ? 'true' : 'false',
							'action'     => $user_id ? 'show_users' : false,
							'product_id' => $user_id ? array_shift( $product_id ) : false,
						),
						admin_url( 'admin.php' )
					)
				)
			);
			exit;
		}

		/* === YITH WOOCOMMERCE EMAIL TEMPLATES INTEGRATION === */

		/**
		 * Filters email template available on yith-wcet
		 *
		 * @param mixed $templates Currently available templates.
		 * @return mixed Fitlered templates
		 * @since 2.0.13
		 */
		public function register_emails_for_custom_templates( $templates ) {
			$templates[] = array(
				'id'   => 'yith-wcwl-ask-an-estimate-mail',
				'name' => __( 'Wishlist "Ask an estimate"', 'yith-woocommerce-wishlist' ),
			);
			$templates[] = array(
				'id'   => 'yith-wcwl-promotion-mail',
				'name' => __( 'Wishlist Promotion', 'yith-woocommerce-wishlist' ),
			);

			return $templates;
		}

		/* === ADMIN ONLY AJAX === */

		/**
		 * Returns coupons upon search
		 *
		 * @param string $term String to match; if nothing is passed, it will be retrieved from query string.
		 * @return void
		 * @since 3.0.0
		 */
		public function json_search_coupons( $term = '' ) {
			if ( ! current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
				die;
			}

			check_ajax_referer( 'search-products', 'security' );

			if ( empty( $term ) && isset( $_GET['term'] ) ) {
				$term = (string) sanitize_text_field( wp_unslash( $_GET['term'] ) );
			}

			if ( empty( $term ) ) {
				wp_die();
			}

			if ( ! empty( $_GET['limit'] ) ) {
				$limit = absint( $_GET['limit'] );
			} else {
				$limit = absint( apply_filters( 'woocommerce_json_search_limit', 30 ) );
			}

			$include_ids = ! empty( $_GET['include'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['include'] ) ) : array();
			$exclude_ids = ! empty( $_GET['exclude'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['exclude'] ) ) : array();

			$coupons        = array();
			$coupon_objects = array();
			$ids            = get_posts(
				array(
					's'              => $term,
					'post_type'      => 'shop_coupon',
					'posts_per_page' => $limit,
					'post__in'       => $include_ids,
					'post__not_id'   => $exclude_ids,
					'fields'         => 'ids',
				)
			);

			if ( ! empty( $ids ) ) {
				foreach ( $ids as $coupon_id ) {
					$coupon_objects[] = new WC_Coupon( $coupon_id );
				}
			}

			foreach ( $coupon_objects as $coupon_object ) {
				$formatted_name = $coupon_object->get_code();

				$coupons[ $formatted_name ] = rawurldecode( $formatted_name );
			}

			wp_send_json( apply_filters( 'woocommerce_json_search_found_coupons', $coupons ) );
		}

		/* === WPML INTEGRATION === */

		/**
		 * Make sure that Ask an Estimate fields are registered for translation on WPML String Translation system
		 *
		 * @param mixed $old_value Previous option value.
		 * @param mixed $new_value Current option value.
		 */
		public function register_ask_an_estimate_fields_for_translation( $old_value, $new_value ) {
			$fields     = yith_wcwl_maybe_format_field_array( $new_value );
			$old_fields = yith_wcwl_maybe_format_field_array( $old_value );
			$old_fields = array_diff_key( $old_fields, $fields );

			if ( $old_fields && function_exists( 'icl_unregister_string' ) ) {
				foreach ( $old_fields as $field_slug => $field ) {
					icl_unregister_string( 'ask-an-estimate-form', "field_{$field_slug}_label" );
					icl_unregister_string( 'ask-an-estimate-form', "field_{$field_slug}_placeholder" );
					icl_unregister_string( 'ask-an-estimate-form', "field_{$field_slug}_description" );
				}
			}

			if ( $fields ) {
				foreach ( $fields as $field_slug => $field ) {
					do_action( 'wpml_register_single_string', 'ask-an-estimate-form', "field_{$field_slug}_label", $field['label'] );
					do_action( 'wpml_register_single_string', 'ask-an-estimate-form', "field_{$field_slug}_placeholder", $field['placeholder'] );
					do_action( 'wpml_register_single_string', 'ask-an-estimate-form', "field_{$field_slug}_description", $field['description'] );
				}
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Admin_Premium class
 *
 * @return \YITH_WCWL_Admin_Premium
 * @since 2.0.0
 */
function YITH_WCWL_Admin_Premium() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	return YITH_WCWL_Admin_Premium::get_instance();
}
