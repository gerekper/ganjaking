<?php

namespace ACP\Bookmark\Setting;

use AC\ListScreen;
use ACP\Bookmark\Entity\Segment;
use ACP\Bookmark\SegmentRepository;
use ACP\Bookmark\Type\SegmentId;

class PreferredSegment {

	public const FIELD_SEGMENT = 'filter_segment';

	private $list_screen;

	private $segment_repository;

	public function __construct( ListScreen $list_screen, SegmentRepository $segment_repository ) {
		$this->list_screen = $list_screen;
		$this->segment_repository = $segment_repository;
	}

	public function get_segment(): ?Segment {
		$segment_id = $this->list_screen->get_preference( self::FIELD_SEGMENT );

		if ( ! $segment_id ) {
			return null;
		}

		return $this->segment_repository->find( new SegmentId( (int) $segment_id ) );
	}

}