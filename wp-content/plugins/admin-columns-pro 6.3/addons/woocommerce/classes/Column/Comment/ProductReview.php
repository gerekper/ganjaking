<?php

namespace ACA\WC\Column\Comment;

use AC;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACP;

/**
 * @since 3.0
 */
class ProductReview extends AC\Column
	implements ACP\Filtering\Filterable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_group( 'woocommerce' )
		     ->set_type( 'column-wc-comment_product_review' )
		     ->set_label( __( 'Product Review', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		if ( ! $this->get_raw_value( $id ) ) {
			return $this->get_empty_char();
		}

		return ac_helper()->icon->yes();
	}

	public function get_raw_value( $id ) {
		return 'product' === get_post_type( get_comment( $id )->comment_post_ID );
	}

	public function filtering() {
		return new Filtering\Comment\ProductReview( $this );
	}

	public function search() {
		return new Search\Comment\ProductReview();
	}
}