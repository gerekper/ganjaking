<?php

namespace ACA\YoastSeo\Column;

use AC;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Export;

abstract class TermMeta extends AC\Column {

	/**
	 * @return string;
	 */
	abstract protected function get_meta_key();

	public function is_valid() {
		return true;
	}

	public function get_raw_value( $id ) {
		$meta_key = $this->get_meta_key();
		$meta = get_option( 'wpseo_taxonomy_meta' );

		if ( ! is_array( $meta ) ) {
			return false;
		}

		return isset( $meta[ $this->get_taxonomy() ][ $id ][ $meta_key ] )
			? $meta[ $this->get_taxonomy() ][ $id ][ $meta_key ]
			: false;
	}

}