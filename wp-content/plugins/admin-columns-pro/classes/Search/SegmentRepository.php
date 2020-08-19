<?php

namespace ACP\Search;

use AC\Type\ListScreenId;
use ACP\Search\Entity\Segment;
use ACP\Search\Type\SegmentId;
use DateTime;
use InvalidArgumentException;
use RuntimeException;

class SegmentRepository {

	const FILTER_USER = 'user_id';
	const FILTER_LIST_SCREEN = 'list_screen_id';
	const FILTER_GLOBAL = 'global';
	const ORDER_BY = 'orderby';
	const ORDER = 'order';

	const TABLE = 'ac_segments';

	/**
	 * @param SegmentId $id
	 *
	 * @return Segment|null
	 */
	public function find( SegmentId $id ) {
		global $wpdb;

		$sql = "
			SELECT *
			FROM " . $wpdb->prefix . self::TABLE . "
			WHERE id = %s
		";

		$result = $wpdb->get_row( $wpdb->prepare( $sql, $id->get_id() ) );

		if ( ! isset( $result->id ) ) {
			return null;
		}

		return $this->create_segment_from_row( $result );
	}

	/**
	 * @param array $args
	 *
	 * @return Segment[]
	 */
	public function find_all( array $args = [] ) {
		global $wpdb;

		$args = array_merge( [
			self::FILTER_USER        => null,
			self::FILTER_LIST_SCREEN => null,
			self::FILTER_GLOBAL      => null, // Global available to all users
			self::ORDER_BY           => 'date_created', // e.g. `name`, `date_created`, `id`, `user_id`
			self::ORDER              => null,
		], $args );

		$sql = "
			SELECT * 
			FROM {$wpdb->prefix}" . self::TABLE;

		$and = [];

		if ( $args[ self::FILTER_LIST_SCREEN ] ) {
			$listScreenId = $args[ self::FILTER_LIST_SCREEN ];

			if ( ! $listScreenId instanceof ListScreenId ) {
				throw new InvalidArgumentException( 'Expected a ListScreenId for list screen id.' );
			}

			$and[] = $wpdb->prepare( "`list_screen_id` = %s", $listScreenId->get_id() );
		}

		if ( $args[ self::FILTER_USER ] ) {
			$and[] = $wpdb->prepare( "`user_id` = %d", $args[ self::FILTER_USER ] );
		}

		if ( is_bool( $args[ self::FILTER_GLOBAL ] ) ) {
			$and[] = $wpdb->prepare( "`global` = %d", $args[ self::FILTER_GLOBAL ] );
		}

		if ( $and ) {
			$sql .= "\nWHERE " . implode( "\nAND ", $and );
		}

		$order = $args[ self::ORDER ] === 'ASC'
			? $args[ self::ORDER ]
			: 'DESC';

		$sql .= sprintf( "\nORDER BY `%s` %s", $wpdb->_escape( $args[ self::ORDER_BY ] ), $order );

		$segments = [];

		foreach ( $wpdb->get_results( $sql ) as $row ) {
			$segments[ $row->id ] = $this->create_segment_from_row( $row );
		}

		return $segments;
	}

	/**
	 * @param int $user_id
	 *
	 * @return Segment[]
	 */
	public function find_all_by_user( $user_id ) {
		return $this->find_all( [
			self::FILTER_USER => $user_id,
		] );
	}

	/**
	 * @param object $row
	 *
	 * @return Segment
	 */
	private function create_segment_from_row( $row ) {
		return new Segment(
			new SegmentId( (int) $row->id ),
			new ListScreenId( $row->list_screen_id ),
			(int) $row->user_id,
			$row->name,
			unserialize( $row->url_parameters ),
			(bool) $row->global
		);
	}

	/**
	 * @param ListScreenId $list_screen_id
	 * @param int          $user_id
	 * @param string       $name
	 * @param array        $url_parameters
	 * @param bool         $global
	 *
	 * @return Segment
	 */
	public function create( ListScreenId $list_screen_id, $user_id, $name, array $url_parameters, $global ) {
		global $wpdb;

		if ( ! is_string( $name ) ) {
			throw new InvalidArgumentException( 'Expected a string for name.' );
		}

		if ( ! is_int( $user_id ) ) {
			throw new InvalidArgumentException( 'Expected an integer for user id.' );
		}

		if ( ! is_bool( $global ) ) {
			throw new InvalidArgumentException( 'Expected a boolean for global setting.' );
		}

		$inserted = $wpdb->insert(
			$wpdb->prefix . self::TABLE,
			[
				'list_screen_id' => $list_screen_id->get_id(),
				'user_id'        => $user_id,
				'name'           => $name,
				'url_parameters' => serialize( $url_parameters ),
				'global'         => $global,
				'date_created'   => ( new DateTime() )->format( 'Y-m-d H:i:s' ),
			],
			[
				'%s',
				'%d',
				'%s',
				'%s',
				'%d',
				'%s',
			]
		);

		if ( $inserted !== 1 ) {
			throw new RuntimeException( 'Failed to save segment.' );
		}

		return new Segment(
			new SegmentId( $wpdb->insert_id ),
			$list_screen_id,
			$user_id,
			$name,
			$url_parameters,
			$global
		);
	}

	/**
	 * @param SegmentId $id
	 */
	public function delete( SegmentId $id ) {
		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . self::TABLE,
			[
				'id' => $id->get_id(),
			],
			[
				'%d',
			]
		);
	}

}