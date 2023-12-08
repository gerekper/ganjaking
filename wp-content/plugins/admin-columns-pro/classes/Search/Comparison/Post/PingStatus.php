<?php

namespace ACP\Search\Comparison\Post;

class PingStatus extends CommentStatus {

	protected function get_field(): string {
		return 'ping_status';
	}

}