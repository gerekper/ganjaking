<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 2.0
 */
class Gallery extends AC\Column\Meta
	implements ACP\Editing\Editable {

	public function __construct() {
		$this->set_group( 'woocommerce' )
		     ->set_type( 'column-wc-product-gallery' )
		     ->set_label( __( 'Gallery', 'woocommerce' ) );
	}

	public function get_meta_key() {
		return '_product_image_gallery';
	}

	public function get_raw_value( $id ) {
		return explode( ',', parent::get_raw_value( $id ) );
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\Image() )->set_multiple( true )->set_clear_button( true ),
			new Editing\Storage\Product\Gallery()
		);
	}

	public function register_settings() {
		$this->add_setting( new AC\Settings\Column\Images( $this ) );
	}

}