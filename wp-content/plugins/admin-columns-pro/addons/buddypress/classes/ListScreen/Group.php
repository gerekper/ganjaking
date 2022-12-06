<?php

namespace ACA\BP\ListScreen;

use AC;
use ACA\BP\Column;
use ACA\BP\Editing;
use ACP;
use BP_Groups_List_Table;
use ReflectionException;

class Group extends AC\ListScreenWP
	implements ACP\Editing\ListScreen {

	public function __construct() {
		$this->set_key( 'bp-groups' )
		     ->set_screen_id( 'toplevel_page_bp-groups' )
		     ->set_screen_base( 'admin' )
		     ->set_page( 'bp-groups' )
		     ->set_label( __( 'Groups', 'codepress-admin-columns' ) )
		     ->set_group( 'buddypress' );
	}

	public function get_heading_hookname() {
		return 'bp_groups_list_table_get_columns';
	}

	/**
	 * @param string $value
	 * @param string $column_name
	 * @param array  $group
	 *
	 * @return string
	 */
	public function manage_value( $value, $column_name, $group ) {
		return $this->get_display_value_by_column_name( $column_name, $group['id'], $value );
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	protected function get_object( $id ) {
		return (array) groups_get_group( $id );
	}

	public function set_manage_value_callback() {
		add_action( 'bp_groups_admin_get_group_custom_column', [ $this, 'manage_value' ], 100, 3 );
	}

	public function is_current_screen( $wp_screen ) {
		return $wp_screen && $wp_screen->id === $this->get_screen_id() && 'edit' !== filter_input( INPUT_GET, 'action' );
	}

	public function get_screen_link() {
		return add_query_arg( [ 'page' => $this->get_page(), 'layout' => $this->get_layout_id() ], $this->get_admin_url() );
	}

	/**
	 * @return BP_Groups_List_Table
	 */
	public function get_list_table() {
		// Hook suffix is required when using the list screen, mainly in Ajax
		if ( ! isset( $GLOBALS['hook_suffix'] ) ) {
			$GLOBALS['hook_suffix'] = $this->get_screen_id();
		}

		return new BP_Groups_List_Table();
	}

	/**
	 * @throws ReflectionException
	 */
	protected function register_column_types() {
		$this->register_column_types_from_list( [
			Column\Group\Avatar::class,
			Column\Group\Creator::class,
			Column\Group\Description::class,
			Column\Group\Id::class,
			Column\Group\Name::class,
			Column\Group\NameOnly::class,
			Column\Group\Status::class,
		] );
	}

	public function get_table_attr_id() {
		return '#bp-groups-form';
	}

	public function editing() {
		return new Editing\Strategy\Group();
	}

}