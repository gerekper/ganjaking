<?php

namespace ACP\ListScreen;

use AC\WpListTableFactory;
use ACP\ListScreen;
use WP_MS_Users_List_Table;

class MSUser extends ListScreen\User {

	public function __construct() {
		parent::__construct();

		$this->set_label( __( 'Network Users' ) )
		     ->set_singular_label( __( 'Network User' ) )
		     ->set_key( 'wp-ms_users' )
		     ->set_screen_base( 'users-network' )
		     ->set_screen_id( 'users-network' )
		     ->set_group( 'network' )
		     ->set_network_only( true );
	}

	/**
	 * @return WP_MS_Users_List_Table
	 */
	protected function get_list_table() {
		return ( new WpListTableFactory() )->create_network_user_table( $this->get_screen_id() );
	}

	protected function get_admin_url() {
		return network_admin_url( 'users.php' );
	}

	public function get_edit_link() {
		return add_query_arg( [
			'list_screen' => $this->get_key(),
			'layout_id'   => $this->get_layout_id(),
		], ac_get_admin_network_url( 'columns' ) );
	}

	/**
	 * @param $id
	 *
	 * @return string HTML
	 * @since 4.0
	 */
	public function get_single_row( $id ) {
		ob_start();
		$this->get_list_table()->single_row( $this->get_object( $id ) );

		return ob_get_clean();
	}

}