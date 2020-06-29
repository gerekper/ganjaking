<?php

namespace ACP\Editing\Model;

use AC;
use AC\Storage\Transaction;
use ACP\Editing\Model;
use WP_Post;

/**
 * @property AC\Column\Menu $column
 */
abstract class Menu extends Model {

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	abstract protected function get_title( $id );

	public function get_view_settings() {
		$options = [];

		$menus = wp_get_nav_menus();

		if ( $menus && ! is_wp_error( $menus ) ) {
			foreach ( $menus as $menu ) {
				$options[ $menu->term_id ] = $menu->name;
			}
		}

		return [
			'type'         => 'select2_dropdown',
			'multiple'     => true,
			'clear_button' => true,
			'options'      => $options,
		];
	}

	public function get_edit_value( $id ) {
		$menus = [];

		foreach ( $this->column->get_menus( $id ) as $menu ) {
			$menus[] = $menu->term_id;
		}

		return $menus;
	}

	/**
	 * Return list of menu items if the object ID is present
	 *
	 * @param int $menu_id
	 * @param int $object_id
	 *
	 * @return array|false
	 */
	private function item_exists( $menu_id, $object_id ) {
		$items = wp_get_nav_menu_items( $menu_id, [ 'post_status' => 'publish' ] );

		if ( ! $items ) {
			return false;
		}

		$items = wp_filter_object_list( $items, [ 'object' => $this->column->get_object_type() ] );

		return in_array( $object_id, wp_list_pluck( $items, 'object_id' ) );
	}

	public function save( $id, $menu_ids ) {
		$transaction = new Transaction();
		$menu_ids = empty( $menu_ids ) ? [] : $menu_ids;

		$results = [];

		// Delete item from menu
		foreach ( $this->column->get_menu_item_ids( $id ) as $menu_item_id ) {
			if ( ! in_array( $menu_item_id, $menu_ids, true ) ) {
				$result = wp_delete_post( $menu_item_id, true );

				$results[] = $result instanceof WP_Post;
			}
		}

		// Add item to menu
		foreach ( $menu_ids as $menu_id ) {
			if ( $this->item_exists( $menu_id, $id ) ) {
				continue;
			}

			$item = [
				'menu-item-object-id' => $id,
				'menu-item-db-id'     => 0,
				'menu-item-object'    => $this->column->get_object_type(),
				'menu-item-type'      => $this->column->get_item_type(),
				'menu-item-title'     => $this->get_title( $id ),
				'menu-item-status'    => 'publish',
			];

			$result = wp_update_nav_menu_item( $menu_id, 0, $item );

			if ( is_wp_error( $result ) ) {
				$this->set_error( $result );

				$transaction->rollback();

				return false;
			}
		}

		$transaction->commit();

		return ! in_array( false, $results, true );
	}

}