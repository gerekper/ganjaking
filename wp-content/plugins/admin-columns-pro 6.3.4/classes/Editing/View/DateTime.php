<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class DateTime extends View {

	use WeekstartTrait;

	public function __construct() {
		parent::__construct( 'date_time' );

		$this->set_week_start( 1 );
	}

}