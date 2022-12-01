<?php
/**
 * A class for representing an item in a product catalog.
 *
 * @package WC_Instagram/Product_Catalog/Items
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Product_Catalog_Item class.
 */
class WC_Instagram_Product_Catalog_Item {

	/**
	 * The product object.
	 *
	 * @var WC_Product
	 */
	protected $product;

	/**
	 * The tax location.
	 *
	 * @var array
	 */
	protected $tax_location = array();

	/**
	 * The Google attributes.
	 *
	 * @var array
	 */
	protected $google_attributes;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @throws Exception If the product is not valid.
	 *
	 * @param mixed $the_product Product object or ID.
	 */
	public function __construct( $the_product ) {
		$product = wc_instagram_get_product( $the_product );

		if ( ! $product ) {
			throw new Exception( _x( 'Invalid product.', 'exception message', 'woocommerce-instagram' ) );
		}

		$this->product = $product;
	}

	/**
	 * Gets the product object to work with. Use this method to obtain the postmeta needed in the catalog.
	 *
	 * @since 3.3.0
	 *
	 * @return WC_Product
	 */
	protected function get_target() {
		return $this->product;
	}

	/**
	 * Gets the product object.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_Product
	 */
	public function get_product() {
		return $this->product;
	}

	/**
	 * Gets the specified product property.
	 *
	 * @since 3.0.0
	 *
	 * @param string $prop The product property.
	 * @return mixed The property value. Null on failure.
	 */
	public function get_prop( $prop ) {
		// Not allowed property.
		if ( 'product' === $prop ) {
			return null;
		}

		$rename_props = array(
			'link' => 'permalink',
		);

		if ( array_key_exists( $prop, $rename_props ) ) {
			$prop = $rename_props[ $prop ];
		}

		$value  = null;
		$getter = "get_{$prop}";

		if ( is_callable( array( $this, $getter ) ) ) {
			$value = call_user_func( array( $this, $getter ) );
		} elseif ( is_callable( array( $this->get_product(), $getter ) ) ) {
			$value = call_user_func( array( $this->get_product(), $getter ) );
		}

		return $value;
	}

	/**
	 * Gets the product ID.
	 *
	 * @since 3.0.0
	 *
	 * @param string $format Optional. The product ID format. Default '{product_id}'.
	 * @return string
	 */
	public function get_id( $format = '{product_id}' ) {
		$product_id = $this->parse_format( $format, $this->get_product()->get_id() );

		/**
		 * Filters the product's ID.
		 *
		 * @since 3.0.0
		 *
		 * @param string     $product_id The product ID.
		 * @param string     $format     The product ID format.
		 * @param WC_Product $product    Product object.
		 */
		return apply_filters( 'wc_instagram_product_id', $product_id, $format, $this->get_product() );
	}

	/**
	 * Gets the product MPN.
	 *
	 * @since 3.0.0
	 *
	 * @param string $format Optional. The MPN format. Default '{product_id}'.
	 * @return string
	 */
	public function get_mpn( $format = '{product_id}' ) {
		$mpn = $this->parse_format( $format, $this->get_product()->get_id() );

		/**
		 * Filters the product's MPN.
		 *
		 * @since 3.0.0
		 *
		 * @param string     $mpn     The product MPN number.
		 * @param string     $format  The product MPN format.
		 * @param WC_Product $product Product object.
		 */
		return apply_filters( 'wc_instagram_product_mpn', $mpn, $format, $this->get_product() );
	}

