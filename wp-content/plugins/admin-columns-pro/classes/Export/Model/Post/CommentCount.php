<?php

namespace ACP\Export\Model\Post;

use ACP\Column;
use ACP\Export\Service;

class CommentCount implements Service {

	private $column;

	public function __construct( Column\Post\CommentCount $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$setting = $this->column->get_setting_comment_count();

		return $setting
			? (string) $setting->get_comment_count( $id )
			: '';
	}

}