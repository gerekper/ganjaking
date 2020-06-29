<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.0
 */
class PageTemplate extends AC\Column\Post\PageTemplate
	implements Filtering\Filterable, Sorting\Sortable, Editing\Editable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Post\PageTemplate( $this->get_post_type(), $this->get_meta_key() );
	}

	public function editing() {
		return new Editing\Model\Post\PageTemplate( $this );
	}

	public function filtering() {
		return new Filtering\Model\Post\PageTemplate( $this );
	}

	public function search() {
		return new Search\Comparison\Post\PageTemplate( $this->get_page_templates() );
	}

}