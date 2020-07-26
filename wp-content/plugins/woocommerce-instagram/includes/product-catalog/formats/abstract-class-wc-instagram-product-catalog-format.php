<?php
/**
 * A generic class for rendering a product catalog in a specific format.
 *
 * @package WC_Instagram/Product Catalog/Formats
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Product_Catalog_Format class.
 */
abstract class WC_Instagram_Product_Catalog_Format {

	/**
	 * The format used to render the product catalog.
	 *
	 * @var string
	 */
	protected $format = '';

	/**
	 * The charset used to render the product catalog.
	 *
	 * @var string
	 */
	protected $charset = 'UTF-8';

	/**
	 * The currency code.
	 *
	 * @var string
	 */
	protected $currency = '';

	/**
	 * The Product Catalog object.
	 *
	 * @var WC_Instagram_Product_Catalog
	 */
	protected $product_catalog;

	/**
	 * The products items.
	 *
	 * @var array
	 */
	protected $product_items = array();

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @throws Exception If the parameter `$the_catalog` is not valid.
	 *
	 * @param mixed $the_catalog Product catalog object, ID or slug.
	 * @param array $args        Optional. Additional arguments.
	 */
	public function __construct( $the_catalog, $args = array() ) {
		$product_catalog = wc_instagram_get_product_catalog( $the_catalog );

		if ( ! $product_catalog instanceof WC_Instagram_Product_Catalog ) {
			throw new Exception( _x( 'Invalid product catalog.', 'exception message', 'woocommerce-instagram' ) );
		}

		$args = wp_parse_args(
			$args,
			array(
				'charset'  => get_bloginfo( 'charset' ),
				'currency' => get_woocommerce_currency(),
			)
		);

		$this->product_catalog = $product_catalog;
		$this->charset         = $args['charset'];

		if ( $this->product_catalog->get_include_currency() ) {
			$this->currency = $args['currency'];
		}

		$this->load_product_items( $this->product_catalog->get_products() );
	}

	/**
	 * Gets format used to render the product catalog.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_format() {
		return $this->format;
	}

	/**
	 * Gets charset used to render the product catalog.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_charset() {
		return $this->charset;
	}

	/**
	 * Gets the currency code used in prices.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * Gets the Product Catalog object.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_Instagram_Product_Catalog
	 */
	public function get_product_catalog() {
		return $this->product_catalog;
	}

	/**
	 * Gets the product items.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_product_items() {
		return $this->product_items;
	}

	/**
	 * Gets the formatted product catalog.
	 *
	 * @since 3.0.0
	 *
	 * return string
	 */
	public function get_output() {
		return '';
	}

	/**
	 * Loads the product items from the product objects.
	 *
	 * @since 3.0.0
	 *
	 * @param array $products Product list.
	 */
	protected function load_product_items( $products ) {
		$include_variations = $this->get_product_catalog()->get_include_variations();

		foreach ( $products as $product ) {
			try {
				if ( $include_variations && $product instanceof WC_Product_Variable ) {
					$variations = $product->get_available_variations();

					foreach ( $variations as $variation ) {
						$product_variation = wc_get_product( $variation['variation_id'] );

						if ( $product_variation ) {
							$this->product_items[] = new WC_Instagram_Product_Catalog_Item_Variation( $product_variation );
						}
					}
				} else {
					$this->product_items[] = new WC_Instagram_Product_Catalog_Item( $product );
				}
			} catch ( Exception $e ) {
				continue;
			}
		}
	}

	/**
	 * Gets the properties of the catalog item.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Instagram_Product_Catalog_Item $product_item Optional. The catalog item. Default null.
	 * @return array
	 */
	protected function get_item_props( $product_item = null ) {
		$props = array(
			'id'                        => 'esc_attr',
			'item_group_id'             => 'esc_attr',
			'title'                     => 'esc_html',
			'description'               => 'esc_html',
			'link'                      => 'esc_url',
			'image_link'                => 'esc_url',
			'condition'                 => 'esc_attr',
			'availability'              => 'esc_attr',
			'price'                     => 'esc_attr',
			'sale_price'                => 'esc_attr',
			'sale_price_effective_date' => 'esc_html',
			'mpn'                       => 'esc_attr',
			'brand'                     => 'esc_html',
			'additional_image_link'     => 'esc_url',
			'google_product_category'   => 'esc_attr',
		);

		if ( $product_item && ! $product_item instanceof WC_Instagram_Product_Catalog_Item_Variation ) {
			unset( $props['item_group_id'] );
		}

		/**
		 * Filters the properties of the catalog items.
		 *
		 * @since 3.0.0
		 *
		 * @param array                               $props        An array with the item properties.
		 * @param WC_Instagram_Product_Catalog_Item   $product_item The catalog item.
		 * @param WC_Instagram_Product_Catalog_Format $formatter    The product catalog formatter.
		 */
		return apply_filters( 'wc_instagram_product_catalog_item_props', $props, $product_item, $this );
	}

