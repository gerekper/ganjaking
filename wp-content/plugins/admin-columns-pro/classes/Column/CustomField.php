<?php

namespace ACP\Column;

use AC;
use AC\MetaType;
use ACP\ApplyFilter;
use ACP\ApplyFilter\CustomField\StoredDateFormat;
use ACP\Column\CustomField\EditingModelFactory;
use ACP\Column\CustomField\ExportModelFactory;
use ACP\Column\CustomField\FilteringModelFactory;
use ACP\Column\CustomField\SearchComparisonFactory;
use ACP\Editing;
use ACP\Editing\Settings\EditableType;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Settings;
use ACP\Sorting;

class CustomField extends AC\Column\CustomField
	implements Sorting\Sortable, Editing\Editable, Filtering\Filterable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return Sorting\Model\CustomFieldFactory::create( $this->get_field_type(), $this->get_meta_type(), $this->get_meta_key(), $this );
	}

	public function editing() {
		return EditingModelFactory::create( $this->get_field_type(), new Editing\Storage\Meta( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) ), $this );
	}

	public function filtering() {
		return FilteringModelFactory::create( $this->get_field_type(), $this );
	}

	public function search() {
		return SearchComparisonFactory::create( $this->get_field_type(), $this->get_meta_key(), $this->get_meta_type(), [
			'date_format' => ( new StoredDateFormat( $this ) )->apply_filters( Search\Comparison\Meta\DateFactory::FORMAT_DATETIME ),
		] );
	}

	public function export() {
		return ExportModelFactory::create( $this->get_field_type(), $this );
	}

	public function register_settings() {
		$this->add_setting( new Settings\Column\CustomField( $this ) )
		     ->add_setting( new AC\Settings\Column\BeforeAfter( $this ) );

		$unsupported_field_types = EditingModelFactory::unsupported_field_types();

		if ( ! in_array( $this->get_field_type(), $unsupported_field_types ) ) {
			$setting = new Editing\Settings\CustomField( $this );

			if ( in_array( $this->get_field_type(), [ Settings\Column\CustomFieldType::TYPE_DEFAULT, Settings\Column\CustomFieldType::TYPE_TEXT ] ) ) {
				$section = ( new EditableType\Text( $this, EditableType\Text::TYPE_TEXT ) );
				$section->set_values( $this->get_options() );

				$setting->add_section( $section );
			}

			$this->add_setting( $setting );
		}

	}

}