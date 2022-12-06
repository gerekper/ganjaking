<?php

namespace ACP\Asset\Script;

use AC\Asset\Location\Absolute;
use AC\Asset\Script;
use AC\Capabilities;
use AC\ColumnSize;
use AC\ListScreen;
use AC\Translation\Confirmation;
use AC\Type\ColumnWidth;
use ACP\Settings\ListScreen\HideOnScreen;

class Table extends Script {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	/**
	 * @var ColumnSize\UserStorage
	 */
	private $user_storage;

	/**
	 * @var ColumnSize\ListStorage
	 */
	private $list_storage;

	public function __construct(
		Absolute $location,
		ListScreen $list_screen,
		ColumnSize\UserStorage $user_storage,
		ColumnSize\ListStorage $list_storage
	) {
		parent::__construct( 'acp-table', $location );

		$this->list_screen = $list_screen;
		$this->user_storage = $user_storage;
		$this->list_storage = $list_storage;
	}

	private function is_column_order_active() {
		$hide_on_screen = new HideOnScreen\ColumnOrder();

		return (bool) apply_filters( 'acp/column_order/active', ! $hide_on_screen->is_hidden( $this->list_screen ), $this->list_screen );
	}

	private function is_column_resize_active() {
		$hide_on_screen = new HideOnScreen\ColumnResize();

		return (bool) apply_filters( 'acp/resize_columns/active', ! $hide_on_screen->is_hidden( $this->list_screen ), $this->list_screen );
	}

	public function register() {
		parent::register();

		$this->add_inline_variable( 'ACP_TABLE', [
			'column_screen_option' => [
				'has_manage_admin_cap' => current_user_can( Capabilities::MANAGE ),
			],
			'column_order'         => [
				'active'        => $this->is_column_order_active(),
				'current_order' => array_keys( $this->list_screen->get_columns() ),
			],
			'column_width'         => [
				'active'                    => $this->is_column_resize_active(),
				'can_reset'                 => $this->user_storage->exists( $this->list_screen->get_id() ),
				'minimal_pixel_width'       => 50,
				'column_sizes_current_user' => $this->get_column_sizes_by_user( $this->list_screen ),
				'column_sizes'              => $this->get_column_sizes( $this->list_screen ),
			],
		] );

		wp_localize_script( $this->get_handle(), 'ACP_TABLE_I18N', array_merge( [
			'column_screen_option' => [
				'button_reset'              => _x( 'Reset', 'column-resize-button', 'codepress-admin-columns' ),
				'label'                     => __( 'Columns', 'codepress-admin-columns' ),
				'resize_columns_tool'       => __( 'Resize Columns', 'codepress-admin-columns' ),
				'reset_confirmation'        => sprintf( '%s %s', __( 'Restore the current column widths and order to their defaults.', 'codepress-admin-columns' ), __( 'Are you sure?', 'codepress-admin-columns' ) ),
				'save_changes'              => __( 'Save changes', 'codepress-admin-columns' ),
				'save_changes_confirmation' => sprintf( '%s %s', __( 'Save the current column widths and order changes as the new default for ALL users.', 'codepress-admin-columns' ), __( 'Are you sure?', 'codepress-admin-columns' ) ),
				'tip_reset'                 => __( 'Reset columns to their default widths and order.', 'codepress-admin-columns' ),
				'tip_save_changes'          => __( 'Save the current column widths and order changes.', 'codepress-admin-columns' ),
			],
		], Confirmation::get() ) );
	}

	private function get_column_sizes( ListScreen $list_screen ) {
		$result = [];

		if ( $list_screen->get_settings() ) {
			foreach ( $this->list_storage->get_all( $list_screen ) as $column_name => $width ) {
				$result[ $column_name ] = $this->create_vars( $width );
			}
		}

		return $result;
	}

	private function get_column_sizes_by_user( ListScreen $list_screen ) {
		$result = [];

		if ( $list_screen->get_settings() ) {
			foreach ( $this->user_storage->get_all( $list_screen->get_id() ) as $column_name => $width ) {
				$result[ $column_name ] = $this->create_vars( $width );
			}
		}

		return $result;
	}

	private function create_vars( ColumnWidth $width ) {
		return [
			'value' => $width->get_value(),
			'unit'  => $width->get_unit(),
		];
	}

}