<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Editing;
use ACP\Editing\Editable;

class Status extends AC\Column\Comment\Status
	implements Editable {

	public function editing() {
		return new Editing\Service\Comment\Status();
	}

}