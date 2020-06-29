<?php

namespace ACP\Editing\Settings;

use AC;
use AC\View;
use ACP;

class BulkEditing extends AC\Settings\Column {

	/**
	 * @return array
	 */
	protected function define_options() {
		return [
			'bulk-editing',
		];
	}

	/**
	 * @return string
	 */
	private function get_instructions() {
		$view = new View();
		$view->set_template( 'tooltip/bulk-editing' );

		return $view->render();
	}

	/**
	 * @return View
	 */
	public function create_view() {
		$view = new View();
		$view->set( 'label', __( 'Bulk Editing', 'codepress-admin-columns' ) )
		     ->set( 'instructions', $this->get_instructions() )
		     ->set( 'setting',
			     sprintf( '<em>%s</em>', $this->get_status_label() )
		     );

		return $view;
	}

	private function get_status_label() {
		return $this->is_enabled() ?
			__( 'Enabled', 'codepress-admin-columns' ) :
			__( 'Disabled', 'codepress-admin-columns' );
	}

	/**
	 * @return bool
	 */
	private function is_enabled() {
		if ( ! $this->column instanceof ACP\Editing\Editable ) {
			return false;
		}

		if ( $this->column->editing() instanceof ACP\Editing\Model\Disabled ) {
			return false;
		}

		return $this->column->editing()->is_bulk_edit_active();
	}

}