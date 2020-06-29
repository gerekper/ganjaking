<?php
! defined( 'YITH_POS' ) && exit; // Exit if accessed directly


if ( ! class_exists( 'YITH_POS_Frontend' ) ) {
	/**
	 * Class YITH_POS_Frontend
	 * Main Frontend Class
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	class YITH_POS_Frontend {

		/** @var YITH_POS_Frontend */
		private static $_instance;

		private $regex_string;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_POS_Frontend
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		private function __construct() {
			add_action( 'template_redirect', array( $this, 'register_login_logout_handler' ) );

			add_action( 'yith_pos_footer', array( $this, 'print_script_settings' ) );

			add_filter( 'woocommerce_rest_product_object_query', array( $this, 'extends_rest_product_query' ), 10, 2 );

			//search filter
			add_filter( 'woocommerce_rest_product_object_query', array( $this, 'search_product_args' ), 10, 2 );
			add_filter( 'woocommerce_rest_product_object_query', array( $this, 'filter_product_on_sale' ), 10, 2 );

			//order filter
			add_filter( 'woocommerce_rest_shop_order_object_query', array( $this, 'search_order_args' ), 10, 2 );

			// add parent_categories to product variations (for coupon check)
			add_filter( 'woocommerce_rest_prepare_product_variation_object', array( $this, 'rest_parent_categories' ), 10, 3 );
			add_filter( 'woocommerce_rest_prepare_product_object', array( $this, 'rest_parent_categories' ), 10, 3 );

			add_filter( 'woocommerce_rest_prepare_shop_order_object', array( $this, 'rest_order_fields' ), 10, 3 );
			add_filter( 'woocommerce_rest_prepare_shop_order_object', array( $this, 'add_custom_meta_to_order_api_response' ), 10, 3 );

			// product thumbnails
			add_filter( 'woocommerce_rest_prepare_product_variation_object', array( $this, 'rest_product_thumbnails' ), 10, 3 );
			add_filter( 'woocommerce_rest_prepare_product_object', array( $this, 'rest_product_thumbnails' ), 10, 3 );

			//customer VAT
			add_filter( 'woocommerce_billing_fields', array( $this, 'add_billing_vat' ) );

			//internal YITH POS redirect
			add_action( 'init', array( $this, 'pos_page_rewrite' ), 0 );
			add_filter( 'option_rewrite_rules', array( $this, 'rewrite_rules' ), 1 );
		}


		/**
		 * Filter the product on sale.
		 * This method has been written because WC add all product on sale
		 * as post_in without the products excluded.
		 *
		 * @return mixed
		 */
		public function filter_product_on_sale( $args, $request ) {

			if ( isset( $_GET[ 'yith_on_sale' ] ) ) {
				$on_sale_ids        = wc_get_product_ids_on_sale();
				$exclude            = isset( $_GET[ 'exclude' ] ) ? explode( ',', $_GET[ 'exclude' ] ) : array();
				$args[ 'post__in' ] = array_diff( $on_sale_ids, $exclude );
				unset( $args[ 'post__not_in' ] );
			}

			return $args;
		}

		/**
		 * Hook into order API response to add custom field data.
		 *
		 * @param $response
		 * @param $object
		 * @param $request
		 *
		 * @return mixed $response
		 */
		public function add_custom_meta_to_order_api_response( $response, $object, $request ) {
			if ( $object ) {
				$data                               = $response->get_data();
				$data[ 'multiple_payment_methods' ] = yith_pos_get_order_payment_methods( $object );
				$response->set_data( $data );
			}

			return $response;
		}

		/**
		 * Check if the permalink should be flushed.
		 *
		 * @param $rules
		 *
		 * @return bool
		 */
		public function rewrite_rules( $rules ) {
			return isset( $rules[ $this->regex_string ] ) ? $rules : false;
		}

		/**
		 * All the child-pages of pos will open the pos page without change the current URL
		 */
		public function pos_page_rewrite() {
			$pos_page_id = yith_pos_get_pos_page_id();

			$base_url           = $this->get_base_url();
			$regex              = '^' . $base_url . '?([^/]*)';
			$this->regex_string = $regex;
			add_rewrite_rule( $regex, "index.php?page_id=" . $pos_page_id, 'top' );

		}


		/**
		 * @param $address_fields
		 *
		 * @return mixed
		 */
		public function add_billing_vat( $address_fields ) {
			$address_fields[ 'billing_vat' ] = array(
				'label'    => __( 'VAT', 'yith-point-of-sale-for-woocommerce' ),
				'required' => false,
				'type'     => 'text',
				'class'    => array( 'form-row-wide' ),
				'priority' => 35,
			);

			return $address_fields;
		}

		/**
		 * @param            $response
		 * @param WC_Product $object
		 * @param            $request
		 *
		 * @return mixed
		 */
		public function rest_parent_categories( $response, $object, $request ) {
			if ( $object && $object->is_type( 'variation' ) ) {
				$categories = array();
				if ( $variable = wc_get_product( $object->get_parent_id() ) ) {
					$categories = $this->get_taxonomy_terms( $variable );
				}
				$data                        = $response->get_data();
				$data[ 'parent_categories' ] = $categories;
				$response->set_data( $data );
			}

			return $response;
		}

		/**
		 * @param            $response
		 * @param WC_Product $object
		 * @param            $request
		 *
		 * @return mixed
		 */
		public function rest_product_thumbnails( $response, $object, $request ) {
			if ( $object ) {
				$data  = $response->get_data();
				$image = yith_pos_rest_get_product_thumbnail( $object->get_id() );

				if ( $image ) {
					$data[ 'yithPosImage' ] = $image;
					$response->set_data( $data );
				}
			}

			return $response;
		}

		/**
		 * @param            $response
		 * @param WC_Order   $object
		 * @param            $request
		 *
		 * @return mixed
		 */
		public function rest_order_fields( $response, $object, $request ) {
			if ( $object ) {
				$data = $response->get_data();
				// Item Thumbnails
				foreach ( $data[ 'line_items' ] as &$line_item ) {
					$variation_id                = ! empty( $line_item[ 'variation_id' ] ) ? $line_item[ 'variation_id' ] : 0;
					$product_id                  = $line_item[ 'product_id' ];
					$line_item[ 'yithPosImage' ] = yith_pos_rest_get_product_thumbnail( $product_id, $variation_id );
				}

				$info = array();
				// Store Name
				if ( $store_id = $object->get_meta( '_yith_pos_store' ) ) {
					$info[ 'store_name' ] = yith_pos_get_store_name( $store_id );
				}

				// Register Name
				if ( $register_id = $object->get_meta( '_yith_pos_register' ) ) {
					$info[ 'register_name' ] = yith_pos_get_register_name( $register_id );
				}

				// Cashier Name
				if ( $cashier_id = $object->get_meta( '_yith_pos_cashier' ) ) {
					$info[ 'cashier_name' ] = yith_pos_get_employee_name( $cashier_id, array( 'hide_nickname' => true ) );
				}

				if ( $info ) {
					$data[ 'yith_pos_data' ] = $info;
				}

				$response->set_data( $data );
			}

			return $response;
		}

		protected function get_taxonomy_terms( $product, $taxonomy = 'cat' ) {
			$terms = array();

			foreach ( wc_get_object_terms( $product->get_id(), 'product_' . $taxonomy ) as $term ) {
				$terms[] = array(
					'id'   => $term->term_id,
					'name' => $term->name,
					'slug' => $term->slug,
				);
			}

			return $terms;
		}


		/**
		 * Extends REST product query
		 *
		 * @param array $args
		 * @param array $request
		 *
		 * @return array
		 */
		public function extends_rest_product_query( $args, $request ) {
			if ( isset( $request[ 'yith_pos_stock_status' ] ) ) {
				$stock_statuses = explode( ',', $request[ 'yith_pos_stock_status' ] );
				$meta_query     = array(
					'key'     => '_stock_status',
					'value'   => $stock_statuses,
					'compare' => 'IN',
				);
				if ( isset( $args[ 'meta_query' ] ) ) {
					$args[ 'meta_query' ][] = $meta_query;
				} else {
					$args[ 'meta_query' ] = array( $meta_query );
				}
			}

			if ( isset( $request[ 'exclude_category' ] ) ) {
				$stock_statuses = explode( ',', $request[ 'exclude_category' ] );
				$tax_query      = array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $stock_statuses,
					'operator' => 'NOT IN'
				);
				if ( isset( $args[ 'tax_query' ] ) ) {
					$args[ 'tax_query' ][] = $tax_query;
				} else {
					$args[ 'tax_query' ] = array( $tax_query );
				}
			}

			return $args;
		}

		/**
		 * Extend search product to sku for product and product variation.
		 *
		 * @param $args
		 * @param $request
		 *
		 * @return mixed
		 */
		public function search_product_args( $args, $request ) {

			if ( isset( $_GET[ 'queryName' ] ) && isset( $args[ 's' ] ) && $_GET[ 'queryName' ] === 'yith_pos_search' ) {
				global $wpdb;

				add_filter( 'pre_get_posts', array( $this, 'filter_query_post_type' ), 10 );

				$per_page = isset( $args[ 'posts_per_page' ] ) ? $args[ 'posts_per_page' ] : 9;
				$search   = '%' . esc_sql( $args[ 's' ] ) . '%';

				if ( yith_plugin_fw_is_true( $_GET[ 'barcode' ] ) ) {
					$barcode_meta = defined( 'YITH_YWBC_SLUG' ) ? '_ywbc_barcode_display_value' : '_sku';
					$barcode_meta = apply_filters( 'yith_pos_barcode_custom_field', $barcode_meta );
					$query        = $wpdb->prepare( "SELECT p.ID FROM $wpdb->posts p
                            LEFT JOIN $wpdb->postmeta pm2 ON ( pm2.post_id = p.ID)
                            WHERE p.post_type in('product', 'product_variation') AND p.post_status = 'publish'
                            AND  pm2.meta_key LIKE %s AND pm2.meta_value LIKE %s
                            GROUP BY p.ID LIMIT %d", $barcode_meta, $search, $per_page );
				} else {
					$query = $wpdb->prepare( "SELECT p.ID FROM $wpdb->posts p
                            LEFT JOIN $wpdb->postmeta pm1 ON ( pm1.post_id = p.ID)
                            WHERE p.post_type in('product', 'product_variation') AND p.post_status = 'publish'
                            AND  p.post_title LIKE %s OR  ( pm1.meta_key LIKE '_sku' AND pm1.meta_value LIKE %s )
                            GROUP BY p.ID LIMIT %d", $search, $search, $per_page );
				}

				$results = $wpdb->get_col( $query );

				if ( $results ) {
					$args[ 'post__in' ] = $results;
					unset( $args[ 's' ] );
				}

			}

			return $args;
		}


		/**
		 * Filter the orders for the store.
		 *
		 * @param $args
		 * @param $request
		 *
		 * @return mixed
		 */
		public function search_order_args( $args, $request ) {
			if ( isset( $_GET[ 'queryName' ] ) && $_GET[ 'queryName' ] === 'yith_pos_search_orders' ) {
				$meta_query = array(
					'relation' => 'AND',
					array(
						'key'     => '_yith_pos_order',
						'value'   => '1',
						'compare' => '=='
					)
				);

				isset( $_GET[ 'store' ] ) && array_push( $meta_query, array( 'key' => '_yith_pos_store', 'value' => $_GET[ 'store' ], 'compare' => '==' ) );
				isset( $_GET[ 'register' ] ) && array_push( $meta_query, array( 'key' => '_yith_pos_register', 'value' => $_GET[ 'register' ], 'compare' => '==' ) );
				isset( $_GET[ 'cashier' ] ) && array_push( $meta_query, array( 'key' => '_yith_pos_cashier', 'value' => $_GET[ 'cashier' ], 'compare' => '==' ) );

				$args[ 'meta_query' ] = apply_filters( 'yith_pos_search_order_meta_query', $meta_query, $request );
			}

			return $args;
		}

		/**
		 * Extend the query also for product variation.
		 *
		 * @param $query
		 */
		public function filter_query_post_type( $query ) {
			$query->query_vars[ 'post_type' ] = array( 'product', 'product_variation' );
		}


		public function register_login_logout_handler() {
			if ( is_yith_pos() ) {

				$register_id = isset( $_POST[ 'register' ] ) ? $_POST[ 'register' ] : yith_pos_register_logged_in();
				if ( isset( $_GET[ 'yith-pos-register-direct-login-nonce' ], $_GET[ 'register' ] ) ) {
					$register_id = absint( $_GET[ 'register' ] );
				}
				if ( $register_id && ! isset( $_GET[ 'user-editing' ] ) && ! isset( $_GET[ 'yith-pos-take-over-nonce' ] ) ) {
					$user_editing = yith_pos_check_register_lock( $register_id );
					if ( $user_editing ) {
						// another user is managing the register
						$args = array(
							'user-editing' => $user_editing,
							'register'     => $register_id,
							'store'        => isset( $_POST[ 'store' ] ) ? $_POST[ 'store' ] : ''
						);
						wp_redirect( add_query_arg( $args, yith_pos_get_pos_page_url() ) );
						exit;
					}
				}
				$action      = '';
				$register_id = false;
				$redirect    = false;

				if ( isset( $_POST[ 'yith-pos-register-login-nonce' ], $_POST[ 'register' ] ) && wp_verify_nonce( $_POST[ 'yith-pos-register-login-nonce' ], 'yith-pos-register-login' ) ) {
					$action      = 'login';
					$register_id = absint( $_POST[ 'register' ] );
					$redirect    = yith_pos_get_pos_page_url();
				} else if ( isset( $_GET[ 'yith-pos-register-direct-login-nonce' ], $_GET[ 'register' ] ) && wp_verify_nonce( $_GET[ 'yith-pos-register-direct-login-nonce' ], 'yith-pos-register-direct-login' ) ) {
					$action      = 'direct-login';
					$register_id = absint( $_GET[ 'register' ] );
					$redirect    = yith_pos_get_pos_page_url();
				} else if ( isset( $_GET[ 'yith-pos-take-over-nonce' ], $_GET[ 'register' ] ) && wp_verify_nonce( $_GET[ 'yith-pos-take-over-nonce' ], 'yith-pos-take-over' ) ) {
					$action      = 'take-over';
					$register_id = absint( $_GET[ 'register' ] );
					$redirect    = yith_pos_get_pos_page_url();
				} else if ( isset( $_GET[ 'yith-pos-register-close-nonce' ], $_GET[ 'register' ] ) && wp_verify_nonce( $_GET[ 'yith-pos-register-close-nonce' ], 'yith-pos-register-close-' . $_GET[ 'register' ] ) ) {
					$action      = 'close-register';
					$register_id = absint( $_GET[ 'register' ] );
					// TODO: redirect to a specific page to show the report for closing register
					$redirect = yith_pos_get_pos_page_url();
				} else if ( ! empty( $_GET[ 'yith-pos-user-logout' ] ) ) {
					$action   = 'logout';
					$redirect = yith_pos_get_pos_page_url();
				} else if ( ! empty( $_GET[ 'yith-pos-register-logout' ] ) ) {
					$action   = 'register-logout';
					$redirect = yith_pos_get_pos_page_url();
				}

				if ( $register_id && ! yith_pos_user_can_use_register( $register_id ) ) {
					wp_die( __( 'Error: you cannot get access to this Register!', 'yith-point-of-sale-for-woocommerce' ) );
				}

				switch ( $action ) {
					case 'login':
					case 'direct-login':
					case 'take-over':
						if ( $register_id ) {
							yith_pos_maybe_open_register( $register_id );
							yith_pos_set_register_lock( $register_id );
							yith_pos_register_login( $register_id );
						}
						break;

					case 'close-register':
						if ( $register_id ) {
							yith_pos_close_register( $register_id );
						}
					// no break // Please DON'T break me, since I need to logout ;-)
					case 'register-logout':
					case 'logout':
						if ( $register_id = yith_pos_register_logged_in() ) {
							yith_pos_unset_register_lock( $register_id );
						}
						yith_pos_register_logout();

						if ( 'logout' === $action ) {
							wp_logout();
						}
						break;
				}

				if ( $redirect ) {
					wp_redirect( $redirect );
					exit;
				}
			}
		}

		public function print_script_settings() {
			$settings = YITH_POS_Settings()->get_frontend_settings();
			if ( $settings ) {
				?>
                <script type="text/javascript">
                    var yithPosSettings = yithPosSettings || JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( $settings ) ); ?>' ) );
                </script>
				<?php
			}
		}

		/**
		 * @return false|mixed|string
		 */
		public function get_base_url() {
			$pos_url  = yith_pos_get_pos_page_url();
			$site_url = strtok( get_site_url(), '?' );
			$base_url = str_replace( $site_url, '', $pos_url );
			$start    = stripos( $base_url, '/' );
			$base_url = $start == 0 ? substr( $base_url, 1 ) : $base_url;

			return $base_url;
		}

	}

	/**
	 * Unique access to instance of YITH_POS_Frontend class
	 *
	 * @return YITH_POS_Frontend
	 * @since 1.0.0
	 */
	function YITH_POS_Frontend() {
		return YITH_POS_Frontend::get_instance();
	}
}
