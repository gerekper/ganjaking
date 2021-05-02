<?php

namespace ACP\ThirdParty\YoastSeo\Column;

use AC;
use ACP\Export;
use ACP\Sorting;
use ACP\Sorting\Type\DataType;

class Readability extends AC\Column\Meta
	implements Export\Exportable, Sorting\Sortable {

	public function __construct() {
		$this->set_original( true )
		     ->set_group( 'yoast-seo' )
		     ->set_type( 'wpseo-score-readability' );
	}

	// The display value is handled by the native column
	public function get_value( $id ) {
		return false;
	}

	public function get_meta_key() {
		return '_yoast_wpseo_content_score';
	}

	public function export() {
		return new Export\Model\StrippedRawValue( $this );
	}

	public function sorting() {
		return ( new Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key(), new DataType( DataType::NUMERIC ) );
	}

}