<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YITH_WooCommerce_Gift_Cards' ) ) {

	/**
	 *
	 * @class   YITH_WooCommerce_Gift_Cards
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_WooCommerce_Gift_Cards {

		const YWGC_DB_VERSION_OPTION = 'yith_gift_cards_db_version';


		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

        /**
         * @var string Premium version landing link
         */
        protected $_premium_landing = '//yithemes.com/themes/plugins/yith-woocommerce-gift-cards/';

        /**
         * @var string Plugin official documentation
         */
        protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-gift-cards/';

        /**
         * @var string Plugin panel page
         */
        protected $_panel_page = 'yith_woocommerce_gift_cards_panel';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-gift-cards/';

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null ( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {

			$this->includes ();
			$this->init_hooks ();
			$this->start ();
		}


		public function includes() {
			//todo check if free plugin contains them
			require_once ( YITH_YWGC_DIR . 'lib/class-yith-wc-product-gift-card.php' );
			require_once ( YITH_YWGC_DIR . 'lib/class-yith-ywgc-cart-checkout.php' );
			require_once ( YITH_YWGC_DIR . 'lib/class-yith-ywgc-emails.php' );
			require_once ( YITH_YWGC_DIR . 'lib/class-yith-ywgc-gift-cards-table.php' );
		}

		public function init_hooks() {
			/**
			 * Do some stuff on plugin init
			 */
			add_action ( 'init', array( $this, 'on_plugin_init' ) );

			/**
			 * Hide the temporary gift card product from being shown on shop page
			 */

			add_filter ( 'yith_plugin_status_sections', array( $this, 'set_plugin_status' ) );

            /* === Show Plugin Information === */

            add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWGC_DIR . '/' . basename( YITH_YWGC_FILE ) ), array(
                $this,
                'action_links',
            ) );

            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

		}


		public function start() {
			//  Init the backend
			YITH_YWGC_Backend::get_instance ();

			//  Init the frontend
			YITH_YWGC_Frontend::get_instance ();
		}



		/**
		 * Execute update on data used by the plugin that has been changed passing
		 * from a DB version to another
		 */
		public function update_database() {

			/**
			 * Init DB version if not exists
			 */
			$db_version = get_option ( self::YWGC_DB_VERSION_OPTION );

			if ( ! $db_version ) {
				//  Update from previous version where the DB option was not set
				global $wpdb;

				//  Update metakey from YITH Gift Cards 1.0.0
				$query = "Update {$wpdb->prefix}woocommerce_order_itemmeta
                        set meta_key = '" . YWGC_META_GIFT_CARD_POST_ID . "'
                        where meta_key = 'gift_card_post_id'";
				$wpdb->query ( $query );

				$db_version = '1.0.0';
			}

			/**
			 * Start the database update step by step
			 */
			if ( version_compare ( $db_version, '1.0.0', '<=' ) ) {

				//  Set gift card placeholder with catalog visibility equal to "hidden"
				$placeholder_id = get_option ( YWGC_PRODUCT_PLACEHOLDER );

				yit_save_prop ( wc_get_product ( $placeholder_id ), '_visibility', 'hidden' );

				$db_version = '1.0.1';
			}

			if ( version_compare ( $db_version, '1.0.1', '<=' ) ) {

				//  extract the user_id from the order where a gift card is applied and register
				//  it so the gift card will be shown on my-account

				$args = array(
					'numberposts' => - 1,
					'meta_key'    => YWGC_META_GIFT_CARD_ORDERS,
					'post_type'   => YWGC_CUSTOM_POST_TYPE_NAME,
					'post_status' => 'any',
				);

				//  Retrieve the gift cards matching the criteria
				$posts = get_posts ( $args );

				foreach ( $posts as $post ) {
					$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $post->ID ) );

					if ( ! $gift_card->exists () ) {
						continue;
					}

					/** @var WC_Order $order */
					$orders = $gift_card->get_registered_orders ();
					foreach ( $orders as $order_id ) {
						$order = wc_get_order ( $order_id );
						if ( $order ) {
							$gift_card->register_user ( yit_get_prop ( $order, 'customer_user' ) );
						}
					}
				}

				$db_version = '1.0.2';  //  Continue to next step...
			}

			if ( version_compare( $db_version, '1.0.2', '<=' ) ) {
				flush_rewrite_rules();
				$db_version = '1.0.3';  //  Continue to next step...
			}

			//  Update the current DB version
			update_option ( self::YWGC_DB_VERSION_OPTION, YITH_YWGC_DB_CURRENT_VERSION );
		}

		/**
		 *  Execute all the operation need when the plugin init
		 */
		public function on_plugin_init() {

			$this->fix_missing_default_gift_card ();

			$this->init_post_type ();

			$this->init_plugin ();

			$this->update_database ();
		}


		public function fix_missing_default_gift_card() {

			if ( isset( $_GET['ywgc-reset'] ) ) {
				$placeholder_id = get_option ( YWGC_PRODUCT_PLACEHOLDER );

				delete_option ( YWGC_PRODUCT_PLACEHOLDER );
				wp_delete_post ( $placeholder_id, true );

				$redirect_url = remove_query_arg ( 'ywgc-reset' );
				wp_redirect ( $redirect_url );
				exit;
			}
		}

		/**
		 * Initialize plugin data, if any
		 */
		public function init_plugin() {
			//nothing to do
		}

		public function current_user_can_create() {
			return apply_filters ( 'ywgc_can_create_gift_card', true );
		}

		/**
		 * Register the custom post type
		 */
		public function init_post_type() {
			$args = array(
				'label'               => esc_html__( 'Gift Cards', 'yith-woocommerce-gift-cards' ),
				'description'         => esc_html__( 'Gift Cards', 'yith-woocommerce-gift-cards' ),
				// 'labels' => $labels,
				// Features this CPT supports in Post Editor
				'supports'            => array(
					'title',
					'editor',
				),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'menu_position'       => 9,
				'can_export'          => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-clipboard',
				'query_var'           => false,
			);
			
			// Registering your Custom Post Type
			register_post_type ( YWGC_CUSTOM_POST_TYPE_NAME, $args );
		}
		
		
		/**
		 * Retrieve a gift card product instance from the gift card code
		 *
		 * @param string $code the gift card code to search for
		 *
		 * @return YITH_YWGC_Gift_Card
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_gift_card_by_code( $code ) {

			$args = array( 'gift_card_number' => $code );

			return new YITH_YWGC_Gift_Card( $args );
		}

		/**
		 * Generate a new gift card code
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function generate_gift_card_code() {

			//http://stackoverflow.com/questions/3521621/php-code-for-generating-decent-looking-coupon-codes-mix-of-alphabets-and-number
			$code = strtoupper ( substr ( base_convert ( sha1 ( uniqid ( mt_rand () ) ), 16, 36 ), 0, 16 ) );

			$code = sprintf ( "%s-%s-%s-%s",
				substr ( $code, 0, 4 ),
				substr ( $code, 4, 4 ),
				substr ( $code, 8, 4 ),
				substr ( $code, 12, 4 )
			);

			return apply_filters( 'yith_ywgc_gift_card_code', $code );
		}

        /**
         * Action links
         *
         *
         * @return void
         * @since    1.3.2
         * @author   Daniel Sanchez <daniel.sanchez@yithemes.com>
         */
        public function action_links( $links ) {

            $links = is_array($links) ? $links : array();
            $links = yith_add_action_links( $links, $this->_panel_page, false );

            return $links;

        }

        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.3.2
         * @author   Daniel Sanchez <daniel.sanchez@yithemes.com>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWGC_FREE_INIT' ) {

            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
                $new_row_meta_args['slug'] = YITH_YWGC_SLUG;
            }

            return $new_row_meta_args;
        }

	}
}

