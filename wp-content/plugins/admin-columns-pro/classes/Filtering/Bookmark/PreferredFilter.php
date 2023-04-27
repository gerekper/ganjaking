<?php

namespace ACP\Filtering\Bookmark;

use AC\ListScreen;
use ACP\Bookmark;
use ACP\Bookmark\SegmentRepository;

class PreferredFilter {

	/**
	 * @var SegmentRepository
	 */
	private $segment_repository;

	public function __construct( SegmentRepository $segment_repository ) {
		$this->segment_repository = $segment_repository;
	}

	public function findFilters( ListScreen $list_screen, string $request_key ): array {
		$preference = new Bookmark\Setting\PreferredSegment( $list_screen, $this->segment_repository );

		$segment = $preference->get_segment();

		if ( ! $segment ) {
			return [];
		}

		$params = $segment->get_url_parameters();

		return $params[ $request_key ] ?? [];
	}

}