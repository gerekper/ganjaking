<?php

namespace ACA\YoastSeo\Column\Post;

use AC;
use ACP;
use ACP\Sorting\Type\DataType;

class Score extends AC\Column\Meta
	implements ACP\Export\Exportable, ACP\Sorting\Sortable {

	public function __construct() {
		$this->set_original( true )
		     ->set_group( 'yoast-seo' )
		     ->set_type( 'wpseo-score' );
	}

	// The display value is handled by the native column
	public function get_value( $id ) {
		return false;
	}

	public function register_settings() {
		$width = $this->get_setting( 'width' );
		$width->set_default( 63 );
		$width->set_default( 'px', 'width_unit' );
	}

	public function get_meta_key() {
		return '_yoast_wpseo_linkdex';
	}

	public function export() {
		return new ACP\Export\Model\StrippedRawValue( $this );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key(), new DataType( DataType::NUMERIC ) );
	}

}