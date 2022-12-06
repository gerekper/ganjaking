<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\GlossaryOptions;
use ACA\JetEngine\Field\GlossaryOptionsTrait;
use ACA\JetEngine\Field\Options;
use ACA\JetEngine\Field\OptionsTrait;

class Radio extends Field implements Options, GlossaryOptions {

	use OptionsTrait, GlossaryOptionsTrait;

	const TYPE = 'radio';

}