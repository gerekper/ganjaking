<?php

namespace ACP\Column\User;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Editing\View\Text;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Nicename extends AC\Column\User\Nicename
	implements Editing\Editable, Export\Exportable, Search\Searchable, Sorting\Sortable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function editing() {
		return new Editing\Service\User\Nicename( ( new Text() )->set_placeholder( $this->get_label() ) );
	}

	public function export() {
		return new Export\Model\User\Nicename();
	}

	public function search() {
		return new Search\Comparison\User\Nicename();
	}

	public function sorting() {
		return new Sorting\Model\User\UserField( 'user_nicename' );
	}

}