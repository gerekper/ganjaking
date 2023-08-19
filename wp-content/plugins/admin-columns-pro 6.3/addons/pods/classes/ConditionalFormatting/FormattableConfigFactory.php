<?php

namespace ACA\Pods\ConditionalFormatting;

use ACA\Pods\Field;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter\DateFormatter;
use ACP\ConditionalFormat\Formatter\FilterHtmlFormatter;
use ACP\ConditionalFormat\Formatter\FloatFormatter;
use ACP\ConditionalFormat\Formatter\IntegerFormatter;
use ACP\ConditionalFormat\Formatter\SanitizedFormatter;
use ACP\ConditionalFormat\Formatter\StringFormatter;

class FormattableConfigFactory {

	public function create( Field $field ): ?FormattableConfig {
		switch ( true ) {
			case $field instanceof Field\Boolean:
			case $field instanceof Field\File:
			case $field instanceof Field\Color:
			case $field instanceof Field\Password:
			case $field instanceof Field\Pick\Media:
				return null;

			case $field instanceof Field\Number:
				return new FormattableConfig( new IntegerFormatter() );

			case $field instanceof Field\Currency:
				return new FormattableConfig( SanitizedFormatter::from_ignore_strings( new FloatFormatter() ) );

			case $field instanceof Field\Date:
				return new FormattableConfig( new DateFormatter\FormatFormatter( 'Y-m-d' ) );

			case $field instanceof Field\Datetime:
				return new FormattableConfig( new DateFormatter\FormatFormatter( 'Y-m-d H:i:s' ) );

			case $field instanceof Field\Wysiwyg:
			case $field instanceof Field\Website:
			case $field instanceof Field\Pick\PostType:
			case $field instanceof Field\Pick\User:
				return new FormattableConfig( new FilterHtmlFormatter( new StringFormatter() ) );

			default:
				return new FormattableConfig();
		}
	}

}