<?php

namespace ACA\WC\Column\Product;

use AC;
use ACP;

class ProductCat extends AC\Column
	implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Filtering\Filterable, ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_original( true );
		$this->set_type( 'product_cat' );
	}

	public function get_taxonomy() {
		return 'product_cat';
	}

	// Overwrite the Edit setting with a new dependent setting
	public function register_settings() {
		parent::register_settings();

		$this->add_setting( ( new ACP\Editing\Settings\Factory\Taxonomy( $this ) )->create() );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Taxonomy( $this->get_taxonomy() );
	}

	public function editing() {
		return new ACP\Editing\Service\Post\Taxonomy( $this->get_taxonomy(), 'on' === $this->get_option( 'enable_term_creation' ) );
	}

	public function filtering() {
		return new ACP\Filtering\Model\Post\Taxonomy( $this );
	}

	public function export() {
		return new ACP\Export\Model\Post\Taxonomy( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Taxonomy( $this->get_taxonomy() );
	}

}