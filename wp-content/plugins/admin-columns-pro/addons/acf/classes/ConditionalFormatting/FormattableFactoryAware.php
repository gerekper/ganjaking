<?php

namespace ACA\ACF\ConditionalFormatting;

use ACP;

interface FormattableFactoryAware extends ACP\ConditionalFormat\Formattable {

	public function set_formattable_factory( FormattableFactory $factory );

}