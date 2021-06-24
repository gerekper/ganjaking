<?php
/**
 * A class for representing a product catalog.
 *
 * @package WC_Instagram/Product Catalog
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Product_Catalog class.
 */
class WC_Instagram_Product_Catalog extends WC_Instagram_Data {

	/**
	 * Object data.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $data = array(
		'title'                       => '',
		'slug'                        => '',
		'filter_by'                   => '',
		'products_option'             => '',
		'product_cats_option'         => '',
		'product_cats'                => array(),
		'product_types_option'        => '',
		'product_types'               => array(),
		'virtual_products'            => '',
		'downloadable_products'       => '',
		'stock_status'                => '',
		'include_product_ids'         => array(),
		'exclude_product_ids'         => array(),
		'id_format'                   => '{product_id}',
		'group_id_format'             => '{parent_id}',
		'mpn_format'                  => '{product_id}',
		'brand'                       => '',
		'google_product_category'     => '',
		'condition'                   => 'new',
		'images_option'               => 'all',
		'include_variations'          => true,
		'include_currency'            => true,
		'description_field'           => 'description',
		'variation_description_field' => 'description',
		'include_tax'                 => false,
		'tax_location'                => array(),
	);

	/**
	 * The product IDs of this catalog.
	 *
	 * @var array
	 */
	protected $product_ids;

	/**
	 * The products of this catalog.
	 *
	 * @var array
	 */
	protected $products;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $data The catalog data.
	 */
	public function __construct( array $data = array() ) {
		$data = $this->parse_data( $data );

		parent::__construct( $data );
	}

	/**
	 * Parses the catalog data.
	 *
	 * @since 3.0.0
	 *
	 * @param array $data The catalog data.
	 * @return array
	 */
	protected function parse_data( $data ) {
		$data = $this->parse_mpn_format( $data );
		$data = $this->parse_taxes( $data );

		$rename_data = array(
			'product_id'              => 'id_format',
			'product_group_id'        => 'group_id_format',
			'product_brand'           => 'brand',
			'product_google_category' => 'google_product_category',
			'product_condition'       => 'condition',
			'product_images_option'   => 'images_option',
		);

		foreach ( $rename_data as $key => $replacement ) {
			if ( ! empty( $data[ $key ] ) ) {
				$data[ $replacement ] = $data[ $key ];
			}

			unset( $data[ $key ] );
		}

		$bool_data = array( 'include_variations', 'include_currency' );

		foreach ( $bool_data as $key ) {
			if ( isset( $data[ $key ] ) ) {
				$data[ $key ] = wc_string_to_bool( $data[ $key ] );
			}
		}

		return $data;
	}

	/**
	 * Generates the product MPN format.
	 *
	 * @since 3.0.0
	 *
	 * @param array $data The catalog data.
	 * @return array
	 */
	protected function parse_mpn_format( $data ) {
		$mpn_option = ( isset( $data['product_mpn'] ) ? $data['product_mpn'] : 'id' );

		if ( 'sku' === $mpn_option ) {
			$format = '{product_sku}';
		} elseif ( 'custom' === $mpn_option && ! empty( $data['custom_mpn'] ) ) {
			$format = $data['custom_mpn'];
		} else {
			$format = '{product_id}';
		}

		/**
		 * Filters the product MPN format.
		 *
		 * @since 3.0.0
		 *
		 * @param string $format The MPN format.
		 * @param array  $data   The product catalog settings data.
		 */
		$data['mpn_format'] = apply_filters( 'wc_instagram_product_mpn_format', $format, $data );

		unset( $data['product_mpn'], $data['custom_mpn'] );

		return $data;
	}

	/**
	 * Parses the tax data.
	 *
	 * @since 3.0.0
	 *
	 * @param array $data The catalog data.
	 * @return array
	 */
	protected function parse_taxes( $data ) {
		$inc_tax      = ( isset( $data['include_tax'] ) && wc_string_to_bool( $data['include_tax'] ) );
		$tax_location = array();
		$tax_based_on = get_option( 'woocommerce_tax_based_on' );

		if ( $inc_tax && 'base' !== $tax_based_on && ! empty( $data['tax_country'] ) ) {
			$tax_location = array( $data['tax_country'], '', '', '' );
		}

		$data['include_tax']  = $inc_tax;
		$data['tax_location'] = $tax_location;

		unset( $data['tax_country'] );

		return $data;
	}

