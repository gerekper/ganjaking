<?php
/**
 * @package Polylang-WC
 */

/**
 * Decorates the Product variation data store.
 *
 * @since 1.6
 */
class PLLWC_Variation_Data_Store_CPT implements WC_Object_Data_Store_Interface {
	/**
	 * The decorated product variation data store.
	 *
	 * @var WC_Object_Data_Store_Interface
	 */
	protected $variation_data_store;

	/**
	 * Product language data store.
	 *
	 * @var PLLWC_Product_Language_CPT
	 */
	protected $language_data_store;

	/**
	 * Language used to filter attributes when reading a product variation.
	 *
	 * @var string|false
	 */
	private $attribute_filter_lang = false;

	/**
	 * Replaces the product variation data store used by WooCommerce by our own.
	 *
	 * @since 1.6
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'woocommerce_product-variation_data_store', array( __CLASS__, 'filter_data_store' ) );
	}

	/**
	 * Decorates the Product variation data store.
	 *
	 * @since 1.6
	 *
	 * @param string|WC_Object_Data_Store_Interface $store A data store object or class name.
	 * @return PLLWC_Variation_Data_Store_CPT
	 */
	public static function filter_data_store( $store ) {
		if ( $store instanceof WC_Object_Data_Store_Interface ) {
			// In case the object has already been decorated by a 3rd party plugin.
			return new self( $store );
		} else {
			return new self( new WC_Product_Variation_Data_Store_CPT() );
		}
	}

	/**
	 * Constructor.
	 *
	 * @since 1.6
	 *
	 * @param WC_Object_Data_Store_Interface $store A data store object to decorate.
	 */
	public function __construct( $store ) {
		$this->variation_data_store = $store;
		$this->language_data_store  = PLLWC_Data_Store::load( 'product_language' );
	}


	/**
	 * Reads a product from the database and sets its data.
	 *
	 * @since 1.6
	 *
	 * @param WC_Product_Variation $product Product object.
	 * @return void
	 */
	public function read( &$product ) {
		$this->attribute_filter_lang = $this->language_data_store->get_language( $product->get_id() );
		add_filter( 'get_terms_args', array( $this, 'get_terms_args' ) );

		$this->variation_data_store->read( $product );

		remove_filter( 'get_terms_args', array( $this, 'get_terms_args' ) );
	}

	/**
	 * Method to create a new product in the database.
	 *
	 * @since 1.6
	 *
	 * @param WC_Product $product Product object.
	 * @return void
	 */
	public function create( &$product ) {
		$this->variation_data_store->create( $product );
	}

	/**
	 * Method to update a product in the database.
	 *
	 * @since 1.6
	 *
	 * @param WC_Product $product Product object.
	 * @return void
	 */
	public function update( &$product ) {
		$this->variation_data_store->update( $product );
	}

	/**
	 * Method to delete a product from the database.
	 *
	 * @since 1.6
	 *
	 * @param WC_Product $product Product object.
	 * @param array      $args    Array of args to pass to the delete method.
	 * @return void
	 */
	public function delete( &$product, $args = array() ) {
		$this->variation_data_store->delete( $product, $args );
	}

	/**
	 * Returns an array of meta for an object.
	 *
	 * @since 1.6
	 *
	 * @param WC_Data $object WC_Data object.
	 * @return array
	 */
	public function read_meta( &$object ) {
		return $this->variation_data_store->read_meta( $object );
	}

	/**
	 * Deletes meta based on meta ID.
	 *
	 * @since 1.6
	 * @param WC_Data  $object WC_Data object.
	 * @param stdClass $meta  (containing at least ->id).
	 */
	public function delete_meta( &$object, $meta ) {
		$this->variation_data_store->delete_meta( $object, $meta );
	}

	/**
	 * Add new piece of meta.
	 *
	 * @since 1.6
	 *
	 * @param WC_Data  $object WC_Data object.
	 * @param stdClass $meta (containing ->key and ->value).
	 * @return int meta ID
	 */
	public function add_meta( &$object, $meta ) {
		return $this->variation_data_store->add_meta( $object, $meta );
	}

	/**
	 * Update meta.
	 *
	 * @since 1.6
	 *
	 * @param WC_Data  $object WC_Data object.
	 * @param stdClass $meta (containing ->id, ->key and ->value).
	 * @return void
	 */
	public function update_meta( &$object, $meta ) {
		$this->variation_data_store->update_meta( $object, $meta );
	}

	/**
	 * Helper method to filter internal meta keys from all meta data rows for the object.
	 *
	 * @since 1.6.1
	 *
	 * @param WC_Data $object        WC_Data object.
	 * @param array   $raw_meta_data Array of std object of meta data to be filtered.
	 * @return mixed|void
	 */
	public function filter_raw_meta_data( &$object, $raw_meta_data ) {
		return $this->variation_data_store->filter_raw_meta_data( $object, $raw_meta_data );
	}

	/**
	 * Delegates the method calls to the decorated object.
	 *
	 * @since 1.6
	 *
	 * @param string $method Method name.
	 * @param array  $args   Method arguments.
	 * @return mixed
	 */
	public function __call( $method, $args ) {
		return call_user_func_array( array( $this->variation_data_store, $method ), $args );
	}

	/**
	 * Ensure that the attribute term retrieved with `get_term_by()` slug is in
	 * the product language when generating the product variation title and summary.
	 *
	 * @since 1.6
	 *
	 * @param array $args The terms query arguments.
	 * @return array
	 */
	public function get_terms_args( $args ) {
		if ( empty( $this->attribute_filter_lang ) ) {
			return $args;
		}

		if ( ! isset( $args['taxonomy'] ) || empty( $args['slug'] ) || count( $args['taxonomy'] ) !== 1 || 0 !== strpos( reset( $args['taxonomy'] ), 'pa_' ) ) {
			return $args;
		}

		// These arguments are all added by `get_term_by()`. Having them all should help us detecting the usage of this function.
		$get_terms_by_args = array(
			'get'                    => 'all',
			'number'                 => 1,
			'update_term_meta_cache' => false,
			'orderby'                => 'none',
		);

		if ( array_diff_assoc( $get_terms_by_args, $args ) ) {
			return $args;
		}

		$args['lang'] = $this->attribute_filter_lang;

		return $args;
	}
}
