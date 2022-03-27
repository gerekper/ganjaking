<?php

namespace ACP\Table\Script;

use AC\Asset\Location;
use AC\Asset\Script;
use AC\Capabilities;
use AC\ColumnSize\ListStorage;
use AC\ColumnSize\UserStorage;
use AC\ListScreen;

class ColumnResize extends Script {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	/**
	 * @var UserStorage
	 */
	private $user_storage;

	/**
	 * @var ListStorage
	 */
	private $list_storage;

	public function __construct( Location $location, ListScreen $list_screen, UserStorage $user_storage, ListStorage $list_storage ) {
		parent::__construct( 'acp-width-configurator', $location );

		$this->list_screen = $list_screen;
		$this->user_storage = $user_storage;
		$this->list_storage = $list_storage;
	}

	public function register() {
		parent::register();

		$list_screen_label = $this->list_screen->get_label();

		if ( $this->list_screen->get_title() ) {
			$list_screen_label = sprintf( '%s - %s', $list_screen_label, $this->list_screen->get_title() );
		}

		wp_localize_script( $this->handle, 'ACP_COLUMN_RESIZE_I18N', [
			'set_default_confirmation' => sprintf( __( 'Are you sure you want to set the current column widths as the default for the %s screen for ALL users?', 'codepress-admin-columns' ), sprintf( '"%s"', $list_screen_label ) ),
			'resize_columns_tool'      => __( 'Resize Columns', 'codepress-admin-columns' ),
			'reset'                    => __( 'Reset column widths', 'codepress-admin-columns' ),
			'set_default'              => __( 'Set as default', 'codepress-admin-columns' ),
			'tip_resize'               => sprintf( '%s %s', __( 'Enable the column width resize tool.', 'codepress-admin-columns' ), __( 'Drag the edge of a column header to resize it.', 'codepress-admin-columns' ) ),
			'tip_reset'                => __( 'Reset columns to their default width.', 'codepress-admin-columns' ),
			'button_reset'             => _x( 'Reset', 'column-resize-button', 'codepress-admin-columns' ),
			'tip_set_default'          => __( 'Set current column widths as their default for ALL users.', 'codepress-admin-columns' ),
		] );

		$this->add_inline_variable( 'ACP_COLUMN_RESIZE', [
			'can_set_default'           => current_user_can( Capabilities::MANAGE ),
			'can_reset'                 => $this->user_storage->exists( $this->list_screen->get_id() ),
			'minimal_pixel_width'       => 50,
			'column_sizes_current_user' => $this->get_column_sizes_by_user( $this->list_screen ),
			'column_sizes'              => $this->get_column_sizes( $this->list_screen ),
		] );
	}

	private function get_column_sizes( ListScreen $list_screen ) {
		$result = [];

		if ( ! $list_screen->get_settings() ) {
			return $result;
		}

		foreach ( $list_screen->get_columns() as $column ) {
			$column_width = $this->list_storage->get( $list_screen, $column->get_name() );

			if ( ! $column_width ) {
				continue;
			}

			$result[ $column->get_name() ] = [
				'value' => $column_width->get_value(),
				'unit'  => $column_width->get_unit(),
			];
		}

		return $result;
	}

	private function get_column_sizes_by_user( ListScreen $list_screen ) {
		$result = [];

		if ( ! $list_screen->get_settings() ) {
			return $result;
		}

		foreach ( $list_screen->get_columns() as $column ) {
			$column_width = $this->user_storage->get( $list_screen->get_id(), $column->get_name() );

			if ( ! $column_width ) {
				continue;
			}

			$result[ $column->get_name() ] = [
				'value' => $column_width->get_value(),
				'unit'  => $column_width->get_unit(),
			];
		}

		return $result;
	}

}