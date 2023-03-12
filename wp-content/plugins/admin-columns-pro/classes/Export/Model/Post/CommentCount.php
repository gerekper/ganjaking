<?php

namespace ACP\Export\Model\Post;

use AC;
use ACP\Column;
use ACP\Export\Service;

class CommentCount implements Service {

	private $column;

	public function __construct( Column\Post\CommentCount $column ) {
		$this->column = $column;
	}

	private function get_setting(): ?AC\Settings\Column\CommentCount {
		$setting = $this->column->get_setting( 'comment_count' );

		return $setting instanceof AC\Settings\Column\CommentCount
			? $setting
			: null;
	}

	public function get_value( $id ) {
		$setting = $this->get_setting();

		return $setting
			? (string) $setting->get_comment_count( $id )
			: '';
	}

}