<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;

/**
 * @since 1.1
 */
class Upsells extends AC\Column
	implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function __construct() {
		$this->set_group( 'woocommerce' )
		     ->set_type( 'column-wc-upsells' )
		     ->set_label( __( 'Upsells', 'codepress-admin-columns' ) );
	}

	public function get_value( $post_id ) {
		$upsells = [];

		foreach ( $this->get_raw_value( $post_id ) as $id ) {
			$upsells[] = ac_helper()->html->link( get_edit_post_link( $id ), get_the_title( $id ) );
		}

		$upsells = array_filter( $upsells );

		if ( ! $upsells ) {
			return $this->get_empty_char();
		}

		return implode( ', ', $upsells );
	}

	public function get_raw_value( $post_id ) {
		return wc_get_product( $post_id )->get_upsell_ids();
	}

	public function editing() {
		return new Editing\Product\Upsells();
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

	public function search() {
		return new Search\Product\Upsells();
	}

}