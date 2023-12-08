<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Categories extends AC\Column\Post\Categories
	implements Sorting\Sortable, Editing\Editable, Export\Exportable, Search\Searchable {

	// Overwrite the Edit setting with a new dependent setting
	public function register_settings() {
		parent::register_settings();

		$this->add_setting( ( new Editing\Settings\Factory\Taxonomy( $this ) )->create() );
	}

	public function sorting() {
		return new Sorting\Model\Post\Taxonomy( $this->get_taxonomy() );
	}

	public function editing() {
		return new Editing\Service\Post\Taxonomy( $this->get_taxonomy(), 'on' === $this->get_option( 'enable_term_creation' ) );
	}

	public function export() {
		return new Export\Model\Post\Taxonomy( $this->get_taxonomy() );
	}

	public function search() {
		return new Search\Comparison\Post\Taxonomy( $this->get_taxonomy() );
	}

}