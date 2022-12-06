<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class BackordersAllowed extends AC\Column\Meta
	implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Filtering\Filterable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-wc-backorders_allowed' );
		$this->set_label( __( 'Backorders Allowed', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_backorders';
	}

	public function get_value( $post_id ) {

		switch ( $this->get_raw_value( $post_id ) ) {
			case 'no' :
				$value = ac_helper()->icon->no( __( 'No' ) );

				break;
			case 'yes' :
				$value = ac_helper()->icon->yes( __( 'Yes' ) );

				break;
			case 'notify' :
				$icon_email = ac_helper()->icon->dashicon( [ 'icon' => 'email-alt' ] );
				$value = ac_helper()->html->tooltip( ac_helper()->icon->yes() . $icon_email, __( 'Yes, but notify customer', 'woocommerce' ) );

				break;
			default :
				$value = $this->get_empty_char();
		}

		return $value;
	}

	public function get_raw_value( $post_id ) {
		return $this->get_backorders( $post_id );
	}

	public function editing() {
		return new Editing\Product\BackordersAllowed();
	}

	public function sorting() {
		return new Sorting\Product\BackordersAllowed();
	}

	public function filtering() {
		return new Filtering\Product\BackordersAllowed( $this );
	}

	public function search() {
		return new Search\Product\BackordersAllowed();
	}

	public function get_backorders( $id ) {
		return wc_get_product( $id )->get_backorders();
	}

}