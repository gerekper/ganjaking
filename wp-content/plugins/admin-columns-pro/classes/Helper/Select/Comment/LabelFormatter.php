<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Comment;

use WP_Comment;

interface LabelFormatter {

	public function format_label( WP_Comment $comment ): string;

	public function format_label_unique( WP_Comment $comment ): string;

}