	/**
	 * Gets the value for the specified property of a catalog item.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Instagram_Product_Catalog_Item $product_item The catalog item.
	 * @param string                            $prop         The catalog item property.
	 * @return mixed
	 */
	protected function get_item_value( $product_item, $prop ) {
		$product_catalog = $this->get_product_catalog();

		switch ( $prop ) {
			case 'id':
				$value = $product_item->get_id( $product_catalog->get_id_format() );
				break;
			case 'item_group_id':
				$value = '';

				if ( $product_item instanceof WC_Instagram_Product_Catalog_Item_Variation ) {
					$value = $product_item->get_group_id( $product_catalog->get_group_id_format() );
				}
				break;
			case 'title':
				$value = $product_item->get_prop( 'name' );
				break;
			case 'price':
			case 'sale_price':
				$price        = ( 'sale_price' === $prop ? 'sale' : 'regular' );
				$tax          = ( $product_catalog->get_include_tax() ? 'incl' : 'excl' );
				$tax_location = $product_catalog->get_tax_location();

				$value = '';

				if ( 'sale' !== $price || $product_item->get_product()->is_on_sale() ) {
					$value = $this->format_price( $product_item->get_price( $price, $tax, $tax_location ) );
				}
				break;
			case 'sale_price_effective_date':
				$value = '';

				if ( $product_item->get_product()->is_on_sale() ) {
					$from = $product_item->get_product()->get_date_on_sale_from();
					$to   = $product_item->get_product()->get_date_on_sale_to();

					if ( $from && $to ) {
						$value = sprintf(
							'%1$s/%2$s',
							$this->format_date( $from ),
							$this->format_date( $to )
						);
					}
				}
				break;
			case 'mpn':
				$value = $product_item->get_mpn( $product_catalog->get_mpn_format() );
				break;
			case 'brand':
				$value = ( $product_item->get_brand() ? $product_item->get_brand() : $product_catalog->get_brand() );
				break;
			case 'condition':
				$value = ( $product_item->get_condition() ? $product_item->get_condition() : $product_catalog->get_condition() );
				break;
			case 'google_product_category':
				$value = ( $product_item->get_google_product_category() ? $product_item->get_google_product_category() : $product_catalog->get_google_product_category() );
				break;
			case 'description':
				$value = '';

				if ( $product_item instanceof WC_Instagram_Product_Catalog_Item_Variation ) {
					$desc_field = $product_catalog->get_variation_description_field();

					if ( 0 === strpos( $desc_field, 'parent_' ) ) {
						$parent = $product_item->get_parent();
						$value  = ( 'parent_description' === $desc_field ? $parent->get_description() : $parent->get_short_description() );
					}
				} elseif ( 'short_description' === $product_catalog->get_description_field() ) {
					$value = $product_item->get_product()->get_short_description();
				}

				// Use the 'description' field as a fallback.
				if ( ! $value ) {
					$value = $product_item->get_prop( $prop );
				}

				// Strip HTML tags.
				$value = sanitize_textarea_field( $value );
				break;
			case 'additional_image_link':
				$option = ( $product_item->get_images_option() ? $product_item->get_images_option() : $product_catalog->get_images_option() );
				$value  = ( 'all' === $option ? $product_item->get_additional_image_links() : array() );
				break;
			default:
				$value = $product_item->get_prop( $prop );
				break;
		}

		/**
		 * Filters the value for the specified property of a catalog item.
		 *
		 * @since 3.0.0
		 *
		 * @param mixed                             $value        The catalog item value.
		 * @param string                            $prop         The catalog item property.
		 * @param WC_Instagram_Product_Catalog_Item $product_item The catalog item.
		 */
		return apply_filters( 'wc_instagram_product_catalog_item_value', $value, $prop, $product_item );
	}

	/**
	 * Gets the formatted catalog item.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Instagram_Product_Catalog_Item $product_item The catalog item.
	 * @return array
	 */
	protected function get_formatted_item( $product_item ) {
		$values = array();
		$props  = $this->get_item_props( $product_item );

		foreach ( $props as $prop => $callback ) {
			$value    = $this->get_item_value( $product_item, $prop );
			$callback = ( $callback ? $callback : 'esc_attr' );

			if ( is_array( $value ) ) {
				$values[ $prop ] = array_map( $callback, $value );
			} else {
				$values[ $prop ] = call_user_func( $callback, $value );
			}
		}

		/**
		 * Filters the formatted catalog item.
		 *
		 * @since 3.0.0
		 *
		 * @param array                               $values       The formatted catalog item.
		 * @param WC_Instagram_Product_Catalog_Item   $product_item The catalog item.
		 * @param WC_Instagram_Product_Catalog_Format $formatter    The product catalog formatter.
		 */
		return apply_filters( 'wc_instagram_product_catalog_formatted_item', $values, $product_item, $this );
	}

	/**
	 * Converts the price to a valid format.
	 *
	 * @since 3.0.0
	 *
	 * @param float $price Raw price.
	 * @return string
	 */
	protected function format_price( $price ) {
		$negative = $price < 0;
		$price    = floatval( $negative ? $price * -1 : $price );

		return number_format( $price, 2, '.', '' ) . ( $this->currency ? " {$this->currency}" : '' );
	}

	/**
	 * Converts the date to a valid format.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_DateTime $date Date.
	 * @return string
	 */
	protected function format_date( $date ) {
		if ( ! $date instanceof WC_DateTime ) {
			return '';
		}

		return $date->date_i18n( 'Y-m-d\TH:iO' );
	}
}
