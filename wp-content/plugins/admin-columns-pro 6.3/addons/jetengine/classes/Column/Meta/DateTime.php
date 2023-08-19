<?php

namespace ACA\JetEngine\Column\Meta;

use ACA\JetEngine\Column;
use ACA\JetEngine\Editing;
use ACA\JetEngine\Field;
use ACA\JetEngine\Search;
use ACA\JetEngine\Sorting;
use ACA\JetEngine\Value\DefaultValueFormatterTrait;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

/**
 * @property Field\Type\DateTime $field
 */
class DateTime extends Column\Meta implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable {

	use Search\SearchableTrait,
		Sorting\SortableTrait,
		Editing\EditableTrait,
		DefaultValueFormatterTrait;

	protected function register_settings() {
		$this->add_setting( new \AC\Settings\Column\Date( $this ) );
	}

	public function conditional_format(): ?FormattableConfig {
		$formatter = $this->field->is_timestamp()
			? new ACP\ConditionalFormat\Formatter\DateFormatter\FormatFormatter( 'U' )
			: new ACP\ConditionalFormat\Formatter\DateFormatter\FormatFormatter( 'Y-m-d\TH:i' );

		return new FormattableConfig( $formatter );
	}

}