<?php

namespace ACP\Editing\Model\Site;

use ACP\Column;
use ACP\Editing\Model;

/**
 * @property Column\NetworkSite\Option $column
 */
class Option extends Model {

	public function __construct( Column\NetworkSite\Option $column ) {
		parent::__construct( $column );
	}

	public function save( $id, $value ) {
		switch_to_blog( $id );

		$result = update_option( $this->column->get_option_name(), $value );

		restore_current_blog();

		return $result;
	}

}