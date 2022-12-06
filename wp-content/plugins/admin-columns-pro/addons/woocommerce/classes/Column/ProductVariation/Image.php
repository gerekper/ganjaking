<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACP;

/**
 * @since 3.0
 */
class Image extends ACP\Column\Post\FeaturedImage {

	public function __construct() {
		parent::__construct();

		$this->set_label( null )
		     ->set_type( 'variation_image' )
		     ->set_original( true );
	}

	public function register_settings() {
		parent::register_settings();

		// Change defaults
		$setting = $this->get_setting( 'image' );

		if ( $setting instanceof AC\Settings\Column\Image ) {

			$setting->set_image_size( 'cpac-custom' );
			$setting->set_image_size_h( 40 );
			$setting->set_image_size_w( 40 );
		}
	}

	public function is_valid() {
		return true;
	}

	public function editing() {
		return new ACP\Editing\Service\Post\FeaturedImage();
	}

}