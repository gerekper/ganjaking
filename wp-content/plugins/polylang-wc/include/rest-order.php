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
		if ( empty( PLL()->rest_api ) ) {
			return;
		}

		parent::__construct( PLL()->rest_api, array( 'shop_order' => array( 'filters' => false, 'translations' => false ) ) );

		$this->data_store = PLLWC_Data_Store::load( 'order_language' );
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
