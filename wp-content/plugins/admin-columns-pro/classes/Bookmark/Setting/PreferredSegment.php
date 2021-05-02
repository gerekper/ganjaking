<?php

namespace ACP\Bookmark\Setting;

use AC\ListScreen;
use ACP\Bookmark\Entity\Segment;
use ACP\Bookmark\SegmentRepository;
use ACP\Bookmark\Type\SegmentId;

class PreferredSegment {

	const FIELD_SEGMENT = 'filter_segment';

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	/**
	 * @var SegmentRepository
	 */
	private $segment_repository;

	/**
	 * @param ListScreen        $list_screen
	 * @param SegmentRepository $segment_repository
	 */
	public function __construct( ListScreen $list_screen, SegmentRepository $segment_repository ) {
		$this->list_screen = $list_screen;
		$this->segment_repository = $segment_repository;
	}

	/**
	 * @return Segment|null
	 */
	public function get_segment() {
		$segment_id = $this->list_screen->get_preference( self::FIELD_SEGMENT );

		if ( ! $segment_id ) {
			return null;
		}

		return $this->segment_repository->find( new SegmentId( (int) $segment_id ) );
	}

}