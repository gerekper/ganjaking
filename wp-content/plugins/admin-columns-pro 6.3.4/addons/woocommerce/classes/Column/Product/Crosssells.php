<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;

class Crosssells extends AC\Column
	implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-crosssells' )
		     ->set_label( __( 'Cross Sells', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $post_id ) {
		$crosssells = [];

		foreach ( $this->get_raw_value( $post_id ) as $id ) {
			$crosssells[] = ac_helper()->html->link( get_edit_post_link( $id ), get_the_title( $id ) );
		}

		$value = implode( ', ', array_filter( $crosssells ) );

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		return $value;
	}

	public function get_raw_value( $post_id ) {
		return wc_get_product( $post_id )->get_cross_sell_ids();
	}

	public function editing() {
		return new Editing\Product\Crosssells();
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

	public function search() {
		return new Search\Product\Crosssells();
	}

}