<?php

namespace ACA\JetEngine\Settings;

use AC;
use ACA\JetEngine\Column\Meta;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\Type;

final class SettingFactory {

	public function create( Field $field, Meta $column ): array {
		switch ( true ) {
			case $field instanceof Type\Date:
			case $field instanceof Type\DateTime:
				return [ new AC\Settings\Column\Date( $column ) ];
			case $field instanceof Type\Number:
				return [ new AC\Settings\Column\NumberFormat( $column ) ];
			case $field instanceof Type\Textarea:
			case $field instanceof Type\Wysiwyg:
				return [ new AC\Settings\Column\WordLimit( $column ) ];
			case $field instanceof Type\Text:
				return [ new AC\Settings\Column\CharacterLimit( $column ) ];
			case $field instanceof Type\Gallery:
				return [ new AC\Settings\Column\Images( $column ) ];
			case $field instanceof Type\Posts:
				return [ new AC\Settings\Column\Post( $column ) ];
			default:
				return [];
		}
	}

}