<?php

namespace ACA\BbPress\Column\Topic;

use AC;
use ACA\BbPress\Editing;
use ACP\Editing\Editable;

class Forum extends AC\Column
	implements Editable {

	public function __construct() {
		$this->set_original( true );
		$this->set_type( 'bbp_topic_forum' );
	}

	public function editing() {
		return new Editing\Service\Topic\Forum();
	}

	public function is_valid() {
		return 'topic' === $this->get_post_type();
	}

}