	/**
	 * Gets the product's availability.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_availability() {
		$availability = ( $this->get_product()->is_in_stock() ? 'in stock' : 'out of stock' );

		/**
		 * Filters the product's availability.
		 *
		 * @since 3.1.0
		 *
		 * @param string     $availability  The product's availability.
		 * @param WC_Product $product       Product object.
		 */
		return apply_filters( 'wc_instagram_product_availability', $availability, $this->get_product() );
	}

	/**
	 * Gets the product's condition.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_condition() {
		$condition = $this->get_target()->get_meta( '_instagram_condition' );

		/**
		 * Filters the product's condition.
		 *
		 * @since 3.0.0
		 *
		 * @param string     $condition The product's condition.
		 * @param WC_Product $product   Product object.
		 */
		return apply_filters( 'wc_instagram_product_condition', $condition, $this->get_product() );
	}

	/**
	 * Gets the product's brand.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_brand() {
		$brand = $this->get_target()->get_meta( '_instagram_brand' );

		/**
		 * Filters the product's brand.
		 *
		 * @since 3.0.0
		 *
		 * @param string     $brand   The product's brand.
		 * @param WC_Product $product Product object.
		 */
		return apply_filters( 'wc_instagram_product_brand', $brand, $this->get_product() );
	}

	/**
	 * Gets the Google product category.
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	public function get_google_product_category() {
		$category_id = $this->get_target()->get_meta( '_instagram_google_product_category' );

		// Use the value of the 'product_cat' taxonomy as a fallback.
		if ( ! $category_id ) {
			$product_id = $this->get_target()->get_id();
			$categories = get_the_terms( $product_id, 'product_cat' );

			if ( is_array( $categories ) ) {
				foreach ( $categories as $category ) {
					$category_id = (string) wc_instagram_get_google_product_category_for_term( $category );

					if ( $category_id ) {
						break;
					}
				}
			}
		}

		/**
		 * Filters the product's google_product_category value.
		 *
		 * @since 3.3.0
		 *
		 * @param int        $category_id The Google product category ID.
		 * @param WC_Product $product     Product object.
		 */
		return apply_filters( 'wc_instagram_product_google_product_category', $category_id, $this->get_product() );
	}

	/**
	 * Gets which product images to include in the catalog.
	 *
	 * @since 3.2.0
	 *
	 * @return string
	 */
	public function get_images_option() {
		$option = $this->get_target()->get_meta( '_instagram_images_option' );

		/**
		 * Filters which product images to include in the catalog.
		 *
		 * @since 3.2.0
		 *
		 * @param string     $option  The images to include.
		 * @param WC_Product $product Product object.
		 */
		return apply_filters( 'wc_instagram_product_images_option', $option, $this->get_product() );
	}

	/**
	 * Gets the product's image link.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_image_link() {
		$image_id = $this->get_product()->get_image_id();

		if ( ! $image_id && $this->get_product()->get_parent_id() ) {
			$parent_product = wc_get_product( $this->get_product()->get_parent_id() );

			if ( $parent_product ) {
				$image_id = $parent_product->get_image_id();
			}
		}

		$image = ( $image_id ? wp_get_attachment_url( $image_id ) : '' );

		return ( $image ? $image : wc_placeholder_img_src() );
	}

	/**
	 * Gets the additional product's image links.
	 *
	 * @since 3.2.0
	 *
	 * @param int $limit Optional. The maximum number of images to retrieve. Default: 10.
	 * @return array
	 */
	public function get_additional_image_links( $limit = 10 ) {
		$image_ids = $this->get_product()->get_gallery_image_ids();

		if ( count( $image_ids ) > $limit ) {
			$image_ids = array_slice( $image_ids, 0, $limit );
		}

		return array_map( 'wp_get_attachment_url', $image_ids );
	}

	/**
	 * Gets the product's price.
	 *
	 * @since 3.0.0
	 *
	 * @param string $price        Optional. Price type. Default empty.
	 * @param string $tax          Optional. Include tax in price?. Accepts 'excl', 'incl. Default 'excl.
	 * @param array  $tax_location Optional. The tax location. Default: empty.
	 * @return float
	 */
	public function get_price( $price = '', $tax = 'excl', $tax_location = array() ) {
		$product = $this->get_product();
		$getter  = 'get_' . ( $price ? "{$price}_" : '' ) . 'price';

		// Fallback for invalid getter.
		if ( ! method_exists( $product, $getter ) ) {
			$getter = 'get_price';
		}

		$args = array(
			'price' => call_user_func( array( $product, $getter ) ),
		);

		if ( 'incl' === $tax ) {
			$this->set_tax_location( $tax_location );
			add_filter( 'woocommerce_get_tax_location', array( $this, 'mock_tax_location' ) );

			$price = wc_get_price_including_tax( $product, $args );

			remove_filter( 'woocommerce_get_tax_location', array( $this, 'mock_tax_location' ) );
			$this->set_tax_location( array() );
		} else {
			$price = wc_get_price_excluding_tax( $product, $args );
		}

		return $price;
	}

	/**
	 * Gets the Google attributes.
	 *
	 * @since 3.7.0
	 *
	 * @return array
	 */
	public function get_google_attributes() {
		if ( is_null( $this->google_attributes ) ) {
			$this->load_google_attributes();
		}

		return $this->google_attributes;
	}

	/**
	 * Gets a Google attribute by key.
	 *
	 * @since 3.7.0
	 *
	 * @param string $key Attribute key.
	 * @return mixed The attribute value. Null if not found.
	 */
	public function get_google_attribute( $key ) {
		$google_attributes = $this->get_google_attributes();

		return ( isset( $google_attributes[ $key ] ) ? $google_attributes[ $key ] : null );
	}

	/**
	 * Loads the Google attributes.
	 *
	 * @since 3.7.0
	 */
	protected function load_google_attributes() {
		$attributes = $this->get_product()->get_attributes();

		foreach ( $attributes as $name => $attribute ) {
			$attribute_id = ( $attribute instanceof WC_Product_Attribute ? $attribute->get_id() : wc_attribute_taxonomy_id_by_name( $name ) );
			$google_pa    = WC_Instagram_Attribute_Relationships::get_relationship( $attribute_id );

			if ( ! $google_pa ) {
				continue;
			}

			if ( $attribute instanceof WC_Product_Attribute ) {
				$terms = $attribute->get_terms();
			} else {
				$terms = array(
					get_term_by( 'slug', $attribute, $name ),
				);
			}

			if ( empty( $terms ) ) {
				return;
			}

			$has_options = WC_Instagram_Google_Product_Attributes::has_options( $google_pa );
			$values      = array();

			foreach ( $terms as $term ) {
				if ( $term instanceof WP_Term ) {
					$values[] = ( $has_options ? get_term_meta( $term->term_id, 'google_pa_value', true ) : $term->name );
				}
			}

			$this->google_attributes[ $google_pa ] = join( ', ', array_filter( $values ) );
		}
	}

	/**
	 * Sets the tax location.
	 *
	 * @since 3.0.0
	 *
	 * @param array $tax_location The tax location.
	 */
	protected function set_tax_location( $tax_location ) {
		$this->tax_location = $tax_location;
	}

	/**
	 * Mocks the tax location.
	 *
	 * @since 3.0.0
	 *
	 * @param array $location The tax location.
	 * @return array
	 */
	public function mock_tax_location( $location ) {
		return ( ! empty( $this->tax_location ) ? $this->tax_location : $location );
	}

	/**
	 * Parses the format string and replaces the placeholders by the product data.
	 *
	 * @since 3.0.0
	 *
	 * @param string $format  The format to parse.
	 * @param mixed  $default Optional. The default value. Default empty.
	 * @return string
	 */
	protected function parse_format( $format, $default = '' ) {
		$value = str_replace(
			array(
				'{product_id}',
				'{parent_id}',
				'{product_sku}',
			),
			array(
				$this->get_product()->get_id(),
				$this->get_product()->get_parent_id(),
				$this->get_product()->get_sku(),
			),
			$format
		);

		if ( ! $value ) {
			$value = $default;
		}

		/**
		 * Filters the generated value after parsing the format string.
		 *
		 * @since 3.0.0
		 *
		 * @param string     $value   The value of the format string.
		 * @param string     $format  The format to parse.
		 * @param mixed      $default The default value.
		 * @param WC_Product $product Product object.
		 */
		return apply_filters( 'wc_instagram_product_parse_format', $value, $format, $value, $this->get_product() );
	}
}
