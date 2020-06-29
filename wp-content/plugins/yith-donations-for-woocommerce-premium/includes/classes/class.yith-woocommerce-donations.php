<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WC_Donations' ) ) {

	class YITH_WC_Donations {

		protected $_donation_id;
		protected static $_instance;
		protected $_panel;
		protected $_panel_page;

		protected $_premium;
		protected $_suffix;
		protected $_messages;

		public function __construct() {

			//Init class attributes
			$this->_panel                  = null;
			$this->_panel_page             = 'yith_wc_donations';
			$this->_premium                = 'premium.php';
			$this->_suffix                 = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';


			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YWCDS_DIR . '/' . basename( YWCDS_FILE ) ), array(
				$this,
				'action_links'
			) );
			//Add row meta
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			add_action( 'init', array( $this, 'init_ywds_plugin' ) );
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'add_form_donation' ), 35 );

			add_action( 'wp_enqueue_scripts', array( $this, 'add_free_frontend_style_script' ) );
			//Add menu field under YITH_PLUGIN
			add_action( 'yith_wc_donations_premium', array( $this, 'premium_tab' ) );

			add_filter( 'woocommerce_is_purchasable', array( $this, 'set_donation_purchasable' ), 10, 2 );

			add_filter( 'woocommerce_get_cart_item_from_session', array(
				$this,
				'get_cart_donation_item_from_session'
			), 11, 2 );
			add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_donation_item' ), 11, 1 );

			add_filter( 'woocommerce_cart_item_name', array( $this, 'print_cart_item_donation' ), 10, 3 );

			if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
				add_action( 'woocommerce_checkout_create_order_line_item', array(
					$this,
					'add_new_order_item'
				), 10, 3 );

			} else {
				add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_order_item_meta' ), 10, 3 );
			}

			add_filter( 'woocommerce_order_item_name', array( $this, 'print_donation_in_order' ), 10, 2 );


			add_action( 'wp_ajax_ywcds_add_donation', array( $this, 'ywcds_add_donation_ajax' ) );
			add_action( 'wp_ajax_nopriv_ywcds_add_donation', array( $this, 'ywcds_add_donation_ajax' ) );

			add_action( 'woocommerce_add_to_cart', array( $this, 'add_donation_single_product' ), 25 );
			add_action( 'wp_loaded', array( $this, 'ywcds_add_donation' ), 25 );


			if ( is_admin() ) {

				add_action( 'admin_menu', array( $this, 'add_yith_donations_menu' ), 5 );
				add_filter( 'yith_plugin_fw_get_field_template_path', array(
					$this,
					'get_donations_custom_field_path'
				), 10, 2 );
			}

			add_action( 'widgets_init', array( $this, 'register_donations_widget' ) );

			$this->_donation_id = get_option( '_ywcds_donation_product_id' );


		}

		/** return single instance of class
		 * @author YIThemes
		 * @since 1.0.0
		 * @return YITH_WC_Donations
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, defined( 'YWCDS_PREMIUM' ) );

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 * @param  $init_file
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCDS_FREE_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = 'yith-donations-for-woocommerce';
			}

			return $new_row_meta_args;
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing_url . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing_url;
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  void
		 */
		public function premium_tab() {
			$premium_tab_template = YWCDS_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_yith_donations_menu() {
			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = apply_filters( 'ywcds_add_premium_tab', array(
				'settings'        => __( 'Settings', 'yith-donations-for-woocommerce' ),
				'premium-landing' => __( 'Premium Version', 'yith-donations-for-woocommerce' )
			) );

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'plugin_slug'      => 'yith-donations-for-woocommerce',
				'page_title'       => __( 'Donations', 'yith-donations-for-woocommerce' ),
				'menu_title'       => 'Donations',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWCDS_DIR . '/plugin-options',

			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}


		public function get_donations_custom_field_path( $field_template, $field ) {
			$custom_types = apply_filters( 'ywcds_custom_types', array(
				'donation-product-link',

			) );

			if ( in_array( $field['type'], $custom_types ) ) {
				$field_template = YWCDS_TEMPLATE_PATH . '/admin/' . $field['type'] . '.php';
			}


			return $field_template;
		}


		/**include user style and script
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function add_free_frontend_style_script() {

			wp_register_script( 'ywcds_free_frontend', YWCDS_ASSETS_URL . 'js/ywcds_free_frontend' . $this->_suffix . '.js', array( 'jquery' ), YWCDS_VERSION, true );
			wp_enqueue_script( 'ywcds_free_frontend' );

			$yith_wcds_frontend_l10n = array(
				'ajax_url'          => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
				'is_user_logged_in' => is_user_logged_in(),
				'ajax_loader_url'   => YWCDS_ASSETS_URL . 'assets/images/ajax-loader.gif',
				'actions'           => array(
					'add_donation_to_cart' => 'ywcds_add_donation',
				),
				'messages'          => $this->_init_messages(),
				'mon_decimal_point' => wc_get_price_decimal_separator(),
				'redirect_after_add_to_cart' => apply_filters( 'ywcds_redirect_after_add_to_cart_url',false )
			);

			wp_localize_script( 'ywcds_free_frontend', 'yith_wcds_frontend_l10n', $yith_wcds_frontend_l10n );

			wp_enqueue_style( 'ywcds_style_frontend' );
		}

		/**create YITH Product Donation and init default messages
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function init_ywds_plugin() {
			wp_register_style( 'ywcds_style_frontend', YWCDS_ASSETS_URL . 'css/ywcds_frontend.css', array(), YWCDS_VERSION );

			$donation_id = get_option( '_ywcds_donation_product_id', - 1 );
			$product     = wc_get_product( $donation_id );

			if ( $donation_id == - 1 || empty ( $product ) ) {

				$args = array(
					'post_author'  => get_current_user_id(),
					'post_type'    => 'product',
					'post_status'  => 'publish',
					'post_title'   => __( 'YITH Donations for WooCommerce', 'yith-donations-for-woocommerce' ),
					'post_content' => ''
				);

				$donation_id = wp_insert_post( $args );
				$product                 = wc_get_product( $donation_id );

				/*Update the options meta of our donation*/
				$product->set_tax_class('zero-rate');
				$product->set_tax_status('none');
				$product->set_catalog_visibility( 'hidden');
				$product->set_virtual('yes');
				$product->set_featured('no');
				$product->set_manage_stock('no');
				$product->set_sold_individually('yes');

				$product->save();


				update_option( '_ywcds_donation_product_id', $donation_id );
			}

			$this->_messages = $this->_init_messages();

		}

		/**init messages default
		 * @author YIThemes
		 * @since 1.0.0
		 * @return mixed|void
		 */
		private function _init_messages() {

			$messages = array(
				'no_number'   => __( 'Please enter a valid value', 'yith-donations-for-woocommerce' ),
				'empty'       => __( 'Please enter a number', 'yith-donations-for-woocommerce' ),
				'already'     => __( 'You have already added a donation to the cart', 'yith-donations-for-woocommerce' ),
				'success'     => __( 'Thanks for your donation', 'yith-donations-for-woocommerce' ),
				'text_button' => __( 'Add a donation', 'yith-donations-for-woocommerce' ),
				'negative'    => __( 'Please enter a number greater than 0', 'yith-donations-for-woocommerce' )
			);

			return apply_filters( 'ywcds_init_messages', $messages );
		}

		/**print from donation, in single page product/s
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function add_form_donation() {
			$product_ass_id = get_option( 'ywcds_product_donation' );
			global $product;

			$product_id = $product ? $product->get_id() : false ;
			if ( empty( $product_ass_id ) || $product_id != $product_ass_id ) {
				return;
			}

			$args = array(
				'message_for_donation' => get_option( 'ywcds_message_for_donation' ),

				'product_id' => $product_id,
			);

			wc_get_template( 'add-donation-form-single-product.php', $args, '', YWCDS_TEMPLATE_PATH );
		}

		/**manage donation in single product
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function add_donation_single_product() {


			if ( isset( $_REQUEST['amount_single_product'] ) && $_REQUEST['amount_single_product'] != '' && isset( $_REQUEST['add-to-cart'] ) ) {

				$product_id = $_REQUEST['add-to-cart'];
				$amount     = $_REQUEST['amount_single_product'];
				$res        = $this->add( $product_id, '', 1, $amount );

			}

		}

		public function get_donation_id() {

			$donation_id = yit_wpml_object_id( $this->_donation_id, 'product', true );

			return $donation_id;

		}

		/** add donation in cart
		 * @author YIThemes
		 * @since 1.0.0
		 *
		 * @param $product_id
		 * @param int $variation_id
		 * @param int $quantity
		 * @param $amount
		 *
		 * @return string
		 */
		public function add( $product_id, $variation_id = - 1, $quantity = 1, $amount ) {


			if ( ! empty( $amount ) ) {

				$amount = ywcds_format_number( $amount );
				if ( ! is_numeric( $amount ) ) {
					return 'no_number';
				}

				$donation_id = $this->get_donation_id();
				if ( $amount != null && $amount * 1 > 0 ) {


					$cart_item_data = apply_filters( 'ywcds_add_cart_item_data', array(
							'ywcds_amount'       => $amount,
							'ywcds_product_id'   => $product_id != $donation_id ? $product_id : - 1,
							'ywcds_variation_id' => $variation_id,
							'ywcds_data_added'   => date( "Y-m-d H:i:s" ),
							'ywcds_quantity'     => $quantity
						)
					);

					remove_action( 'woocommerce_add_to_cart', array( $this, 'add_donation_single_product' ), 25 );

					WC()->cart->add_to_cart( $donation_id, 1, '', array(), $cart_item_data );

					add_action( 'woocommerce_add_to_cart', array( $this, 'add_donation_single_product' ), 25 );

					if ( $product_id != $this->_donation_id ) {
						wc_add_notice( $this->get_message( 'success' ) );
					}

					return 'true';

				} else {

					return 'negative';
				}

			} else {
				return 'empty';
			}


		}

		/**  manage donation if js is not enabled
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function ywcds_add_donation() {

			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				if ( isset( $_REQUEST['add_donation_to_cart'] ) ) {


					$product_id = $_REQUEST['add_donation_to_cart'];
					$amount     = isset( $_REQUEST['ywcds_amount'] ) ? $_REQUEST['ywcds_amount'] : '';
					$result     = $this->add( $product_id, '', 1, $amount );

					$redirect_to_cart = 'yes' == get_option( 'woocommerce_cart_redirect_after_add' );

					if ( apply_filters( 'ywcds_redirect_if_successful', $result == 'true' ) ) {
						$_url = apply_filters('ywcds_redirect_after_add_to_cart_url', wc_get_cart_url() );
					} else {

						$_url = remove_query_arg( array(
							'ywcds_amount',
							'add_donation_to_cart',
							'ywcds_submit_widget'
						) );
					}

					$donation_id      = $this->get_donation_id();
					$donation_product = wc_get_product( $donation_id );

					if ( 'true' == $result ) {
						$message = sprintf( '%s %s', $donation_product->get_title(), __( 'have been added to your cart.', 'yith-donations-for-woocommerce' ) );
						$type    = 'success';
					} else if ( 'negative' == $result ) {
						$message = $this->get_message( 'negative' );
						$type    = 'error';
					} else {
						$message = $this->get_message( 'empty' );
						$type    = 'error';
					}

					$message = apply_filters( 'ywcds_donation_message', array(
						'message' => $message,
						'type'    => $type
					), $result );
					wc_add_notice( $message['message'], $message['type'] );
					wp_safe_redirect( $_url );
					exit;
				}
			}
		}

		/**
		 * adjust the product based on cart session data
		 * @author YIThemes
		 * @since 1.0.1
		 *
		 * @param $cart_item
		 * @param $values
		 *
		 * @return mixed
		 */
		public function get_cart_donation_item_from_session( $cart_item, $values ) {

			$donation_id = $this->get_donation_id();
			if ( $cart_item['product_id'] == $donation_id && isset( $values['ywcds_amount'] ) && ! empty( $values['ywcds_amount'] ) ) {

				$cart_item['ywcds_amount']       = apply_filters( 'ywcds_amount_from_session', $values['ywcds_amount'], $cart_item, $values );
				$cart_item['ywcds_product_id']   = isset( $values['ywcds_product_id'] ) ? $values['ywcds_product_id'] : - 1;
				$cart_item['ywcds_variation_id'] = isset( $values['ywcds_variation_id'] ) ? $values['ywcds_variation_id'] : '';
				$cart_item['ywcds_quantity']     = isset( $values['ywcds_quantity'] ) ? $values['ywcds_quantity'] : '';
				$cart_item['ywcds_data_added']   = isset( $values['ywcds_data_added'] ) ? $values['ywcds_data_added'] : '';

				$cart_item = $this->add_cart_donation_item( $cart_item );
			}


			return $cart_item;
		}


		/** change the price of the item in the cart
		 * @author YIThemes
		 * @since 1.0.1
		 *
		 * @param $cart_item
		 *
		 * @return mixed
		 */
		public function add_cart_donation_item( $cart_item ) {

			$donation_id = $this->get_donation_id();

			if ( $cart_item['product_id'] == $donation_id ) {

				$product = $cart_item['data'];

				$cart_item = apply_filters( 'ywcds_add_cart_item_data', $cart_item );

				$product->set_price( $cart_item['ywcds_amount'] );

			}

			return $cart_item;
		}

		/** add donation in order item meta
		 * @author YIThemes
		 * @since 1.0.1
		 *
		 * @param $item_id
		 * @param $values
		 * @param $cart_item_key
		 */
		public function add_order_item_meta( $item_id, $values, $cart_item_key ) {

			$cart_item = WC()->cart->get_cart_item( $cart_item_key );

			if ( isset( $cart_item['ywcds_amount'] ) ) {

				$product_ass_id = ! empty( $cart_item['ywcds_variation_id'] ) ? $cart_item['ywcds_variation_id'] : $cart_item['ywcds_product_id'];

				if ( $product_ass_id != - 1 ) {
					$product = wc_get_product( $product_ass_id );

					$donation_name = ywcds_get_product_donation_title( $product );

					wc_add_order_item_meta( $item_id, '_ywcds_donation_name', $donation_name );
				}
			}
		}

		/**
		 * add donation in order item meta
		 * @author YITHEMES
		 *
		 * @param WC_Order_Item_Product $item
		 * @param string $cart_item_key
		 * @param $values
		 */
		public function add_new_order_item( $item, $cart_item_key, $values ) {

			$cart_item = WC()->cart->get_cart_item( $cart_item_key );


			if ( isset( $cart_item['ywcds_amount'] ) ) {

				$product_ass_id = ! empty( $cart_item['ywcds_variation_id'] ) ? $cart_item['ywcds_variation_id'] : $cart_item['ywcds_product_id'];

				if ( $product_ass_id != - 1 ) {
					$product = wc_get_product( $product_ass_id );

					$donation_name = ywcds_get_product_donation_title( $product );
					$item->add_meta_data( '_ywcds_donation_name', $donation_name );


				}

			}
		}

		/**print right donation name in order
		 * @author YIThemes
		 * @since  1.0.1
		 *
		 * @param $name
		 * @param WC_Order_Item_Product $item
		 *
		 * @return mixed
		 */
		public function print_donation_in_order( $name, $item ) {

			$is_wc_version_3_0 = version_compare( WC()->version, '3.0.0', '>=' );

			if ( $is_wc_version_3_0 ) {
				$value = $item->get_meta( '_ywcds_donation_name' );
			} else {

				$value = isset( $item['item_meta']['_ywcds_donation_name'] ) ? $item['item_meta']['_ywcds_donation_name'][0] : '';
			}

			if ( isset( $value ) && $value != '' ) {

				return $value;
			}

			return $name;
		}

		/**print right donation in cart item
		 * @author YIThemes
		 * @since 1.0.1
		 *
		 * @param $product_title
		 * @param $cart_item
		 * @param $cart_item_key
		 *
		 * @return string
		 */
		public function print_cart_item_donation( $product_title, $cart_item, $cart_item_key ) {

			$product_id = $cart_item['product_id'];

			$donation_id = $this->get_donation_id();

			if ( $product_id == $donation_id ) {

				$product_ass_id = ! empty( $cart_item['ywcds_variation_id'] ) ? $cart_item['ywcds_variation_id'] : $cart_item['ywcds_product_id'];

				if ( $product_ass_id != - 1 ) {
					$product = wc_get_product( $product_ass_id );
					if ( $product ) {
						$product_title = ywcds_get_product_donation_title( $product );
					}
				}

			}

			return $product_title;
		}

		/**add donation in ajax
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function ywcds_add_donation_ajax() {
			if ( isset( $_REQUEST['add_donation_to_cart'] ) ) {

				$product_id = $_REQUEST['add_donation_to_cart'];
				$amount     = $_REQUEST['ywcds_amount'];
				$result     = $this->add( $product_id, '', 1, $amount );
				$message    = '';

				switch ( $result ) {

					case 'no_number':
						$message = $this->_messages['no_number'];
						break;

					case 'empty':
						$message = $this->_messages['empty'];
						break;

					case 'already'  :
						$message = $this->_messages['already'];
						break;

					case 'negative':
						$message = $this->_messages['negative'];
						break;
					default :
						$message = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_page_permalink( 'cart' ), __( 'View Cart', 'woocommerce' ), $this->_messages['success'] );
						break;
				}

				if ( $result == 'true' ) {
					WC_AJAX::get_refreshed_fragments();
				} else {
					wp_send_json(
						array(
							'result'  => $result,
							'message' => $message
						)
					);
				}
			}
		}

		/** set donation product as purchasable
		 * @author YIThemes
		 * @since 1.0.0
		 *
		 * @param $purchasable
		 * @param WC_Product $product
		 *
		 * @return bool
		 */
		public function set_donation_purchasable( $purchasable, $product ) {
			$donation_id = $this->get_donation_id();
			$product_id  =  $product->get_id();

			return $product_id == $donation_id ? true : $purchasable;
		}

		/**register widget
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function register_donations_widget() {

			register_widget( 'YITH_Donations_Form_Widget' );
		}

		/**get label by key
		 * @author YIThemes
		 * @since 1.0.0
		 *
		 * @param $key
		 *
		 * @return mixed
		 */
		public function get_message( $key ) {

			return $this->_messages[ $key ];
		}


	}
}
