<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Editing;
use ACA\MetaBox\Editing\StorageFactory;
use ACA\MetaBox\Sorting;
use ACP\ConditionalFormat\FilteredHtmlFormatTrait;
use ACP\ConditionalFormat\Formattable;
use ACP\Sorting\Sortable;

class AdvancedTaxonomies extends Taxonomies implements Sortable, Formattable {

	use FilteredHtmlFormatTrait;

	public function sorting() {
		return ( new Sorting\Factory\AdvancedTaxonomy() )->create( $this );
	}

	public function editing() {
		return $this->is_clonable()
			? false
			: new Editing\Service\TaxonomiesAdvanced(
				( new StorageFactory() )->create( $this ),
				$this->get_taxonomy()
			);
	}

	public function search() {
		return false;
	}
}