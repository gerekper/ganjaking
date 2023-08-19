<?php

namespace ACP\Editing\Storage;

use AC;
use AC\Storage\Transaction;
use ACP\Editing\Storage;
use RuntimeException;
use WP_Post;

abstract class Menu implements Storage {

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

	abstract protected function get_title( int $id ): string;

	public function get( $id ) {
		$helper = new AC\Helper\Menu();

		return $helper->get_terms(
			$helper->get_ids( $id, $this->object_type ),
			[
				'fields' => 'ids',
			]
		);
	}

	/**
	 * Return list of menu items if the object ID is present
	 */
	private function item_exists( int $menu_id, int $object_id ): bool {
		$items = wp_get_nav_menu_items( $menu_id, [ 'post_status' => 'publish' ] );

		if ( ! $items ) {
			return false;
		}

		$items = wp_filter_object_list( $items, [ 'object' => $this->object_type ] );

		return in_array( $object_id, wp_list_pluck( $items, 'object_id' ) );
	}

	public function update( int $id, $data ): bool {
		$transaction = new Transaction();

		$menu_ids = ! empty( $data ) ? (array) $data : [];
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