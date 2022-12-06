<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use WC_Order;

/**
 * @since 3.0
 */
abstract class Address extends AC\Column\Meta
	implements ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	/**
	 * @param WC_Order $order
	 *
	 * @return string
	 */
	abstract protected function get_formatted_address( WC_Order $order );

	/**
	 * @return string
	 */
	abstract protected function get_meta_key_prefix();

	/**
	 * @return Settings\Address
	 */
	abstract protected function get_setting_address_object();

	public function conditional_format(): ?FormattableConfig {
		return new FormattableConfig();
	}

	/**
	 * @return false|string
	 */
	protected function get_address_property() {
		$setting = $this->get_setting( 'address_property' );

		if ( ! $setting instanceof Settings\Address ) {
			return false;
		}

		return $setting->get_address_property();
	}

	public function get_meta_key() {
		if ( ! $this->get_address_property() ) {
			return false;
		}

		return $this->get_meta_key_prefix() . $this->get_address_property();
	}

	/**
	 * @return bool
	 */
	protected function is_single_meta_key() {
		return ! in_array( $this->get_address_property(), [ '', 'full_name' ] );
	}

	public function get_raw_value( $id ) {

		switch ( $this->get_address_property() ) {
			case '':
				return $this->get_formatted_address( wc_get_order( $id ) );
			case 'full_name':
				$value = sprintf( '%s %s', get_post_meta( $id, $this->get_meta_key_prefix() . 'first_name', true ), get_post_meta( $id, $this->get_meta_key_prefix() . 'last_name', true ) );

				return trim( $value ) ? $value : $this->get_empty_char();
			default:
				return parent::get_raw_value( $id );
		}
	}

	public function register_settings() {
		$this->add_setting( $this->get_setting_address_object() );
	}

	public function filtering() {
		if ( ! $this->is_single_meta_key() ) {
			return new ACP\Filtering\Model\Disabled( $this );
		}

		return new ACP\Filtering\Model\Meta( $this );
	}

	public function search() {
		if ( ! $this->is_single_meta_key() ) {
			return false;
		}

		return ( new Search\ShopOrder\AddressFactory() )->create( $this->get_address_property(), $this->get_meta_key() );
	}

	public function sorting() {
		if ( ! $this->is_single_meta_key() ) {
			return new ACP\Sorting\Model\Disabled();
		}

		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function editing() {
		return ( new Editing\ShopOrder\AddressFactory() )->create( $this->get_address_property(), $this );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}