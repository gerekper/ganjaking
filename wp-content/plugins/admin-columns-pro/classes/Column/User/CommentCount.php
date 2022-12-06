<?php

namespace ACP\Column\User;

use AC;
use ACP\ConditionalFormat;
use ACP\Sorting;

/**
 * @since 2.0
 */
class CommentCount extends AC\Column\User\CommentCount implements Sorting\Sortable, ConditionalFormat\Formattable {

	use ConditionalFormat\IntegerFormattableTrait;

	public function sorting() {
		return new Sorting\Model\User\CommentCount();
	}

}