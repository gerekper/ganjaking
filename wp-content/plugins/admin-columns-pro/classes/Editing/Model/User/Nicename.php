<?php

namespace ACP\Editing\Model\User;

use AC\Column;
use ACP\Editing\Service;

/**
 * @deprecated 5.6
 */
class Nicename extends Service\User\Nicename {

	public function __construct( Column $column ) {
		parent::__construct( $column->get_label() );
	}

}