<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class Url extends Field
	implements Field\Placeholder, Field\DefaultValue {

	use PlaceholderTrait,
		DefaultValueTrait;
}