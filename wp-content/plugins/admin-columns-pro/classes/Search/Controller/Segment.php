<?php

namespace ACP\Search\Controller;

use AC;
use AC\Exception;
use AC\ListScreenRepository;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Controller;
use ACP\Search\Entity;
use ACP\Search\SegmentRepository;
use ACP\Search\Type\SegmentId;

class Segment extends Controller {

	/**
	 * @var AC\ListScreen;
	 */
	protected $list_screen;

	/**
	 * @var SegmentRepository
	 */
	private $segment_repository;

	public function __construct( ListScreenRepository\Storage $storage, Request $request, SegmentRepository $segment_repository ) {
		parent::__construct( $request );

		$id = $request->get( 'layout' );

		if ( ListScreenId::is_valid_id( $id ) ) {
			$this->list_screen = $storage->find( new ListScreenId( $id ) );
		}

		if ( ! $this->list_screen instanceof AC\ListScreen ) {
			throw Exception\RequestException::parameters_invalid();
		}

		$this->segment_repository = $segment_repository;
	}

	/**
	 * @param Entity\Segment $segment
	 *
	 * @return array
	 */
	protected function get_segment_response( Entity\Segment $segment ) {
		$query_string = array_merge( $segment->get_url_parameters(), [
			'ac-segment' => $segment->get_id()->get_id(),
		] );

		foreach ( $query_string as $k => $v ) {
			$query_string[ $k ] = urlencode_deep( $v );
		}

		$url = add_query_arg(
			$query_string,
			$this->list_screen->get_screen_link()
		);

		return [
			'id'     => $segment->get_id()->get_id(),
			'name'   => $segment->get_name(),
			'url'    => $url,
			'global' => $segment->is_global(),
		];
	}

	public function read_action() {
		$response = new Response\Json();

		$list_screen_id = $this->list_screen->get_id();

		$user_segments = $this->segment_repository->find_all( [
			SegmentRepository::FILTER_LIST_SCREEN => $list_screen_id,
			SegmentRepository::FILTER_USER        => get_current_user_id(),
			SegmentRepository::FILTER_GLOBAL      => false,
		] );

		$global_segments = $this->segment_repository->find_all( [
			SegmentRepository::FILTER_LIST_SCREEN => $list_screen_id,
			SegmentRepository::FILTER_GLOBAL      => true,
			SegmentRepository::ORDER_BY           => 'name',
			SegmentRepository::ORDER              => 'ASC',
		] );

		/**
		 * @var $segments Entity\Segment[]
		 */
		$segments = array_merge( $user_segments, $global_segments );

		/**
		 * @var $segments Entity\Segment[]
		 */
		$segments = apply_filters( 'acp/search/segments_list', $segments, $this->list_screen );

		$data = [];

		foreach ( $segments as $segment ) {
			$data[] = $this->get_segment_response( $segment );
		}

		$response
			->set_parameters( $data )
			->success();
	}

	public function create_action() {
		$response = new Response\Json();

		$data = filter_var_array(
			$this->request->get_parameters()->all(),
			[
				'name'                     => FILTER_SANITIZE_STRING,
				'query_string'             => FILTER_DEFAULT,
				'whitelisted_query_string' => FILTER_DEFAULT,
				'global'                   => FILTER_SANITIZE_NUMBER_INT,
			]
		);

		parse_str( $data['query_string'], $url_parameters );
		parse_str( $data['whitelisted_query_string'], $whitelisted_url_parameters );

		foreach ( $whitelisted_url_parameters as $whitelisted_url_parameter => $placeholder ) {
			if ( ! isset( $url_parameters[ $whitelisted_url_parameter ] ) ) {
				continue;
			}

			$whitelisted_url_parameters[ $whitelisted_url_parameter ] = $url_parameters[ $whitelisted_url_parameter ];
		}

		// Check capability before allowing global segments
		$global = $data['global'] && current_user_can( AC\Capabilities::MANAGE );

		$segment = $this->segment_repository->create(
			$this->list_screen->get_id(),
			get_current_user_id(),
			$data['name'],
			$whitelisted_url_parameters,
			$global
		);

		$response
			->set_parameters( [
				'segment' => $this->get_segment_response( $segment ),
			] )
			->success();
	}

	public function delete_action() {
		$response = new Response\Json();
		$id = $this->request->filter( 'id', FILTER_SANITIZE_NUMBER_INT );

		if ( ! $id ) {
			$response->error();
		}

		$segment_id = new SegmentId( (int) $id );
		$segment = $this->segment_repository->find( $segment_id );

		if ( ! $segment ) {
			$response->error();
		}

		if ( ! current_user_can( AC\Capabilities::MANAGE ) && $segment->get_user_id() !== get_current_user_id() ) {
			$response->error();
		}

		$this->segment_repository->delete( $segment_id );

		$response->success();
	}

}