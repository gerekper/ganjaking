<?php
/**
 * A class for representing a product catalog.
 *
 * @package WC_Instagram/Product_Catalog
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Instagram_Data', false ) ) {
	include_once WC_INSTAGRAM_PATH . 'includes/abstracts/abstract-wc-instagram-data.php';
}

if ( ! trait_exists( 'WC_Instagram_Data_Status', false ) ) {
	include_once WC_INSTAGRAM_PATH . 'includes/traits/trait-wc-instagram-data-status.php';
}

/**
 * WC_Instagram_Product_Catalog class.
 */
class WC_Instagram_Product_Catalog extends WC_Instagram_Data {

	use WC_Instagram_Data_Status;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'instagram_product_catalog';

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
		'status'                      => 'draft',
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
		'default_description'         => '',
		'include_tax'                 => false,
		'tax_location'                => array(),
		'include_stock'               => false,
		'stock_quantity'              => 10,
		'backorder_stock_quantity'    => 0,
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
			if ( isset( $data[ $key ] ) ) {
				$data[ $replacement ] = $data[ $key ];
			}

			unset( $data[ $key ] );
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
		$format     = '';
		$mpn_option = ( isset( $data['product_mpn'] ) ? $data['product_mpn'] : '' );

		if ( 'id' === $mpn_option ) {
			$format = '{product_id}';
		} elseif ( 'sku' === $mpn_option ) {
			$format = '{product_sku}';
		} elseif ( 'custom' === $mpn_option && ! empty( $data['custom_mpn'] ) ) {
			$format = $data['custom_mpn'];
		}

		if ( ! empty( $format ) ) {
			/**
			 * Filters the product MPN format.
			 *
			 * @since 3.0.0
			 *
			 * @param string $format The MPN format.
			 * @param array  $data   The product catalog settings data.
			 */
			$data['mpn_format'] = apply_filters( 'wc_instagram_product_mpn_format', $format, $data );
		}

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
		if ( empty( $data['tax_country'] ) || ! isset( $data['include_tax'] ) ) {
			return $data;
		}

		$inc_tax      = wc_string_to_bool( $data['include_tax'] );
		$tax_location = array();
		$tax_based_on = get_option( 'woocommerce_tax_based_on' );

		if ( $inc_tax && 'base' !== $tax_based_on ) {
			$tax_location = array( $data['tax_country'], '', '', '' );
		}

		$data['tax_location'] = $tax_location;

		unset( $data['tax_country'] );

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting data from the product catalog object.
	|
	*/

	/**
	 * Gets the catalog title.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_title( $context = 'view' ) {
		return $this->get_prop( 'title', $context );
	}

	/**
	 * Gets the catalog slug.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_slug( $context = 'view' ) {
		return $this->get_prop( 'slug', $context );
	}

	/**
	 * Gets the product ID format.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_id_format( $context = 'view' ) {
		return $this->get_prop( 'id_format', $context );
	}

	/**
	 * Gets the product group ID format.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_group_id_format( $context = 'view' ) {
		return $this->get_prop( 'group_id_format', $context );
	}

	/**
	 * Gets the product MPN format.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_mpn_format( $context = 'view' ) {
		return $this->get_prop( 'mpn_format', $context );
	}

	/**
	 * Gets the field used as the product description.
	 *
	 * @since 3.1.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_description_field( $context = 'view' ) {
		return $this->get_prop( 'description_field', $context );
	}

	/**
	 * Gets the field used as the variation description.
	 *
	 * @since 3.1.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_variation_description_field( $context = 'view' ) {
		return $this->get_prop( 'variation_description_field', $context );
	}

	/**
	 * Gets the default description for products without a description.
	 *
	 * @since 4.2.0
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_default_description( $context = 'view' ) {
		return $this->get_prop( 'default_description', $context );
	}

	/**
	 * Gets the brand of the products.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_brand( $context = 'view' ) {
		return $this->get_prop( 'brand', $context );
	}

	/**
	 * Gets the google_product_category value of the products.
	 *
	 * @since 3.3.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_google_product_category( $context = 'view' ) {
		return $this->get_prop( 'google_product_category', $context );
	}

	/**
	 * Gets the condition of the products.
	 *
	 * @since 3.1.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_condition( $context = 'view' ) {
		return $this->get_prop( 'condition', $context );
	}

	/**
	 * Gets which product images to include in the catalog.
	 *
	 * @since 3.2.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_images_option( $context = 'view' ) {
		return $this->get_prop( 'images_option', $context );
	}

	/**
	 * Gets if to include product variations.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return bool
	 */
	public function get_include_variations( $context = 'view' ) {
		return $this->get_prop( 'include_variations', $context );
	}

	/**
	 * Gets if to include the currency code in prices.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return bool
	 */
	public function get_include_currency( $context = 'view' ) {
		return $this->get_prop( 'include_currency', $context );
	}

	/**
	 * Gets if prices include tax.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return bool
	 */
	public function get_include_tax( $context = 'view' ) {
		return $this->get_prop( 'include_tax', $context );
	}

	/**
	 * Gets the tax location.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return array
	 */
	public function get_tax_location( $context = 'view' ) {
		return $this->get_prop( 'tax_location', $context );
	}

	/**
	 * Gets the stock status.
	 *
	 * @since 3.6.0
	 * @since 4.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_stock_status( $context = 'view' ) {
		return $this->get_prop( 'stock_status', $context );
	}

	/**
	 * Gets whether to include the product stock.
	 *
	 * @since 4.1.0
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return bool
	 */
	public function get_include_stock( $context = 'view' ) {
		return $this->get_prop( 'include_stock', $context );
	}

