<?php
! defined( 'YITH_POS' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_POS_Assets' ) ) {
	/**
	 * it handles the assets
	 *
	 * @class  YITH_POS_Assets
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	class YITH_POS_Assets {
		private static $_instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_POS_Assets
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_POS_Assets constructor.
		 */
		private function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'register_common_scripts' ), 11 );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_common_scripts' ), 11 );

			if ( YITH_POS::is_request( 'admin' ) ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ), 11 );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 11 );
			} else {
				add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_scripts' ), 11 );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ), 11 );
			}

			// Script Translations
			add_filter( 'pre_load_script_translations', array( $this, 'script_translations' ), 10, 4 );
		}

		private function _get_script_version() {
			$version = YITH_POS_VERSION;
			if ( defined( 'YITH_POS_SCRIPT_DEBUG' ) && YITH_POS_SCRIPT_DEBUG ) {
				$version .= '-' . time();
			}

			return $version;
		}

		/**
		 * Register common scripts
		 */
		public function register_common_scripts() {

		}

		/**
		 * Register admin scripts
		 */
		public function register_admin_scripts() {
			global $post;
			$post_id = $post && isset( $post->ID ) ? $post->ID : '';

			$version = $this->_get_script_version();

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'yith-pos-admin-globals', YITH_POS_ASSETS_URL . '/js/admin/globals' . $suffix . '.js', array( 'jquery' ), $version, true );

			wp_register_script( 'yith-pos-admin', YITH_POS_ASSETS_URL . '/js/admin/admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'yith-pos-admin-globals' ), $version, true );

			wp_register_script( 'yith-pos-admin-validation', YITH_POS_ASSETS_URL . '/js/admin/validation' . $suffix . '.js', array( 'jquery' ), $version, true );
			wp_register_script( 'yith-pos-admin-store-wizard', YITH_POS_ASSETS_URL . '/js/admin/store-wizard' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'yit-metabox' ), $version, true );
			wp_register_script( 'yith-pos-admin-store-edit', YITH_POS_ASSETS_URL . '/js/admin/store-edit' . $suffix . '.js', array( 'jquery', 'yith-pos-admin-globals', 'yit-metabox' ), $version, true );
			wp_register_script( 'yith-pos-admin-receipt-edit', YITH_POS_ASSETS_URL . '/js/admin/receipt-edit' . $suffix . '.js', array( 'jquery' ), $version, true );
			wp_register_script( 'yith-pos-admin-gateways', YITH_POS_ASSETS_URL . '/js/admin/gateways' . $suffix . '.js', array( 'jquery' ), $version, true );
			wp_register_script( 'yith-pos-admin-products', YITH_POS_ASSETS_URL . '/js/admin/products' . $suffix . '.js', array( 'jquery' ), $version, true );
			wp_register_script( 'yith-pos-admin-prevent-restock-items', YITH_POS_ASSETS_URL . '/js/admin/prevent-restock-items' . $suffix . '.js', array( 'jquery' ), $version, true );

			wp_register_style( 'yith-pos-admin', YITH_POS_ASSETS_URL . '/css/admin/admin.css', array(), $version );
			wp_register_style( 'yith-pos-admin-store-edit', YITH_POS_ASSETS_URL . '/css/admin/store-edit.css', array(), $version );
			wp_register_style( 'yith-pos-admin-receipt-edit', YITH_POS_ASSETS_URL . '/css/admin/receipt-edit.css', array(), $version );
			wp_register_style( 'yith-pos-admin-products', YITH_POS_ASSETS_URL . '/css/admin/products.css', array( 'yith-plugin-fw-fields' ), $version );

			wp_register_style( 'yith-pos-admin-dashboard', YITH_POS_ASSETS_URL . '/css/admin/dashboard.css', array(), $version );

			$dashboard_deps = array( 'wp-api-fetch', 'wp-components', 'wp-element', 'wp-hooks', 'wp-i18n', 'wp-data', 'wc-components' );
			wp_register_script( 'yith-pos-dashboard', YITH_POS_REACT_URL . '/dashboard/index.js', $dashboard_deps, $version, true );

			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'yith-pos-dashboard', 'yith-point-of-sale-for-woocommerce', YITH_POS_LANGUAGES_PATH );
			}

			/**
			 * Localize
			 */

			$admin_i18n = array(
				'store-description'     => __( 'Here you can create, edit and manage all the Stores.', 'yith-point-of-sale-for-woocommerce' ),
				'register-description'  => __( 'Here you can manage all the Registers of the Stores', 'yith-point-of-sale-for-woocommerce' ),
				'receipt-description'   => __( 'Here you can manage all the Receipt Templates', 'yith-point-of-sale-for-woocommerce' ),
				'one_register_required' => __( 'You need to create at least one Register before proceeding', 'yith-point-of-sale-for-woocommerce' ),
				'restock_not_allowed'   => __( 'To restock items automatically for a POS order you need WooCommerce 4.1 or greater.<br />For previous versions, you can restock them manually after refunding the order.', 'yith-point-of-sale-for-woocommerce' )
			);

			wp_localize_script( 'yith-pos-admin', 'admin_i18n', $admin_i18n );
			wp_localize_script( 'yith-pos-admin-store-wizard', 'admin_i18n', $admin_i18n );
			wp_localize_script( 'yith-pos-admin-prevent-restock-items', 'admin_i18n', $admin_i18n );

			$yith_pos_store_edit = array(
				'post_id'                           => $post_id,
				'create_register_nonce'             => wp_create_nonce( 'yith-pos-create-register' ),
				'update_register_nonce'             => wp_create_nonce( 'yith-pos-update-register' ),
				'delete_register_nonce'             => wp_create_nonce( 'yith-pos-delete-register' ),
				'i18n_register_delete_confirmation' => __( 'Are you sure you want to delete this Register?', 'yith-point-of-sale-for-woocommerce' ),
			);

			wp_localize_script( 'yith-pos-admin-store-edit', 'yith_pos_store_edit', $yith_pos_store_edit );
		}

		/**
		 * Register frontend scripts
		 */
		public function register_frontend_scripts() {
			$version = $this->_get_script_version();
			$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if (class_exists('YIT_Assets')){
				YIT_Assets::instance()->register_styles_and_scripts();
			}

			wp_register_style( 'yith-pos-open-sans', '//fonts.googleapis.com/css?family=Open+Sans:400,600,700,800&display=swap' );

			wp_register_style( 'yith-pos-font', YITH_POS_ASSETS_URL . '/css/frontend/font.css', array(), $version );

			wp_register_style( 'yith-pos-frontend', YITH_POS_ASSETS_URL . '/css/frontend/pos.css', array(), $version );
			wp_register_style( 'yith-pos-login', YITH_POS_ASSETS_URL . '/css/frontend/login.css', array('yith-pos-font'), $version );

			wp_register_style( 'yith-pos-rtl', YITH_POS_ASSETS_URL . '/css/frontend/pos-rtl.css', array(), $version );


			$pos_deps = array( 'wp-api-fetch', 'wp-components', 'wp-element', 'wp-hooks', 'wp-i18n', 'wp-data', 'wp-date' );
			wp_register_script( 'yith-pos-frontend', YITH_POS_REACT_URL . '/pos/index.js', $pos_deps, $version, true );

			wp_register_script( 'yith-pos-register-login', YITH_POS_ASSETS_URL . '/js/register-login' . $suffix . '.js', array( 'jquery' ), $version, true );
		}

		/**
		 * Enqueue admin scripts
		 */
		public function enqueue_admin_scripts() {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			if ( 'shop_order' === $screen_id ) {
				wp_enqueue_style( 'yith-pos-admin' );
			}
			if ( 'product' === $screen_id ) {
				wp_enqueue_script( 'yith-plugin-fw-fields' );
			}

			if ( in_array( $screen_id, yith_pos_admin_screen_ids() ) ) {
				wp_enqueue_script( 'yith-pos-admin' );
				wp_enqueue_script( 'yith-pos-admin-validation' );
				wp_enqueue_style( 'yith-pos-admin' );
			}

			if ( YITH_POS_Post_Types::$store === $screen_id ) {
				wp_enqueue_style( 'yith-pos-admin-store-edit' );
				wp_enqueue_script( 'yith-pos-admin-store-edit' );
				if ( yith_pos_is_store_wizard() ) {
					wp_enqueue_script( 'yith-pos-admin-store-wizard' );
				}
			}

			if ( in_array( $screen_id, array( YITH_POS_Post_Types::$register, YITH_POS_Post_Types::$store ) ) ) {
				wp_enqueue_script( 'selectWoo' );
				wp_enqueue_script( 'wc-enhanced-select' );
			}

			if ( in_array( $screen_id, array( 'edit-' . YITH_POS_Post_Types::$register, 'edit-' . YITH_POS_Post_Types::$store ) ) ) {
				wp_enqueue_script( 'yith-plugin-fw-fields' );
			}

			if ( YITH_POS_Post_Types::$receipt === $screen_id ) {
				wp_enqueue_style( 'yith-pos-admin-receipt-edit' );
				wp_enqueue_script( 'yith-pos-admin-receipt-edit' );
			}

			if ( 'woocommerce_page_wc-settings' === $screen_id ) {
				wp_enqueue_script( 'yith-pos-admin-gateways' );
			}

			$is_dashboard_page = 'yith-plugins_page_yith_pos_panel' === $screen_id && ! isset( $_GET[ 'tab' ] ) || ( isset( $_GET[ 'tab' ] ) && 'dashboard' === $_GET[ 'tab' ] );
			if ( $is_dashboard_page && yith_pos_is_wc_admin_enabled() ) {
				wp_enqueue_style( 'wc-components' );
				wp_enqueue_style( defined( 'WC_ADMIN_APP' ) ? WC_ADMIN_APP : 'wc-admin-app' );
				wp_enqueue_style( 'yith-pos-admin-dashboard' );
				wp_enqueue_script( 'yith-pos-dashboard' );

				wp_localize_script( 'yith-pos-dashboard', 'yithPosSettings', YITH_POS_Settings()->get_admin_settings() );
			}

			if ( function_exists( 'YITH_POS_Stock_Management' ) && 'product' == $screen_id ) {
				wp_enqueue_style( 'yith-pos-admin-products' );
				wp_enqueue_script( 'yith-pos-admin-products' );
			}
		}

		/**
		 * Enqueue frontend scripts
		 */
		public function enqueue_frontend_scripts() {
			if ( is_yith_pos() ) {
				yith_pos_enqueue_style( 'yith-pos-open-sans' );
				if ( yith_pos_can_view_register() ) {
					yith_pos_enqueue_style( 'yith-pos-font' );
					yith_pos_enqueue_style( 'yith-pos-frontend' );
					yith_pos_enqueue_script( 'yith-pos-frontend' );
				} else {
					yith_pos_enqueue_style( 'yith-plugin-fw-fields' );
					yith_pos_enqueue_style( 'yith-pos-login' );
					$pos_login = $this->get_login_style();
					wp_add_inline_style( 'yith-pos-login', $pos_login );
					yith_pos_enqueue_script( 'yith-pos-register-login' );
				}

				if ( function_exists( 'wp_set_script_translations' ) ) {
					wp_set_script_translations( 'yith-pos-frontend', 'yith-point-of-sale-for-woocommerce', YITH_POS_DIR . 'languages' );

				}

				if ( is_rtl() ) {
					yith_pos_enqueue_style( 'yith-pos-rtl' );
				}
			}
		}

		public function get_login_style() {
			$primary_color    = ( $meta = get_option( 'yith_pos_registers_primary' ) ) ? $meta : '#09adaa';
			$secondary_color  = ( $meta = get_option( 'yith_pos_registers_products_background' ) ) ? $meta : '#eaeaea';
			$background_color = ( $meta = get_option( 'yith_pos_login_background_color' ) ) ? $meta : '#';
			$css              = "
		        body{ background-color:{$background_color}}
		        #login .input-login, .yith-pos-form select{ border-color: {$secondary_color} }
		        #login .input-login:focus,.yith-pos-form select:focus { border-color: {$primary_color} }
		        #login .input-login:focus + label.float-label, #login .input-login:valid + label.float-label { color: {$primary_color} }
		        .yith-pos-form select:focus + label.float-label, .yith-pos-form select:valid + label.float-label { color: {$primary_color} }
		        .login-submit{ background-color: {$secondary_color} }
		        #login input[type=checkbox]+span:before{ color: {$primary_color}; border-color: {$secondary_color}}
                #login .login-submit{background-color: {$primary_color}}
		        #yith-pos-store-register-form a{ color: {$primary_color}; }
		        #login .login-submit, .yith-pos-form .submit{background-color: {$primary_color}}";

			if ( $meta = get_option( 'yith_pos_login_background_image' ) ) {
				$css .= " body{ background: url({$meta}) center center; background-size: cover; background-repeat: no-repeat;}";
			}

			return $css;
		}

		/**
		 * Create the json translation through the PHP file
		 * so it's possible using normal translations (with PO files) also for JS translations
		 *
		 * @param string|null $json_translations
		 * @param string      $file
		 * @param string      $handle
		 * @param string      $domain
		 *
		 * @return string|null
		 */
		public function script_translations( $json_translations, $file, $handle, $domain ) {
			if ( 'yith-point-of-sale-for-woocommerce' === $domain && in_array( $handle, array( 'yith-pos-dashboard', 'yith-pos-frontend' ) ) ) {
				$path = YITH_POS_LANGUAGES_PATH . 'yith-point-of-sale-for-woocommerce.php';
				if ( file_exists( $path ) ) {
					$translations = include $path;

					$json_translations = json_encode( array(
						                                  "domain"      => "yith-point-of-sale-for-woocommerce",
						                                  'locale_data' => array(
							                                  'messages' =>
								                                  array(
									                                  '' => array(
										                                  'domain'       => 'yith-point-of-sale-for-woocommerce',
										                                  'lang'         => get_locale(),
										                                  'plural-forms' => "nplurals=2; plural=(n != 1);"
									                                  )
								                                  )
								                                  +
								                                  $translations
						                                  )
					                                  ) );

				}
			}

			return $json_translations;
		}


	}
}
