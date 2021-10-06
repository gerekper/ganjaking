<?php

namespace ACP\Editing\Model\User;

use AC\Column;
use ACP\Editing\Service;

/**
 * @deprecated 5.6
 */
class Email extends Service\User\Email {

	public function __construct( Column $column ) {
		parent::__construct( $column->get_label() );
	}

}