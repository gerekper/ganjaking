<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACP;

class Thumb extends AC\Column\Meta
	implements ACP\Editing\Editable {

	public function __construct() {
		$this->set_type( 'column-wc-variation_thumb' );
		$this->set_label( __( 'Product image', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
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

	public function register_settings() {
		$this->add_setting( new AC\Settings\Column\Image( $this ) );
	}

}