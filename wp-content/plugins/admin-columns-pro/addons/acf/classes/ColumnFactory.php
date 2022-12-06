<?php

namespace ACA\ACF;

use ACA\ACF\Column\Repeater;
use ACA\ACF\Column\Unsupported;

class ColumnFactory {

	/**
	 * @var ColumnInstantiator
	 */
	private $column_initiator;

	/**
	 * @var CloneColumnFactory
	 */
	private $clone_column_factory;

	public function __construct( ColumnInstantiator $column_initiator ) {
		$this->column_initiator = $column_initiator;
		$this->clone_column_factory = new CloneColumnFactory( $this );
	}

	/**
	 * @param array $settings
	 *
	 * @return Column|null
	 */
	public function create( array $settings ) {
		if ( isset( $settings['_clone'] ) ) {
			return $this->clone_column_factory->create( $settings );
		}

		switch ( $settings['type'] ) {
			case FieldType::TYPE_GROUP:
				return ( new GroupColumnFactory( $this ) )->create( $settings );

			case FieldType::TYPE_REPEATER:
				return $this->create_column( new Repeater(), $settings );

			case FieldType::TYPE_BOOLEAN:
			case FieldType::TYPE_BUTTON_GROUP:
			case FieldType::TYPE_CHECKBOX:
			case FieldType::TYPE_COLOR_PICKER:
			case FieldType::TYPE_DATE_PICKER:
			case FieldType::TYPE_DATE_TIME_PICKER:
			case FieldType::TYPE_EMAIL:
			case FieldType::TYPE_FILE:
			case FieldType::TYPE_FLEXIBLE_CONTENT:
			case FieldType::TYPE_GALLERY:
			case FieldType::TYPE_GOOGLE_MAP:
			case FieldType::TYPE_IMAGE:
			case FieldType::TYPE_LINK:
			case FieldType::TYPE_NUMBER:
			case FieldType::TYPE_OEMBED:
			case FieldType::TYPE_PAGE_LINK:
			case FieldType::TYPE_PASSWORD:
			case FieldType::TYPE_POST:
			case FieldType::TYPE_RADIO:
			case FieldType::TYPE_RANGE:
			case FieldType::TYPE_RELATIONSHIP:
			case FieldType::TYPE_SELECT:
			case FieldType::TYPE_TAXONOMY:
			case FieldType::TYPE_TEXT:
			case FieldType::TYPE_TEXTAREA:
			case FieldType::TYPE_TIME_PICKER:
			case FieldType::TYPE_URL:
			case FieldType::TYPE_USER:
			case FieldType::TYPE_WYSIWYG:
				return $this->create_column( new Column(), $settings );
			default:
				return $this->create_column( new Unsupported(), $settings );
		}
	}

	private function create_column( Column $column, array $settings ) {
		$column->set_label( $settings['label'] )
		       ->set_type( $settings['key'] );

		$this->column_initiator->initiate( $column );

		return $column;
	}

}