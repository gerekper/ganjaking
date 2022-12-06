<?php

namespace ACP\Column\User;

use AC;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class LastPost extends AC\Column\User\LastPost
	implements Sorting\Sortable, Export\Exportable, Search\Searchable, ConditionalFormat\Formattable {

	public function sorting() {
		return new Sorting\Model\User\MaxPostDate( $this->get_related_post_type(), (array) $this->get_related_post_stati() );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

	public function search() {
		return new Search\Comparison\User\MaxPostDate( $this->get_related_post_type(), (array) $this->get_related_post_stati() );
	}

	public function conditional_format(): ?FormattableConfig {
		$display_property = $this->get_setting( 'post' );

		$formatter = $display_property instanceof AC\Settings\Column\Post && 'date' === $display_property->get_post_property_display()
			? new Formatter\DateFormatter\FormatFormatter()
			: null;

		return new ConditionalFormat\FormattableConfig( $formatter );
	}

}