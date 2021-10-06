<?php

namespace ACP\Editing\Service;

use AC;
use AC\Request;
use AC\Storage\Transaction;
use ACP\Editing\Service;
use ACP\Editing\View;
use RuntimeException;
use WP_Post;

abstract class Menu implements Service {

	/**
	 * @var string
	 */
	protected $object_type;

	/**
	 * @var string
	 */
	protected $item_type;

	public function __construct( $object_type, $item_type ) {
		$this->object_type = (string) $object_type;
		$this->item_type = (string) $item_type;
	}

	public function get_view( $context ) {
		$view = new View\AdvancedSelect( $this->get_options() );
		$view->set_multiple( true )
		     ->set_clear_button( true );

		return $view;
	}

	public function get_value( $id ) {
		$helper = new AC\Helper\Menu();

		return $helper->get_terms(
			$helper->get_ids( $id, $this->object_type ),
			[
				'fields' => 'ids',
			]
		);
	}

	private function get_options() {
		$menus = wp_get_nav_menus();

		if ( ! $menus || is_wp_error( $menus ) ) {
			return [];
		}

		$options = [];

		foreach ( $menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}

		return $options;
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	abstract protected function get_title( $id );

	/**
	 * Return list of menu items if the object ID is present
	 *
	 * @param int $menu_id
	 * @param int $object_id
	 *
	 * @return bool
	 */
	private function item_exists( $menu_id, $object_id ) {
		$items = wp_get_nav_menu_items( $menu_id, [ 'post_status' => 'publish' ] );

		if ( ! $items ) {
			return false;
		}

		$items = wp_filter_object_list( $items, [ 'object' => $this->object_type ] );

		return in_array( $object_id, wp_list_pluck( $items, 'object_id' ) );
	}

	public function update( Request $request ) {
		$id = (int) $request->get( 'id' );
		$menu_ids = $request->get( 'value' );

		$transaction = new Transaction();

		if ( empty( $menu_ids ) ) {
			$menu_ids = [];
		}

		$results = [];

		$helper = new AC\Helper\Menu();

		// Delete item from menu
		foreach ( $helper->get_ids( $id, $this->object_type ) as $menu_item_id ) {
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
				'menu-item-object'    => $this->object_type,
				'menu-item-type'      => $this->item_type,
				'menu-item-title'     => $this->get_title( $id ),
				'menu-item-status'    => 'publish',
			];

			$result = wp_update_nav_menu_item( $menu_id, 0, $item );

			if ( is_wp_error( $result ) ) {
				$transaction->rollback();

				throw new RuntimeException( $result->get_error_message() );
			}
		}

		$transaction->commit();

		return ! in_array( false, $results, true );
	}

}