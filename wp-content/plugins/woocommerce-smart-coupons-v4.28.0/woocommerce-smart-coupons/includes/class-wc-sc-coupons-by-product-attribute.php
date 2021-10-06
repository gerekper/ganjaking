<?php
/**
 * Class to handle feature Coupons By Product Attribute
 *
 * @author      StoreApps
 * @category    Admin
 * @package     wocommerce-smart-coupons/includes
 * @version     1.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupons_By_Product_Attribute' ) ) {

	/**
	 * Class WC_SC_Coupons_By_Product_Attribute
	 */
	class WC_SC_Coupons_By_Product_Attribute {

		/**
		 * Variable to hold instance of this class
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'usage_restriction' ), 10, 2 );
			add_action( 'save_post', array( $this, 'process_meta' ), 10, 2 );
			add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'validate' ), 11, 4 );
			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'handle_non_product_type_coupons' ), 11, 3 );
			add_filter( 'wc_smart_coupons_export_headers', array( $this, 'export_headers' ) );
			add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );
			add_filter( 'is_protected_meta', array( $this, 'make_action_meta_protected' ), 10, 3 );
			add_filter( 'sc_generate_coupon_meta', array( $this, 'generate_coupon_attribute_meta' ), 10, 2 );
			add_action( 'wc_sc_new_coupon_generated', array( $this, 'copy_coupon_attributes_meta' ) );
		}

		/**
		 * Get single instance of this class
		 *
		 * @return this class Singleton object of this class
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name = '', $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Display field for coupon by product attribute
		 *
		 * @param integer   $coupon_id The coupon id.
		 * @param WC_Coupon $coupon    The coupon object.
		 */
		public function usage_restriction( $coupon_id = 0, $coupon = null ) {
			global $wp_version;

			$product_attribute_ids         = array();
			$exclude_product_attribute_ids = array();
			$coupon_types                  = wc_get_coupon_types();
			$product_coupon_types          = wc_get_product_coupon_types();
			$non_product_coupon_types      = array();
			foreach ( $coupon_types as $coupon_type => $coupon_label ) {
				if ( ! in_array( $coupon_type, $product_coupon_types, true ) ) {
					$non_product_coupon_types[] = $coupon_label;
				}
			}
			if ( ! empty( $non_product_coupon_types ) ) {
				$non_product_coupon_types_label = '"' . implode( ', ', $non_product_coupon_types ) . '"';
			} else {
				$non_product_coupon_types_label = '';
			}
			if ( ! empty( $coupon_id ) ) {
				$product_attribute_ids = get_post_meta( $coupon_id, 'wc_sc_product_attribute_ids', true );
				if ( ! empty( $product_attribute_ids ) ) {
					$product_attribute_ids = explode( '|', $product_attribute_ids );
				} else {
					$product_attribute_ids = array();
				}

				$exclude_product_attribute_ids = get_post_meta( $coupon_id, 'wc_sc_exclude_product_attribute_ids', true );
				if ( ! empty( $exclude_product_attribute_ids ) ) {
					$exclude_product_attribute_ids = explode( '|', $exclude_product_attribute_ids );
				} else {
					$exclude_product_attribute_ids = array();
				}
			}

			$attribute_taxonomies       = wc_get_attribute_taxonomies();
			$attribute_options          = array();
			$attribute_taxonomies_label = array();

			if ( ! empty( $attribute_taxonomies ) && is_array( $attribute_taxonomies ) ) {
				$attribute_taxonomies_name = array();
				foreach ( $attribute_taxonomies as $attribute_taxonomy ) {
					$attribute_name  = isset( $attribute_taxonomy->attribute_name ) ? $attribute_taxonomy->attribute_name : '';
					$attribute_label = isset( $attribute_taxonomy->attribute_label ) ? $attribute_taxonomy->attribute_label : '';
					if ( ! empty( $attribute_name ) && ! empty( $attribute_label ) ) {
						$attribute_taxonomy_name                                = wc_attribute_taxonomy_name( $attribute_name );
						$attribute_taxonomies_name[]                            = $attribute_taxonomy_name;
						$attribute_taxonomies_label[ $attribute_taxonomy_name ] = $attribute_label;
					}
				}
				if ( ! empty( $attribute_taxonomies_name ) ) {
					$args = array(
						'orderby'    => 'name',
						'hide_empty' => 0,
					);
					$args = apply_filters( 'woocommerce_product_attribute_terms', $args );
					if ( version_compare( $wp_version, '4.5.0', '>=' ) ) {
						$attribute_taxonomies_terms = get_terms(
							array_merge(
								array(
									'taxonomy' => $attribute_taxonomies_name,
								),
								$args
							)
						);
					} else {
						$attribute_taxonomies_terms = get_terms( $attribute_taxonomies_name, $args );
					}
					if ( ! empty( $attribute_taxonomies_terms ) && is_array( $attribute_taxonomies_terms ) ) {
						foreach ( $attribute_taxonomies_terms as $attribute_taxonomy_term ) {
							$attribute_taxonomy       = $attribute_taxonomy_term->taxonomy;
							$attribute_taxonomy_label = isset( $attribute_taxonomies_label[ $attribute_taxonomy ] ) ? $attribute_taxonomies_label[ $attribute_taxonomy ] : '';
							if ( empty( $attribute_taxonomy_label ) ) {
								continue;
							}
							$attribute_term_id   = $attribute_taxonomy_term->term_id;
							$attribute_term_name = $attribute_taxonomy_term->name;
							$attribute_title     = __( 'Attribute=', 'woocommerce-smart-coupons' ) . $attribute_taxonomy_label . ':' . __( 'Value=', 'woocommerce-smart-coupons' ) . $attribute_term_name;
							$attribute_label     = $attribute_taxonomy_label . ': ' . $attribute_term_name;

							$attribute_options[ $attribute_term_id ] = array(
								'title' => $attribute_title,
								'label' => $attribute_label,
							);
						}
					}
				}
			}
			?>
			<div class="options_group smart-coupons-field">
				<p class="form-field">
					<label for="wc_sc_product_attribute_ids"><?php echo esc_html__( 'Product attributes', 'woocommerce-smart-coupons' ); ?></label>
					<select id="wc_sc_product_attribute_ids" name="wc_sc_product_attribute_ids[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No product attributes', 'woocommerce-smart-coupons' ); ?>">
						<?php
						if ( ! empty( $attribute_options ) ) {
							foreach ( $attribute_options as $attribute_id => $attribute_data ) {
								echo '<option title="' . esc_attr( $attribute_data['title'] ) . '" value="' . esc_attr( $attribute_id ) . '"' . esc_attr( selected( in_array( (string) $attribute_id, $product_attribute_ids, true ), true, false ) ) . '>' . esc_html( $attribute_data['label'] ) . '</option>';
							}
						}
						?>
					</select>
					<?php
					/* translators: Non product type coupon labels */
					$tooltip_text = sprintf( esc_html__( 'Product attributes that the coupon will be applied to, or that need to be in the cart in order for the %s to be applied.', 'woocommerce-smart-coupons' ), $non_product_coupon_types_label );
					echo wc_help_tip( $tooltip_text ); // phpcs:ignore
					?>
				</p>
			</div>
			<div class="options_group smart-coupons-field">
				<p class="form-field">
					<label for="wc_sc_exclude_product_attribute_ids"><?php echo esc_html__( 'Exclude attributes', 'woocommerce-smart-coupons' ); ?></label>
					<select id="wc_sc_exclude_product_attribute_ids" name="wc_sc_exclude_product_attribute_ids[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No product attributes', 'woocommerce-smart-coupons' ); ?>">
						<?php
						if ( ! empty( $attribute_options ) ) {
							foreach ( $attribute_options as $attribute_id => $attribute_data ) {
								echo '<option title="' . esc_attr( $attribute_data['title'] ) . '" value="' . esc_attr( $attribute_id ) . '"' . esc_attr( selected( in_array( (string) $attribute_id, $exclude_product_attribute_ids, true ), true, false ) ) . '>' . esc_html( $attribute_data['label'] ) . '</option>';
							}
						}
						?>
					</select>
					<?php
					/* translators: Non product type coupon labels */
					$tooltip_text = sprintf( esc_html__( 'Product attributes that the coupon will not be applied to, or that cannot be in the cart in order for the %s to be applied.', 'woocommerce-smart-coupons' ), $non_product_coupon_types_label );
					echo wc_help_tip( $tooltip_text ); // phpcs:ignore
					?>
				</p>
			</div>
			<?php
		}

		/**
		 * Save coupon by product attribute data in meta
		 *
		 * @param  Integer $post_id The coupon post ID.
		 * @param  WP_Post $post    The coupon post.
		 */
		public function process_meta( $post_id = 0, $post = null ) {
			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) {
				return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}
			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) { // phpcs:ignore
				return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== $post->post_type ) {
				return;
			}

			$product_attribute_ids = ( isset( $_POST['wc_sc_product_attribute_ids'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_product_attribute_ids'] ) ) : array(); // phpcs:ignore
			$product_attribute_ids = implode( '|', $product_attribute_ids ); // Store attribute ids as delimited data instead of serialized data.
			update_post_meta( $post_id, 'wc_sc_product_attribute_ids', $product_attribute_ids );

			$exclude_product_attribute_ids = ( isset( $_POST['wc_sc_exclude_product_attribute_ids'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_exclude_product_attribute_ids'] ) ) : array(); // phpcs:ignore
			$exclude_product_attribute_ids = implode( '|', $exclude_product_attribute_ids ); // Store attribute ids as delimited data instead of serialized data.
			update_post_meta( $post_id, 'wc_sc_exclude_product_attribute_ids', $exclude_product_attribute_ids );
		}

		/**
		 * Function to validate coupons for against product attributes
		 *
		 * @param bool            $valid Coupon validity.
		 * @param WC_Product|null $product Product object.
		 * @param WC_Coupon|null  $coupon Coupon object.
		 * @param array|null      $values Values.
		 * @return bool           $valid
		 */
		public function validate( $valid = false, $product = null, $coupon = null, $values = null ) {

			$backtrace = wp_list_pluck( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ), 'function' ); // phpcs:ignore

			// If coupon is already invalid, no need for further checks.
			// Ignore this check if the discount type is a non-product-type discount.
			if ( true !== $valid && ! in_array( 'handle_non_product_type_coupons', $backtrace, true ) ) {
				return $valid;
			}

			if ( empty( $product ) || empty( $coupon ) ) {
				return $valid;
			}

			if ( $this->is_wc_gte_30() ) {
				$coupon_id = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
			} else {
				$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
			}

			if ( ! empty( $coupon_id ) ) {

				$product_attribute_ids         = get_post_meta( $coupon_id, 'wc_sc_product_attribute_ids', true );
				$exclude_product_attribute_ids = get_post_meta( $coupon_id, 'wc_sc_exclude_product_attribute_ids', true );

				if ( ! empty( $product_attribute_ids ) || ! empty( $exclude_product_attribute_ids ) ) {
					$current_product_attribute_ids = $this->get_product_attributes( $product );
					if ( ! empty( $product_attribute_ids ) ) {
						$product_attribute_ids = explode( '|', $product_attribute_ids );
					}
					$product_attribute_found = true;
					if ( ! empty( $product_attribute_ids ) && is_array( $product_attribute_ids ) ) {
						$common_attribute_ids = array_intersect( $product_attribute_ids, $current_product_attribute_ids );
						if ( count( $common_attribute_ids ) > 0 ) {
							$product_attribute_found = true;
						} else {
							$product_attribute_found = false;
						}
					}

					if ( ! empty( $exclude_product_attribute_ids ) ) {
						$exclude_product_attribute_ids = explode( '|', $exclude_product_attribute_ids );
					}

					$exclude_attribute_found = false;
					if ( ! empty( $exclude_product_attribute_ids ) && is_array( $exclude_product_attribute_ids ) ) {
						$common_exclude_attribute_ids = array_intersect( $exclude_product_attribute_ids, $current_product_attribute_ids );
						if ( count( $common_exclude_attribute_ids ) > 0 ) {
							$exclude_attribute_found = true;
						} else {
							$exclude_attribute_found = false;
						}
					}

					$valid = ( $product_attribute_found && ! $exclude_attribute_found ) ? true : false;
				}
			}

			return $valid;
		}

		/**
		 * Function to get product attributes of a given product.
		 *
		 * @param  WC_Product $product Product.
		 * @return array  $product_attributes_ids IDs of product attributes
		 */
		public function get_product_attributes( $product = null ) {

			$product_attributes_ids = array();

			if ( ! is_a( $product, 'WC_Product' ) ) {
				// Check if product id has been passed.
				if ( is_numeric( $product ) ) {
					$product = wc_get_product( $product );
				}
			}

			if ( ! is_a( $product, 'WC_Product' ) ) {
				return $product_attribute_ids;
			}

			$product_attributes = $product->get_attributes();
			if ( ! empty( $product_attributes ) ) {
				if ( true === $product->is_type( 'variation' ) ) {
					foreach ( $product_attributes as $variation_taxonomy => $variation_slug ) {
						$variation_attribute = get_term_by( 'slug', $variation_slug, $variation_taxonomy );
						if ( is_object( $variation_attribute ) ) {
							$product_attributes_ids[] = $variation_attribute->term_id;
						}
					}
				} else {
					$product_id = ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
					if ( ! empty( $product_id ) ) {
						foreach ( $product_attributes as $attribute ) {
							if ( isset( $attribute['is_taxonomy'] ) && ! empty( $attribute['is_taxonomy'] ) ) {
								$attribute_taxonomy_name = $attribute['name'];
								$product_term_ids        = wc_get_product_terms( $product_id, $attribute_taxonomy_name, array( 'fields' => 'ids' ) );
								if ( ! empty( $product_term_ids ) && is_array( $product_term_ids ) ) {
									foreach ( $product_term_ids as $product_term_id ) {
										$product_attributes_ids[] = $product_term_id;
									}
								}
							}
						}
					}
				}
			}

			return $product_attributes_ids;
		}

		/**
		 * Function to validate non product type coupons against product attribute restriction
		 * We need to remove coupon if it does not pass attribute validation even for single cart item in case of non product type coupons e.g fixed_cart, smart_coupon since these coupon type require all products in the cart to be valid
		 *
		 * @param boolean      $valid Coupon validity.
		 * @param WC_Coupon    $coupon Coupon object.
		 * @param WC_Discounts $discounts Discounts object.
		 * @throws Exception Validation exception.
		 * @return boolean  $valid Coupon validity
		 */
		public function handle_non_product_type_coupons( $valid = true, $coupon = null, $discounts = null ) {

			// If coupon is already invalid, no need for further checks.
			if ( true !== $valid ) {
				return $valid;
			}

			if ( ! is_a( $coupon, 'WC_Coupon' ) ) {
				return $valid;
			}

			if ( $this->is_wc_gte_30() ) {
				$coupon_id     = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
			} else {
				$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
			}

			if ( ! empty( $coupon_id ) ) {
				$product_attribute_ids         = get_post_meta( $coupon_id, 'wc_sc_product_attribute_ids', true );
				$exclude_product_attribute_ids = get_post_meta( $coupon_id, 'wc_sc_exclude_product_attribute_ids', true );
				// If product attributes are not set in coupon, stop further processing and return from here.
				if ( empty( $product_attribute_ids ) && empty( $exclude_product_attribute_ids ) ) {
					return $valid;
				}
			} else {
				return $valid;
			}

			$product_coupon_types = wc_get_product_coupon_types();

			// Proceed if it is non product type coupon.
			if ( ! in_array( $discount_type, $product_coupon_types, true ) ) {
				if ( class_exists( 'WC_Discounts' ) && isset( WC()->cart ) ) {
					$wc_cart           = WC()->cart;
					$wc_discounts      = new WC_Discounts( $wc_cart );
					$items_to_validate = array();
					if ( is_callable( array( $wc_discounts, 'get_items_to_validate' ) ) ) {
						$items_to_validate = $wc_discounts->get_items_to_validate();
					} elseif ( is_callable( array( $wc_discounts, 'get_items' ) ) ) {
						$items_to_validate = $wc_discounts->get_items();
					} elseif ( isset( $wc_discounts->items ) && is_array( $wc_discounts->items ) ) {
						$items_to_validate = $wc_discounts->items;
					}
					if ( ! empty( $items_to_validate ) && is_array( $items_to_validate ) ) {
						$valid_products   = array();
						$invalid_products = array();
						foreach ( $items_to_validate as $item ) {
							$cart_item    = clone $item; // Clone the item so changes to wc_discounts item do not affect the originals.
							$item_product = isset( $cart_item->product ) ? $cart_item->product : null;
							$item_object  = isset( $cart_item->object ) ? $cart_item->object : null;
							if ( ! is_null( $item_product ) && ! is_null( $item_object ) ) {
								if ( $coupon->is_valid_for_product( $item_product, $item_object ) ) {
									$valid_products[] = $item_product;
								} else {
									$invalid_products[] = $item_product;
								}
							}
						}

						// If cart does not have any valid product then throw Exception.
						if ( 0 === count( $valid_products ) ) {
							$error_message = __( 'Sorry, this coupon is not applicable to selected products.', 'woocommerce-smart-coupons' );
							$error_code    = defined( 'E_WC_COUPON_NOT_APPLICABLE' ) ? E_WC_COUPON_NOT_APPLICABLE : 0;
							throw new Exception( $error_message, $error_code );
						} elseif ( count( $invalid_products ) > 0 && ! empty( $exclude_product_attribute_ids ) ) {

							$exclude_product_attribute_ids = explode( '|', $exclude_product_attribute_ids );
							$excluded_products             = array();
							foreach ( $invalid_products as $invalid_product ) {
								$product_attributes = $this->get_product_attributes( $invalid_product );
								if ( ! empty( $product_attributes ) && is_array( $product_attributes ) ) {
									$common_exclude_attribute_ids = array_intersect( $exclude_product_attribute_ids, $product_attributes );
									if ( count( $common_exclude_attribute_ids ) > 0 ) {
										$excluded_products[] = $invalid_product->get_name();
									}
								}
							}

							if ( count( $excluded_products ) > 0 ) {
								// If cart contains any excluded product and it is being excluded from our excluded product attributes then throw Exception.
								/* translators: 1. Singular/plural label for product(s) 2. Excluded product names */
								$error_message = sprintf( __( 'Sorry, this coupon is not applicable to the %1$s: %2$s.', 'woocommerce-smart-coupons' ), _n( 'product', 'products', count( $excluded_products ), 'woocommerce-smart-coupons' ), implode( ', ', $excluded_products ) );
								$error_code    = defined( 'E_WC_COUPON_EXCLUDED_PRODUCTS' ) ? E_WC_COUPON_EXCLUDED_PRODUCTS : 0;
								throw new Exception( $error_message, $error_code );
							}
						}
					}
				}
			}

			return $valid;
		}

		/**
		 * Add meta in export headers
		 *
		 * @param  array $headers Existing headers.
		 * @return array
		 */
		public function export_headers( $headers = array() ) {

			$headers['wc_sc_product_attribute_ids']         = __( 'Product Attributes', 'woocommerce-smart-coupons' );
			$headers['wc_sc_exclude_product_attribute_ids'] = __( 'Exclude Attributes', 'woocommerce-smart-coupons' );

			return $headers;

		}

		/**
		 * Post meta defaults for product attribute ids meta
		 *
		 * @param  array $defaults Existing postmeta defaults.
		 * @return array $defaults Modified postmeta defaults
		 */
		public function postmeta_defaults( $defaults = array() ) {

			$defaults['wc_sc_product_attribute_ids']         = '';
			$defaults['wc_sc_exclude_product_attribute_ids'] = '';

			return $defaults;
		}

		/**
		 * Make meta data of product attribute ids protected
		 *
		 * @param bool   $protected Is protected.
		 * @param string $meta_key The meta key.
		 * @param string $meta_type The meta type.
		 * @return bool $protected
		 */
		public function make_action_meta_protected( $protected = false, $meta_key = '', $meta_type = '' ) {

			$sc_product_attribute_keys = array(
				'wc_sc_product_attribute_ids',
				'wc_sc_exclude_product_attribute_ids',
			);

			if ( in_array( $meta_key, $sc_product_attribute_keys, true ) ) {
				return true;
			}

			return $protected;
		}

		/**
		 * Add product's attribute in coupon meta
		 *
		 * @param  array $data The row data.
		 * @param  array $post The POST values.
		 * @return array Modified data
		 */
		public function generate_coupon_attribute_meta( $data = array(), $post = array() ) {

			$product_attribute_ids = ( isset( $post['wc_sc_product_attribute_ids'] ) ) ? wc_clean( wp_unslash( $post['wc_sc_product_attribute_ids'] ) ) : array(); // phpcs:ignore
			$data['wc_sc_product_attribute_ids'] = implode( '|', $product_attribute_ids ); // Store attribute ids as delimited data instead of serialized data.

			$exclude_product_attribute_ids = ( isset( $post['wc_sc_exclude_product_attribute_ids'] ) ) ? wc_clean( wp_unslash( $post['wc_sc_exclude_product_attribute_ids'] ) ) : array(); // phpcs:ignore
			$data['wc_sc_exclude_product_attribute_ids'] = implode( '|', $exclude_product_attribute_ids ); // Store attribute ids as delimited data instead of serialized data.

			return $data;
		}

		/**
		 * Function to copy product's attribute meta in newly generated coupon
		 *
		 * @param  array $args The arguments.
		 */
		public function copy_coupon_attributes_meta( $args = array() ) {

			$new_coupon_id = ( ! empty( $args['new_coupon_id'] ) ) ? absint( $args['new_coupon_id'] ) : 0;
			$coupon        = ( ! empty( $args['ref_coupon'] ) ) ? $args['ref_coupon'] : false;

			if ( empty( $new_coupon_id ) || empty( $coupon ) ) {
				return;
			}

			if ( $this->is_wc_gte_30() ) {
				$product_attribute_ids         = $coupon->get_meta( 'wc_sc_product_attribute_ids' );
				$exclude_product_attribute_ids = $coupon->get_meta( 'wc_sc_exclude_product_attribute_ids' );
			} else {
				$old_coupon_id                 = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$product_attribute_ids         = get_post_meta( $old_coupon_id, 'wc_sc_product_attribute_ids', true );
				$exclude_product_attribute_ids = get_post_meta( $old_coupon_id, 'wc_sc_exclude_product_attribute_ids', true );
			}
			update_post_meta( $new_coupon_id, 'wc_sc_product_attribute_ids', $product_attribute_ids );
			update_post_meta( $new_coupon_id, 'wc_sc_exclude_product_attribute_ids', $exclude_product_attribute_ids );

		}
	}
}

WC_SC_Coupons_By_Product_Attribute::get_instance();
