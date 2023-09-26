<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Editing\Editable;

class Status extends AC\Column\Comment\Status
	implements Editable, ConditionalFormat\Formattable {

	use ConditionalFormat\ConditionalFormatTrait;

	public function editing() {
		return new Editing\Service\Comment\Status();
	}

}