<?php

namespace ACP\ThirdParty\bbPress\Column;

use AC;
use ACP\Editing;
use ACP\ThirdParty\bbPress;

class TopicForum extends AC\Column
	implements Editing\Editable {

	public function __construct() {
		$this->set_original( true );
		$this->set_type( 'bbp_topic_forum' );
	}

	public function editing() {
		return new bbPress\Editing\TopicForum( $this );
	}

	public function is_valid() {
		return 'topic' === $this->get_post_type();
	}

}