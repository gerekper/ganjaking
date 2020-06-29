<?php

namespace ACP\Search\Query;

class Bindings {

	/**
	 * @var int
	 */
	private static $aliases = [];

	/**
	 * @var string
	 */
	protected $where = '';

	/**
	 * @var string
	 */
	protected $join = '';

	/**
	 * @var string
	 */
	protected $group_by = '';

	/**
	 * @var array
	 */
	protected $meta_query = [];

	/**
	 * @param string $column
	 *
	 * @return string
	 */
	public function get_unique_alias( $column ) {
		if ( ! isset( self::$aliases[ $column ] ) ) {
			self::$aliases[ $column ] = 0;
		}

		return $column . '_ac' . self::$aliases[ $column ]++;
	}

	/**
	 * @return string
	 */
	public function get_where() {
		return $this->where;
	}

	/**
	 * @param string $where
	 *
	 * @return $this
	 */
	public function where( $where ) {
		$this->where = $where;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_join() {
		return $this->join;
	}

	/**
	 * @param string $join
	 *
	 * @return $this
	 */
	public function join( $join ) {
		$this->join = $join;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_group_by() {
		return $this->group_by;
	}

	/**
	 * @param string $column
	 *
	 * @return $this
	 */
	public function group_by( $column ) {
		$this->group_by = $column;

		return $this;
	}

	/**
	 * @return array
	 */
	public function get_meta_query() {
		return $this->meta_query;
	}

	/**
	 * @param array $args
	 *
	 * @return $this
	 */
	public function meta_query( array $args ) {
		$this->meta_query = $args;

		return $this;
	}

}