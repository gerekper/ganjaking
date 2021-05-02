<?php

namespace ACP\Bookmark;

use AC\ListScreen;
use AC\ListScreenRepository\Storage;
use AC\Registrable;
use AC\Request;
use ACP\Bookmark\Controller\RequestSetter;
use ACP\Bookmark\Setting;

class Addon implements Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var SegmentRepository
	 */
	private $segment_repository;

	public function __construct( Storage $storage, Request $request, SegmentRepository $segment_repository ) {
		$this->storage = $storage;
		$this->request = $request;
		$this->segment_repository = $segment_repository;
	}

	public function register() {
		add_action( 'wp_ajax_acp_search_segment_request', [ $this, 'segment_request' ] );
		add_action( 'ac/table/list_screen', [ $this, 'request_setter' ] );
		add_action( 'acp/list_screen/deleted', [ $this, 'delete_segments_after_list_screen_deleted' ] );
		add_action( 'deleted_user', [ $this, 'delete_segments_after_user_deleted' ] );
	}

	public function request_setter( ListScreen $list_screen ) {
		$search_setter = new RequestSetter( new Setting\PreferredSegment( $list_screen, $this->segment_repository ) );
		$search_setter->handle( $this->request );
	}

	public function segment_request() {
		check_ajax_referer( 'ac-ajax' );

		$segment = new Controller\Segment(
			$this->storage,
			$this->request,
			$this->segment_repository
		);

		$segment->dispatch( $this->request->get( 'method' ) );
	}

	/**
	 * @param ListScreen $list_screen
	 */
	public function delete_segments_after_list_screen_deleted( ListScreen $list_screen ) {
		$segments = $this->segment_repository->find_all( [
			SegmentRepository::FILTER_LIST_SCREEN => $list_screen->get_id(),
		] );

		foreach ( $segments as $segment ) {
			$this->segment_repository->delete( $segment->get_id() );
		}
	}

	/**
	 * @param int $user_id
	 */
	public function delete_segments_after_user_deleted( $user_id ) {
		$segments = $this->segment_repository->find_all( [
			SegmentRepository::FILTER_USER   => (int) $user_id,
			SegmentRepository::FILTER_GLOBAL => false,
		] );

		foreach ( $segments as $segment ) {
			$this->segment_repository->delete( $segment->get_id() );
		}
	}

}