<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMC_Premium' ) ) {
	/**
	 * WooCommerce Mailchimp
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMC_Premium extends YITH_WCMC {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCMC_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCMC_Premium
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
		 * @return \YITH_WCMC_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_shortcode' ) );
			add_action( 'init', array( $this, 'post_form_subscribe' ) );
			add_action( 'init', array( $this, 'register_campaign_cookie' ) );
			add_action( 'init', array( $this, 'register_gutenberg_block' ) );
			add_action( 'init', array( $this, 'init_elementor_widgets' ) );

			add_action( 'woocommerce_register_form', array( $this, 'add_checkbox_to_registration_form' ) );
			add_action( 'woocommerce_created_customer', array( $this, 'subscribe_from_registration_form' ), 10, 2 );

			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'register_ecommerce360_campaign_data' ), 10, 1 );

			add_action( 'init', array( $this, 'register_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// handles ajax requests.
			add_action( 'wp_ajax_yith_wcmc_subscribe', array( $this, 'ajax_form_subscribe' ) );
			add_action( 'wp_ajax_nopriv_yith_wcmc_subscribe', array( $this, 'ajax_form_subscribe' ) );

			// inits widget.
			add_action( 'widgets_init', array( $this, 'register_widget' ) );

			parent::__construct();
		}

		/**
		 * Install db tables when updating to new version of db structure
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function install_db() {
			global $wpdb;

			// adds tables name in global $wpdb
			$wpdb->yith_wcmc_register = $wpdb->prefix . 'yith_wcmc_register';

			$current_db_version = get_option( 'yith_wcmc_db_version', '' );
			if ( version_compare( $current_db_version, YITH_WCMC_DB_VERSION, '>=' ) ) {
				return;
			}

			// assure dbDelta function is defined
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			// retrieve table charset
			$charset_collate = $wpdb->get_charset_collate();

			// adds register table
			$sql = "CREATE TABLE $wpdb->yith_wcmc_register (
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    item_type varchar(255) NOT NULL,
                    item_id bigint(20) NOT NULL,
                    mc_id varchar(255) NOT NULL,
                    last_updated datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                    PRIMARY KEY ID (ID)
				) $charset_collate;";

			// update db to latest version
			dbDelta( $sql );

			// perform related operation for specific db update
			add_action( 'yith_wcmc_update_db_2.0.0', array( $this, 'install_update_200' ) );

			do_action( 'yith_wcmc_update_db_' . YITH_WCMC_DB_VERSION );
			update_option( 'yith_wcmc_db_version', YITH_WCMC_DB_VERSION );
		}

		/* === ENQUEUE SCRIPTS === */

		/**
		 * Register scripts
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_scripts() {
			$path   = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '/unminified' : '';
			$prefix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';

			wp_register_script( 'yith-wcmc', YITH_WCMC_URL . 'assets/js' . $path . '/yith-wcmc' . $prefix . '.js', array(
				'jquery',
				'jquery-blockui'
			), YITH_WCMC_VERSION, true );

			wp_localize_script( 'yith-wcmc', 'yith_wcmc', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'actions'  => array(
					'yith_wcmc_subscribe_action' => 'yith_wcmc_subscribe'
				)
			) );
		}

		/**
		 * Enqueue scripts
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'yith-wcmc' );
		}

		/* === HANDLE ECOMMERCE 360 INTEGRATION === */

		/**
		 * Register cookie for ecommerce 360 campagins
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_campaign_cookie() {
			$ecommerce360_enable          = 'yes' == get_option( 'yith_wcmc_ecommerce360_enable' );
			$ecommerce360_cookie_lifetime = get_option( 'yith_wcmc_ecommerce360_cookie_lifetime' );

			if ( isset( $_GET['mc_cid'] ) || isset( $_GET['mc_eid'] ) || isset( $_GET['mc_tc'] ) && $ecommerce360_enable ) {
				$data_to_set = array();

				if ( ! empty( $_GET['mc_cid'] ) ) {
					$data_to_set['cid'] = $_GET['mc_cid'];
				}

				if ( ! empty( $_GET['mc_eid'] ) ) {
					$data_to_set['eid'] = $_GET['mc_eid'];
				}

				if ( ! empty( $_GET['mc_tc'] ) ) {
					$data_to_set['tc'] = $_GET['mc_tc'];
				}

				$parsed_data = urlencode( serialize( $data_to_set ) );

				wc_setcookie( 'yith_wcmc_ecommerce_360', $parsed_data, time() + $ecommerce360_cookie_lifetime );
			}
		}

		/* === HANDLE STORE INTEGRATION === */

		/**
		 * Call subscribe API handle, to register user to a specific list
		 *
		 * @param $order_id int Order id
		 *
		 * @return bool status of the operation
		 */
		public function order_subscribe( $order_id, $args = array() ) {
			$order            = wc_get_order( $order_id );
			$integration_mode = get_option( 'yith_wcmc_mailchimp_integration_mode', 'simple' );
			$args             = array();

			if ( 'simple' == $integration_mode ) {
				//manage groups
				$selected_groups = get_option( 'yith_wcmc_mailchimp_groups', array() );
				$group_structure = $this->_create_group_structure( $selected_groups );

				if ( ! empty( $group_structure ) ) {
					$args['interests'] = $group_structure;

					$merge_fields        = new stdClass();
					$merge_fields->FNAME = yit_get_prop( $order, 'billing_first_name', true );
					$merge_fields->LNAME = yit_get_prop( $order, 'billing_last_name', true );

					$args['merge_fields'] = $merge_fields;
				}

				$res = parent::order_subscribe( $order_id, $args );
			} else {
				$res              = true;
				$advanced_options = get_option( 'yith_wcmc_advanced_integration', array() );

				if ( ! empty( $advanced_options ) ) {
					foreach ( $advanced_options as $option ) {

						// checks conditions
						$selected_conditions = isset( $option['conditions'] ) ? $option['conditions'] : array();
						if ( ! empty( $selected_conditions ) ) {
							if ( ! $this->_check_conditions( $selected_conditions, $order_id ) ) {
								continue;
							}
						}

						// set list id to current section
						$args['id'] = $option['list'];

						// manage groups
						$selected_groups = isset( $option['groups'] ) ? $option['groups'] : array();
						$group_structure = $this->_create_group_structure( $selected_groups );

						if ( ! empty( $group_structure ) ) {
							$args['interests'] = $group_structure;
						}

						// manage fields
						$selected_fields = isset( $option['fields'] ) ? $option['fields'] : array();
						$field_structure = $this->_create_field_structure( $selected_fields, $order_id );

						if ( ! empty( $field_structure ) ) {
							$args['merge_fields'] = $field_structure;
						}

						$partial = parent::order_subscribe( $order_id, $args );
						$res     = ( ! $partial ) ? false : $res;
					}
				}
			}

			return $res;
		}

		/**
		 * Register MailChimp eCommerce360 campaign data (if ecommerce 360 integration is enabled)
		 *
		 * @param $order_id int WooCommerce order id
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_ecommerce360_campaign_data( $order_id ) {
			$order = wc_get_order( $order_id );

			// handle campaigns (if ecommerce 360 integration is enabled)
			$ecommerce360_enable = 'yes' == get_option( 'yith_wcmc_ecommerce360_enable' );

			if ( $ecommerce360_enable ) {
				$cookie_campaign = isset( $_COOKIE['yith_wcmc_ecommerce_360'] ) ? maybe_unserialize( urldecode( $_COOKIE['yith_wcmc_ecommerce_360'] ) ) : false;
				yit_save_prop( $order, '_yith_wcmc_ecommerce_360_data', $cookie_campaign );

				// delete cookie
				wc_setcookie( 'yith_wcmc_ecommerce_360', '', time() - 3600 );
			}
		}

		/**
		 * Handle campaigns
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ecommerce360_handling( $order_id ) {
			$order = wc_get_order( $order_id );

			$campaign_data      = get_post_meta( $order_id, '_yith_wcmc_ecommerce_360_data', true );
			$campaign_processed = get_post_meta( $order_id, '_yith_wcmc_ecommerce_360_processed', true );

			$list_id  = apply_filters( 'yith_wcmc_store_list', get_option( 'yith_wcmc_ecommerce360_list' ) );
			$store_id = get_option( "yith_wcmc_{$list_id}_store" );

			if ( $campaign_processed != 'yes' && isset( $campaign_data['cid'] ) && isset( $campaign_data['eid'] ) ) {

				// create a store if not yet created
				if ( ! $store_id ) {
					$store_id = $this->_create_store( $list_id );
					update_option( "yith_wcmc_{$list_id}_store", $store_id );
				}

				$request_arg = array(
					'id'               => $order_id,
					'customer'         => array(),
					'campaign_id'      => isset( $campaign_data['cid'] ) ? $campaign_data['cid'] : false,
					'currency_code'    => get_woocommerce_currency(),
					'order_total'      => $order->get_total(),
					'tax_total'        => $order->get_total_tax(),
					'shipping_total'   => $order->get_shipping_total(),
					'tracking_code'    => isset( $campaign_data['tc'] ) ? $campaign_data['tc'] : '',
					'shipping_address' => array(
						'name'          => $order->shipping_first_name . ' ' . $order->shipping_last_name,
						'address1'      => $order->shipping_address_1,
						'address2'      => $order->shipping_address_2,
						'city'          => $order->shipping_city,
						'province_code' => $order->shipping_state,
						'postal_code'   => $order->shipping_postcode,
						'country'       => $order->shipping_country
					),
					'billing_address'  => array(
						'name'          => $order->billing_first_name . ' ' . $order->billing_last_name,
						'address1'      => $order->billing_address_1,
						'address2'      => $order->billing_address_2,
						'city'          => $order->billing_city,
						'province_code' => $order->billing_state,
						'postal_code'   => $order->billing_postcode,
						'country'       => $order->billing_country
					)
				);

				$items_arg  = array();
				$line_items = $order->get_items( 'line_item' );

				if ( ! empty( $line_items ) ) {
					foreach ( $line_items as $item_id => $item ) {
						if ( is_object( $item ) ) {
							/**
							 * @var $item \WC_Order_Item_Product
							 */
							$product_id           = $item->get_product_id();
							$product_variation_id = $item->get_variation_id();
							$product              = $item->get_product();
						} else {
							$product_id           = $item['product_id'];
							$product_variation_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : '';
							$product              = wc_get_product( $product_variation_id ? $product_variation_id : $product_id );
						}

						$items_arg[] = array(
							'id'                 => $item_id,
							'product_id'         => $product_id,
							'product_variant_id' => $product_variation_id,
							'quantity'           => $item['qty'],
							'price'              => $order->get_item_total( $item )
						);
					}
				}

				$request_arg['lines'] = $items_arg;
				$this->do_request( 'post', "ecommerce/stores/{$store_id}/orders", $request_arg );

				yit_save_prop( $order, '_yith_wcmc_ecommerce_360_processed', 'yes' );
			}
		}

		/**
		 * Create structure to register mail to interest groups
		 *
		 * @param $selected_groups array Array of selected groups
		 *
		 * @return stdClass A valid object to use in subscription request
		 * @since 1.0.0
		 */
		protected function _create_group_structure( $selected_groups ) {
			$group_to_register = new stdClass();

			if ( empty( $selected_groups ) ) {
				return $group_to_register;
			}

			foreach ( $selected_groups as $group ) {
				if ( strpos( $group, '-' ) === false ) {
					continue;
				}

				list( $group_id, $interest_id ) = explode( '-', $group );

				$group_to_register->$interest_id = true;
			}

			return $group_to_register;
		}

		/**
		 * Create structure to register fields to a specific user
		 *
		 * @param $selected_fields array Array of selected fields to register
		 * @param $order_id        int Order id
		 *
		 * @return stdClass A valid object to use in subscription request
		 * @since 1.0.0
		 */
		protected function _create_field_structure( $selected_fields, $order_id ) {
			$field_structure = new stdClass();

			if ( empty( $selected_fields ) ) {
				return $field_structure;
			}

			$order = wc_get_order( $order_id );

			if ( empty( $order ) ) {
				return $field_structure;
			}

			foreach ( $selected_fields as $field ) {
				if ( in_array( $field['checkout'], array( 'billing_address', 'shipping_address' ) ) ) {
					$type   = str_replace( '_address', '', $field['checkout'] );
					$method = "get_{$type}_";

					$field_value = array(
						'addr1' => $order->{$method . 'address_1'}(),
						'addr2' => $order->{$method . 'address_2'}(),
						'city'  => $order->{$method . 'city'}(),
						'state' => $order->{$method . 'country'}(),
						'zip'   => $order->{$method . 'postcode'}(),
					);
				} else {
					$field_value = yit_get_prop( $order, $field['checkout'], true );
				}

				// handling for Checkout Manager custom fields
				if ( function_exists( 'ywccp_is_custom_field' ) && ywccp_is_custom_field( $field['checkout'] ) || apply_filters('yith_wcmc_handle_checkout_manager_custom_fields', false ) ) {
					$field_value = yit_get_prop( $order, '_' . $field['checkout'], true );
				}

				$field_structure->{$field['merge_var']} = $field_value;

				if ( ! is_null( WC()->customer ) ) {
					$checkout_fields = WC()->checkout()->get_checkout_fields();

					foreach ( $checkout_fields as $group => $fields ) {
						if ( isset( $fields[ $field['checkout'] ] ) ) {
							$label = $fields[ $field['checkout'] ]['label'];
							break;
						}
					}
				}

				if ( empty( $label ) ) {
					$label = $field['checkout'];
				}

				$this->_register_customer_personal_data( $order_id, $field['checkout'], $label, $field_value );
			}

			return $field_structure;
		}

		/**
		 * Check if selected conditions are matched
		 *
		 * @param $selected_conditions array Array of selected conditions to match
		 * @param $order_id            int Order id
		 *
		 * @return boolean True, if all conditions are matched; false otherwise
		 * @since 1.0.0
		 */
		protected function _check_conditions( $selected_conditions, $order_id ) {
			$order            = wc_get_order( $order_id );
			$condition_result = true;

			if ( empty( $selected_conditions ) ) {
				return true;
			}

			foreach ( $selected_conditions as $condition ) {
				$condition_type = $condition['condition'];

				switch ( $condition_type ) {
					case 'product_in_cart':

						$set_operator      = $condition['op_set'];
						$selected_products = is_array( $condition['products'] ) ? $condition['products'] : explode( ',', $condition['products'] );
						$items             = $order->get_items( 'line_item' );
						$products_in_cart  = array();

						if ( ! empty( $items ) ) {
							foreach ( $items as $item ) {
								if ( is_object( $item ) ) {
									/**
									 * @var $item \WC_Order_Item_Product
									 */
									$products_in_cart[] = $item->get_product_id();
									$products_in_cart[] = $item->get_variation_id();
								} else {
									if ( ! empty( $item['product_id'] ) && ! in_array( $item['product_id'], $products_in_cart ) ) {
										$products_in_cart[] = $item['product_id'];
									}

									if ( ! empty( $item['variation_id'] ) && ! in_array( $item['variation_id'], $products_in_cart ) ) {
										$products_in_cart[] = $item['variation_id'];
									}
								}
							}
						}

						switch ( $set_operator ) {
							case 'contains_one':

								if ( ! empty( $selected_products ) && ! empty( $products_in_cart ) ) {
									$found = false;
									foreach ( (array) $selected_products as $product ) {
										if ( in_array( $product, $products_in_cart ) ) {
											$found = true;
											break;
										}
									}

									if ( ! $found ) {
										$condition_result = false;
									}
								} elseif ( ! empty( $selected_products ) ) {
									$condition_result = false;
								}

								break;
							case 'contains_all':

								if ( ! empty( $selected_products ) && ! empty( $products_in_cart ) ) {
									foreach ( (array) $selected_products as $product ) {
										if ( ! in_array( $product, $products_in_cart ) ) {
											$condition_result = false;
											break;
										}
									}
								} elseif ( ! empty( $selected_products ) ) {
									$condition_result = false;
								}

								break;
							case 'not_contain':

								if ( ! empty( $selected_products ) && ! empty( $products_in_cart ) ) {
									foreach ( (array) $selected_products as $product ) {
										if ( in_array( $product, $products_in_cart ) ) {
											$condition_result = false;
											break;
										}
									}
								} elseif ( ! empty( $selected_products ) ) {
									$condition_result = false;
								}

								break;
						}

						break;
					case 'product_cat_in_cart':

						$set_operator  = $condition['op_set'];
						$selected_cats = $condition['prod_cats'];
						$items         = $order->get_items( 'line_item' );
						$cats_in_cart  = array();

						if ( ! empty( $items ) ) {
							foreach ( $items as $item ) {
								/**
								 * @var $item array|\WC_Order_Item_Product
								 */
								$product_id = is_object( $item ) ? $item->get_product_id() : $item['product_id'];
								$item_terms = get_the_terms( $product_id, 'product_cat' );

								if ( ! empty( $item_terms ) ) {
									foreach ( $item_terms as $term ) {
										if ( ! in_array( $term->term_id, $cats_in_cart ) ) {
											$cats_in_cart[] = $term->term_id;
										}
									}
								}
							}
						}

						switch ( $set_operator ) {
							case 'contains_one':

								if ( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ) {
									$found = false;
									foreach ( (array) $selected_cats as $cat ) {
										if ( in_array( $cat, $cats_in_cart ) ) {
											$found = true;
											break;
										}
									}

									if ( ! $found ) {
										$condition_result = false;
									}
								} elseif ( ! empty( $selected_cats ) ) {
									$condition_result = false;
								}

								break;
							case 'contains_all':

								if ( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ) {
									foreach ( (array) $selected_cats as $cat ) {
										if ( ! in_array( $cat, $cats_in_cart ) ) {
											$condition_result = false;
											break;
										}
									}
								} elseif ( ! empty( $selected_cats ) ) {
									$condition_result = false;
								}

								break;
							case 'not_contain':

								if ( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ) {
									foreach ( (array) $selected_cats as $cat ) {
										if ( in_array( $cat, $cats_in_cart ) ) {
											$condition_result = false;
											break;
										}
									}
								} elseif ( ! empty( $selected_cats ) ) {
									$condition_result = false;
								}

								break;
						}

						break;
					case 'order_total':

						$number_operator = $condition['op_number'];
						$threshold       = $condition['order_total'];
						$order_total     = $order->get_total();

						switch ( $number_operator ) {
							case 'less_than':
								if ( ! ( $order_total < $threshold ) ) {
									$condition_result = false;
								}
								break;
							case 'less_or_equal':
								if ( ! ( $order_total <= $threshold ) ) {
									$condition_result = false;
								}
								break;
							case 'equal':
								if ( ! ( $order_total == $threshold ) ) {
									$condition_result = false;
								}
								break;
							case 'greater_or_equal':
								if ( ! ( $order_total >= $threshold ) ) {
									$condition_result = false;
								}
								break;
							case 'greater_than':
								if ( ! ( $order_total > $threshold ) ) {
									$condition_result = false;
								}
								break;
						}

						break;
					case 'custom':

						$operator       = $condition['op_mixed'];
						$field_key      = $condition['custom_key'];
						$expected_value = $condition['custom_value'];

						// retrieve field value (first check in post meta)
						$field = yit_get_prop( $order, $field_key, true );

						// retrieve field value (then check in $_REQUEST superglobal)
						if ( empty( $field ) ) {
							$field = isset( $_REQUEST[ $field_key ] ) ? $_REQUEST[ $field_key ] : '';
						}

						// nothing found? condition failed
						if ( empty( $field ) ) {
							$condition_result = false;
							break;
						}

						switch ( $operator ) {
							case 'is':
								if ( ! ( strcmp( $field, $expected_value ) == 0 ) ) {
									$condition_result = false;
								}
								break;
							case 'not_is':
								if ( ! ( strcmp( $field, $expected_value ) != 0 ) ) {
									$condition_result = false;
								}
								break;
							case 'contains':
								if ( ! ( strpos( $field, $expected_value ) !== false ) ) {
									$condition_result = false;
								}
								break;
							case 'not_contains':
								if ( ! ( strpos( $field, $expected_value ) === false ) ) {
									$condition_result = false;
								}
								break;
							case 'less_than':
								if ( ! ( $field < $expected_value ) ) {
									$condition_result = false;
								}
								break;
							case 'less_or_equal':
								if ( ! ( $field <= $expected_value ) ) {
									$condition_result = false;
								}
								break;
							case 'equal':
								if ( ! ( $field == $expected_value ) ) {
									$condition_result = false;
								}
								break;
							case 'greater_or_equal':
								if ( ! ( $field >= $expected_value ) ) {
									$condition_result = false;
								}
								break;
							case 'greater_than':
								if ( ! ( $field > $expected_value ) ) {
									$condition_result = false;
								}
								break;
						}

						break;
				}

				if ( ! $condition_result ) {
					break;
				}
			}

			return $condition_result;
		}

		/* === HANDLE REGISTRATION FORM === */

		/**
		 * Print checkbox to subscribe to newsletter inside registration form
		 *
		 * @return void
		 */
		public function add_checkbox_to_registration_form() {
			$enabled       = 'yes' == get_option( 'yith_wcmc_register_subscription' );
			$show_checkbox = 'yes' == get_option( 'yith_wcmc_register_subscription_checkbox' );

			if ( $enabled && $show_checkbox ) {
				$checkbox_label   = get_option( 'yith_wcmc_register_subscription_checkbox_label' );
				$checkbox_checked = 'yes' == get_option( 'yith_wcmc_register_subscription_checkbox_default' );

				if ( function_exists( 'wc_privacy_policy_page_id' ) ) {
					$privacy_link   = sprintf( '<a href="%s">%s</a>', get_the_permalink( wc_privacy_policy_page_id() ), apply_filters( 'yith_wcmc_privacy_policy_page_label', __( 'Privacy Policy', 'yith-woocommerce-mailchimp' ) ) );
					$checkbox_label = str_replace( '%privacy_policy%', $privacy_link, $checkbox_label );
				}

				$template_name = 'mailchimp-subscription-checkbox.php';
				$located       = locate_template( array(
					trailingslashit( WC()->template_path() ) . 'wcmc/' . $template_name,
					trailingslashit( WC()->template_path() ) . $template_name,
					'wcmc/' . $template_name,
					$template_name
				) );

				if ( ! $located ) {
					$located = YITH_WCMC_DIR . 'templates/' . $template_name;
				}

				include_once( $located );
			}
		}

		/**
		 * Subscribe brand new customer to newsletter
		 *
		 * @param $customer_id   int Customer id
		 * @param $new_user_data mixed Array of data for the customer just subscribed
		 *
		 * @return void
		 */
		public function subscribe_from_registration_form( $customer_id, $new_user_data ) {
			$enabled         = 'yes' == get_option( 'yith_wcmc_register_subscription' );
			$show_checkbox   = 'yes' == get_option( 'yith_wcmc_register_subscription_checkbox' );
			$submitted_value = isset( $_POST['yith_wcmc_subscribe_me'] ) ? 'yes' : 'no';

			// return if admin don't want to subscribe users at this point
			if ( ! $enabled ) {
				return;
			}

			// return if subscription checkbox is printed, but not submitted
			if ( $show_checkbox && $submitted_value == 'no' ) {
				return;
			}

			$list_id         = get_option( 'yith_wcmc_register_mailchimp_list' );
			$email_type      = get_option( 'yith_wcmc_register_email_type' );
			$double_optin    = 'yes' == get_option( 'yith_wcmc_register_double_optin' );
			$update_existing = 'yes' == get_option( 'yith_wcmc_register_update_existing' );

			if ( empty( $list_id ) ) {
				return;
			}

			$user       = get_userdata( $customer_id );
			$email      = $new_user_data['user_email'];
			$first_name = $user->first_name;
			$last_name  = $user->last_name;
			$user_id    = $customer_id;

			$merge_fields        = new stdClass();
			$merge_fields->FNAME = $first_name;
			$merge_fields->LNAME = $last_name;

			$args = array(
				'email_address'   => $email,
				'merge_fields'    => apply_filters( 'yith_wcmc_subscribe_merge_vars', $merge_fields ),
				'email_type'      => $email_type,
				'status'          => $double_optin ? 'pending' : 'subscribed',
				'update_existing' => $update_existing
			);

			//manage groups
			$selected_groups = apply_filters('yith_wcmc_register_mailchimp_groups_registration_form', get_option( 'yith_wcmc_register_mailchimp_groups', array() ) ) ;
			$group_structure = $this->_create_group_structure( $selected_groups );

			if ( ! empty( $group_structure ) ) {
				$args['interests'] = $group_structure;
			}

			do_action( 'yith_wcmc_user_subscribing_after_registration', $customer_id );

			$res = $this->subscribe( $list_id, $email, $args );

			if ( isset( $res['status'] ) && ! $res['status'] ) {
				return;
			}

			// register subscribed list
			$this->_register_customer_subscribed_lists( isset( $args['id'] ) ? $args['id'] : $list_id, $email, $user_id );

			do_action( 'yith_wcmc_user_subscribed_after_registration', $customer_id );
		}

		/* === HANDLE SHORTCODE === */

		/**
		 * Register newsletter subscription form shortcode
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_shortcode() {
			add_shortcode( 'yith_wcmc_subscription_form', array( $this, 'print_shortcode' ) );
		}

		/**
		 * Print newsletter subscription form shortcode
		 *
		 * @param $atts    array Array of attributes passed to shortcode
		 * @param $content string Shortcode content
		 *
		 * @return string Shortcode template
		 * @since 1.0.0
		 */
		public function print_shortcode( $atts, $content = "" ) {
			// generate unique shortcode id
			$unique_id = mt_rand();

			// generate basic default array
			$defaults = array(
				'title'                        => get_option( 'yith_wcmc_shortcode_title' ),
				'submit_label'                 => get_option( 'yith_wcmc_shortcode_submit_button_label' ),
				'success_message'              => get_option( 'yith_wcmc_shortcode_success_message' ),
				'show_privacy_field'           => get_option( 'yith_wcmc_shortcode_show_privacy_field' ),
				'privacy_label'                => get_option( 'yith_wcmc_shortcode_privacy_label' ),
				'hide_form_after_registration' => get_option( 'yith_wcmc_shortcode_hide_after_registration' ),
				'email_type'                   => get_option( 'yith_wcmc_shortcode_email_type', 'html' ),
				'double_optin'                 => get_option( 'yith_wcmc_shortcode_double_optin' ),
				'update_existing'              => get_option( 'yith_wcmc_shortcode_update_existing' ),
				'list'                         => get_option( 'yith_wcmc_shortcode_mailchimp_list' ),
				'groups'                       => implode( '#%,%#', get_option( 'yith_wcmc_shortcode_mailchimp_groups', array() ) ),
				'groups_to_prompt'             => get_option( 'yith_wcmc_shortcode_mailchimp_groups_selectable', array() ),
				'widget'                       => 'no'
			);

			// add defaults for fields
			$selected_fields = get_option( 'yith_wcmc_shortcode_custom_fields' );
			$textual_fields  = '';

			if ( ! empty( $selected_fields ) ) {
				$first = true;
				foreach ( $selected_fields as $field ) {
					if ( ! isset( $field['merge_var'] ) ) {
						continue;
					}

					if ( ! $first ) {
						$textual_fields .= '|';
					}

					$textual_fields .= $field['name'] . ',' . $field['merge_var'];

					$first = false;
				}
			}

			$fields_default = array( 'fields' => $textual_fields );
			$defaults       = array_merge( $defaults, $fields_default );

			// add defaults for style
			$style_defaults = array(
				'enable_style'           => get_option( 'yith_wcmc_shortcode_style_enable' ),
				'round_corners'          => get_option( 'yith_wcmc_shortcode_subscribe_button_round_corners', 'no' ),
				'background_color'       => get_option( 'yith_wcmc_shortcode_subscribe_button_background_color' ),
				'text_color'             => get_option( 'yith_wcmc_shortcode_subscribe_button_color' ),
				'border_color'           => get_option( 'yith_wcmc_shortcode_subscribe_button_border_color' ),
				'background_hover_color' => get_option( 'yith_wcmc_shortcode_subscribe_button_background_hover_color' ),
				'text_hover_color'       => get_option( 'yith_wcmc_shortcode_subscribe_button_hover_color' ),
				'border_hover_color'     => get_option( 'yith_wcmc_shortcode_subscribe_button_border_hover_color' ),
				'custom_css'             => get_option( 'yith_wcmc_shortcode_custom_css' ),
			);

			$defaults = array_merge( $defaults, $style_defaults );

			// generate atts array
			$atts = shortcode_atts( $defaults, $atts );

			// generate structure for fields
			$fields_chunk    = array();
			$fields_subchunk = array_filter( explode( '|', $atts['fields'] ) );
			if ( ! empty( $fields_subchunk ) ) {
				foreach ( $fields_subchunk as $subchunk ) {
					if ( strpos( $subchunk, ',' ) === false ) {
						continue;
					}

					list( $name, $merge_var ) = explode( ',', $subchunk );
					$fields_chunk[ $merge_var ] = array( 'name' => $name, 'merge_var' => $merge_var );
				}
			}
			$atts['fields'] = $fields_chunk;

			// extract variables for the template
			extract( $atts );

			// define context
			$context = ( isset( $widget ) && $widget == 'yes' ) ? 'widget' : 'shortcode';

			// replace "yes"/"no" values with true/false
			$show_privacy_field = ( 'yes' == $show_privacy_field );
			$double_optin       = ( 'yes' == $double_optin );
			$update_existing    = ( 'yes' == $update_existing );
			$enable_style       = ( 'yes' == $enable_style );
			$round_corners      = ( 'yes' == $round_corners );

			// set privacy label
			if ( function_exists( 'wc_privacy_policy_page_id' ) ) {
				$privacy_link  = sprintf( '<a href="%s">%s</a>', get_the_permalink( wc_privacy_policy_page_id() ), apply_filters( 'yith_wcmc_privacy_policy_page_label', __( 'Privacy Policy', 'yith-woocommerce-mailchimp' ) ) );
				$privacy_label = str_replace( '%privacy_policy%', $privacy_link, $privacy_label );
			}
			// let's third part filter privacy policy label
			$privacy_label = apply_filters( 'yith_wcmc_privacy_policy_shortcode_label', $privacy_label );

			if ( empty( $list ) ) {
				return '';
			}

			// retrieve fields informations from mailchimp
			$fields_data = array(
				'EMAIL' => array(
					'public'   => true,
					'type'     => 'email',
					'tag'      => 'EMAIL',
					'required' => true
				)
			);

			if ( ! empty( $fields ) ) {
				$fields_data_raw = $this->do_request( 'get', "lists/{$list}/merge-fields" );

				if ( ! empty( $fields_data_raw['merge_fields'] ) ) {
					$fields_data_raw = $fields_data_raw['merge_fields'];

					foreach ( $fields_data_raw as $data ) {
						$fields_data[ $data['tag'] ] = $data;
					}
				}
			}

			// retrieve groups informations from mailchimp
			$groups_data      = array();
			$groups_to_prompt = ! is_array( $groups_to_prompt ) ? explode( '#%,%#', $groups_to_prompt ) : $groups_to_prompt;

			if ( ! empty( $groups_to_prompt ) ) {
				$available_groups     = $this->do_request( 'get', "lists/{$list}/interest-categories" );
				$available_groups_ids = wp_list_pluck( $available_groups['categories'], 'id' );

				foreach ( $groups_to_prompt as $interest_raw ) {
					if ( strpos( $interest_raw, '-' ) === false ) {
						continue;
					}

					list( $group_id, $interest_id ) = explode( '-', $interest_raw );

					if ( false !== $index = array_search( $group_id, $available_groups_ids ) ) {
						$interest = $this->do_request( 'get', "lists/{$list}/interest-categories/{$group_id}/interests/{$interest_id}" );

						if ( ! isset( $interest['name'] ) ) {
							continue;
						}

						if ( ! isset( $groups_data[ $group_id ] ) ) {
							$groups_data[ $group_id ] = array(
								'id'        => $group_id,
								'name'      => $available_groups['categories'][ $index ]['title'],
								'type'      => $available_groups['categories'][ $index ]['type'],
								'interests' => array(
									$interest_id => $interest['name']
								)
							);
						} else {
							$groups_data[ $group_id ]['interests'][ $interest_id ] = $interest['name'];
						}
					}
				}
			}

			// retrieve style information for template
			$style = '';

			if ( $enable_style ) {
				$style = sprintf(
					'#subscription_form_%d input[type="submit"]{
					    color: %s;
					    border: 1px solid %s;
					    border-radius: %dpx;
					    background: %s;
					}
					#subscription_form_%d input[type="submit"]:hover{
					    color: %s;
					    border: 1px solid %s;
					    background: %s;
					}
					%s',
					$unique_id,
					$text_color,
					$border_color,
					( $round_corners ) ? 5 : 0,
					$background_color,
					$unique_id,
					$text_hover_color,
					$border_hover_color,
					$background_hover_color,
					$custom_css
				);
			}

			$use_placeholders = apply_filters( 'yith_wcmc_use_placeholders_instead_of_labels', false );

			// retrieve template for the subscription form
			$template_name = 'mailchimp-subscription-form.php';

			$located = locate_template( array(
				trailingslashit( WC()->template_path() ) . 'wcmc/' . $template_name,
				trailingslashit( WC()->template_path() ) . $template_name,
				'wcmc/' . $template_name,
				$template_name
			) );

			if ( ! $located ) {
				$located = YITH_WCMC_DIR . 'templates/' . $template_name;
			}

			// returns form template
			ob_start();

			include( $located );

			return ob_get_clean();
		}

		/**
		 * Print single subscription form field
		 *
		 * @param $id             int Unique id of the shortcode
		 * @param $panel_options  array Array of options setted in settings panel
		 * @param $mailchimp_data array Array of data retreieved from mailchimp server
         * @param $context string Value of the context (shortcode)
		 * @param $custom_field_id string (optional) id of the custom field (shortcode)
         *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_field( $id, $panel_options, $mailchimp_data, $context = 'shortcode', $custom_field_id = '' ) {
			if ( ! $mailchimp_data['public'] ) {
				return;
			}

			$use_placeholders = apply_filters( 'yith_wcmc_use_placeholders_instead_of_labels', false );
            $placeholder      = ! empty( $panel_options['name'] ) && $use_placeholders ? $panel_options['name'] : '';
            if ( $placeholder && $custom_field_id &&  function_exists( 'icl_t' ) ) :
                $placeholder = esc_html( icl_t( 'admin_texts_plugin_yith-woocommerce-mailchimp-premium', "yith_wcmc_{$context}_custom_fields[$custom_field_id]", $placeholder ) );
            endif;
			// retrieve template for the subscription form
			$template_name = strtolower( $mailchimp_data['type'] ) . '.php';

			$located = locate_template( array(
				trailingslashit( WC()->template_path() ) . 'wcmc/types/' . $template_name,
				trailingslashit( WC()->template_path() ) . 'types/' . $template_name,
				'wcmc/types/' . $template_name,
				'types/' . $template_name
			) );

			if ( ! $located ) {
				$located = YITH_WCMC_DIR . 'templates/types/' . $template_name;
			}

			include( $located );
		}

		/**
		 * Print single subscription interests group form
		 *
		 * @param $id             int Unique form id
		 * @param $mailchimp_data mixed Array of options retrieved from MailChimp servers
		 *
		 * @return void
		 * @since 1.0.7
		 */
		public function print_groups( $id, $mailchimp_data ) {
			// set correct index in MailChimp data
			$mailchimp_data['tag']                    = $mailchimp_data['id'];
			$mailchimp_data['required']               = false;
			$mailchimp_data['options']['choices']     = $mailchimp_data['interests'];
			$mailchimp_data['use_id_instead_of_name'] = true;

			$use_placeholders = apply_filters( 'yith_wcmc_use_placeholders_instead_of_labels', false );
			$placeholder      = ! empty( $mailchimp_data['name'] ) && $use_placeholders ? $mailchimp_data['name'] : '';

			// retrieve template for the subscription form
			$template_name = strtolower( $mailchimp_data['type'] ) . '.php';

			$located = locate_template( array(
				trailingslashit( WC()->template_path() ) . 'wcmc/types/' . $template_name,
				trailingslashit( WC()->template_path() ) . 'types/' . $template_name,
				'wcmc/types/' . $template_name,
				'types/' . $template_name
			) );

			if ( ! $located ) {
				$located = YITH_WCMC_DIR . 'templates/types/' . $template_name;
			}

			include( $located );
		}

		/**
		 * Register Gutenberg blocks for current plugin
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_gutenberg_block() {
			$selected_fields = get_option( 'yith_wcmc_shortcode_custom_fields' );
			$textual_fields  = '';

			if ( ! empty( $selected_fields ) ) {
				$first = true;
				foreach ( $selected_fields as $field ) {
					if ( ! isset( $field['merge_var'] ) ) {
						continue;
					}

					if ( ! $first ) {
						$textual_fields .= '|';
					}

					$textual_fields .= $field['name'] . ',' . $field['merge_var'];

					$first = false;
				}
			}

			$blocks = array(
				'yith-wcmc-subscription-form' => array(
					'script'         => 'yith-wcmc',
					'title'          => _x( 'YITH MailChimp Subscription Form', '[gutenberg]: block name', 'yith-woocommerce-mailchimp' ),
					'description'    => _x( 'Show form to subscribe to your MailChimp list', '[gutenberg]: block description', 'yith-woocommerce-mailchimp' ),
					'shortcode_name' => 'yith_wcmc_subscription_form',
					'attributes'     => array(
						'title'                        => array(
							'type'    => 'text',
							'label'   => __( 'Form title', 'yith-woocommerce-mailchimp' ),
							'default' => get_option( 'yith_wcmc_shortcode_title' ),
						),
						'submit_label'                 => array(
							'type'    => 'text',
							'label'   => __( '"Submit" button label', 'yith-woocommerce-mailchimp' ),
							'default' => get_option( 'yith_wcmc_shortcode_submit_button_label' ),
						),
						'success_message'              => array(
							'type'    => 'textarea',
							'label'   => __( '"Successfully Registered" message', 'yith-woocommerce-mailchimp' ),
							'default' => get_option( 'yith_wcmc_shortcode_success_message' ),
						),
						'show_privacy_field'           => array(
							'type'    => 'select',
							'label'   => __( 'Show privacy checkbox', 'yith-woocommerce-mailchimp' ),
							'options' => array(
								'no'  => __( 'Hide privacy checkbox', 'yith-woocommerce-mailchimp' ),
								'yes' => __( 'Show privacy checkbox', 'yith-woocommerce-mailchimp' ),
							),
							'default' => get_option( 'yith_wcmc_shortcode_show_privacy_field' ),
						),
						'privacy_label'                => array(
							'type'    => 'text',
							'label'   => __( 'Privacy field label', 'yith-woocommerce-mailchimp' ),
							'default' => get_option( 'yith_wcmc_shortcode_privacy_label' ),
						),
						'hide_form_after_registration' => array(
							'type'    => 'select',
							'label'   => __( 'Hide form after registration', 'yith-woocommerce-mailchimp' ),
							'options' => array(
								'no'  => __( 'Do not hide form after registration', 'yith-woocommerce-mailchimp' ),
								'yes' => __( 'Hide form after registration', 'yith-woocommerce-mailchimp' ),
							),
							'default' => get_option( 'yith_wcmc_shortcode_hide_after_registration' ),
						),
						'email_type'                   => array(
							'type'    => 'select',
							'label'   => __( 'Email type', 'yith-woocommerce-mailchimp' ),
							'options' => array(
								'html' => __( 'HTML', 'yith-woocommerce-mailchimp' ),
								'text' => __( 'Text', 'yith-woocommerce-mailchimp' ),
							),
							'default' => get_option( 'yith_wcmc_shortcode_email_type', 'html' ),
						),
						'double_optin'                 => array(
							'type'    => 'select',
							'label'   => __( 'Double Opt-in', 'yith-woocommerce-mailchimp' ),
							'options' => array(
								'no'  => __( 'Disable double opt-in', 'yith-woocommerce-mailchimp' ),
								'yes' => __( 'Enable double opt-in', 'yith-woocommerce-mailchimp' ),
							),
							'default' => get_option( 'yith_wcmc_shortcode_double_optin' ),
						),
						'update_existing'              => array(
							'type'    => 'select',
							'label'   => __( 'Update existing', 'yith-woocommerce-mailchimp' ),
							'options' => array(
								'no'  => __( 'Do not update existing', 'yith-woocommerce-mailchimp' ),
								'yes' => __( 'Update existing', 'yith-woocommerce-mailchimp' ),
							),
							'default' => get_option( 'yith_wcmc_shortcode_update_existing' ),
						),
						'list'                         => array(
							'type'    => 'select',
							'label'   => __( 'MailChimp list', 'yith-woocommerce-mailchimp' ),
							'options' => $this->retrieve_lists(),
							'default' => get_option( 'yith_wcmc_shortcode_mailchimp_list' ),
						),
						'groups'                       => array(
							'type'    => 'textarea',
							'label'   => __( 'Auto-subscribe interest groups (list of GROUP_ID-INTEREST_ID separated by special token #%,%#)', 'yith-woocommerce-mailchimp' ),
							'default' => implode( '#%,%#', get_option( 'yith_wcmc_shortcode_mailchimp_groups', array() ) ),
						),
						'groups_to_prompt'             => array(
							'type'    => 'textarea',
							'label'   => __( 'Show the following interest groups (list of GROUP_ID-INTEREST_ID separated by special token #%,%#)', 'yith-woocommerce-mailchimp' ),
							'default' => implode( '#%,%#', get_option( 'yith_wcmc_shortcode_mailchimp_groups_selectable', array() ) ),
						),
						'fields'                       => array(
							'type'    => 'textarea',
							'label'   => __( 'Fields (list of LABEL,MERGE_VAR separated by special token |)', 'yith-woocommerce-mailchimp' ),
							'default' => $textual_fields,
						),
						'enable_style'                 => array(
							'type'    => 'select',
							'label'   => __( 'Enable custom CSS', 'yith-woocommerce-mailchimp' ),
							'options' => array(
								'no'  => __( 'Do not enable custom style', 'yith-woocommerce-mailchimp' ),
								'yes' => __( 'Enable custom style', 'yith-woocommerce-mailchimp' ),
							),
							'default' => get_option( 'yith_wcmc_shortcode_style_enable' ),
						),
						'round_corners'                => array(
							'type'    => 'select',
							'label'   => __( 'Round Corners for "Subscribe" Button	 (only when custom style enabled)', 'yith-woocommerce-mailchimp' ),
							'options' => array(
								'no'  => __( 'Do not round corners', 'yith-woocommerce-mailchimp' ),
								'yes' => __( 'Round Corners', 'yith-woocommerce-mailchimp' ),
							),
							'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_round_corners', 'no' ),
						),
						'background_color'             => array(
							'type'    => 'colorpicker',
							'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_background_color' ),
						),
						'text_color'                   => array(
							'type'    => 'colorpicker',
							'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_color' ),
						),
						'border_color'                 => array(
							'type'    => 'colorpicker',
							'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_border_color' ),
						),
						'background_hover_color'       => array(
							'type'    => 'colorpicker',
							'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_background_hover_color' ),
						),
						'text_hover_color'             => array(
							'type'    => 'colorpicker',
							'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_hover_color' ),
						),
						'border_hover_color'           => array(
							'type'    => 'colorpicker',
							'default' => get_option( 'yith_wcmc_shortcode_subscribe_button_border_hover_color' ),
						),
						'custom_css'                   => array(
							'type'    => 'textarea',
							'label'   => __( 'Custom CSS (only when custom style enabled)', 'yith-woocommerce-mailchimp' ),
							'default' => get_option( 'yith_wcmc_shortcode_custom_css' ),
						),
					),
				)
			);
			yith_plugin_fw_gutenberg_add_blocks( $blocks );
		}

		/**
		 * Register custom widgets for Elementor
		 *
		 * @return void
		 */
		public function init_elementor_widgets() {
			// check if elementor is active.
			if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
				return;
			}

			// include widgets.
			include_once YITH_WCMC_INC . 'widget/elementor/class.yith-wcmc-elementor-subscription-form.php';

			// register widgets.
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_elementor_widgets' ) );
		}

		/**
		 * Register Elementor Widgets
		 *
		 * @return void
		 */
		public function register_elementor_widgets() {
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new YITH_WCMC_Elementor_Subscription_Form() );
		}

		/* === HANDLE WIDGET === */

		/**
		 * Registers widget used to show subscription form
		 *
		 * @return void
		 * @since1.0.0
		 */
		public function register_widget() {
			register_widget( 'YITH_WCMC_Widget' );
		}

		/* === HANDLE FORM SUBSCRIPTION === */

		/**
		 * Register a user using form fields
		 *
		 * @return array Array with status code and messages
		 * @since 1.0.0
		 */
		public function form_subscribe() {
			// retrieve minimum required fields for subscription
			$list               = isset( $_POST['list'] ) ? $_POST['list'] : false;
			$email              = isset( $_POST['EMAIL'] ) ? $_POST['EMAIL'] : false;
			$nonce              = isset( $_POST['yith_wcmc_subscribe_nonce'] ) ? $_POST['yith_wcmc_subscribe_nonce'] : false;
			$show_privacy_field = isset( $_POST['show_privacy_field'] ) ? $_POST['show_privacy_field'] == 'yes' : false;
			$privacy_agreement  = isset( $_POST['privacy_agreement'] );

			// check existance of minimum required fields
			if ( empty( $list ) || empty( $email ) || empty( $nonce ) ) {
				return array(
					'status'  => false,
					'code'    => false,
					'message' => apply_filters( 'yith_wcmc_missing_required_arguments_error_message', __( 'Required arguments missing', 'yith-woocommerce-mailchimp' ) )
				);
			}

			if ( $show_privacy_field && ! $privacy_agreement ) {
				return array(
					'status'  => false,
					'code'    => false,
					'message' => apply_filters( 'yith_wcmc_privacy_error_message', __( 'You must agree privacy agreement', 'yith-woocommerce-mailchimp' ) )
				);
			}

			// check nonce
			if ( ! wp_verify_nonce( $nonce, 'yith_wcmc_subscribe' ) ) {
				return array(
					'status'  => false,
					'code'    => false,
					'message' => apply_filters( 'yith_wcmc_operation_denied_error_message', __( 'Ops! It seems you are not allowed to do this', 'yith-woocommerce-mailchimp' ) )
				);
			}

			// retrieve additional params
			$member_hash     = md5( strtolower( $email ) );
			$groups          = isset( $_POST['groups'] ) ? $_POST['groups'] : '';
			$email_type      = isset( $_POST['email_type'] ) ? $_POST['email_type'] : 'html';
			$double_optin    = ! empty( $_POST['double_optin'] ) ? true : false;
			$update_existing = ! empty( $_POST['update_existing'] ) ? true : false;
			$success_message = ! empty( $_POST['success_message'] ) ? wp_kses_post( $_POST['success_message'] ) : __( 'Great! You\'re now subscribed to our newsletter', 'yith-woocommerce-mailchimp' );
			$yith_wcmc_group = isset( $_POST['yith_wcmc_group'] ) ? $_POST['yith_wcmc_group'] : '';

			//retrieve specific interests of group
			$specific_interests = array();
			if ( ! empty( $yith_wcmc_group ) ) {
				foreach ( $yith_wcmc_group as $list_id => $list_interests_array ) {
					foreach ( $list_interests_array as $item => $interest_id ) {
						$specific_interests[] = $list_id . '-' . $interest_id;
					}
				}
				$selected_interests = implode( '#%,%#', $specific_interests );
				$groups             = $groups . '#%,%#' . $selected_interests;
			}

			// retrieve merge vars
			$fields_row_data = $this->do_request( 'get', "lists/{$list}/merge-fields" );
			$fields_data     = isset( $fields_row_data['merge_fields'] ) ? $fields_row_data['merge_fields'] : array();

			$data_to_submit = new stdClass();
			if ( ! empty( $fields_data ) ) {
				foreach ( $fields_data as $field ) {
					$interest_id = $field['tag'];
					if ( isset( $_POST[ $interest_id ] ) && '' != $_POST[ $interest_id ] ) {
						$value = $_POST[ $interest_id ];

						// reformat submitted values
						if ( $field['type'] == 'birthday' ) {
							$value = str_replace( '-', '/', substr( $value, - 5 ) );
						}

						$data_to_submit->$interest_id = $value;
					}
				}
			}

			// retrieve groups
			$groups_to_submit = new stdClass();
			if ( ! empty( $groups ) ) {
				$groups_chunks = explode( '#%,%#', $groups );

				if ( ! empty( $groups_chunks ) ) {
					foreach ( $groups_chunks as $chunk ) {
						if ( strpos( $chunk, '-' ) === false ) {
							continue;
						}

						list( $group_id, $interest_id ) = explode( '-', $chunk );

						$groups_to_submit->$interest_id = true;
					}
				}
			}

			// retrieve chosen interests
			$available_groups     = $this->do_request( 'get', "lists/{$list}/interest-categories" );
			$available_groups_ids = ! isset( $available_groups['status'] ) ? wp_list_pluck( $available_groups['categories'], 'id' ) : array();
			if ( ! empty( $available_groups_ids ) ) {
				foreach ( $available_groups_ids as $group_id ) {
					if ( isset( $_POST[ $group_id ] ) && $interests_id = $_POST[ $group_id ] ) {
						if ( ! empty( $interests_id ) ) {
							foreach ( $interests_id as $interest_id ) {
								$groups_to_submit->$interest_id = true;
							}
						}
					}
				}
			}

			// generate argument structure to send within the request

			$args = array(
				'email_address'   => $email,
				'email_type'      => $email_type,
				'status'          => $double_optin ? 'pending' : 'subscribed',
				'merge_fields'    => $data_to_submit,
				'interests'       => $groups_to_submit,
				'update_existing' => $update_existing
			);

			$res = $this->subscribe( $list, $email, $args );

			do_action( 'yith_wcmc_user_subscribed_in_form_subscribe', $args, $list );

			if ( ! isset( $res['status'] ) || $res['status'] ) {
				$res = array(
					'status'  => true,
					'message' => apply_filters( 'yith_wcmc_correctly_subscribed_message', stripslashes( $success_message ), $list, $email )
				);
			}

			if ( is_user_logged_in() ) {
				// register subscribed list
				$this->_register_customer_subscribed_lists( $list, $email, get_current_user_id() );
			}

			return $res;
		}

		/**
		 * Calls form_subscribe(), when posting form data, and adds woocommerce notice with result of the operation
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function post_form_subscribe() {
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['yith_wcmc_subscribe_nonce'] ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				$res = $this->form_subscribe();

				wc_add_notice( $res['message'], ( $res['status'] ) ? 'yith-wcmc-success' : 'yith-wcmc-error' );
			}
		}

		/* === HANDLES AJAX REQUESTS === */

		/**
		 * Calls form_subscribe(), from an AJAX request, and print JSON encoded version of its result
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ajax_form_subscribe() {
			wp_send_json( $this->form_subscribe() );
		}
	}
}

/**
 * Unique access to instance of YITH_WCMC_Premium class
 *
 * @return \YITH_WCMC_Premium
 * @since 1.0.0
 */
function YITH_WCMC_Premium() {
	return YITH_WCMC_Premium::get_instance();
}