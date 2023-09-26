<?php

namespace ACA\JetEngine\Search;

use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\Type;
use ACA\JetEngine\Search;
use ACP;

final class ComparisonFactory {

	/**
	 * @param Field  $field
	 * @param string $meta_type
	 *
	 * @return ACP\Search\Comparison|false
	 */
	public function create( Field $field, $meta_type ) {
		switch ( true ) {
			case $field instanceof Type\Number:
				return new ACP\Search\Comparison\Meta\Number( $field->get_name(), $meta_type );

			case $field instanceof Type\ColorPicker:
			case $field instanceof Type\Time:
			case $field instanceof Type\IconPicker:
			case $field instanceof Type\Text:
			case $field instanceof Type\Textarea:
			case $field instanceof Type\Wysiwyg:
				return new ACP\Search\Comparison\Meta\Text( $field->get_name(), $meta_type );

			case $field instanceof Type\Switcher:
				return new ACP\Search\Comparison\Meta\Toggle( $field->get_name(), $meta_type, [ 'true' => __( 'On', 'codepress-admin-columns' ), 'false' => __( 'Off', 'codepress-admin-columns' ) ] );

			case $field instanceof Type\Checkbox:
				return new Search\Comparison\Checkbox( $field->get_name(), $meta_type, $field->get_options(), $field->value_is_array() );

			case $field instanceof Type\Posts:
				return $field->is_multiple()
					? new ACP\Search\Comparison\Meta\Posts( $field->get_name(), $meta_type, $field->get_related_post_types() )
					: new ACP\Search\Comparison\Meta\Post( $field->get_name(), $meta_type, $field->get_related_post_types() );

			case $field instanceof Type\Media:
				return new ACP\Search\Comparison\Meta\Media( $field->get_name(), $meta_type );

			case $field instanceof Type\Radio:
				return new ACP\Search\Comparison\Meta\Select( $field->get_name(), $meta_type, $field->get_options() );

			case $field instanceof Type\Select:
				return $field->is_multiple()
					? new ACP\Search\Comparison\Meta\MultiSelect( $field->get_name(), $meta_type, $field->get_options() )
					: new ACP\Search\Comparison\Meta\Select( $field->get_name(), $meta_type, $field->get_options() );

			case $field instanceof Type\Date:
				return $field->is_timestamp()
					? new ACP\Search\Comparison\Meta\DateTime\Timestamp( $field->get_name(), $meta_type )
					: new ACP\Search\Comparison\Meta\DateTime\ISO( $field->get_name(), $meta_type );

			case $field instanceof Type\DateTime:
				return $field->is_timestamp()
					? new ACP\Search\Comparison\Meta\DateTime\Timestamp( $field->get_name(), $meta_type )
					: false;
		}

		return false;
	}

}