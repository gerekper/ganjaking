<?php
/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMC_Admin_Premium' ) ) {
	/**
	 * WooCommerce Mailchimp Admin
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMC_Admin_Premium extends YITH_WCMC_Admin {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCMC_Admin_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * List of available tab for mailchimp panel
		 *
		 * @var array
		 * @access public
		 * @since  1.0.0
		 */
		public $available_tabs = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCMC_Admin_Premium
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @param array $details
		 *
		 * @return \YITH_WCMC_Admin_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			// register premium options
			add_filter( 'yith_wcmc_available_admin_tabs', array( $this, 'add_premium_settings_tabs' ) );
			add_filter( 'yith_wcmc_integration_options', array( $this, 'register_premium_integration_options' ) );
			add_filter( 'yith_wcmc_checkout_options', array( $this, 'register_premium_checkout_options' ) );
			add_action( 'woocommerce_admin_field_yith_wcmc_advanced_integration', array(
				$this,
				'print_custom_yith_wcmc_advanced_integration'
			) );
			add_action( 'woocommerce_admin_field_yith_wcmc_custom_fields', array(
				$this,
				'print_custom_yith_wcmc_custom_fields'
			), 10, 1 );
			add_action( 'woocommerce_admin_field_yith_wcmc_store_integration_status', array(
				$this,
				'print_custom_yith_wcmc_store_integration_status'
			) );
			add_action( 'woocommerce_admin_field_date_range', array( $this, 'print_custom_date_range' ) );

			// adds dashboard widget
			add_action( 'admin_init', array( $this, 'refresh_lists_for_widget' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );

			// AJAX Handler
			add_action( 'wp_ajax_add_advanced_panel_item', array( $this, 'print_advanced_integration_item' ) );
			add_action( 'wp_ajax_add_advanced_panel_field', array( $this, 'print_advanced_integration_field' ) );
			add_action( 'wp_ajax_add_advanced_panel_condition', array(
				$this,
				'print_advanced_integration_condition'
			) );
			add_action( 'wp_ajax_add_custom_field', array( $this, 'print_custom_fields_item' ) );
			add_action( 'wp_ajax_disconnect_store_via_ajax', array( $this, 'disconnect_store_via_ajax' ) );

			// handle exports
			add_action( 'yit_panel_wc_after_update', array( $this, 'manage_users_export' ) );
			add_action( 'yit_panel_wc_after_update', array( $this, 'manage_csv_download' ) );
			add_action( 'admin_notices', array( $this, 'print_notice_after_export' ) );
			add_filter( 'pre_update_option_yith_wcmc_export_list', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_export_email_type', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_export_double_optin', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_export_update_existing', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_export_user_set', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_export_users', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_export_filter_product', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_export_filter_category', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_export_filter_tag', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_csv_user_set', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_csv_users', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_csv_filter_product', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_csv_filter_category', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcmc_csv_filter_tag', '__return_empty_string' );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// register translation managment
			if ( defined( 'ICL_PLUGIN_PATH' ) ) {
				add_filter( 'update_option_yith_wcmc_widget_custom_fields', array(
					$this,
					'register_widget_custom_fields_translation'
				), 10, 2 );
				add_filter( 'update_option_yith_wcmc_shortcode_custom_fields', array(
					$this,
					'register_shortcode_custom_fields_translation'
				), 10, 2 );
			}

			parent::__construct();
		}

		/**
		 * Enqueue scripts and stuffs
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue() {
			global $pagenow;
			$path   = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '/unminified' : '';
			$prefix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';

			parent::enqueue();

			if ( $pagenow == 'index.php' ) {
				wp_enqueue_style( 'yith-wcmc-admin', YITH_WCMC_URL . '/assets/css/admin/yith-wcmc.css', array(), YITH_WCMC_VERSION );
				wp_enqueue_style( 'owl-carousel', YITH_WCMC_URL . '/assets/css/owl.carousel.css', array(), '1.3.3' );
				wp_enqueue_script( 'owl-carousel', YITH_WCMC_URL . '/assets/js/owl.carousel.min.js', array( 'jquery' ), '1.3.3', true );
			}

			if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && 'yith_wcmc_panel' == $_GET['page'] ) {

				if ( ( isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'checkout' ) || ! isset( $_REQUEST['tab'] ) ) {
					$items        = get_option( 'yith_wcmc_advanced_integration', array() );
					$items_number = empty( $items ) ? 0 : count( $items );

					wp_enqueue_script( 'yith-wcmc-advanced-panel', YITH_WCMC_URL . 'assets/js/admin' . $path . '/yith-wcmc-advanced-panel' . $prefix . '.js', array(
						'jquery',
						'jquery-blockui'
					), YITH_WCMC_VERSION, true );
					wp_enqueue_style( 'yith-wcmc-advanced-panel', YITH_WCMC_URL . 'assets/css/admin/yith-wcmc-advanced-panel.css', array(), YITH_WCMC_VERSION );

					wp_localize_script(
						'yith-wcmc-advanced-panel',
						'yith_wcmc_advanced_panel',
						array(
							'actions' => array(
								'add_advanced_panel_item_action'      => 'add_advanced_panel_item',
								'add_advanced_panel_field_action'     => 'add_advanced_panel_field',
								'add_advanced_panel_condition_action' => 'add_advanced_panel_condition'
							),
							'item_id' => ++ $items_number
						)
					);
				}

				if ( isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'shortcode' ) {
					$items        = get_option( 'yith_wcmc_shortcode_custom_fields', array() );
					$items_number = count( $items );

					wp_enqueue_script( 'yith-wcmc-custom-fields', YITH_WCMC_URL . 'assets/js/admin' . $path . '/yith-wcmc-custom-fields' . $prefix . '.js', array(
						'jquery',
						'jquery-blockui',
						'jquery-ui-sortable'
					), YITH_WCMC_VERSION, true );
					wp_enqueue_style( 'yith-wcmc-custom-fields', YITH_WCMC_URL . 'assets/css/admin/yith-wcmc-custom-fields.css', array(), YITH_WCMC_VERSION );

					wp_localize_script(
						'yith-wcmc-custom-fields',
						'yith_wcmc_custom_fields',
						array(
							'actions' => array(
								'add_custom_field_action' => 'add_custom_field'
							),
							'item_id' => ++ $items_number
						)
					);
				}

				if ( isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'widget' ) {
					$items        = get_option( 'yith_wcmc_widget_custom_fields', array() );
					$items_number = count( $items );

					wp_enqueue_script( 'yith-wcmc-custom-fields', YITH_WCMC_URL . 'assets/js/admin' . $path . '/yith-wcmc-custom-fields' . $prefix . '.js', array(
						'jquery',
						'jquery-blockui',
						'jquery-ui-sortable'
					), YITH_WCMC_VERSION, true );
					wp_enqueue_style( 'yith-wcmc-custom-fields', YITH_WCMC_URL . 'assets/css/admin/yith-wcmc-custom-fields.css', array(), YITH_WCMC_VERSION );

					wp_localize_script(
						'yith-wcmc-custom-fields',
						'yith_wcmc_custom_fields',
						array(
							'actions' => array(
								'add_custom_field_action' => 'add_custom_field'
							),
							'item_id' => ++ $items_number
						)
					);
				}

				if ( isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'export' ) {
					wp_enqueue_script( 'yith-wcmc-export-panel', YITH_WCMC_URL . 'assets/js/admin' . $path . '/yith-wcmc-export-panel' . $prefix . '.js', array( 'jquery' ), YITH_WCMC_VERSION, true );

					wp_localize_script( 'yith-wcmc-export-panel', 'yith_wcmc_export_panel', array(
						'labels' => array(
							'export_users' => __( 'Export Users', 'yith-woocommerce-mailchimp' ),
							'download_csv' => __( 'Download CSV', 'yith-woocommerce-mailchimp' )
						)
					) );
				}
			}
		}

		/**
		 * Adds premium option tabs
		 *
		 * @param $tabs array Original array of option tabs
		 *
		 * @return array filtered array of option tabs
		 * @since 1.0.0
		 */
		public function add_premium_settings_tabs( $tabs ) {
			$tabs['store']     = __( 'Store', 'yith-woocommerce-mailchimp' );
			$tabs['register']  = __( 'Register', 'yith-woocommerce-mailchimp' );
			$tabs['shortcode'] = __( 'Shortcode', 'yith-woocommerce-mailchimp' );
			$tabs['widget']    = __( 'Widget', 'yith-woocommerce-mailchimp' );
			$tabs['export']    = __( 'Export', 'yith-woocommerce-mailchimp' );

			unset( $tabs['premium'] );

			return $tabs;
		}

		/**
		 * Add integration premium settings
		 *
		 * @param $options array Array of original options, to filter
		 *
		 * @return array Filtered array of options
		 */
		public function register_premium_integration_options( $options ) {
			$list_options = YITH_WCMC()->retrieve_lists();

			$ecommerce_360 = array(
				'ecommerce-360-options' => array(
					'title' => __( 'eCommerce 360', 'yith-woocommerce-mailchimp' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'yith_wcmc_ecommerce360_options'
				),

				'ecommerce-360-enable' => array(
					'title'   => __( 'Enable eCommerce 360 integration', 'yith-woocommerce-mailchimp' ),
					'type'    => 'checkbox',
					'id'      => 'yith_wcmc_ecommerce360_enable',
					'desc'    => __( 'When you check this option, data of the orders made by users coming from a campaign will be collected and sent to MailChimp servers, for segmentation purpose', 'yith-woocommerce-mailchimp' ),
					'default' => ''
				),

				'ecommerce-360-cookie-lifetime' => array(
					'title'   => __( 'eCommerce 360 cookie lifetime', 'yith-woocommerce-mailchimp' ),
					'type'    => 'number',
					'id'      => 'yith_wcmc_ecommerce360_cookie_lifetime',
					'desc'    => __( 'Seconds that have to pass before eCommerce 360 cookies expire', 'yith-woocommerce-mailchimp' ),
					'default' => WEEK_IN_SECONDS
				),

				'ecommerce-360-list' => array(
					'title'             => __( 'eCommerce 360 list', 'yith-woocommerce-mailchimp' ),
					'type'              => 'select',
					'id'                => 'yith_wcmc_ecommerce360_list',
					'desc'              => __( 'Select a list for store handling', 'yith-woocommerce-mailchimp' ),
					'options'           => $list_options,
					'custom_attributes' => empty( $list_options ) ? array(
						'disabled' => 'disabled'
					) : array(),
					'css'               => 'min-width:300px;',
					'class'             => 'list-select'
				),

				'ecommerce-360-options-end' => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcmc_ecommerce_360_options'
				),
			);

			$options['integration'] = array_merge( $options['integration'], $ecommerce_360 );

			// remove videobox
			unset( $options['integration']['mailchimp-video-box'] );

			return $options;
		}

		/**
		 * Add checkout premium settings
		 *
		 * @param $options array Array of original options, to filter
		 *
		 * @return array Filtered array of options
		 */
		public function register_premium_checkout_options( $options ) {
			$selected_list  = get_option( 'yith_wcmc_mailchimp_list' );
			$groups_options = ( ! empty( $selected_list ) ) ? YITH_WCMC()->retrieve_groups( $selected_list ) : array();

			$mode_option = array(
				'title'   => __( 'Integration mode', 'yith-woocommerce-mailchimp' ),
				'type'    => 'select',
				'desc'    => __( 'Select whether to use a basic set of options or add integration settings', 'yith-woocommerce-mailchimp' ),
				'id'      => 'yith_wcmc_mailchimp_integration_mode',
				'options' => array(
					'simple'   => __( 'Simple', 'yith-woocommerce-mailchimp' ),
					'advanced' => __( 'Advanced', 'yith-woocommerce-mailchimp' )
				)
			);

			$options['checkout'] = array_merge(
				array_slice( $options['checkout'], 0, 11 ),
				array(
					'checkout-mode' => $mode_option
				),
				array_slice( $options['checkout'], 11, count( $options['checkout'] ) )
			);

			$group_option = array(
				'title'             => __( 'Interest groups', 'yith-woocommerce-mailchimp' ),
				'type'              => 'multiselect',
				'desc'              => __( 'Select an interest group for the new user', 'yith-woocommerce-mailchimp' ),
				'id'                => 'yith_wcmc_mailchimp_groups',
				'options'           => $groups_options,
				'custom_attributes' => empty( $groups_options ) ? array(
					'disabled' => 'disabled'
				) : array(),
				'class'             => 'chosen_select',
				'css'               => 'width:300px;'
			);

			$advanced_panel_option = array(
				'title' => __( 'Advanced options', 'yith-woocommerce-mailchimp' ),
				'type'  => 'yith_wcmc_advanced_integration',
				'id'    => 'yith_wcmc_advanced_integration',
				'value' => ''
			);

			$advanced_options = array(
				'checkout-groups'   => $group_option,
				'checkout-advanced' => $advanced_panel_option

			);

			$options['checkout'] = array_merge(
				array_slice( $options['checkout'], 0, 13 ),
				$advanced_options,
				array_slice( $options['checkout'], 13, count( $options['checkout'] ) )
			);

			return $options;
		}

		/**
		 * Delete options specific to an API Key
		 *
		 * @param $old_value string Old key value
		 * @param $value     string New key value
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function delete_old_key_options( $old_value, $value ) {
			parent::delete_old_key_options( $old_value, $value );

			delete_option( 'yith_wcmc_mailchimp_groups' );
			delete_option( 'yith_wcmc_advanced_integration' );
			delete_option( 'yith_wcmc_shortcode_mailchimp_list' );
			delete_option( 'yith_wcmc_shortcode_mailchimp_groups' );
			delete_option( 'yith_wcmc_widget_mailchimp_list' );
			delete_option( 'yith_wcmc_widget_mailchimp_groups' );
		}

		/* === PRINT CUSTOM TYPE FIELDS FOR PANEL === */

		/**
		 * Prints the template for advanced checkout options
		 *
		 * @param $value array Array of field settings
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_custom_yith_wcmc_advanced_integration( $value ) {
			// init customer object, to handle checkout fields

			// Session class, handles session data for users - can be overwritten if custom handler is needed
			$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

			include_once( WC()->plugin_path() . '/includes/abstracts/abstract-wc-session.php' );
			include_once( WC()->plugin_path() . '/includes/class-wc-session-handler.php' );
			include_once( WC()->plugin_path() . '/includes/wc-cart-functions.php' );

			// Class instances
			// WC()->frontend_includes();
			WC()->session  = new $session_class();
			WC()->cart     = new WC_Cart();
			WC()->customer = new WC_Customer();

			$advanced_options = get_option( 'yith_wcmc_advanced_integration', array() );


			include( YITH_WCMC_DIR . 'templates/admin/types/advanced-integration.php' );
		}

		/**
		 * Print advanced integration item
		 *
		 * @param $args array An array with options of the item to output (optional)
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_advanced_integration_item( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'item_id'         => isset( $_POST['item_id'] ) ? $_POST['item_id'] : 0,
				'selected_list'   => 0,
				'selected_groups' => array(),
				'fields'          => array(),
				'conditions'      => array()
			) );

			extract( $args );

			$lists  = YITH_WCMC()->retrieve_lists();
			$groups = ( ! empty( $selected_list ) ) ? YITH_WCMC()->retrieve_groups( $selected_list ) : array();

			include( YITH_WCMC_DIR . 'templates/admin/types/advanced-integration-item.php' );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				die();
			}
		}

		/**
		 * Print advanced integration item
		 *
		 * @param $args array An array with options of the item to output (optional)
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_advanced_integration_field( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'item_id'            => isset( $_POST['item_id'] ) ? $_POST['item_id'] : 0,
				'field_id'           => isset( $_POST['field_id'] ) ? $_POST['field_id'] : 0,
				'selected_list'      => isset( $_POST['list_id'] ) ? $_POST['list_id'] : 0,
				'selected_checkout'  => '',
				'selected_merge_var' => ''
			) );
			extract( $args );

			$fields          = ( ! empty( $selected_list ) ) ? YITH_WCMC()->retrieve_fields( $selected_list ) : array();
			$checkout_fields = array();

			// retrieve dynamic checkout fields
			$checkout_fields_raw = apply_filters( 'yith_wcmc_get_checkout_fields', WC()->checkout()->checkout_fields, $args );

			if ( ! empty( $checkout_fields_raw ) ) {
				foreach ( $checkout_fields_raw as $category_id => $category ) {
					if ( ! empty( $category ) ) {
						$checkout_fields[ $category_id ]['name']   = ucwords( str_replace( '_', ' ', $category_id ) );
						$checkout_fields[ $category_id ]['fields'] = array(
							"{$category_id}_address" => __( 'Complete address (only to use with Address type field)', 'yith-woocommerce-mailchimp' )
						);

						foreach ( $category as $id => $field ) {
							$checkout_fields[ $category_id ]['fields'][ $id ] = isset( $field['label'] ) ? $field['label'] : ucwords( str_replace( '_', ' ', $id ) );
						}
					}
				}
			}

			//unset account fields, if needed
			$unset_account_fields = apply_filters( 'yith_wcmc_unset_account_fields', true );

			if ( $unset_account_fields ) {
				unset( $checkout_fields['account'] );
			}

			// adds custom checkout fields
			$custom_fields = array(
				'shipping_method_title' => __( 'Shipping method name', 'yith-woocommerce-mailchimp' ),
				'payment_method_title'  => __( 'Payment method name', 'yith-woocommerce-mailchimp' ),
				'customer_user'         => __( 'User ID', 'yith-woocommerce-mailchimp' )
			);

			if ( isset( $checkout_fields['custom'] ) ) {
				$checkout_fields['custom']['fields'] = array_merge(
					$checkout_fields['custom']['fields'],
					$custom_fields
				);
			} else {
				$checkout_fields['custom']['name']   = __( 'Custom', 'yith-woocommerce-mailchimp' );
				$checkout_fields['custom']['fields'] = $custom_fields;
			}

			include( YITH_WCMC_DIR . 'templates/admin/types/advanced-integration-field.php' );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				die();
			}
		}

		/**
		 * Print advanced integration item
		 *
		 * @param $args array An array with options of the item to output (optional)
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_advanced_integration_condition( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'item_id'      => isset( $_POST['item_id'] ) ? $_POST['item_id'] : 0,
				'condition_id' => isset( $_POST['condition_id'] ) ? $_POST['condition_id'] : 0,
				'condition'    => 'product_in_cart',
				'op_set'       => 'contain',
				'op_number'    => 'less_than',
				'products'     => array(),
				'prod_cats'    => array(),
				'order_total'  => 0,
				'custom_key'   => '',
				'op_mixed'     => 'is',
				'custom_value' => ''
			) );

			extract( $args );

			$json_ids = array();
			if ( ! empty( $products ) ) {
				$product_ids = ! is_array( $products ) ? array_filter( array_map( 'absint', explode( ',', $products ) ) ) : $products;

				foreach ( $product_ids as $product_id ) {
					//$product = wc_get_product( $product_id );
					$json_ids[ $product_id ] = wp_kses_post( get_the_title( $product_id ) );
				}
			}

			include( YITH_WCMC_DIR . 'templates/admin/types/advanced-integration-condition.php' );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				die();
			}
		}

		/**
		 * Output custom fields type
		 *
		 * @param $value array Array representing the field to print
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_custom_yith_wcmc_custom_fields( $value ) {
			$id             = isset( $value['id'] ) ? $value['id'] : false;
			$fields_options = get_option( $id, array() );
			$list_id        = get_option( str_replace( 'custom_fields', 'mailchimp_list', $id ) );

			include( YITH_WCMC_DIR . 'templates/admin/types/custom-fields.php' );
		}

		/**
		 * Prints an item for custom firelds type
		 *
		 * @param $args array Array with the argument required for the template
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_custom_fields_item( $args ) {
			$args = wp_parse_args( $args, array(
				'id'                 => isset( $_POST['id'] ) ? $_POST['id'] : '',
				'item_id'            => isset( $_POST['item_id'] ) ? $_POST['item_id'] : '',
				'selected_list'      => isset( $_POST['list_id'] ) ? $_POST['list_id'] : 0,
				'selected_name'      => '',
				'selected_merge_var' => '',
				'removable'          => true
			) );

			extract( $args );

			$fields = ( ! empty( $selected_list ) ) ? YITH_WCMC()->retrieve_fields( $selected_list ) : array();

			include( YITH_WCMC_DIR . 'templates/admin/types/custom-fields-item.php' );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				die();
			}
		}

		/**
		 * Print "yith_wcmc_store_integration_status" custom field
		 *
		 * @param $value mixed Args
		 *
		 * @return void
		 */
		public function print_custom_yith_wcmc_store_integration_status( $value ) {
			$store_info = YITH_WCMC_Store()->get_store_info();
			$lists      = YITH_WCMC_Premium()->retrieve_lists();

			include( YITH_WCMC_DIR . 'templates/admin/types/store-integration-status.php' );
		}

		/**
		 * Print date range type
		 *
		 * @param $value array Array of options used to print field
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_custom_date_range( $value ) {
			include( YITH_WCMC_DIR . 'templates/admin/types/date-range.php' );
		}

		/* === LICENCE HANDLING === */

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCMC_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WCMC_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WCMC_INIT, YITH_WCMC_SECRET_KEY, YITH_WCMC_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WCMC_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}

			YIT_Upgrade()->register( YITH_WCMC_SLUG, YITH_WCMC_INIT );
		}

		/* === HANDLE EXPORT === */

		/**
		 * Manage user export, requesting a batch subscribe
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function manage_users_export() {
			if ( isset( $_POST['export_users'] ) && isset( $_POST['yith_wcmc_export_list'] ) ) {
				$list_id                = $_POST['yith_wcmc_export_list'];
				$email_type             = isset( $_POST['yith_wcmc_export_email_type'] ) ? $_POST['yith_wcmc_export_email_type'] : 'html';
				$double_optin           = isset( $_POST['yith_wcmc_export_double_optin'] );
				$update_existing        = isset( $_POST['yith_wcmc_export_update_existing'] );
				$users                  = $this->_retrieve_users_to_export( array(
					'user_set'         => isset( $_POST['yith_wcmc_export_user_set'] ) ? $_POST['yith_wcmc_export_user_set'] : 'all',
					'users_selected'   => isset( $_POST['yith_wcmc_export_users'] ) ? $_POST['yith_wcmc_export_users'] : '',
					'product_filter'   => ! empty( $_POST['yith_wcmc_export_filter_product'] ) ? explode( ',', $_POST['yith_wcmc_export_filter_product'] ) : array(),
					'category_filter'  => ! empty( $_POST['yith_wcmc_export_filter_category'] ) ? explode( ',', $_POST['yith_wcmc_export_filter_category'] ) : array(),
					'tag_filter'       => ! empty( $_POST['yith_wcmc_export_filter_tag'] ) ? explode( ',', $_POST['yith_wcmc_export_filter_tag'] ) : array(),
					'from_date_filter' => isset( $_POST['yith_wcmc_export_filter_date']['from'] ) ? $_POST['yith_wcmc_export_filter_date']['from'] : '',
					'to_date_filter'   => isset( $_POST['yith_wcmc_export_filter_date']['to'] ) ? $_POST['yith_wcmc_export_filter_date']['to'] : ''
				) );
				$waiting_products_field = ! empty( $_POST['yith_wcmc_export_field_waiting_products'] ) ? $_POST['yith_wcmc_export_field_waiting_products'] : '';
				$batch                  = array();

				if ( ! empty( $users ) ) {
					foreach ( $users as $user ) {
						$member_hash = md5( strtolower( $user['user_email'] ) );

						$batch[] = array(
							'method' => $update_existing ? 'PUT' : 'POST',
							'path'   => $update_existing ? "lists/{$list_id}/members/{$member_hash}" : "lists/{$list_id}/members",
							'body'   => json_encode( array(
								'email_address' => $user['user_email'],
								'email_type'    => $email_type,
								'status'        => $double_optin ? 'pending' : 'subscribed',
								'merge_fields'  => array_merge(
									array(
										'FNAME' => $user['user_first_name'],
										'LNAME' => $user['user_last_name']
									),
									( $waiting_products_field && ! empty( $user['waiting_products'] ) ) ? array(
										$waiting_products_field => $user['waiting_products']
									) : array()
								)
							) )
						);
					}
				}

				$res = YITH_WCMC()->do_request( 'post', 'batches', array(
					'operations' => $batch
				) );

				wp_redirect( esc_url_raw( add_query_arg( array(
					'batch_submitted' => isset( $res['status'] ) && in_array( $res['status'], array(
							'pending',
							'started',
							'finished'
						) )
				) ) ) );
				die();
			}
		}

		/**
		 * Manage CSV download
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function manage_csv_download() {
			if ( isset( $_POST['export_csv'] ) ) {
				$users = $this->_retrieve_users_to_export( array(
					'user_set'         => isset( $_POST['yith_wcmc_csv_user_set'] ) ? $_POST['yith_wcmc_csv_user_set'] : 'all',
					'users_selected'   => isset( $_POST['yith_wcmc_csv_users'] ) ? $_POST['yith_wcmc_csv_users'] : '',
					'product_filter'   => ! empty( $_POST['yith_wcmc_csv_filter_product'] ) ? explode( ',', $_POST['yith_wcmc_csv_filter_product'] ) : array(),
					'category_filter'  => ! empty( $_POST['yith_wcmc_csv_filter_category'] ) ? explode( ',', $_POST['yith_wcmc_csv_filter_category'] ) : array(),
					'tag_filter'       => ! empty( $_POST['yith_wcmc_csv_filter_tag'] ) ? explode( ',', $_POST['yith_wcmc_csv_filter_tag'] ) : array(),
					'from_date_filter' => isset( $_POST['yith_wcmc_csv_filter_date']['from'] ) ? $_POST['yith_wcmc_csv_filter_date']['from'] : '',
					'to_date_filter'   => isset( $_POST['yith_wcmc_csv_filter_date']['to'] ) ? $_POST['yith_wcmc_csv_filter_date']['to'] : ''
				) );

				if ( ! empty( $users ) ) {
					$csv = '';

					// add csv heading
					$csv .= '"Email Address",FNAME,LNAME';

					if ( isset( $_POST['yith_wcmc_csv_user_set'] ) && $_POST['yith_wcmc_csv_user_set'] == 'waiting_lists' ) {
						$csv .= ',"Waiting Products"';
					}

					$csv .= "\n";

					foreach ( $users as $user ) {
						$csv .= '"' . $user['user_email'] . '",';
						$csv .= '"' . $user['user_first_name'] . '",';
						$csv .= '"' . $user['user_last_name'] . '"';

						if ( isset( $_POST['yith_wcmc_csv_user_set'] ) && $_POST['yith_wcmc_csv_user_set'] == 'waiting_lists' ) {
							$csv .= ',"' . $user['waiting_products'] . '"';
						}

						$csv .= "\n";
					}

					header( 'Content-Type: text/csv; charset=utf-8' );
					header( 'Content-Disposition: attachment; filename=data.csv' );

					echo $csv;
					die();
				}
			}
		}

		/**
		 * Prints notice after user export
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_notice_after_export() {
			if ( isset( $_GET['batch_submitted'] ) ) {
				$class = ( $_GET['batch_submitted'] ) ? 'updated' : 'error';
				?>
				<div class="<?php echo $class ?>">
					<p>
						<?php
						if ( $_GET['batch_submitted'] ) {
							_e( 'Batch request was correctly sent to MailChimp Servers; it will be processed as soon as possible', 'yith-woocommerce-mailchimp' );
						} else {
							_e( 'There was an error with your batch request; please, try later', 'yith-woocommerce-mailchimp' );
						}
						?>
					</p>
				</div>
				<?php
			}
		}

		/**
		 * Retrieve users to export
		 *
		 * @param $args array Array of parameters to use to filter users
		 *
		 * @return array Array of users to export
		 * @since 1.0.0
		 */
		protected function _retrieve_users_to_export( $args = array() ) {
			$args = wp_parse_args(
				$args,
				array(
					'user_set'         => 'all',
					'users_selected'   => '',
					'product_filter'   => '',
					'category_filter'  => '',
					'tag_filter'       => '',
					'from_date_filter' => '',
					'to_date_filter'   => ''
				)
			);

			/**
			 * @var $user_set
			 * @var $users_selected
			 * @var $product_filter
			 * @var $category_filter
			 * @var $tag_filter
			 * @var $from_date_filter
			 * @var $to_date_filter
			 */
			extract( $args );

			$users_id = array();
			$users    = array();

			switch ( $user_set ) {
				case 'set':
					$users_id = explode( ',', $users_selected );
					break;
				case 'filter':
					$get_posts_args = array(
						'posts_per_page' => - 1,
						'post_type'      => wc_get_order_types( 'view-orders' ),
						'post_status'    => array( 'wc-processing', 'wc-completed' )
					);

					if ( ! empty( $from_date_filter ) ) {
						$get_posts_args['meta_query'][] = array(
							'key'     => '_completed_date',
							'value'   => $from_date_filter . ' 00:00:00',
							'compare' => '>='
						);
					}

					if ( ! empty( $to_date_filter ) ) {
						$get_posts_args['meta_query'][] = array(
							'key'     => '_completed_date',
							'value'   => $to_date_filter . ' 23:59:59',
							'compare' => '<='
						);
					}

					$query  = new WP_Query( $get_posts_args );
					$orders = $query->get_posts();

					if ( ! empty( $orders ) ) {
						foreach ( $orders as $order ) {
							$order_obj   = wc_get_order( $order->ID );
							$customer    = $order_obj->customer_user;
							$order_items = $order_obj->get_items( 'line_item' );

							if ( ( ! empty( $product_filter ) || ! empty( $category_filter ) || ! empty( $tag_filter ) ) && ! empty( $order_items ) ) {
								foreach ( $order_items as $item_id => $item ) {
									$valid        = true;
									$product_id   = $item['product_id'];
									$variation_id = $item['variation_id'];

									// filter for products, if any selected
									if ( ! empty( $product_filter ) && ! ( in_array( $product_id, $product_filter ) || in_array( $variation_id, $product_filter ) ) ) {
										$valid = false;
										continue;
									}

									// filter for category, if any selected
									if ( ! empty( $category_filter ) ) {
										$product_cats    = get_the_terms( $product_id, 'product_cat' );
										$product_cat_ids = array();

										if ( ! empty( $product_cats ) ) {
											foreach ( $product_cats as $term ) {
												$product_cat_ids[] = $term->term_id;
											}
										}

										$cat_intersect = array_intersect( $product_cat_ids, $category_filter );

										if ( empty( $cat_intersect ) ) {
											$valid = false;
											continue;
										}
									}

									// filter for tag, if any selected
									if ( ! empty( $tag_filter ) ) {
										$product_tags    = get_the_terms( $product_id, 'product_tag' );
										$product_tag_ids = array();

										if ( ! empty( $product_tags ) ) {
											foreach ( $product_tags as $tag ) {
												$product_tag_ids[] = $tag->term_id;
											}
										}

										$tag_intersect = array_intersect( $product_tag_ids, $tag_filter );

										if ( empty( $tag_intersect ) ) {
											$valid = false;
											continue;
										}
									}

									if ( $valid && ! in_array( $customer, $users_id ) && ! empty( $customer ) ) {
										$users_id[] = $customer;
									}
								}
							} else {
								if ( ! in_array( $customer, $users_id ) ) {
									$users_id[] = $customer;
								}
							}
						}
					}

					break;
				case 'customers':
					$users_selected = get_users( array(
						'meta_key'   => 'paying_customer',
						'meta_value' => 1,
						'fields'     => array( 'ID' )
					) );

					if ( ! empty( $users_selected ) ) {
						foreach ( $users_selected as $user_obj ) {
							$users_id[] = $user_obj->ID;
						}
					}
					break;
				case 'waiting_lists':
					global $wpdb;

					$formatted_set = array();
					$waiting_lists = $wpdb->get_results( $wpdb->prepare( "SELECT pm.post_id AS product_id, p.post_name AS product_slug, pm.meta_value AS list FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID WHERE pm.meta_key = %s", '_yith_wcwtl_users_list' ), ARRAY_A );

					if ( ! empty( $waiting_lists ) ) {
						foreach ( $waiting_lists as $record ) {
							$waiting_list = maybe_unserialize( $record['list'] );
							$product_slug = $record['product_slug'];

							if ( ! empty( $waiting_list ) ) {
								foreach ( $waiting_list as $registered_email ) {
									if ( ! in_array( $registered_email, array_keys( $formatted_set ) ) ) {
										$formatted_set[ $registered_email ] = array();
									}

									$formatted_set[ $registered_email ][] = $product_slug;
								}
							}
						}
					}

					if ( ! empty( $formatted_set ) ) {
						foreach ( $formatted_set as $email => $products ) {
							$users[] = array(
								'user_email'       => $email,
								'waiting_products' => implode( ', ', $products ),
								'user_first_name'  => false,
								'user_last_name'   => false,
							);
						}
					}

					break;
				case 'all':
				default:
					$users_selected = get_users( array(
						'fields' => array( 'ID' )
					) );

					if ( ! empty( $users_selected ) ) {
						foreach ( $users_selected as $user_obj ) {
							$users_id[] = $user_obj->ID;
						}
					}
					break;
			}

			if ( ! empty( $users_id ) ) {
				foreach ( $users_id as $id ) {
					$user = get_user_by( 'id', $id );

					if ( ! $user ) {
						continue;
					}

					$users[] = array(
						'id'              => $id,
						'user_email'      => $user->user_email,
						'user_first_name' => ! empty( $user->billing_first_name ) ? $user->billing_first_name : $user->first_name,
						'user_last_name'  => ! empty( $user->billing_last_name ) ? $user->billing_last_name : $user->last_name,
					);
				}
			}

			return $users;
		}

		/* === HANDLE DASHBOARD WIDGET === */

		/**
		 * Performs a reuqest to mailchimp to update lists stats
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function refresh_lists_for_widget() {
			if ( isset( $_GET['refresh_lists_nonce'] ) && wp_verify_nonce( $_GET['refresh_lists_nonce'], 'refresh_lists_action' ) ) {
				YITH_WCMC()->do_request( 'get', 'lists', array(), true );

				wp_redirect( esc_url_raw( remove_query_arg( 'refresh_lists_nonce' ) ) );
			}
		}

		/**
		 * Register dashboard widget
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_dashboard_widget() {
			if ( current_user_can( 'manage_options' ) ) {
				wp_add_dashboard_widget( 'yith_wcmc_dashboard_widget', 'MailChimp Lists Stats', array(
					$this,
					'print_dashboard_widget'
				) );
			}
		}

		/**
		 * Print dashboard widget
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_dashboard_widget() {
			$profile = YITH_WCMC()->do_request( 'get' );

			$user_id  = isset( $profile['account_id'] ) ? $profile['account_id'] : false;
			$username = isset( $profile['username'] ) ? $profile['username'] : false;
			$name     = isset( $profile['account_name'] ) ? $profile['account_name'] : false;
			$email    = isset( $profile['email'] ) ? $profile['email'] : false;

			$lists = YITH_WCMC()->do_request( 'get', 'lists' );

			include( YITH_WCMC_DIR . 'templates/admin/mailchimp-dashboard-widget.php' );
		}

		/* === HANDLE STRING TRANSLATION === */

		/**
		 * Register string for translation in WPML String Translation
		 *
		 * @param $old_value mixed Old option value
		 * @param $new_value mixed New option value
		 *
		 * @return void
		 * @since 1.0.3
		 */
		public function register_widget_custom_fields_translation( $old_value, $new_value ) {
			if ( ! function_exists( 'icl_unregister_string' ) || ! function_exists( 'icl_register_string' ) ) {
				return;
			}

			if ( ! empty( $old_value ) ) {
				foreach ( $old_value as $id => $field ) {
					if ( ! isset( $new_value[ $id ] ) ) {
						icl_unregister_string( 'admin_texts_plugin_yith-woocommerce-mailchimp-premium', 'yith_wcmc_widget_custom_fields[' . $field['merge_var'] . ']' );
					}
				}
			}

			if ( ! empty( $new_value ) ) {
				foreach ( $new_value as $id => $field ) {
					icl_register_string( 'admin_texts_plugin_yith-woocommerce-mailchimp-premium', 'yith_wcmc_widget_custom_fields[' . $field['merge_var'] . ']', $field['name'] );
				}
			}
		}

		/**
		 * Register string for translation in WPML String Translation
		 *
		 * @param $old_value mixed Old option value
		 * @param $new_value mixed New option value
		 *
		 * @return void
		 * @since 1.0.3
		 */
		public function register_shortcode_custom_fields_translation( $old_value, $new_value ) {
			if ( ! function_exists( 'icl_unregister_string' ) || ! function_exists( 'icl_register_string' ) ) {
				return;
			}

			if ( ! empty( $old_value ) ) {
				foreach ( $old_value as $id => $field ) {
					if ( ! isset( $new_value[ $id ] ) ) {
						icl_unregister_string( 'admin_texts_plugin_yith-woocommerce-mailchimp-premium', 'yith_wcmc_shortcode_custom_fields[' . $field['merge_var'] . ']' );
					}
				}
			}

			if ( ! empty( $new_value ) ) {
				foreach ( $new_value as $id => $field ) {
					icl_register_string( 'admin_texts_plugin_yith-woocommerce-mailchimp-premium', 'yith_wcmc_shortcode_custom_fields[' . $field['merge_var'] . ']', $field['name'] );
				}
			}
		}

		/* === AJAX CALLS === */

		/**
		 * Disconnect store via ajax call
		 *
		 * @return void
		 * @since 1.1.0
		 */
		public function disconnect_store_via_ajax() {
			if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				wp_send_json( false );
			}

			// return if required params are missing
			if ( empty( $_REQUEST['yith_wcmc_ajax_request_nonce'] ) ) {
				wp_send_json( false );
			}

			// return if non check fails
			if ( ! wp_verify_nonce( $_REQUEST['yith_wcmc_ajax_request_nonce'], 'yith_wcmc_ajax_request' ) ) {
				wp_send_json( false );
			}

			// do request
			$result = YITH_WCMC_Store()->maybe_disconnect_store();

			// return json encoded result
			wp_send_json( $result );
		}
	}
}

/**
 * Unique access to instance of YITH_WCMC_Admin_Premium class
 *
 * @return \YITH_WCMC_Admin_Premium
 * @since 1.0.0
 */
function YITH_WCMC_Admin_Premium() {
	return YITH_WCMC_Admin_Premium::get_instance();
}