	/**
	 * Gets the catalog title.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->get_prop( 'title' );
	}

	/**
	 * Gets the catalog slug.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->get_prop( 'slug' );
	}

	/**
	 * Sets the catalog title.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name The catalog title.
	 */
	public function set_title( $name ) {
		$this->set_prop( 'title', $name );

		$slug = $this->get_slug();

		if ( empty( $slug ) ) {
			$this->set_slug( $name );
		}
	}

	/**
	 * Sets the catalog slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name The catalog slug.
	 */
	public function set_slug( $name ) {
		$this->set_prop( 'slug', sanitize_title( $name ) );
	}

	/**
	 * Gets the product ID format.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_id_format() {
		return $this->get_prop( 'id_format' );
	}

	/**
	 * Gets the product group ID format.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_group_id_format() {
		return $this->get_prop( 'group_id_format' );
	}

	/**
	 * Gets the product MPN format.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_mpn_format() {
		return $this->get_prop( 'mpn_format' );
	}

	/**
	 * Gets the field used as the product description.
	 *
	 * @since 3.1.0
	 *
	 * @return string
	 */
	public function get_description_field() {
		return $this->get_prop( 'description_field' );
	}

	/**
	 * Gets the field used as the variation description.
	 *
	 * @since 3.1.0
	 *
	 * @return string
	 */
	public function get_variation_description_field() {
		return $this->get_prop( 'variation_description_field' );
	}

	/**
	 * Gets the brand of the products.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_brand() {
		return $this->get_prop( 'brand' );
	}

	/**
	 * Gets the google_product_category value of the products.
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	public function get_google_product_category() {
		return $this->get_prop( 'google_product_category' );
	}

	/**
	 * Gets the condition of the products.
	 *
	 * @since 3.1.0
	 *
	 * @return string
	 */
	public function get_condition() {
		return $this->get_prop( 'condition' );
	}

	/**
	 * Gets which product images to include in the catalog.
	 *
	 * @since 3.2.0
	 *
	 * @return string
	 */
	public function get_images_option() {
		return $this->get_prop( 'images_option' );
	}

	/**
	 * Gets if to include product variations.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function get_include_variations() {
		return $this->get_prop( 'include_variations' );
	}

	/**
	 * Gets if to include the currency code in prices.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function get_include_currency() {
		return $this->get_prop( 'include_currency' );
	}

	/**
	 * Gets if prices include tax.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function get_include_tax() {
		return $this->get_prop( 'include_tax' );
	}

	/**
	 * Gets the tax location.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_tax_location() {
		return $this->get_prop( 'tax_location' );
	}

	/**
	 * Gets the product IDs of the catalog.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_product_ids() {
		if ( is_null( $this->product_ids ) ) {
			$query             = $this->get_query();
			$this->product_ids = $query->get_products();
		}

		return $this->product_ids;
	}

	/**
	 * Gets the products of the catalog.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_products() {
		if ( is_null( $this->products ) ) {
			$this->products = array_values( array_filter( array_map( 'wc_get_product', $this->get_product_ids() ) ) );
		}

		return $this->products;
	}

	/**
	 * Gets the object for querying the products of the catalog.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_Instagram_Product_Catalog_Query
	 */
	protected function get_query() {
		$data = array_diff_key( $this->get_data(), array_flip( array( 'title', 'slug' ) ) );

		/**
		 * Filters the catalog data used for querying its products.
		 *
		 * @since 3.0.0
		 *
		 * @param array                        $data            The catalog data.
		 * @param WC_Instagram_Product_Catalog $product_catalog Product Catalog object.
		 */
		$args = apply_filters( 'wc_instagram_product_catalog_query_data', $data, $this );

		/*
		 * Force return the product IDs to improve the performance in case we only need the product IDs or
		 * the number of products of this catalog.
		 */
		$args['return'] = 'ids';

		return new WC_Instagram_Product_Catalog_Query( $args );
	}
}
