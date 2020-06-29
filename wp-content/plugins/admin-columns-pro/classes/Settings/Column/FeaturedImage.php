<?php

namespace ACP\Settings\Column;

use AC;

class FeaturedImage extends AC\Settings\Column
	implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $featured_image_display;

	protected function set_name() {
		$this->name = 'featured_image';
	}

	protected function define_options() {
		return [
			'featured_image_display' => 'image',
		];
	}

	public function get_dependent_settings() {
		$setting = [];

		switch ( $this->get_featured_image_display() ) {
			case 'image' :
				$setting[] = new AC\Settings\Column\Image( $this->column );
				break;
		}

		return $setting;
	}

	/**
	 * @param int   $value
	 * @param mixed $original_value
	 *
	 * @return string|int
	 */
	public function format( $value, $original_value ) {

		switch ( $this->get_featured_image_display() ) {
			case 'filesize':
				$value = $this->get_attachment_size( $value );

				break;
		}

		return $value;
	}

	private function get_attachment_size( $attachment_id ) {
		$file = wp_get_attachment_url( $attachment_id );
		$abs = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $file );

		if ( ! file_exists( $abs ) ) {
			return false;
		}

		return ac_helper()->file->get_readable_filesize( filesize( $abs ) );
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_options( $this->get_display_options() );

		$view = new AC\View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );

		return $view;
	}

	protected function get_display_options() {
		$options = [
			'image'    => __( 'Image' ),
			'filesize' => __( 'Filesize', 'codepress-admin-columns' ),
		];

		return $options;
	}

	/**
	 * @return string
	 */
	public function get_featured_image_display() {
		return $this->featured_image_display;
	}

	/**
	 * @param string $featured_image_display
	 */
	public function set_featured_image_display( $featured_image_display ) {
		$this->featured_image_display = $featured_image_display;
	}

}