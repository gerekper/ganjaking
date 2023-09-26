<?php

namespace ACA\Types\ConditionalFormatting;

use ACA\Types\Field;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter\DateFormatter;
use ACP\ConditionalFormat\Formatter\FilterHtmlFormatter;
use ACP\ConditionalFormat\Formatter\IntegerFormatter;
use ACP\ConditionalFormat\Formatter\StringFormatter;

class FormattableConfigFactory {

	public function create( Field $field ): ?FormattableConfig {
		switch ( true ) {
			case $field instanceof Field\Colorpicker:
			case $field instanceof Field\Checkbox:
			case $field instanceof Field\Image:
				return null;

			case $field instanceof Field\Number:
			case $field instanceof Field\Audio:
				return new FormattableConfig( new IntegerFormatter() );

			case $field instanceof Field\Url:
			case $field instanceof Field\Video:
			case $field instanceof Field\File:
			case $field instanceof Field\Email:
			case $field instanceof Field\Wysiwyg:
				return new FormattableConfig( new FilterHtmlFormatter( new StringFormatter() ) );

			case $field instanceof Field\Date:
				return new FormattableConfig( new DateFormatter\FormatFormatter( 'U' ) );

			default:
				return new FormattableConfig();
		}
	}
}