<?php

namespace ACA\ACF\ConditionalFormatting;

use ACA\ACF\Field;
use ACP\ConditionalFormat\FormattableConfig;

interface FormattableFactory {

	public function create( Field $field ): ?FormattableConfig;
}