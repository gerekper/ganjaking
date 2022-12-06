<?php

namespace ACA\ACF\Sorting\ModelFactory;

use AC;
use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\Sorting\SortingModelFactory;
use ACP\Sorting\FormatValue\SerializedSettingFormatter;
use ACP\Sorting\FormatValue\SettingFormatter;
use ACP\Sorting\Model\Disabled;
use ACP\Sorting\Model\MetaFormatFactory;
use ACP\Sorting\Model\MetaRelatedPostFactory;

class Relation implements SortingModelFactory {

	/**
	 * @var MetaFormatFactory
	 */
	private $meta_format_factory;

	public function __construct() {
		$this->meta_format_factory = new MetaFormatFactory();
	}

	public function create( Field $field, $meta_key, Column $column ) {
		return $field instanceof Field\Multiple && $field->is_multiple()
			? $this->create_multiple_relation_model( $column, $meta_key )
			: $this->create_single_relation_model( $column, $meta_key );
	}

	private function create_single_relation_model( Column $column, $meta_key ) {
		$setting = $column->get_setting( AC\Settings\Column\Post::NAME );

		if ( ! $setting ) {
			return new Disabled();
		}

		$model = ( new MetaRelatedPostFactory() )->create(
			$column->get_meta_type(),
			$setting->get_value(),
			$meta_key
		);

		return $model
			?: $this->meta_format_factory->create(
				$column->get_meta_type(),
				$meta_key,
				new SettingFormatter( $setting )
			);
	}

	private function create_multiple_relation_model( Column $column, $meta_key ) {
		$setting = $column->get_setting( AC\Settings\Column\Post::NAME );

		return $this->meta_format_factory->create(
			$column->get_meta_type(),
			$meta_key,
			new SerializedSettingFormatter( new SettingFormatter( $setting ) )
		);
	}

}