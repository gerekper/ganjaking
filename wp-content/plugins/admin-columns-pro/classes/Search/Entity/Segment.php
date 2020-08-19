<?php

namespace ACP\Search\Entity;

use AC\Type\ListScreenId;
use ACP\Search\Type\SegmentId;

final class Segment {

	/**
	 * @var SegmentId
	 */
	private $id;

	/**
	 * @var ListScreenId
	 */
	private $list_screen_id;

	/**
	 * @var int
	 */
	private $user_id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array
	 */
	private $url_parameters;

	/**
	 * @var bool
	 */
	private $global;

	/**
	 * @param SegmentId    $id
	 * @param ListScreenId $list_screen_id
	 * @param int          $user_id
	 * @param string       $name
	 * @param array        $url_parameters
	 * @param bool         $global
	 */
	public function __construct( SegmentId $id, ListScreenId $list_screen_id, $user_id, $name, array $url_parameters, $global ) {
		$this->id = $id;
		$this->list_screen_id = $list_screen_id;
		$this->user_id = $user_id;
		$this->name = $name;
		$this->url_parameters = $url_parameters;
		$this->global = $global;
	}

	/**
	 * @return SegmentId
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return ListScreenId
	 */
	public function get_list_screen_id() {
		return $this->list_screen_id;
	}

	/**
	 * @return int
	 */
	public function get_user_id() {
		return $this->user_id;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function get_url_parameters() {
		return $this->url_parameters;
	}

	/**
	 * @return bool
	 */
	public function is_global() {
		return $this->global;
	}

}