<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\DefaultValue;
use ACA\JetEngine\Field\DefaultValueTrait;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\TimeStamp;
use ACA\JetEngine\Field\TimestampTrait;

class Date extends Field implements TimeStamp, DefaultValue {

	const TYPE = 'date';

	use TimestampTrait,
		DefaultValueTrait;
}