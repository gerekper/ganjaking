<?php

namespace ACP\Sorting\Model\Post;

use AC;

class CommentCountFactory {

	public function create( $type ) {

		switch ( $type ) {
			case AC\Settings\Column\CommentCount::STATUS_APPROVED :
				return new CommentCount( [ CommentCount::STATUS_APPROVED ] );
			case AC\Settings\Column\CommentCount::STATUS_TRASH :
				return new CommentCount( [ CommentCount::STATUS_TRASH ] );
			case AC\Settings\Column\CommentCount::STATUS_SPAM :
				return new CommentCount( [ CommentCount::STATUS_SPAM ] );
			case AC\Settings\Column\CommentCount::STATUS_PENDING :
				return new CommentCount( [ CommentCount::STATUS_PENDING ] );
			default :
				return new CommentCount( [ CommentCount::STATUS_APPROVED, CommentCount::STATUS_PENDING ] );
		}
	}

}