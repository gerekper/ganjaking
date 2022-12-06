<?php

namespace ACA\WC\Editing\ShopOrder;

use AC\Column;
use ACP;

class AddressFactory {

	/**
	 * @param string      $address_property
	 * @param Column\Meta $column
	 *
	 * @return ACP\Editing\Service|false
	 */
	public function create( $address_property, Column\Meta $column ) {
		switch ( $address_property ) {
			case '' :
			case 'full_name' :
				return false;

			case 'country' :
				$options = array_merge( [ '' => __( 'None', 'codepress-admin-columns' ) ], WC()->countries->get_countries() );

				return new ACP\Editing\Service\Basic(
					new ACP\Editing\View\Select( $options ),
					new ACP\Editing\Storage\Post\Meta( $column->get_meta_key() )
				);

			default :
				return new ACP\Editing\Service\Basic(
					( new ACP\Editing\View\Text() )->set_clear_button( true ),
					new ACP\Editing\Storage\Post\Meta( $column->get_meta_key() )
				);
		}
	}

}