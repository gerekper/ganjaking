<?php

namespace ACA\WC\Column\ProductCategory;

use AC;
use ACA\WC\Export;
use ACP;

class Image extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'thumb' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_meta_key() {
		return 'thumbnail_id';
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			new ACP\Editing\View\Image(),
			new ACP\Editing\Storage\Meta( $this->get_meta_key(), new AC\MetaType( AC\MetaType::TERM ) )
		);
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Image( $this->get_meta_key(), $this->get_meta_type() );
	}

}