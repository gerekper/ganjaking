<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class Relationship extends Field
	implements Field\PostTypeFilterable, Field\TaxonomyFilterable, Field\Multiple {

	use PostTypeTrait,
		TaxonomyFilterableTrait;

	public function is_multiple() {
		return true;
	}

}