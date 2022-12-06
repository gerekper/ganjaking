<?php

namespace ACA\EC\Settings;

use AC;

class ShowFilterLink extends AC\Settings\Column\Toggle
	implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $show_filter_link;

	protected function define_options() {
		return [
			'show_filter_link' => 'on',
		];
	}

	public function create_view() {
		$view = parent::create_view();

		$view->set_data( [
			'label'   => __( 'Link to Filtered Overview', 'codepress-admin-columns' ),
			'tooltip' => __( 'Enabling this option to create a link to the filtered overview of the related post type ', 'codepress-admin-columns' ),
		] );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_show_filter_link() {
		return $this->show_filter_link;
	}

	/**
	 * @param string $show_filter_link
	 *
	 * @return bool
	 */
	public function set_show_filter_link( $show_filter_link ) {
		$this->show_filter_link = $show_filter_link;

		return true;
	}

	public function format( $value, $original_value ) {
		$date = null;

		if ( $this->column->get_setting( 'event_display' ) ) {
			$date = $this->column->get_setting( 'event_display' )->get_value();
		}

		$link = add_query_arg( [
			'post_type'                    => 'tribe_events',
			'ac_related_filter_post_type'  => $this->column->get_post_type(),
			'ac_related_filter_value'      => $original_value,
			'ac_related_filter_date'       => $date,
			'ac_related_filter_return_url' => base64_encode( $_SERVER['QUERY_STRING'] ),
		], admin_url() . 'edit.php' );

		return sprintf( '<a href="%s">%s</a>', esc_url( $link ), $value );
	}
}