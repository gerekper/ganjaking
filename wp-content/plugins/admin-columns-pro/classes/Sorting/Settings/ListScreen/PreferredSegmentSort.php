<?php

namespace ACP\Sorting\Settings\Listscreen;

use ACP\Bookmark\Setting\PreferredSegment;
use ACP\Sorting\Request;
use ACP\Sorting\Type\SortType;

class PreferredSegmentSort {

	/**
	 * @var PreferredSegment
	 */
	private $preferred_segment;

	public function __construct( PreferredSegment $preferred_segment ) {
		$this->preferred_segment = $preferred_segment;
	}

	/**
	 * @return SortType|null
	 */
	public function get() {
		$segment = $this->preferred_segment->get_segment();

		if ( ! $segment ) {
			return null;
		}

		$query_params = $segment->get_url_parameters();

		if ( ! isset( $query_params[ Request\Sort::PARAM_ORDERBY ], $query_params[ Request\Sort::PARAM_ORDER ] ) ) {
			return null;
		}

		return new SortType(
			(string) $query_params[ Request\Sort::PARAM_ORDERBY ],
			(string) $query_params[ Request\Sort::PARAM_ORDER ]
		);
	}

}