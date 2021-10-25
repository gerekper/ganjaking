<?php

namespace ACP\Editing\Model\User;

use ACP\Column;
use ACP\Editing\Model;

/**
 * @property Column\User\Language $column
 */
class Language extends Model\Meta {

	public function __construct( Column\User\Language $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		return [
			'type'    => 'select',
			'options' => $this->column->get_language_options(),
		];
	}

}