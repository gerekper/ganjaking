<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\GlossaryOptions;
use ACA\JetEngine\Field\GlossaryOptionsTrait;
use ACA\JetEngine\Field\Multiple;
use ACA\JetEngine\Field\MultipleTrait;
use ACA\JetEngine\Field\Options;
use ACA\JetEngine\Field\OptionsTrait;

class Select extends Field implements Options, GlossaryOptions, Multiple {

	const TYPE = 'select';

	use OptionsTrait,
		GlossaryOptionsTrait,
		MultipleTrait;
}