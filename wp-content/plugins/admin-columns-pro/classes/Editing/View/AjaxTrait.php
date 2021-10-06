<?php

namespace ACP\Editing\View;

trait AjaxTrait {

	/**
	 * @param bool $use_ajax
	 *
	 * @return $this
	 */
	public function set_ajax_populate( $use_ajax ) {
		$this->set( 'ajax_populate', (bool) $use_ajax );

		return $this;
	}

}