<?php

namespace ACA\ACF\ConditionalFormatting;

use ACA\ACF\Field;
use ACA\ACF\FieldType;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter\DateFormatter;
use ACP\ConditionalFormat\Formatter\FilterHtmlFormatter;
use ACP\ConditionalFormat\Formatter\IntegerFormatter;
use ACP\ConditionalFormat\Formatter\SanitizedFormatter;
use ACP\ConditionalFormat\Formatter\StringFormatter;

class FieldFormattableFactory implements FormattableFactory {

	public function create( Field $field ): ?FormattableConfig {
		$unsupported = [
			FieldType::TYPE_BOOLEAN,
			FieldType::TYPE_COLOR_PICKER,
			FieldType::TYPE_PASSWORD,
			FieldType::TYPE_IMAGE,
			FieldType::TYPE_FILE,
			FieldType::TYPE_IMAGE_CROP,
			FieldType::TYPE_GALLERY,
			FieldType::TYPE_GOOGLE_MAP,
		];

		if ( in_array( $field->get_type(), $unsupported, true ) ) {
			return null;
		}

		switch ( $field->get_type() ) {
			case FieldType::TYPE_NUMBER:
			case FieldType::TYPE_RANGE:
				return new FormattableConfig( new SanitizedFormatter( new IntegerFormatter() ) );
			case FieldType::TYPE_DATE_TIME_PICKER:
				return new FormattableConfig( new DateFormatter\FormatFormatter() );
			case FieldType::TYPE_DATE_PICKER:
				return new FormattableConfig( new DateFormatter\FormatFormatter( 'Ymd' ) );
			case FieldType::TYPE_WYSIWYG:
				return new FormattableConfig( new FilterHtmlFormatter( new StringFormatter() ) );
			default:
				return new FormattableConfig();

		}
	}

}