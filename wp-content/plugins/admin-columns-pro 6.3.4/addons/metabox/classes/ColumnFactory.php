<?php

namespace ACA\MetaBox;

final class ColumnFactory {

	/**
	 * @param array $field_settings
	 *
	 * @return Column|false
	 */
	public function create( array $field_settings ) {
		if ( isset( $field_settings['relationship'] ) && (int) $field_settings['relationship'] === 1 ) {
			return false;
		}

		$column = $this->get_column( $field_settings );

		if ( ! $column ) {
			return false;
		}

		$column->set_label( $field_settings['name'] );
		$column->set_type( $field_settings['id'] );

		return $column;
	}

	/**
	 * @return Column|false
	 */
	private function get_column( $field_settings ) {
		switch ( $field_settings['type'] ) {
			case 'autocomplete':
				return new Column\Autocomplete();
			case 'background':
				return new Column\Background();
			case 'button_group':
				return new Column\ButtonGroup();
			case 'checkbox':
			case 'switch':
				return new Column\Checkbox();
			case 'checkbox_list':
				return new Column\CheckboxList();
			case 'color':
				return new Column\Color();
			case 'date':
				return new Column\Date();
			case 'datetime':
				return new Column\DateTime();
			case 'file':
			case 'file_advanced':
			case 'file_upload':
				return new Column\File();
			case 'fieldset_text':
				return new Column\FieldsetText();
			case 'image':
			case 'image_advanced':
				return new Column\Image();
			case 'single_image':
				return new Column\SingleImage();
			case 'image_select':
				return new Column\ImageSelect();
			case 'key_value':
				return new Column\KeyValue();
			case 'map':
				return new Column\Map();
			case 'osm':
				return new Column\Osm();
			case 'number':
			case 'range':
				return new Column\Number();
			case 'slider':
				return new Column\Slider();
			case 'text':
			case 'tel':
			case 'time':
			case 'email':
			case 'oembed':
			case 'file_input':
				return new Column\Text();
			case 'textarea':
			case 'wysiwyg':
				return new Column\Textarea();
			case 'text_list':
				return new Column\TextList();
			case 'url':
				return new Column\Url();
			case 'video':
				return new Column\Video();
			case 'post':
				return $this->is_multiple( $field_settings )
					? new Column\Posts()
					: new Column\Post();
			case 'taxonomy':
				return $this->is_multiple( $field_settings )
					? new Column\Taxonomies()
					: new Column\Taxonomy();
			case 'taxonomy_advanced':
				return $this->is_multiple( $field_settings )
					? new Column\AdvancedTaxonomies()
					: new Column\AdvancedTaxonomy();
			case 'radio':
			case 'select':
				return new Column\Select();
			case 'select_advanced':
				return new Column\SelectAdvanced();
			case 'user':
				return $this->is_multiple( $field_settings )
					? new Column\Users()
					: new Column\User();
			case 'group':
				return new Column\Group();
			default:
				return false;
		}
	}

	private function is_multiple( $field_settings ) {
		$multiple_setting = isset( $field_settings['multiple'] ) && $field_settings['multiple'];
		$field_type_multiple = in_array( $field_settings['field_type'], [ 'select_tree', 'checkbox_list', 'checkbox_tree' ] );

		return $multiple_setting || $field_type_multiple;
	}

}