<?php

namespace ACA\EC\Settings;

use AC;
use AC\View;

class OrganizerDisplay extends AC\Settings\Column

	implements AC\Settings\FormatValue {

	const NAME = 'display';

	const PROPERTY_EMAIL = 'email';
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_PHONE = 'phone';
	const PROPERTY_WEBSITE = 'website';

	/**
	 * @var string
	 */
	private $post_property;

	protected function set_name() {
		$this->name = self::NAME;
	}

	protected function define_options() {
		return [
			'organizer_display' => self::PROPERTY_TITLE,
		];
	}

	public function get_dependent_settings() {
		return [
			new OrganizerLink( $this->column ),
		];
	}

	/**
	 * @param int   $id
	 * @param mixed $original_value
	 *
	 * @return string|int
	 */
	public function format( $id, $original_value ) {

		switch ( $this->get_organizer_display() ) {
			case self::PROPERTY_EMAIL :
				return get_post_meta( $id, '_OrganizerEmail', true );

			case self::PROPERTY_WEBSITE :
				return get_post_meta( $id, '_OrganizerWebsite', true );

			case self::PROPERTY_PHONE :
				return get_post_meta( $id, '_OrganizerPhone', true );

			case self::PROPERTY_ID :
				return $id;

			case self::PROPERTY_TITLE :
			default :
				return ac_helper()->post->get_title( $id );
		}
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_options( $this->get_display_options() );

		$view = new View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );

		return $view;
	}

	protected function get_display_options() {
		$options = [
			self::PROPERTY_TITLE   => __( 'Title' ),
			self::PROPERTY_ID      => __( 'ID' ),
			self::PROPERTY_PHONE   => __( 'Phone', 'codepress-admin-columns' ),
			self::PROPERTY_EMAIL   => __( 'Email', 'codepress-admin-columns' ),
			self::PROPERTY_WEBSITE => __( 'Website', 'codepress-admin-columns' ),
		];

		asort( $options );

		return $options;
	}

	/**
	 * @return string
	 */
	public function get_organizer_display() {
		return $this->post_property;
	}

	/**
	 * @param string $post_property
	 *
	 * @return bool
	 */
	public function set_organizer_display( $post_property ) {
		$this->post_property = $post_property;

		return true;
	}

}