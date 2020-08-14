<?php
/**
 * User Account Class
 *
 * @author YITH
 * @package YITH WooCommerce One-Click Checkout Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WOCC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WOCC_User_Account' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WOCC_User_Account {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WOCC_User_Account
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WOCC_VERSION;

		/**
		 * User meta name
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_options_user_meta = 'yith-wocc-user-options';

		/**
		 * Action for add address form in ajax
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_action_ajax_address = 'yith_wocc_add_address_ajax';

		/**
		 * Action for edit address
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_action_edit_address = 'yith_wocc_edit_address';

		/**
		 * Action for reomve address
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_action_remove_address = 'yith_wocc_remove_address';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WOCC_User_Account
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->_user_id = get_current_user_id();

			// enqueue scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );

            add_filter( 'woocommerce_account_menu_items', array( $this, 'add_menu_item' ), 10, 1 );
            // one click
            add_action( 'woocommerce_account_one-click_endpoint', array( $this, 'load_account_template' ) );
            // custom address
            add_action( 'woocommerce_account_custom-address_endpoint', array( $this, 'load_custom_address_template' ) );
            // title
            add_filter( 'woocommerce_endpoint_one-click_title', array( $this, 'one_click_endpoint_title' ), 10, 2 );
            add_filter( 'woocommerce_endpoint_custom-address_title', array( $this, 'one_click_endpoint_title' ), 10, 2 );
            // item class
            add_filter( 'woocommerce_account_menu_item_classes', array( $this, 'add_item_menu_class_for_custom_address' ), 10, 2 );

			add_shortcode( 'yith_wocc_myaccount', array( $this, 'my_account_shortcode' ) );

			// save user preference
			add_action( 'init', array( $this, 'init_user_options' ) );
			add_action( 'init', array( $this, 'save_user_options' ) );
			add_action( 'init', array( $this, 'remove_address' ) );
			add_action( 'template_redirect', array( $this, 'save_user_address' ), 5 );

			// add address in ajax directly from select shipping address
			add_action( 'wp_ajax_' . $this->_action_ajax_address, array( $this, 'add_address_ajax' ) );
			add_action( 'wp_ajax_nopriv' . $this->_action_ajax_address, array( $this, 'add_address_ajax' ) );
		}

		/**
		 * Enqueue scripts
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_scripts() {

			global $wp;

			wp_localize_script( 'yith-wocc-script', 'yith_wocc_address', array(
				'action_add' => $this->_action_ajax_address,
				'action_nonce' => wp_create_nonce( $this->_action_ajax_address )
            ));

			if ( is_page( wc_get_page_id( 'myaccount' ) ) && isset( $wp->query_vars['custom-address'] ) ) {
				wp_enqueue_script( 'wc-country-select' );
				wp_enqueue_script( 'wc-address-i18n' );
			}
		}

		/**
		 * My account shortcode section
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function my_account_shortcode() {

			$my_account_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
			$nonce_remove = wp_create_nonce( $this->_action_remove_address );
			$edit_url = wc_get_endpoint_url( 'custom-address', '', $my_account_url );

			$args = apply_filters( 'yith_wocc_my_account_section_args', array(
				'value_options' 	=> get_user_meta( $this->_user_id, $this->_options_user_meta, true ),
				'custom_address' 	=> YITH_WOCC_Frontend_Premium()->get_formatted_custom_address( false ),
                'my_account_url' 	=> $my_account_url,
				'edit_url' 			=> apply_filters( 'yith_wocc_get_edit_address_url', $edit_url, $my_account_url ),
				'remove_url' 		=> add_query_arg( '_nonce', $nonce_remove ),
				'stripe_active'		=> yith_wocc_is_stripe_enabled(),
				'enabled_shipping'	=> yith_wocc_enabled_shipping(),
			));

			ob_start();
			wc_get_template( 'yith-wocc-account-section.php', $args, '', YITH_WOCC_DIR . 'templates/' );
			return ob_get_clean();
		}

		/**
		 * My account option section
		 *
		 * @since 1.0.2
		 * @author Francesco Licandro
         * @deprecated Use instead load_account_template
		 */
		public function my_account_options(){
			$this->load_account_template();
		}

		/**
		 * Init user options
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function init_user_options() {

			if( get_user_meta( $this->_user_id, $this->_options_user_meta, true ) ) {
				return;
			}

			$value = array(
				'activate'	=> '1',
				'use-stripe' => '1'
			);

			add_user_meta( $this->_user_id, $this->_options_user_meta, $value, true );
		}

		/**
		 * Save my account options
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function save_user_options() {

			if( ! isset( $_POST['yith-wocc-save-option'] ) )
				return;

			$activate  = isset( $_POST['yith-wocc-activate'] ) ? '1' : '0';
			$usestripe = isset( $_POST['yith-wocc-use-stripe'] ) ? '1' : '0';

			$new_value = array(
				'activate'	=> $activate,
				'use-stripe' => $usestripe
			);

			update_user_meta( $this->_user_id, $this->_options_user_meta, $new_value );
		}

		/**
		 * Return template for edit/add new shipping address
		 *
		 * @since 1.0.4
		 * @author Francesco Licandro
		 * @return array()
		 */
		public function return_edit_address_template() {

			// set action
			$action = 'add';
			$address = false;

			// get saved user address
			$custom_address = yith_wocc_get_custom_address( $this->_user_id );

			// load address
			if( $custom_address && isset( $_REQUEST['edit'] ) && array_key_exists( esc_attr( $_REQUEST['edit'] ), $custom_address ) ) {

				// set action
				$action = 'edit';
				// set address
				$saved = $custom_address[ $_REQUEST['edit'] ];

				$address = WC()->countries->get_address_fields( $saved['country'], 'shipping_' );

				foreach( $address as $key => $field ) {
					// remove shipping_ from $key to get correct custom value
					$my_key = str_replace( 'shipping_', '', $key );

					$address[ $key ]['value'] = $saved[ $my_key ];
				}
			}

			$return = array(
				'post_content' => $this->get_address_form_html( $address, $action ) // leave it as array to retro compatibility
			);

			return apply_filters( 'yith_wocc_return_edit_address_template', $return );

		}

        /**
         * Get add/edit form address html
         *
         * @since 1.0.0
         * @param boolean|array $address
         * @param string $action
         * @return mixed
         * @author Francesco Licandro
         */
        public function get_address_form_html( $address = false, $action = 'add' ) {

            if( ! $address && WC()->countries instanceof WC_Countries) {

                $address = WC()->countries->get_address_fields( '', 'shipping_' );

                foreach( $address as $key => $field ) {

                    switch( $key ) {
                        case 'shipping_country' :
                            $value = WC()->countries->get_base_country();
                            break;
                        case 'shipping_state' :
                            $value = WC()->countries->get_base_state();
                            break;
                        default :
                            $value = '';
                            break;
                    }

                    $address[ $key ]['value'] = $value;
                }
            }

            // set template args
            $args = array(
                'address'       => $address,
                'action_form'   => $this->_action_edit_address,
                'action'        => $action
            );

            ob_start();

                wc_get_template( 'yith-wocc-form-address.php', $args, '', YITH_WOCC_DIR . 'templates/' );

            return ob_get_clean();

        }

		/**
		 * Save custom user address
		 *
		 * @since 1.0.0
         * @param boolean $ajax
         * @return string | boolean
		 * @author Francesco Licandro
		 */
		public function save_user_address( $ajax = false ) {

            if ( empty( $_REQUEST[ '_action_form' ] ) || $this->_action_edit_address !== $_REQUEST[ '_action_form' ]
			     || empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], $this->_action_edit_address ) ) {
                return false;
            }

            $address = WC()->countries->get_address_fields( esc_attr( $_REQUEST[ 'shipping_country' ] ), 'shipping_' );
			$to_save = array();

			foreach ( $address as $key => $field ) {

				if ( ! isset( $field['type'] ) ) {
					$field['type'] = 'text';
				}

				// Get Value
				switch ( $field['type'] ) {
					case "checkbox" :
                        $_REQUEST[ $key ] = isset( $_REQUEST[ $key ] ) ? 1 : 0;
						break;
					default :
                        $_REQUEST[ $key ] = isset( $_REQUEST[ $key ] ) ? wc_clean( $_REQUEST[ $key ] ) : '';
						break;
				}

				// Hook to allow modification of value
                $_REQUEST[ $key ] = apply_filters( 'woocommerce_process_myaccount_field_' . $key, $_REQUEST[ $key ] );

				// Validation: Required fields
				if ( ! empty( $field['required'] ) && empty( $_REQUEST[ $key ] ) ) {
					wc_add_notice( $field['label'] . ' ' . __( 'is a required field.', 'yith-woocommerce-one-click-checkout' ), 'error' );
				}

				if ( ! empty( $_POST[ $key ] ) ) {

					// Validation rules
					if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
						foreach ( $field['validate'] as $rule ) {
							switch ( $rule ) {
								case 'postcode' :
                                    $_REQUEST[ $key ] = strtoupper( str_replace( ' ', '', $_REQUEST[ $key ] ) );

									if ( ! WC_Validation::is_postcode( $_REQUEST[ $key ], $_REQUEST[ 'shipping_country' ] ) ) {
										wc_add_notice( __( 'Please enter a valid postcode/ZIP.', 'yith-woocommerce-one-click-checkout' ), 'error' );
									} else {
                                        $_REQUEST[ $key ] = wc_format_postcode( $_REQUEST[ $key ], $_REQUEST[ 'shipping_country' ] );
									}
									break;
							}
						}
					}
				}

				// populate save array address
				$my_key = str_replace( 'shipping_', '', $key );
				$to_save[ $my_key ] = $_REQUEST[ $key ];

			}

			if ( wc_notice_count( 'error' ) == 0 ) {

				$saved_address = yith_wocc_get_custom_address( $this->_user_id );

				$key = null;

				if( is_array( $saved_address ) ) {
					end($saved_address);         // move the internal pointer to the end of the array
					$key = key($saved_address);
				}else{
                    $saved_address = array();
                }
				$key = ! is_null( $key ) ? intval( str_replace( 'custom_', '', $key ) ) : 0;

				// check if is edit
				if( isset( $_REQUEST['address_edit'] ) ){
					$edited = $_REQUEST['address_edit'];
					$saved_address[ $edited ] = $to_save;
				}
				else {
					// add new address
					$saved_address[ 'custom_' . ++$key ] = $to_save;
				}


				if( yith_wocc_save_custom_address( $this->_user_id, $saved_address ) && $ajax ) {
					// return array key
                    return array_search( $to_save, $saved_address );
                }


                // else add message and redirect
                $message = isset( $_REQUEST['address_edit'] ) ? __( 'Address changed successfully.', 'yith-woocommerce-one-click-checkout' ) : __( 'Address added successfully.', 'yith-woocommerce-one-click-checkout' );

                wc_add_notice( $message, 'success' );

				$redirect_url = wc_get_endpoint_url( 'one-click' );
				wp_safe_redirect( $redirect_url );
				exit;
			}

            return false;
		}

		/**
		 * Remove custom user address
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function remove_address() {

			if( ! isset( $_GET[ 'remove' ] ) || empty( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], $this->_action_remove_address ) ) {
				return;
			}

			$saved_address = yith_wocc_get_custom_address( $this->_user_id );
			$remove = $_GET['remove'];

			if( ! $saved_address || ! array_key_exists( $remove, $saved_address ) ) {
				return;
			}

			unset( $saved_address[$remove] );

			yith_wocc_save_custom_address( $this->_user_id, $saved_address );

			wc_add_notice( __( 'Custom address removed successfully', 'yith-woocommerce-one-click-checkout' ) );

			$url = remove_query_arg( array( '_nonce', 'remove' ) );
			wp_safe_redirect( $url );
			exit;
		}

		/**
		 * Add address form in ajax
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function add_address_ajax() {

            if( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] != $this->_action_ajax_address
			    || ! isset( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], $this->_action_ajax_address ) ) {
				die();
			}

            $result = $this->save_user_address( true );
			$html = false;

			// get result html
			ob_start();

			// if same errors occurred get notice
			if( ! $result ) {
				wc_print_notices();
			}
			// else get select
			else {
				echo  YITH_WOCC_Frontend_Premium()->shipping_address_select_html( $result ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			$html = ob_get_clean();

			wp_send_json( array(
                'error' => ! $result,
				'key'   => $result,
                'html'  => $html
            ) );

		}

		/**
		 * Add menu item for standard wc account navigation ( for version >= 2.6 )
		 *
		 * @since 1.0.4
		 * @author Francesco Licandro
		 * @param array $items
		 * @return array
		 */
		public function add_menu_item( $items ) {

			$new_items = array();

			if( ! is_array( $items ) ){
				return $items;
			}

			$items_keys = array_keys( $items );
			$last_key = end( $items_keys );

			foreach ( $items as $key => $value ) {
				if( $key == $last_key ) {
					$new_items['one-click'] = _x( 'One Click Checkout', 'Plugin options - user side', 'yith-woocommerce-one-click-checkout' );
				}
				$new_items[$key] = $value;
			}

			return $new_items;
		}

		/**
		 * Load my account section
		 *
		 * @since 1.0.4
		 * @author Francesco Licandro
		 */
		public function load_account_template(){
            echo do_shortcode( '[yith_wocc_myaccount]' );
		}

        /**
         * Load custom address section my account
         *
         * @since 1.3.4
         * @author Francesco Licandro
         * @return void
         */
        public function load_custom_address_template(){
            $template = $this->return_edit_address_template();
            if( isset( $template['post_content'] ) ) {
                echo $template['post_content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }

		/**
		 * Wrap myaccount content for WC 2.6
		 *
		 * @since 1.0.4
		 * @author Francesco Licandro
		 * @param mixed $content
		 * @return mixed
         * @deprecated This method is deprecated. Here for compatibility.
		 */
		public function wrap_myaccount_content( $content ){
			return $content;
		}

		/**
		 * Add class is-active for custom address endpoint
		 *
		 * @since 1.0.4
		 * @author Francesco Licandro
		 * @param array $classes
		 * @param string $endpoint
		 * @return array
		 */
		public function add_item_menu_class_for_custom_address( $classes, $endpoint ) {

			global $wp;

			if( $endpoint == 'one-click' && ( in_array( 'custom-address', $wp->query_vars ) || isset( $wp->query_vars['custom-address'] ) ) ) {
				$classes[] = 'is-active';
			}

			return $classes;
		}

        /**
         * One Click endpoint page title
         *
         * @since 1.3.4
         * @author Francesco Licandro
         * @param string $title
         * @param string $endpoint
         * @return string
         */
        public function one_click_endpoint_title( $title, $endpoint ) {
            return __( 'One-Click Checkout options', 'yith-woocommerce-one-click-checkout' );
        }
	}
}

/**
 * Unique access to instance of YITH_WOCC_User_Account class
 *
 * @return \YITH_WOCC_User_Account
 * @since 1.0.0
 */
function YITH_WOCC_User_Account(){
	return YITH_WOCC_User_Account::get_instance();
}