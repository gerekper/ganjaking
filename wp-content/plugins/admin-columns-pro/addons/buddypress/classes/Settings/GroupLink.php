<?php

namespace ACA\BP\Settings;

use AC;
use AC\View;

class GroupLink extends AC\Settings\Column
	implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $group_link_to;

	protected function define_options() {
		return [
			'group_link_to',
		];
	}

	public function format( $value, $original_value ) {
		$id = $original_value;

		switch ( $this->get_group_link_to() ) {
			case 'edit_group' :
				$link = bp_get_admin_url( 'admin.php?action=edit&page=bp-groups&gid=' . $id );

				break;
			case 'view_group' :
				$link = bp_get_group_permalink( $id );

				break;
			default :
				$link = false;
		}

		if ( $link ) {
			$value = ac_helper()->html->link( $link, $value );
		}

		return $value;
	}

	public function create_view() {
		$select = $this->create_element( 'select' )->set_options( $this->get_display_options() );

		$view = new View( [
			'label'   => __( 'Link To', 'codepress-admin-columns' ),
			'setting' => $select,
		] );

		return $view;
	}

	private function get_display_options() {
		$options = [
			'edit_group' => __( 'Edit Group' ),
			'view_group' => __( 'View Group' ),
		];

		asort( $options );

		$options = array_merge( [ '' => __( 'None' ) ], $options );

		return $options;
	}

	/**
	 * @return string
	 */
	public function get_group_link_to() {
		return $this->group_link_to;
	}

	/**
	 * @param string $group_link_to
	 *
	 * @return bool
	 */
	public function set_group_link_to( $group_link_to ) {
		$this->group_link_to = $group_link_to;

		return true;
	}

}