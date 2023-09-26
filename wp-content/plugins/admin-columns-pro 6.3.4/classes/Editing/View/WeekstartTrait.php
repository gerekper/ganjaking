<?php

namespace ACP\Editing\View;

trait WeekstartTrait {

	public function set_week_start( $week_start ) {
		$this->set( 'weekstart', (int) $week_start );

		return $this;
	}

}