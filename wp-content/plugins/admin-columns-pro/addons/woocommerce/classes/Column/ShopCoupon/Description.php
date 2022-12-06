<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACP;

/**
 * @since 1.0
 */
class Description extends AC\Column
	implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'description' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $post_id ) {
		return (string) ac_helper()->post->get_raw_field( 'post_excerpt', $post_id );
	}

	public function editing() {
		return new Editing\ShopCoupon\Description();
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\PostField( 'post_excerpt' );
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Excerpt();
	}

	public function export() {
		return new Export\ShopCoupon\Description( $this );
	}

}