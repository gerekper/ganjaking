<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Export;
use ACA\WC\Field\ShopOrder;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 3.0
 */
class OrderDate extends AC\Column\Meta
	implements ACP\Export\Exportable, ACP\Sorting\Sortable, ACP\Filtering\Filterable, ACP\Search\Searchable {

	/**
	 * @var ShopOrder\OrderDate
	 */
	private $field;

	public function __construct() {
		$this->set_label( 'Date' )
		     ->set_type( 'column-wc-order_date' )
		     ->set_group( 'woocommerce' );
	}

	public function register_settings() {
		$this->add_setting( new Settings\ShopOrder\OrderDate( $this ) );
	}

	public function get_meta_key() {
		if ( ! $this->get_field() ) {
			return false;
		}

		return $this->get_field()->get_meta_key();
	}

	public function export() {
		$field = $this->get_field();

		if ( $field instanceof ACP\Export\Exportable ) {
			return $field->export();
		}

		return false;
	}

	public function sorting() {
		$field = $this->get_field();

		if ( $field instanceof ACP\Sorting\Sortable ) {
			return $field->sorting();
		}

		return new ACP\Sorting\Model\Disabled();
	}

	public function filtering() {
		$field = $this->get_field();

		if ( $field instanceof ACP\Filtering\Filterable ) {
			return $field->filtering();
		}

		return new ACP\Filtering\Model\Disabled( $this );
	}

	public function search() {
		$field = $this->get_field();

		if ( $field instanceof ACP\Search\Searchable ) {
			return $field->search();
		}

		return false;
	}

	private function set_field() {
		$type = $this->get_setting( 'date_type' )->get_value();

		foreach ( $this->get_fields() as $field ) {
			/** @var ShopOrder\OrderDate $field */
			if ( $field->get_key() === $type ) {
				$this->field = $field;
			}
		}
	}

	/**
	 * @return ShopOrder\OrderDate|false
	 */
	public function get_field() {
		if ( null === $this->field ) {
			$this->set_field();
		}

		return $this->field;
	}

	/**
	 * @return OrderDate[]
	 */
	public function get_fields() {
		return [
			new ShopOrder\OrderDate\Completed( $this ),
			new ShopOrder\OrderDate\Created( $this ),
			new ShopOrder\OrderDate\Modified( $this ),
			new ShopOrder\OrderDate\Paid( $this ),
		];
	}

}