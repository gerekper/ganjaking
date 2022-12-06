<?php

namespace ACA\YoastSeo\Column\Post;

use AC;
use ACP;
use ACP\Sorting\Type\DataType;

class Readability extends AC\Column\Meta
	implements ACP\Export\Exportable, ACP\Sorting\Sortable {

	public function __construct() {
		$this->set_original( true )
		     ->set_group( 'yoast-seo' )
		     ->set_type( 'wpseo-score-readability' );
	}

	// The display value is handled by the native column
	public function get_value( $id ) {
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function get_meta_key() {
		return '_yoast_wpseo_content_score';
	}

	public function export() {
		return new ACP\Export\Model\StrippedRawValue( $this );
	}

	/**
	 * @inheritDoc
	 */
	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key(), new DataType( DataType::NUMERIC ) );
	}

}