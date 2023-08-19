<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Filtering;
use ACP;

/**
 * @since 1.0
 */
class Thumb extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Filtering\Filterable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'thumb' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_meta_key() {
		return '_thumbnail_id';
	}

	public function get_raw_value( $post_id ) {
		return has_post_thumbnail( $post_id ) ? get_post_thumbnail_id( $post_id ) : false;
	}

	public function editing() {
		return new ACP\Editing\Service\Post\FeaturedImage();
	}

	public function filtering() {
		return new Filtering\Product\Thumb( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Post\FeaturedImage( 'product' );
	}

}