	/**
	 * Gets the stock quantity for products without a defined quantity.
	 *
	 * @since 4.1.0
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return int
	 */
	public function get_stock_quantity( $context = 'view' ) {
		return $this->get_prop( 'stock_quantity', $context );
	}

	/**
	 * Gets the backorder stock quantity.
	 *
	 * @since 4.1.0
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return int
	 */
	public function get_backorder_stock_quantity( $context = 'view' ) {
		return $this->get_prop( 'backorder_stock_quantity', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Methods for setting progressive discount data. These should not update
	| anything in the database itself and should only change what is stored in
	| the class object.
	|
	*/

	/**
	 * Set a collection of props in one go, collect any errors, and return the result.
	 * Only sets using public methods.
	 *
	 * @since 4.0.0
	 *
	 * @param array  $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 * @param string $context In what context to run this.
	 * @return bool|WP_Error
	 */
	public function set_props( $props, $context = 'set' ) {
		return parent::set_props( $this->parse_data( $props ), $context );
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
	 * Sets if the catalog includes product variations.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $include_variations Whether to include product variations.
	 */
	public function set_include_variations( $include_variations ) {
		$this->set_bool_prop( 'include_variations', $include_variations );
	}

	/**
	 * Sets if the catalog should include the currency code in prices.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $include_currency Whether to include the currency code in prices.
	 */
	public function set_include_currency( $include_currency ) {
		$this->set_bool_prop( 'include_currency', $include_currency );
	}

	/**
	 * Sets if the catalog prices include tax.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $include_tax Whether the prices include tax.
	 */
	public function set_include_tax( $include_tax ) {
		$this->set_bool_prop( 'include_tax', $include_tax );
	}

	/**
	 * Sets whether to include the product stock.
	 *
	 * @since 4.1.0
	 *
	 * @param mixed $include_stock Whether to include the product stock.
	 */
	public function set_include_stock( $include_stock ) {
		$this->set_bool_prop( 'include_stock', $include_stock );
	}

	/**
	 * Sets the stock quantity for products without a defined quantity.
	 *
	 * @since 4.1.0
	 *
	 * @param int $quantity The stock quantity.
	 */
	public function set_stock_quantity( $quantity ) {
		if ( ! is_numeric( $quantity ) ) {
			return;
		}

		$this->set_prop( 'stock_quantity', absint( $quantity ) );
	}

	/**
	 * Sets the backorder stock quantity.
	 *
	 * @since 4.1.0
	 *
	 * @param int $quantity The stock quantity.
	 */
	public function set_backorder_stock_quantity( $quantity ) {
		if ( ! is_numeric( $quantity ) ) {
			return;
		}

		$this->set_prop( 'backorder_stock_quantity', absint( $quantity ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Queries
	|--------------------------------------------------------------------------
	|
	| Methods for handling the product queries.
	*/

	/**
	 * Gets the product IDs of the catalog.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_product_ids() {
		if ( is_null( $this->product_ids ) ) {
			$this->product_ids = $this->query( array( 'return' => 'ids' ) );
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
	 * Query products from the catalog.
	 *
	 * @since 4.0.0
	 *
	 * @param array $args Optional. Query args. Default empty.
	 * @return array
	 */
	public function query( $args = array() ) {
		$query = $this->get_query( $args );

		return $query->get_products();
	}

	/**
	 * Gets the object for querying the products from the catalog.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Added parameter `$args`.
	 *
	 * @param array $args Optional. Query args. Default empty.
	 * @return WC_Instagram_Product_Catalog_Query
	 */
	protected function get_query( $args = array() ) {
		$data = $this->get_data_without( array( 'id', 'title', 'slug', 'stock_quantity', 'meta_data' ) );

		/**
		 * Filters the catalog data used for querying its products.
		 *
		 * @since 3.0.0
		 *
		 * @param array                        $data            The catalog data.
		 * @param WC_Instagram_Product_Catalog $product_catalog Product Catalog object.
		 */
		$data = apply_filters( 'wc_instagram_product_catalog_query_data', $data, $this );

		$args = wp_parse_args( $args, $data );

		return new WC_Instagram_Product_Catalog_Query( $args );
	}

	/*
	|--------------------------------------------------------------------------
	| Files
	|--------------------------------------------------------------------------
	|
	| Methods for handling the product catalog files.
	*/

	/**
	 * Gets the product catalog file for the specified format.
	 *
	 * @since 4.0.0
	 *
	 * @param string $format  The file format.
	 * @param string $context Optional. The file context. Default empty.
	 * @return WC_Instagram_Product_Catalog_File|false
	 */
	public function get_file( $format, $context = '' ) {
		if ( ! $this->get_id() ) {
			return false;
		}

		return new WC_Instagram_Product_Catalog_File( $this, $format, $context );
	}

	/**
	 * Gets the status of the catalog file.
	 *
	 * @since 4.0.0
	 *
	 * @param string $format The file format.
	 * @return string
	 */
	public function get_file_status( $format ) {
		$file = $this->get_file( $format );

		return ( $file ? $file->get_status() : '' );
	}

	/**
	 * Sets the status of the catalog file.
	 *
	 * @since 4.0.0
	 *
	 * @param string $format The file format.
	 * @param string $status The file status.
	 */
	public function set_file_status( $format, $status ) {
		$file = $this->get_file( $format );

		if ( $file ) {
			$file->set_status( $status );
		}
	}
}
