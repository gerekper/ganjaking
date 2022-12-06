<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

class Taxonomy extends AC\Column\Post\Taxonomy
	implements Sorting\Sortable, Editing\Editable, Filtering\Filterable, Export\Exportable, Search\Searchable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	// Overwrite the Edit setting with a new dependent setting
	public function register_settings() {
		parent::register_settings();

		$this->add_setting( ( new Editing\Settings\Factory\Taxonomy( $this ) )->create() );
	}

	public function sorting() {
		return new Sorting\Model\Post\Taxonomy( $this->get_taxonomy() );
	}

	public function editing() {
		return $this->get_taxonomy() !== null
			? new Editing\Service\Post\Taxonomy( $this->get_taxonomy() ?? '', 'on' === $this->get_option( 'enable_term_creation' ) )
			: false;
	}

	public function filtering() {
		return new Filtering\Model\Post\Taxonomy( $this );
	}

	public function export() {
		return new Export\Model\Post\Taxonomy( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Taxonomy( $this->get_taxonomy() );
	}

}