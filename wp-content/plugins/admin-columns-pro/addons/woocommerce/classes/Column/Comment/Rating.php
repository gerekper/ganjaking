<?php

namespace ACA\WC\Column\Comment;

use AC;
use ACA\WC\Editing;
use ACA\WC\Filtering;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 3.0
 */
class Rating extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_group( 'woocommerce' )
		     ->set_type( 'column-wc-comment_rating' )
		     ->set_label( __( 'Rating', 'woocommerce' ) );
	}

	public function get_meta_key() {
		return 'rating';
	}

	public function get_value( $id ) {
		$rating = $this->get_raw_value( $id );

		if ( ! $rating ) {
			return $this->get_empty_char();
		}

		return ac_helper()->html->stars( $rating, 5 );
	}

	public function editing() {
		return new Editing\Comment\Rating();
	}

	public function filtering() {
		return new Filtering\Number( $this );
	}

	public function sorting() {
		return new Sorting\Comment\Rating( $this->get_meta_key() );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Number( $this->get_meta_key(), $this->get_meta_type() );
	}

}