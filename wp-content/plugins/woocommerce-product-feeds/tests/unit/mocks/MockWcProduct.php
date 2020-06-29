<?php

/**
 * Mock interface so that MockWcProduct can pretend to be a WC_Product where
 * typehinting requires it.
 */
interface WC_Product {}

/**
 * Mocks out properties / methods to pretend to be a WC_Product during tests.
 */
class MockWcProduct implements WC_Product {

	/**
	 * The product ID.
	 * @var int
	 */
	public $id;

	/**
	 * The product data. Pass in a $data array to the constructor to merge with
	 * the details given here.
	 * @var array
	 */
	public $data = [
		'name'               => '',
		'slug'               => '',
		'date_created'       => null,
		'date_modified'      => null,
		'status'             => false,
		'featured'           => false,
		'catalog_visibility' => 'visible',
		'description'        => '',
		'short_description'  => '',
		'sku'                => '',
		'price'              => '',
		'regular_price'      => '',
		'sale_price'         => '',
		'date_on_sale_from'  => null,
		'date_on_sale_to'    => null,
		'total_sales'        => '0',
		'tax_status'         => 'taxable',
		'tax_class'          => '',
		'manage_stock'       => false,
		'stock_quantity'     => null,
		'stock_status'       => 'instock',
		'backorders'         => 'no',
		'sold_individually'  => false,
		'weight'             => '',
		'length'             => '',
		'width'              => '',
		'height'             => '',
		'upsell_ids'         => array(),
		'cross_sell_ids'     => array(),
		'parent_id'          => 0,
		'reviews_allowed'    => true,
		'purchase_note'      => '',
		'attributes'         => array(),
		'default_attributes' => array(),
		'menu_order'         => 0,
		'virtual'            => false,
		'downloadable'       => false,
		'category_ids'       => array(),
		'tag_ids'            => array(),
		'shipping_class_id'  => 0,
		'downloads'          => array(),
		'image_id'           => '',
		'gallery_image_ids'  => array(),
		'download_limit'     => -1,
		'download_expiry'    => -1,
		'rating_counts'      => array(),
		'average_rating'     => 0,
		'review_count'       => 0,
	];

	/**
	 * The product type.
	 *
	 * @var string
	 */
	public $product_type = 'simple';

	/**
	 * Constructor.
	 *
	 * Set / calculate properties based on data passed in.
	 *
	 * @param int    $id           The product ID.
	 * @param string $product_type The product type.
	 * @param array  $data         Product data. Will be merged with defaults.
	 */
	public function __construct( int $id, string $product_type, array $data ) {
		$this->id           = $id;
		$this->product_type = 'simple';
		$this->data['name'] = $data['title'];

		// Make sure only keys we recognise are present.
		$filtered_data = array_intersect_key( $data, $this->data );
		// Merge the data over our default array.
		$this->data = array_merge( $this->data, $filtered_data );
		\WP_Mock::userFunction( 'get_post_meta', array(
			'args'   => [ $this->id, '_product_image_gallery', true ],
			'return' => sprintf( '%d,%d,%d', 100 + $this->id, 200 + $this->id, 300 + $this->id ),
		) );
		\WP_Mock::userFunction( 'get_children', array(
			'args' => [
				[
					'post_parent'    => $this->id,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => 'ASC',
					'orderby'        => 'menu_order',
				],
			],
			'return' => [
				(object) [ 'ID' => 401 ],
				(object) [ 'ID' => 501 ],
			],
		) );
		\WP_Mock::userFunction( 'wc_get_price_excluding_tax', array(
			'args' => [ $this, Mockery::any() ],
			'return' => function ( $product, $args ) {
				return $args['price'] / 1.20;
			},
		) );
		\WP_Mock::userFunction( 'wc_get_price_including_tax', array(
			'args' => [ $this, Mockery::any() ],
			'return' => function ( $product, $args ) {
				return $args['price'];
			},
		) );
	}

	/**
	 * Return the product type.
	 *
	 * @return string.  The product type.
	 */
	public function get_type() {
		return $this->product_type;
	}

	/**
	 * Return the product ID.
	 *
	 * @return int  The product ID.
	 */
	public function get_id() {
		return $this->id;
	}

	public function get_price_excluding_tax() {
		return $this->data['price'] / 1.20;
	}

	public function get_price_including_tax() {
		return $this->get_price();
	}

	public function has_child() {
		return $this->get_type() === 'variable';
	}

	public function get_title() {
		return $this->get_name();
	}

	public function get_permalink() {
		return 'http://www.example.com/permalink-for-' . $this->id;
	}

	public function is_in_stock() {
		return $this->get_stock_status() === 'instock';
	}

	public function get_image_id() {
		return 100 + $this->id;
	}

	public function mock_set_categories( $terms ) {
		\WP_Mock::userFunction( 'get_the_terms', array(
			'args'   => [ $this->get_id(), 'product_cat' ],
			'return' => $terms,
		) );
	}

	public function mock_set_shipping_classes( $terms ) {
		\WP_Mock::userFunction( 'get_the_terms', array(
			'args'   => [ $this->get_id(), 'product_shipping_class' ],
			'return' => $terms,
		) );
	}

	public function mock_set_gpf_config( $config ) {
		\WP_Mock::userFunction( 'get_post_meta', array(
			'args' => [ $this->get_id(), '_woocommerce_gpf_data', true ],
			'return' => $config,
		) );
	}

	/**
	 * Magic method that allows us to retrieve any property from the data array.
	 *
	 * We look at the method name, and if it matches get_{xxx} then we look
	 * for a key in the data array called {xxx}. If we find out, then its
	 * value is returned. If it is not found, or if the method doesn't match
	 * get_{xxx} then an exception will be thrown.
	 *
	 * @param  string $method  The method name that was called, e.g. get_sku
	 * @param  array  $args    The arguments passed to the method.
	 * @return mixed           The value of the requested property.
	 */
	public function __call( $method, $args ) {
		if ( stripos( $method, 'get_' ) !== 0 ) {
			throw new \Exception( 'Invalid method (' . $method . ') on ' . __CLASS__ );
		}
		$field = str_replace( 'get_', '', $method );
		if ( ! array_key_exists( $field, $this->data ) ) {
			throw new \Exception( 'Invalid property requested (' . $field . ') on ' . __CLASS__ );
		}
		return $this->data[ $field ];
	}
}
