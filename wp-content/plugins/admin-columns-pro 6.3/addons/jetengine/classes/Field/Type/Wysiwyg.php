<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\DefaultValue;
use ACA\JetEngine\Field\DefaultValueTrait;
use ACA\JetEngine\Field\Field;

class Wysiwyg extends Field implements DefaultValue {

	const TYPE = 'wysiwyg';

	use DefaultValueTrait;
}