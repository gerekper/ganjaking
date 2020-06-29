<?php

namespace ACP\Editing\Settings;

use AC\View;
use ACP\Editing\Settings;

class Excerpt extends Settings {

	/**
	 * @var string
	 */
	private $editable_type;

	protected function define_options() {
		$managed_options = parent::define_options();

		$managed_options['editable_type'] = 'textarea';

		return $managed_options;
	}

	public function create_view() {
		$view = parent::create_view();

		// Force refresh
		$view->get( 'setting' )->set_attribute( 'data-refresh', 'column' );

		// Sub settings
		if ( $this->is_active() ) {

			$type = $this
				->create_element( 'select', 'editable_type' )
				->set_options( [
						'textarea' => __( 'Textarea', 'codepress-admin-columns' ),
						'text'     => __( 'Text', 'codepress-admin-columns' ),
					]
				);

			$editable_type = new View();
			$editable_type->set( 'label', __( 'Input Type', 'codepress-admin-columns' ) )
			              ->set( 'setting', $type );

			$view->set( 'sections', [ $editable_type ] );
		}

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_editable_type() {
		return $this->editable_type;
	}

	/**
	 * @param string $editable_type
	 *
	 * @return $this
	 */
	public function set_editable_type( $editable_type ) {
		$this->editable_type = $editable_type;

		return $this;
	}

}