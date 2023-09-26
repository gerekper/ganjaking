<?php
/**
 * WC_CSP_Condition_Package_Category class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Category in Package Condition.
 *
 * @class    WC_CSP_Condition_Package_Category
 * @version  1.15.0
 */
class WC_CSP_Condition_Package_Category extends WC_CSP_Package_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'category_in_package';
		$this->title                         = __( 'Category', 'woocommerce-conditional-shipping-and-payments' );
		$this->priority                      = 50;
		$this->supported_global_restrictions = array( 'shipping_methods', 'shipping_countries' );
	}

	/**
	 * Categories condition matching relationship. Values 'or' | 'and'.
	 *
	 * @since  1.3.3
	 *
	 * @return string
	 */
	protected function get_term_relationship() {
		return apply_filters( 'woocommerce_csp_package_category_matching_relationship', 'or' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always return false (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		if ( empty( $args[ 'package' ] ) || empty( $args[ 'package' ][ 'contents' ] ) ) {
			return false;
		}

		$category_names = array();

		foreach ( $data[ 'value' ] as $category_id ) {

			$term = get_term_by( 'id', $category_id, 'product_cat' );

			if ( $term ) {
				$category_names[] = $term->name;
			}
		}

		$package_count = $this->get_package_count( $args );
		$categories    = $this->merge_titles( $category_names, array( 'rel' => $this->get_term_relationship() ) );
		$message       = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {

			$product_names = $this->get_condition_violation_subjects( $data, $args );
			$products      = $this->merge_titles( $product_names );

			if ( count( $product_names ) > 4 ) {

				if ( count( $category_names ) > 1 ) {

					if ( 1 === $package_count ) {
						$message = sprintf( __( 'remove all products from the %2$s categories from your cart', 'woocommerce-conditional-shipping-and-payments' ), $products, $categories );
					} else {
						$message = sprintf( __( 'make sure it contains no products from the %2$s categories', 'woocommerce-conditional-shipping-and-payments' ), $products, $categories );
					}

				} else {

					if ( 1 === $package_count ) {
						$message = sprintf( __( 'remove all products from the %2$s category from your cart', 'woocommerce-conditional-shipping-and-payments' ), $products, $categories );
					} else {
						$message = sprintf( __( 'make sure it contains no products from the %2$s category', 'woocommerce-conditional-shipping-and-payments' ), $products, $categories );
					}
				}

			} else {

				if ( count( $category_names ) > 1 ) {

					if ( 1 === $package_count ) {
						$message = sprintf( _x( 'remove %1$s from your cart', 'products in categories', 'woocommerce-conditional-shipping-and-payments' ), $products, $categories );
					} else {
						$message = sprintf( _x( 'remove %1$s from it', 'products in categories', 'woocommerce-conditional-shipping-and-payments' ), $products, $categories );
					}

				} else {

					if ( 1 === $package_count ) {
						$message = sprintf( _x( 'remove %1$s from your cart', 'products in category', 'woocommerce-conditional-shipping-and-payments' ), $products, $categories );
					} else {
						$message = sprintf( _x( 'remove %1$s from it', 'products in category', 'woocommerce-conditional-shipping-and-payments' ), $products, $categories );
					}
				}
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) ) {

			if ( count( $category_names ) > 1 ) {

				if ( 1 === $package_count ) {
					$message = sprintf( __( 'add some products from the %s categories to your cart', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				} else {
					$message = sprintf( __( 'make sure it contains some products from the %s categories', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				}

			} else {

				if ( 1 === $package_count ) {
					$message = sprintf( __( 'add some products from the %s category to your cart', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				} else {
					$message = sprintf( __( 'make sure it contains some products from the %s category', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				}
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in' ) ) ) {

			if ( count( $category_names ) > 1 ) {

				if ( 1 === $package_count ) {
					$message = sprintf( __( 'make sure that your cart doesn\'t only contain products from the %s categories', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				} else {
					$message = sprintf( __( 'make sure it doesn\'t only contain products from the %s categories', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				}

			} else {

				if ( 1 === $package_count ) {
					$message = sprintf( __( 'make sure that your cart doesn\'t only contain products from the %s category', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				} else {
					$message = sprintf( __( 'make sure it doesn\'t only contain products from the %s category', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				}
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-all-in' ) ) ) {

			if ( count( $category_names ) > 1 ) {

				if ( 1 === $package_count ) {
					$message = sprintf( __( 'make sure that your cart contains only products from the %s categories', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				} else {
					$message = sprintf( __( 'make sure it contains only products from the %s categories', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				}

			} else {

				if ( 1 === $package_count ) {
					$message = sprintf( __( 'make sure that your cart contains only products from the %s category', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				} else {
					$message = sprintf( __( 'make sure it contains only products from the %s category', 'woocommerce-conditional-shipping-and-payments' ), $categories );
				}
			}
		}

		return $message;
	}

	/**
	 * Returns condition resolution placeholder.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return array
	 */
	public function get_condition_resolution_placeholder( $data, $args ) {
		return $this->merge_titles( $this->get_condition_violation_subjects( $data, $args ) );
	}

	/**
	 * Returns condition resolution subjects.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return array
	 */
	public function get_condition_violation_subjects( $data, $args ) {

		$subjects = array();

		foreach ( $args[ 'package' ][ 'contents' ] as $cart_item_key => $cart_item ) {

			$product_category_ids = array();

			// WC back-compat.
			if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ) {
				$product_category_ids = $cart_item[ 'data' ]->get_category_ids();
			} else {
				$product_category_terms = get_the_terms( $cart_item[ 'product_id' ], 'product_cat' );
				if ( $product_category_terms && ! is_wp_error( $product_category_terms ) ) {
					$product_category_ids = wp_list_pluck( $product_category_terms, 'term_id' );
				}
			}

			if ( ! empty( $product_category_ids ) ) {

				$matching_category_ids = array();

				foreach ( $product_category_ids as $product_category_id ) {
					if ( in_array( $product_category_id, $data[ 'value' ] ) ) {
						$matching_category_ids[] = $product_category_id;
					}
				}

				$term_relationship = $this->get_term_relationship();
				$found_item        = false;

				if ( 'or' === $term_relationship && ! empty( $matching_category_ids ) ) {
					$found_item = true;
				} elseif ( 'and' === $term_relationship && count( $matching_category_ids ) === count( $data[ 'value' ] ) ) {
					$found_item = true;
				}

				// Only 'in' and 'not-all-in' modifiers can have violations.
				if ( $found_item && $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {
					$subjects[] = $cart_item[ 'data' ]->get_title();
				} elseif ( ! $found_item && $this->modifier_is( $data[ 'modifier' ], array( 'not-all-in' ) ) ) {
					$subjects[] = $cart_item[ 'data' ]->get_title();
				}
			}
		}

		return array_unique( $subjects );
	}

	/**
	 * Evaluate if a condition field is in effect or not.
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restrictions
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		if ( empty( $args[ 'package' ] ) || empty( $args[ 'package' ][ 'contents' ] ) ) {
			return true;
		}

		return $this->check_package( $args[ 'package' ][ 'contents' ], $data[ 'value' ], $data[ 'modifier' ] );
	}

	/**
	 * Checks a package.
	 *
	 * @since  1.3.3
	 *
	 * @param  array   $package_contents
	 * @param  string  $modifier
	 * @return bool
	 */
	protected function check_package( $package_contents, $category_ids, $modifier ) {

		$found_items = $this->modifier_is( $modifier, array( 'all-in', 'not-all-in' ) );

		foreach ( $package_contents as $cart_item_key => $cart_item ) {

			$product_category_ids = array();

			// WC back-compat.
			if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) && isset( $cart_item[ 'data' ] ) && ( $cart_item[ 'data' ] instanceof WC_Product ) ) {
				$product              = $cart_item[ 'variation_id' ] ? wc_get_product( $cart_item[ 'product_id' ] ) : $cart_item[ 'data' ];
				$product_category_ids = $product ? $product->get_category_ids() : array();
			} else {
				$product_category_terms = get_the_terms( $cart_item[ 'product_id' ], 'product_cat' );
				if ( $product_category_terms && ! is_wp_error( $product_category_terms ) ) {
					$product_category_ids = wp_list_pluck( $product_category_terms, 'term_id' );
				}
			}

			if ( ! empty( $product_category_ids ) ) {

				$categories_matching = 0;

				foreach ( $product_category_ids as $product_category_id ) {
					if ( in_array( $product_category_id, $category_ids ) ) {
						$categories_matching++;
					}
				}

				$term_relationship = $this->get_term_relationship();

				if ( $this->modifier_is( $modifier, array( 'in', 'not-in' ) ) ) {

					if ( 'or' === $term_relationship && $categories_matching ) {
						$found_items = true;
					} elseif ( 'and' === $term_relationship && $categories_matching === count( $category_ids ) ) {
						$found_items = true;
					}

					if ( $found_items ) {
						break;
					}

				} elseif ( $this->modifier_is( $modifier, array( 'all-in', 'not-all-in' ) ) ) {

					if ( 'or' === $term_relationship && ! $categories_matching ) {
						$found_items = false;
					} elseif ( 'and' === $term_relationship && $categories_matching !== count( $category_ids ) ) {
						$found_items = false;
					}

					if ( ! $found_items ) {
						break;
					}
				}
			}
		}

		if ( $found_items ) {
			$result = $this->modifier_is( $modifier, array( 'in', 'all-in' ) );
		} else {
			$result = $this->modifier_is( $modifier, array( 'not-in', 'not-all-in' ) );
		}

		return $result;
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		if ( ! empty( $posted_condition_data[ 'value' ] ) ) {
			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = array_map( 'intval', $posted_condition_data[ 'value' ] );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get categories-in-package condition content for global restrictions.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$categories = array();
		$modifier   = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$categories = $condition_data[ 'value' ];
		}

		if ( is_null( self::$product_categories_tree ) ) {
			$product_categories = ( array ) get_terms( 'product_cat', array( 'get' => 'all' ) );
			self::$product_categories_tree = wc_csp_build_taxonomy_tree( $product_categories );
		}

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
						<option value="in" <?php selected( $modifier, 'in', true ); ?>><?php esc_html_e( 'in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-in" <?php selected( $modifier, 'not-in', true ); ?>><?php esc_html_e( 'not in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="all-in" <?php selected( $modifier, 'all-in', true ); ?>><?php esc_html_e( 'all items in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-all-in" <?php selected( $modifier, 'not-all-in', true ); ?>><?php esc_html_e( 'not all items in package', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value][]" class="multiselect sw-select2" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select categories&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>"><?php
					wc_csp_print_taxonomy_tree_options( self::$product_categories_tree, $categories, apply_filters( 'woocommerce_csp_condition_dropdown_options', array(), $this ) );
				?></select>
			</div>
		</div>
		<?php
	}
}
