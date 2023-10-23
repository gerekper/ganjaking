<?php
/**
 * WAPO Admin Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_Admin' ) ) {

	/**
	 *  Admin class.
	 *  The class manage all the admin behaviors.
	 */
	class YITH_WAPO_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WAPO_Admin
		 */
		protected static $instance;

		/**
		 * Plugin options
		 *
		 * @var array
		 */
		public $options = array();

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		public $version = YITH_WAPO_VERSION;

		/**
		 * The plugin panel
		 *
		 * @var YIT_Plugin_Panel_WooCommerce
		 */
		protected $panel;

		/**
		 * Premium version landing link
		 *
		 * @var string
		 */
		protected $premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-product-add-ons/';

		/**
		 * Panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_wapo_panel';

		/**
		 * Documentation URL
		 *
		 * @var string
		 */
		public $doc_url = 'https://docs.yithemes.com/yith-woocommerce-product-add-ons/';

		/**
		 * Landing URL
		 *
		 * @var string
		 */
		public $plugin_url = 'https://yithemes.com/themes/plugins/yith-woocommerce-product-add-ons/';

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WAPO_Admin | YITH_WAPO_Admin_Premium
		 */
		public static function get_instance() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );
			return ! is_null( $self::$instance ) ? $self::$instance : $self::$instance = new $self();
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			// Admin menu.
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'admin_menu', array( $this, 'old_admin_menu' ), 10 );

			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WAPO_DIR . '/' . basename( YITH_WAPO_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// Enqueue scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// Blocks Settings.
			add_action( 'yith_wapo_show_blocks_tab', array( $this, 'show_blocks_tab' ) );

			// Add admin tabs.
			add_filter( 'yith_wapo_admin_panel_tabs', array( $this, 'yith_wapo_admin_panel_tabs' ), 10, 2 );

			// Update visibility (enable/disable).
			add_action( 'wp_ajax_enable_disable_block', array( $this, 'enable_disable_block' ) );
			add_action( 'wp_ajax_nopriv_enable_disable_block', array( $this, 'enable_disable_block' ) );
			add_action( 'wp_ajax_enable_disable_addon', array( $this, 'enable_disable_addon' ) );
			add_action( 'wp_ajax_nopriv_enable_disable_addon', array( $this, 'enable_disable_addon' ) );

			// Save sortable items.
			add_action( 'wp_ajax_sortable_blocks', array( $this, 'sortable_blocks' ) );
			add_action( 'wp_ajax_nopriv_sortable_blocks', array( $this, 'sortable_blocks' ) );
			add_action( 'wp_ajax_sortable_addons', array( $this, 'sortable_addons' ) );
			add_action( 'wp_ajax_nopriv_sortable_addons', array( $this, 'sortable_addons' ) );

			if ( 'yes' === get_option( 'yith_wapo_show_image_in_cart', 'no' ) ) {
				add_filter( 'woocommerce_order_item_thumbnail', array( $this, 'order_item_thumbnail' ), 10, 2 );
				add_filter( 'woocommerce_admin_order_item_thumbnail', array( $this, 'admin_order_item_thumbnail' ), 10, 3 );
			}

			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_order_item_meta' ) );
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @param array $links Action links.
		 *
		 * @use     plugin_action_links_{$plugin_file_name}
		 * @Return  array
		 */
		public function action_links( $links ) {
			return yith_add_action_links( $links, $this->panel_page, defined( 'YITH_WAPO_PREMIUM' ), YITH_WAPO_SLUG );
		}

		/**
		 * Adds action links to plugin admin page
		 *
		 * @param array    $row_meta_args Row meta arguments.
		 * @param string[] $plugin_meta   An array of the plugin's metadata,
		 *                                including the version, author,
		 *                                author URI, and plugin URI.
		 * @param string   $plugin_file   Path to the plugin file relative to the plugins directory.
		 * @param array    $plugin_data   An array of plugin data.
		 * @param string   $status        Status of the plugin. Defaults are 'All', 'Active',
		 *                                'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                                'Drop-ins', 'Search', 'Paused'.
		 *
		 * @return array
		 */
		public function plugin_row_meta( $row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( YITH_WAPO_INIT === $plugin_file ) {
				$row_meta_args['slug']       = YITH_WAPO_LOCALIZE_SLUG;
				$row_meta_args['is_premium'] = true;
			}
			return $row_meta_args;
		}

		/**
		 * Retrieve the documentation URL.
		 *
		 * @return string
		 */
		protected function get_doc_url() {
			return 'https://docs.yithemes.com/yith-woocommerce-product-add-ons/';
		}

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @use      YIT_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( ! empty( $this->panel ) ) {
                return;
            }

            $capability  = apply_filters( 'yith_wapo_register_panel_capabilities', 'manage_woocommerce' );
            $parent_page = 'yit_plugin_panel';

            $args = array(
                'ui_version'       => 2,
                'create_menu_page' => true,
                'class'            => yith_set_wrapper_class(),
                'parent_slug'      => '',
                'plugin_slug'      => YITH_WAPO_SLUG,
                'page_title'       => 'YITH WooCommerce Product Add-ons & Extra Options',
                'menu_title'       => 'Product Add-ons & Extra Options',
                'capability'       => $capability,
                'parent'           => YITH_WAPO_SLUG,
                'parent_page'      => $parent_page,
                'page'             => $this->panel_page,
                'admin-tabs'       => apply_filters( 'yith_wapo_admin_panel_tabs', array(), $capability ),
                'plugin-url'       => YITH_WAPO_DIR,
                'options-path'     => YITH_WAPO_DIR . 'plugin-options',
                'is_free'          => true,
                'is_extended'      => false,
                'is_premium'       => false,
                'plugin_version'   => YITH_WAPO_VERSION,
                'plugin_icon'      => YITH_WAPO_ASSETS_URL . '/img/plugins/product-add-ons.svg',
                'premium_tab'      => array(
                    'features' => array(
                        array(
                            // translators: Get premium tab string.
                            'title'       => __( 'Choose which users to show the options to  ', 'yith-woocommerce-product-add-ons' ),
                            // translators: Get premium tab string description.
                            'description' => __( 'In the free version, the options are shown to all users. With the premium version, you can instead choose whether to show them only to guest users, users with an account on the shop, or specific user roles. ', 'yith-woocommerce-product-add-ons' ),
                        ),
                        array(
                            // translators: Get premium tab string.
                            'title'       => __( 'Color, texture, or pattern samples + the option to select the color', 'yith-woocommerce-product-add-ons' ),
                            // translators: Get premium tab string description.
                            'description' => __( 'In the premium version, you can use the "color swatch" add-on and define a single or two colors for each sample, or upload an image representing textures, backgrounds, fabrics, materials, etc. If you don\'t want to set samples and want customers to be able to choose any color, use the "color picker" add-on instead: they will be able to select any color from the entire color wheel.', 'yith-woocommerce-product-add-ons' ),
                        ),
                        array(
                            // translators: Get premium tab string.
                            'title'       => __( 'Textarea and quantity selector field', 'yith-woocommerce-product-add-ons' ),
                            // translators: Get premium tab string description.
                            'description' => __( 'Insert a textarea if you need customers to enter a custom text during the product configuration (you can also limit the number of characters allowed) and use the numberic field to allow them to select a quantity.', 'yith-woocommerce-product-add-ons' ),
                        ),
                        array(
                            // translators: Get premium tab string.
                            'title'       => __( 'Text labels, images, and icons', 'yith-woocommerce-product-add-ons' ),
                            // translators: Get premium tab string description.
                            'description' => __( 'Use the "label or image" add-on to visually represent options on your products. You can use this option either for simple text labels (e.g. showing available sizes within buttons), or to insert a preview image, a photo of a model, or an icon identifying the option.', 'yith-woocommerce-product-add-ons' ),
                        ),
                        array(
                            // translators: Get premium tab string.
                            'title'       => __( 'Calendar field with date and time selection', 'yith-woocommerce-product-add-ons' ),
                            // translators: Get premium tab string description.
                            'description' => __( 'Use the calendar field if you want your customers to be able to select a date during the product configuration (e.g. they need to select a delivery date and time, a date of birth for a customizable product, etc.). 
                            You can choose which date and time to show by default in the calendar, disable specific dates, days of the week, or time slots (e.g. to prevent the customer from selecting Saturdays and Sundays or the entire month of December), and more!', 'yith-woocommerce-product-add-ons' ),
                        ),
                        array(
                            // translators: Get premium tab string.
                            'title'       => __( 'File upload field with drag & drop support', 'yith-woocommerce-product-add-ons' ),
                            // translators: Get premium tab string description.
                            'description' => __( 'Allow users to upload files during the product configuration — this option is essential if you sell customizable products and customers need to be able to easily upload illustrations, photos, company logos, etc.', 'yith-woocommerce-product-add-ons' ),
                        ),
                        array(
                            // translators: Get premium tab string.
                            'title'       => __( 'Upsell thanks to the new "product" add-on', 'yith-woocommerce-product-add-ons' ),
                            // translators: Get premium tab string description.
                            'description' => __( 'The "product" add-on allows you to create products that can be assembled, create product bundles, or simply structure an upsell strategy by showing next to your products a section with suggested products that are usually bought with the original product.', 'yith-woocommerce-product-add-ons' ),
                        ),
                        array(
                            // translators: Get premium tab string.
                            'title'       => __( 'Hundreds of advanced options to manage product configuration exactly how you want it to work', 'yith-woocommerce-product-add-ons' ),
                            // translators: Get premium tab string description.
                            'description' => __( 'The premium version of the plugin includes hundreds of options for configuring how add-ons work in your products. For each set of options, you can define, for example, which ones to make mandatory or set the minimum or maximum number of options the user can select to proceed with the purchase. You can also customize the style of the options and choose how to display them on the page (one below the other, in a horizontal grid, etc.).', 'yith-woocommerce-product-add-ons' ),
                        ),
                        array(
                            // translators: Get premium tab string.
                            'title'       => __( 'Regular updates, translations, and technical support', 'yith-woocommerce-product-add-ons' ),
                        ),
                        // ...
                    ),
                ),
            );

            $args = apply_filters( 'yith_wapo_register_panel_args', $args );

            $this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
        }

		/**
		 * Temporary admin link for the 1.x version
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function old_admin_menu() {
			// TODO: Remove in a future version.
			add_submenu_page(
				'edit.php?post_type=product',
				'Add-ons',
				'Add-ons',
				'manage_woocommerce',
				'admin.php?page=yith_wapo_panel'
			);
		}

		/**
		 * Admin enqueue scripts
		 */
		public function admin_enqueue_scripts() {

			if ( isset( $_GET['page'] ) && 'yith_wapo_panel' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				$screen     = get_current_screen();
				$min        = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                // CSS.
				wp_enqueue_style( 'wapo-admin', YITH_WAPO_URL . 'assets/css/admin.css', false, YITH_WAPO_SCRIPT_VERSION );

				// JS.
				wp_register_script( 'yith_wapo_admin', YITH_WAPO_URL . 'assets/js/admin' . $min . '.js', array( 'jquery' ), YITH_WAPO_SCRIPT_VERSION, true );
				wp_enqueue_script( 'yith_wapo_admin' );

				$admin_localize = array(
					'i18n' => array(
						// translators: Conditional logic - empty selected add-on.
						'selectOption' => __( 'Select an add-on', 'yith-woocommerce-product-add-ons' ),
                        // translators: Label printed when selecting "Discount the main product price".
                        'discountLabel' => __( 'Discount', 'yith-woocommerce-product-add-ons' ),
                        // translators: Label printed when selecting other option than "Discount the main product price".
                        'optionCostLabel' => __( 'Option cost', 'yith-woocommerce-product-add-ons' ),
                        // translators: String printed when user try to save the block and the block name is empty.
                        'blockNameRequired' => __( 'Block name is required', 'yith-woocommerce-product-add-ons' ),
					),
				);

				$admin_localize = apply_filters( 'yith_wapo_admin_localize_args', $admin_localize );

				wp_localize_script( 'yith_wapo_admin', 'yith_wapo', $admin_localize );

			}

		}

		/**
		 * Show blocks tab
		 *
		 * @return  void
		 */
		public function show_blocks_tab() {
			yith_wapo_get_view(
				'blocks.php'
			);
		}

		/**
		 * Get the premium landing uri
		 *
		 * @return string The premium landing link.
		 */
		public function get_premium_landing_uri() {
			return apply_filters( 'yith_plugin_fw_premium_landing_uri', $this->premium_landing, YITH_WAPO_SLUG );
		}

		/**
		 * Update block visibility
		 *
		 * @return void
		 */
		public function enable_disable_block() {
			global $wpdb;
			$block_id  = isset( $_POST['block_id'] ) ? floatval( $_POST['block_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$block_vis = isset( $_POST['block_vis'] ) ? floatval( $_POST['block_vis'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Update db table.
			$table = $wpdb->prefix . 'yith_wapo_blocks';
			$data  = array( 'visibility' => $block_vis );
			$wpdb->update( $table, $data, array( 'id' => $block_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			wp_die();
		}

		/**
		 * Update addon visibility
		 *
		 * @return void
		 */
		public function enable_disable_addon() {
			global $wpdb;
			$addon_id  = isset( $_POST['addon_id'] ) ? floatval( $_POST['addon_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$addon_vis = isset( $_POST['addon_vis'] ) ? floatval( $_POST['addon_vis'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Update db table.
			$table = $wpdb->prefix . 'yith_wapo_addons';
			$data  = array( 'visibility' => $addon_vis );
			$wpdb->update( $table, $data, array( 'id' => $addon_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			wp_die();
		}

		/**
		 * Sort blocks list
		 *
		 * @return void
		 */
		public function sortable_blocks() {
			global $wpdb;

            $item_id    = floatval( $_POST['item_id'] ) ?? '';
			$moved_item = isset( $_POST['moved_item'] ) ? floatval( $_POST['moved_item'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$prev_item  = isset( $_POST['prev_item'] ) ? floatval( $_POST['prev_item'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$next_item  = isset( $_POST['next_item'] ) && floatval( $_POST['next_item'] ) > 0 ? floatval( $_POST['next_item'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing

            $priority = 0;

            // $prev_item || $next_item to zero means that they doesn't exists or has already priority zero.
            if ( 0 == $prev_item && $next_item > 0 ) {
                $priority = max( $next_item - 1, 0 ); // Get the value if higher than zero. If not, get zero.
            } elseif ( 0 == $next_item && $prev_item > 0 ) {
                $priority = $prev_item + 1;
            } elseif( $prev_item > 0 && $next_item > 0 ) {
                $gap      = $next_item - $prev_item;
                $med      = floatval( $gap / 2 );
                $med      = min( $med, 1 ); // Get the value if below 1. If not, get 1.

                $priority = $prev_item + $med;
            }

            // Update db table.
			$table = $wpdb->prefix . 'yith_wapo_blocks';
			$data  = array( 'priority' => $priority );
			$wpdb->update( $table, $data, array( 'id' => $item_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

            $data_json = array(
                'itemID'       => $item_id,
                'itemPriority' => $priority
            );

            wp_send_json_success( $data_json );

		}

		/**
		 * Sort addons list
		 *
		 * @return void
		 */
		public function sortable_addons() {
			global $wpdb;
			$moved_item = isset( $_POST['moved_item'] ) ? floatval( $_POST['moved_item'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$prev_item  = isset( $_POST['prev_item'] ) ? floatval( $_POST['prev_item'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$next_item  = isset( $_POST['next_item'] ) ? floatval( $_POST['next_item'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Missing

            $priority = 0;

            // $prev_item || $next_item to zero means that they doesn't exists or has already priority zero.
            if ( 0 == $prev_item && $next_item > 0 ) {
                $priority = max( $next_item - 1, 0 ); // Get the value if higher than zero. If not, get zero.
            } elseif ( 0 == $next_item && $prev_item > 0 ) {
                $priority = $prev_item + 1;
            } elseif( $prev_item > 0 && $next_item > 0 ) {
                $gap      = $next_item - $prev_item;
                $med      = floatval( $gap / 2 );
                $med      = min( $med, 1 ); // Get the value if below 1. If not, get 1.

                $priority = $prev_item + $med;
            }

			// Update db table.
			$table = $wpdb->prefix . 'yith_wapo_addons';
			$data  = array( 'priority' => $priority );
			$wpdb->update( $table, $data, array( 'id' => $moved_item ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

			echo esc_attr( $moved_item . '-' . $priority );

			wp_die();
		}

		/**
		 * Hide order item metas
		 *
		 * @param array $hidden_meta The hidden item metas.
		 *
		 * @return mixed
		 */
		public function hide_order_item_meta( $hidden_meta ) {
			$hidden_meta[] = '_ywapo_product_img';

			return $hidden_meta;
		}

		/**
		 * Change product image in dashboard if replaced by add-ons
		 *
		 * @param string                $image The image.
		 * @param int                   $item_id The item id.
		 * @param WC_Order_Item_Product $item The item object.
		 * @return string
		 */
		public function admin_order_item_thumbnail( $image, $item_id, $item ) {
			return $this->order_item_thumbnail( $image, $item );
		}

		/**
		 * Change product image in order if replaced by add-ons
		 *
		 * @param string                $image The image.
		 * @param WC_Order_Item_Product $item The item object.
		 * @return string
		 */
		public function order_item_thumbnail( $image, $item ) {
			if ( $item instanceof WC_Order_Item_Product ) {
				$wapo_image = $item->get_meta( '_ywapo_product_img' );

				if ( ! empty( $wapo_image ) ) {
					$image = wp_get_attachment_image( $wapo_image );
				}
			}

			return $image;
		}

		/**
		 * Adding admin panel tabs.
		 *
		 * @param array  $admin_tabs The admin tabs array.
		 * @param string $capability The capability of the user.
		 * @return mixed
		 */
		public function yith_wapo_admin_panel_tabs( $admin_tabs, $capability ) {

			$admin_tabs['blocks'] = array(
                // translators: [ADMIN] Options tab.
				'title' => __( 'Options Blocks', 'yith-woocommerce-product-add-ons' ),
				'icon'  => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5zm0 9.75h2.25A2.25 2.25 0 0010.5 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25zm9.75-9.75H18a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0 0018 3.75h-2.25A2.25 2.25 0 0013.5 6v2.25a2.25 2.25 0 002.25 2.25z"></path>
</svg>',
			);

			if ( 'manage_woocommerce' === $capability ) {
				$admin_tabs['settings'] = array(
                    // translators: [ADMIN] Options tab.
					'title' => __( 'General Settings', 'yith-woocommerce-product-add-ons' ),
					'icon'  => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"></path>
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
</svg>',
				);
				$admin_tabs['style']    = array(
                    // translators: [ADMIN] Options tab.
					'title'       => __( 'Style', 'yith-woocommerce-product-add-ons' ),
					'icon'        => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 11.25l1.5 1.5.75-.75V8.758l2.276-.61a3 3 0 10-3.675-3.675l-.61 2.277H12l-.75.75 1.5 1.5M15 11.25l-8.47 8.47c-.34.34-.8.53-1.28.53s-.94.19-1.28.53l-.97.97-.75-.75.97-.97c.34-.34.53-.8.53-1.28s.19-.94.53-1.28L12.75 9M15 11.25L12.75 9"></path>
</svg>',
					'description' => __(
						'Configure style options to customize the add-ons you create.',
						'yith-woocommerce-product-add-ons'
					),
				);
			}

			return $admin_tabs;
		}

	}
}

/**
 * Unique access to instance of YITH_WAPO_Admin class
 *
 * @return YITH_WAPO_Admin
 */
function YITH_WAPO_Admin() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WAPO_Admin::get_instance();
}
