<?php

namespace ACP\Editing\Model\User;

use AC\Column;
use ACP\Editing\Service;

/**
 * @deprecated 5.6
 */
class Url extends Service\User\Url {

	public function __construct( Column $column ) {
		parent::__construct( $column->get_label() );
	}

}