<?php

namespace ACP\Column\Media;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class AlternateText extends AC\Column\Media\AlternateText
	implements Editing\Editable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		return new Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function editing() {
		return new Editing\Model\Media\AlternateText( $this );
	}

	public function search() {
		return new Search\Comparison\Meta\Text( $this->get_meta_key(), AC\MetaType::POST );
	}

}