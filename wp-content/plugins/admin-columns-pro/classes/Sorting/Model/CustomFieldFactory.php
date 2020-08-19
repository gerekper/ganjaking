<?php

namespace ACP\Sorting\Model;

use AC\Column;
use AC\Settings\Column\CustomFieldType;
use AC\Settings\Column\Post;
use ACP\Settings\Column\User;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Type\DataType;

class CustomFieldFactory {

	/**
	 * @param string             $type
	 * @param string             $meta_type
	 * @param string             $meta_key
	 * @param Column\CustomField $column
	 *
	 * @return AbstractModel
	 */
	public static function create( $type, $meta_type, $meta_key, Column\CustomField $column ) {

		switch ( $type ) {

			case CustomFieldType::TYPE_ARRAY :
				return new Disabled();
			case CustomFieldType::TYPE_BOOLEAN :
			case CustomFieldType::TYPE_NUMERIC :
				return ( new MetaFactory() )->create( $meta_type, $meta_key, new DataType( DataType::NUMERIC ) );
			case CustomFieldType::TYPE_DATE :
				// $date_type can be `string`, `numeric`, `date` or `datetime`
				$date_type = apply_filters( 'acp/sorting/custom_field/date_type', DataType::DATETIME, $column );

				return ( new MetaFactory() )->create( $meta_type, $meta_key, new DataType( $date_type ) );
			case CustomFieldType::TYPE_POST :
				// only works on single post ID's
				$model = ( new MetaRelatedPostFactory() )->create( $meta_type, $column->get_setting( Post::NAME )->get_value(), $meta_key );

				if ( ! $model ) {
					$model = ( new MetaFormatFactory() )->create( $meta_type, $meta_key, new FormatValue\SettingFormatter( $column->get_setting( Post::NAME ) ) );
				}

				return $model;
			case CustomFieldType::TYPE_USER :
				// only works on single user ID's
				$model = ( new MetaRelatedUserFactory() )->create( $meta_type, $column->get_setting( User::NAME )->get_value(), $meta_key );

				if ( ! $model ) {
					$model = ( new MetaFormatFactory() )->create( $meta_type, $meta_key, new FormatValue\SettingFormatter( $column->get_setting( User::NAME ) ) );
				}

				return $model;
			case CustomFieldType::TYPE_COUNT :
				return ( new MetaCountFactory() )->create( $meta_type, $meta_key );
			case CustomFieldType::TYPE_TEXT :
			case CustomFieldType::TYPE_NON_EMPTY :
			case CustomFieldType::TYPE_IMAGE :
			case CustomFieldType::TYPE_MEDIA :
			case CustomFieldType::TYPE_URL :
			case CustomFieldType::TYPE_COLOR :
			default :
				return ( new MetaFactory() )->create( $meta_type, $meta_key );
		}
	}

}