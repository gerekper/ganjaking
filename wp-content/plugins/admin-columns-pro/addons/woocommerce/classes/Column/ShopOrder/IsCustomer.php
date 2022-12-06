<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Search;
use ACP;

/**
 * @since 3.5.1
 */
class IsCustomer extends AC\Column\Meta
	implements ACP\Export\Exportable, ACP\Search\Searchable, ACP\Sorting\Sortable {

	public function __construct() {
		$this->set_label( 'Is Customer' )
		     ->set_type( 'column-wc-order_is_customer' )
		     ->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_customer_user';
	}

	public function get_value( $id ) {
		$raw_value = $this->get_raw_value( $id );

		if ( ! $raw_value ) {
			return ac_helper()->icon->no( __( 'Guest', 'woocommerce' ) );
		}

		$tooltip = sprintf( '#%s %s', $raw_value, get_userdata( $raw_value )->display_name );

		return ac_helper()->icon->yes( $tooltip );
	}

	/**
	 * @return ACP\Export\Model
	 */
	public function export() {
		return new ACP\Export\Model\RawValue( $this );
	}

	public function search() {
		return new Search\ShopOrder\IsCustomer();
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

}