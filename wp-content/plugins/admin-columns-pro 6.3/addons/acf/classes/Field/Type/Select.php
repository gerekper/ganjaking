<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class Select extends Field implements Field\Multiple, Field\Choices {

	use MultipleTrait;
	use ChoicesTrait;
}