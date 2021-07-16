<?php
/**
 * WooCommerce Checkout Add-Ons
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Display_Rules;

defined( 'ABSPATH' ) or exit;

/**
 * Product Or Category Display Rule Class
 *
 * @since 2.1.0
 */
class Product_Or_Category extends Display_Rule {


	/** @var string the rule type */
	protected $rule_type = 'product_or_category';


	/**
	 * Sets up the rule.
	 *
	 * @since 2.1.0
	 *
	 * @param array $data
	 *
	 * @type array $values
	 */
	public function __construct( $data = [] ) {

		parent::__construct( [
			'property' => ! empty( $data['property'] ) ? $data['property'] : 'product',
			'values'   => ! empty( $data['values'] ) ? $data['values'] : [],
			'tooltip'  => __( 'The add-on will display when any of the selected products or categories are in the cart.', 'woocommerce-checkout-add-ons' ),
			'add_on'   => ! empty( $data['add_on'] ) ? $data['add_on'] : null,
		] );
	}


	/**
	 * Gets property field.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_property_field() {

		return [
			'id'      => 'product_or_category_property',
			'name'    => 'rules[product_or_category][property]',
			'type'    => 'select',
			'style'   => 'width: 150px;',
			'value'   => $this->get_property(),
			'options' => [
				'product'  => __( 'Product in cart', 'woocommerce-checkout-add-ons' ),
				'category' => __( 'Category in cart', 'woocommerce-checkout-add-ons' ),
			],
		];
	}


	/**
	 * Gets form fields.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_fields() {

		$values          = $this->get_values();
		$product_values  = ! empty( $values['product_ids'] ) ? $values['product_ids'] : [];
		$category_values = ! empty( $values['category_ids'] ) ? $values['category_ids'] : [];

		$category_options = self::get_product_categories_for_select();

		return [
			'product_ids' => [
				'id'                => 'product_ids',
				'name'              => 'rules[product_or_category][values][product_ids][]',
				'type'              => 'product_search',
				'class'             => 'wc-product-search',
				'value'             => $product_values,
				'custom_attributes' => [
					'data-multiple'    => 'true',
					'data-action'      => 'woocommerce_json_search_products_and_variations',
					'data-placeholder' => __( 'Search... or leave blank to ignore products', 'woocommerce-checkout-add-ons' ),
				],
			],
			'category_ids' => [
				'id'                => 'category_ids',
				'name'              => 'rules[product_or_category][values][category_ids]',
				'type'              => 'multiselect',
				'class'             => 'rules-multiselect rules-category-ids',
				'style'             => 'width: 300px;',
				'options'           => $category_options,
				'value'             => $category_values,
				'custom_attributes' => [
					'placeholder' => __( 'Search... or leave blank to ignore categories', 'woocommerce-checkout-add-ons' ),
				],
			],
		];
	}


	/**
	 * Gets id and name of product categories for drop-down.
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	private static function get_product_categories_for_select() {

		$category_options = [];
		$categories       = get_terms( [
			'order'    => 'ASC',
			'orderby'  => 'name',
			'taxonomy' => 'product_cat',
		] );

		foreach ( $categories as $category ) {

			$category_options[ $category->term_id ] = $category->name;
		}

		return $category_options;
	}


	/**
	 * Evaluates the rule, based on the cart contents.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function evaluate() {

		$should_display = true;

		$values          = $this->get_values();
		$product_values  = ! empty( $values['product_ids'] ) ? $values['product_ids'] : [];
		$category_values = ! empty( $values['category_ids'] ) ? $values['category_ids'] : [];

		if ( ! empty( $product_values ) && 'product' === $this->get_property() ) {
			$should_display = $this->evaluate_products( $product_values );
		} elseif ( ! empty( $category_values ) && 'category' === $this->get_property() ) {
			$should_display = $this->evaluate_categories( $category_values );
		}

		return $should_display;
	}


	/**
	 * Checks if the cart contain the selected product(s).
	 *
	 * @since 2.1.0
	 *
 	 * @param array $products array of product IDs
	 * @return bool
	 */
	public function evaluate_products( $products ) {

		$should_display = true;

		if ( WC()->cart instanceof \WC_Cart ) {

			$should_display = false;

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				$product = $cart_item['data'];

				if ( ! $product instanceof \WC_Product ) {
					wc_checkout_add_ons()->log( 'Product data not available from Cart! Product display rule will not be evaluated.' );
					continue;
				}

				foreach ( $products as $product_id ) {

					if ( (int) $product_id === $product->get_id() ) {

						// selected product found
						$should_display = true;
						break 2;
					}
				}
			}
		}

		return $should_display;
	}


	/**
	 * Checks if the cart contain the selected categories.
	 *
	 * @since 2.1.0
	 *
	 * @param array $categories array of term IDs
	 * @return bool
	 */
	public function evaluate_categories( $categories ) {

		$should_display = true;

		if ( WC()->cart instanceof \WC_Cart ) {

			$should_display = false;

			// handle children terms
			foreach ( $categories as $category_term_id ) {

				$children_term_ids = get_term_children( $category_term_id, 'product_cat' );

				if ( ! empty( $children_term_ids ) && ! is_wp_error( $children_term_ids ) ) {
					$categories = array_unique( array_merge( $categories, $children_term_ids ) );
				}
			}

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				$product = $cart_item['data'];

				if ( ! $product instanceof \WC_Product ) {
					wc_checkout_add_ons()->log( 'Product data not available from Cart! Category display rule will not be evaluated.' );
					continue;
				}

				$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

				foreach ( $categories as $category_term_id ) {

					if ( has_term( $category_term_id, 'product_cat', $product_id ) ) {

						// selected category found
						$should_display = true;
						break 2;
					}
				}
			}
		}

		return $should_display;
	}


	/**
	 * Gets a human readable description.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_description() {

		$description = '';

		$values          = $this->get_values();
		$product_values  = ! empty( $values['product_ids'] ) ? $values['product_ids'] : [];
		$category_values = ! empty( $values['category_ids'] ) ? $values['category_ids'] : [];

		if ( ! empty( $product_values ) && 'product' === $this->get_property() ) {

			$product_links = [];
			foreach ( $product_values as $product_id ) {
				$product_links[] = '<a href="' . get_permalink( $product_id ) . '">' . get_the_title( $product_id ) . '</a>';
			}

			$product_links = implode( ', ', $product_links );

			$description = sprintf(
				/* translators: Placeholders: %1$s - product links, %2$s - <strong> tag, %3$s - </strong> tag */
				__( '%2$sCart contains%3$s %1$s', 'woocommerce-checkout-add-ons' ),
				$product_links,
				'<strong>',
				'</strong>'
			);

		} elseif ( ! empty( $category_values ) && 'category' === $this->get_property() ) {

			$category_links = [];
			foreach ( $category_values as $category_term_id ) {
				$category         = get_term( $category_term_id, 'product_cat' );
				$category_links[] = '<a href="' . get_term_link( (int) $category_term_id, 'product_cat' ) . '">' . $category->name . '</a>';
			}

			$category_links = implode( ', ', $category_links );

			$description = sprintf(
				/* translators: Placeholders: %1$s - category links, %2$s - <strong> tag, %3$s - </strong> tag */
				__( '%2$sCart contains%3$s %1$s', 'woocommerce-checkout-add-ons' ),
				$category_links,
				'<strong>',
				'</strong>'
			);

		}

		return $description;
	}


}
