<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Editing\Editable;
use ACP\Editing\Model;

class Status extends AC\Column\Comment\Status
	implements Editable {

	public function editing() {
		return new Model\Comment\Status( $this );
	}

}