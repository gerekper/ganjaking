<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class PostObject extends Field
	implements Field\PostTypeFilterable, Field\TaxonomyFilterable, Field\Multiple {

	use MultipleTrait,
		TaxonomyFilterableTrait,
		PostTypeTrait;
}