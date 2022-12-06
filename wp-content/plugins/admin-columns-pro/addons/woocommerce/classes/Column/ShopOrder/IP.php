<?php

namespace ACA\WC\Column\ShopOrder;

use ACA\WC\Search;
use ACA\WC\Settings;
use ACP;
use ACP\Filtering;

/**
 * @since 3.0
 */
class IP extends ACP\Column\Meta
	implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-order_ip' )
		     ->set_label( __( 'Customer IP address', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		switch ( $this->get_setting( 'ip_property' )->get_value() ) {
			case 'country':
				$key = '_customer_ip_country';

				break;
			default:
				$key = '_customer_ip_address';
		}

		return $key;
	}

	public function register_settings() {
		$this->add_setting( new Settings\ShopOrder\IP( $this ) );
	}

	public function filtering() {
		if ( '_customer_ip_address' === $this->get_meta_key() ) {
			return new Filtering\Model\Disabled( $this );
		}

		return parent::filtering();
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Text( $this->get_meta_key(), $this->get_meta_type() );
	}

}