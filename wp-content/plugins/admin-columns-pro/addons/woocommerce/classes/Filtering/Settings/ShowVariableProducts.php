<?php

namespace ACA\WC\Filtering\Settings;

use AC;
use ACP;

class ShowVariableProducts extends ACP\Filtering\Settings {

	private $filter_allow_variations;

	protected function define_options() {
		$options = parent::define_options();

		$options['filter_allow_variations'] = 'on';

		return $options;
	}

	/**
	 * @return string
	 */
	public function get_filter_allow_variations() {
		return $this->filter_allow_variations;
	}

	/**
	 * @param string $filter_allow_variations
	 *
	 * @return $this
	 */
	public function set_filter_allow_variations( $filter_allow_variations ) {
		$this->filter_allow_variations = $filter_allow_variations;

		return $this;
	}

	public function create_view() {
		$view = parent::create_view();

		$filter = $this->create_element( 'radio', 'filter_allow_variations' )
		               ->set_options( [
			               'on'  => __( 'Yes' ),
			               'off' => __( 'No' ),
		               ] );

		$format_view = new AC\View();
		$format_view->set( 'label', __( 'Show variable products', 'codepress-admin-columns' ) )
		            ->set( 'tooltip', __( 'This will allow you to include variable products in the filter drop down.', 'codepress-admin-columns' ) )
		            ->set( 'setting', $filter );

		$sections = $view->get( 'sections' );
		$sections[] = $format_view;

		$view->set( 'sections', $sections );

		return $view;
	}

}