<?php
/**
 * @package Polylang-WC
 */

/**
 * Exposes the order language in the REST API.
 *
 * @since 1.1
 */
class PLLWC_REST_Order extends PLL_REST_Translated_Object {
	/**
	 * Order language data store.
	 *
	 * @var PLLWC_Order_Language_CPT
	 */
	protected $data_store;

	/**
	 * Constructor.
	 *
	 * @since 1.1
	 */
	public function __construct() {
		add_filter( 'woocommerce_rest_shop_order_object_query', array( $this, 'add_language_query_arg_in_rest' ), 10, 2 );

		if ( empty( PLL()->rest_api ) ) {
			return;
		}

		parent::__construct( PLL()->rest_api, array( 'shop_order' => array( 'filters' => false, 'translations' => false ) ) );

		$this->data_store = PLLWC_Data_Store::load( 'order_language' );
	}

	/**
	 * Adds a `lang` entry to the given array, depending on the language requested in the REST API.
	 * This is used to filter the orders by language in WC's REST route V3 (`/wc/v3/orders`).
	 * Hooked to `woocommerce_rest_{$post_type}_object_query`.
	 *
	 * @see WC_REST_CRUD_Controller::prepare_objects_query()
	 *
	 * @since 1.9
	 *
	 * @param array           $args    Key value array of query var to query value.
	 * @param WP_REST_Request $request The request used.
	 * @return array
	 *
	 * @phpstan-param WP_REST_Request<array{lang?: string}> $request
	 */
	public function add_language_query_arg_in_rest( $args, $request ) {
		$args['lang'] = $request->get_param( 'lang' );
		return $args;
	}

	/**
	 * Returns the object language.
	 *
	 * @since 1.1
	 *
	 * @param array $object Order array.
	 * @return string|false
	 */
	public function get_language( $object ) {
		return $this->data_store->get_language( $object['id'] );
	}

	/**
	 * Sets the object language.
	 *
	 * @since 1.1
	 *
	 * @param string   $lang   Language code.
	 * @param WC_Order $object Instance of WC_Order.
	 * @return bool
	 */
	public function set_language( $lang, $object ) {
		if ( $object instanceof WC_Order ) {
			$this->data_store->set_language( $object->get_id(), $lang );
		}
		return true;
	}
}
