<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class PageLinks extends Field
	implements Field\PostTypeFilterable, Field\TaxonomyFilterable, Field\Multiple {

	use MultipleTrait,
		PostTypeTrait,
		TaxonomyFilterableTrait;
}