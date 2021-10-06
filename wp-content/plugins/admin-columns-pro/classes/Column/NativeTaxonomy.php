<?php

namespace ACP\Column;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

class NativeTaxonomy extends AC\Column
	implements Filtering\Filterable, Editing\Editable, Sorting\Sortable, Export\Exportable, Search\Searchable {

	public function __construct() {
		$this->set_original( true );
	}

	// Overwrite the Edit setting with a new dependent setting
	public function register_settings() {
		parent::register_settings();

		$this->add_setting( ( new Editing\Settings\Factory\Taxonomy( $this ) )->create() );
	}

	public function get_taxonomy() {
		return str_replace( 'taxonomy-', '', $this->get_type() );
	}

	public function filtering() {
		return new Filtering\Model\Post\Taxonomy( $this );
	}

	public function editing() {
		return new Editing\Service\Post\Taxonomy( $this->get_taxonomy(), 'on' === $this->get_option( 'enable_term_creation' ) );
	}

	public function sorting() {
		return new Sorting\Model\Post\Taxonomy( $this->get_taxonomy() );
	}

	public function export() {
		return new Export\Model\Post\Taxonomy( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Taxonomy( $this->get_taxonomy() );
	}

}