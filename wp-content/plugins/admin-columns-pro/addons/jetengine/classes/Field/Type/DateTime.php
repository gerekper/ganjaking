<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\TimeStamp;
use ACA\JetEngine\Field\TimestampTrait;

class DateTime extends Field implements TimeStamp {

	const TYPE = 'datetime-local';

	use TimestampTrait;
}