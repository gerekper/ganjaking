<?php

namespace ACA\ACF\Sorting\ModelFactory;

use AC;
use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\Sorting\SortingModelFactory;
use ACP;
use ACP\Sorting\Model\MetaRelatedUserFactory;

class User implements SortingModelFactory {

	/**
	 * @var ACP\Sorting\Model\MetaFormatFactory
	 */
	private $meta_format_factory;

	public function __construct() {
		$this->meta_format_factory = new ACP\Sorting\Model\MetaFormatFactory();
	}

	public function create( Field $field, $meta_key, Column $column ) {
		return $field instanceof Field\Multiple && $field->is_multiple()
			? $this->create_multiple_relation_model( $column, $meta_key )
			: $this->create_single_relation_model( $column, $meta_key );
	}

	private function create_single_relation_model( Column $column, $meta_key ) {
		$setting = $column->get_setting( AC\Settings\Column\User::NAME );

		$model = ( new MetaRelatedUserFactory() )->create(
			$column->get_meta_type(),
			$setting->get_value(),
			$meta_key
		);

		return $model
			?: $this->meta_format_factory->create(
				$column->get_meta_type(),
				$meta_key,
				new ACP\Sorting\FormatValue\SettingFormatter( $setting )
			);
	}

	private function create_multiple_relation_model( Column $column, $meta_key ) {
		$setting = $column->get_setting( AC\Settings\Column\User::NAME );

		return $this->meta_format_factory->create(
			$column->get_meta_type(),
			$meta_key,
			new ACP\Sorting\FormatValue\SerializedSettingFormatter(
				new ACP\Sorting\FormatValue\SettingFormatter( $setting )
			)
		);
	}

}