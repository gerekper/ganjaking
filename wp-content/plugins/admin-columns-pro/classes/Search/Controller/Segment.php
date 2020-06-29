<?php

namespace ACP\Search\Controller;

use AC;
use AC\Exception;
use AC\ListScreenRepository\Storage;
use AC\Preferences;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Controller;
use ACP\Search;
use ACP\Search\Segments;

class Segment extends Controller {

	/**
	 * @var AC\ListScreen;
	 */
	protected $list_screen;

	/**
	 * @var Search\Middleware\Rules
	 */
	protected $rules;

	/**
	 * @var Segments
	 */
	protected $segments;

	public function __construct( Storage $storage, Request $request, Search\Middleware\Rules $rules ) {
		parent::__construct( $request );

		$id = $request->get( 'layout' );

		if ( ListScreenId::is_valid_id( $id ) ) {
			$this->list_screen = $storage->find( new ListScreenId( $id ) );
		} else {
			$this->list_screen = AC\ListScreenTypes::instance()->get_list_screen_by_key( $request->get( 'list_screen' ) );
		}

		if ( ! $this->list_screen instanceof AC\ListScreen ) {
			throw Exception\RequestException::parameters_invalid();
		}

		$layout_id = $this->list_screen->get_layout_id();

		$this->rules = $rules;
		$this->segments = new Segments(
			new Preferences\Site( sprintf( 'search_segments_%s', $layout_id ? $layout_id : $this->list_screen->get_key() ) )
		);
	}

	/**
	 * @param array $data
	 */
	protected function handle_segments_response( $data = [] ) {
		$response = new Response\Json();

		$errors = [
			Segments::ERROR_DUPLICATE_NAME => __( 'A segment with this name already exists.', 'codepress-admin-columns' ),
			Segments::ERROR_NAME_NOT_FOUND => __( 'Could not find current segment.', 'codepress-admin-columns' ),
			Segments::ERROR_SAVING         => __( 'Could not save the segment.', 'codepress-admin-columns' ),
		];

		if ( $this->segments->has_errors() ) {
			$response
				->set_parameter( 'error', $errors[ $this->segments->get_first_error() ] )
				->error();
		}

		$response
			->set_parameters( $data )
			->success();
	}

	/**
	 * @param Segments\Segment $segment
	 *
	 * @return array
	 */
	protected function get_segment_response( Segments\Segment $segment ) {
		$rules = $this->rules;
		$url = add_query_arg(
			[
				'ac-rules'   => urlencode( json_encode( $rules( (array) $segment->get_value( 'rules' ) ) ) ),
				'order'      => $segment->get_value( 'order' ),
				'orderby'    => $segment->get_value( 'orderby' ),
				'ac-segment' => urlencode( $segment->get_name() ),
			],
			$this->list_screen->get_screen_link()
		);

		return [
			'name' => $segment->get_name(),
			'url'  => $url,
		];
	}

	public function read_action() {
		$response = new Response\Json();
		$data = [];

		foreach ( $this->segments->get_segments() as $segment ) {
			$data[] = $this->get_segment_response( $segment );
		}

		$response
			->set_parameters( $data )
			->success();
	}

	public function create_action() {
		$data = filter_var_array(
			$this->request->get_parameters()->all(),
			[
				'name'    => FILTER_SANITIZE_STRING,
				'rules'   => [
					'filter' => FILTER_DEFAULT,
					'flags'  => FILTER_REQUIRE_ARRAY,
				],
				'order'   => FILTER_SANITIZE_STRING,
				'orderby' => FILTER_SANITIZE_STRING,
			]
		);

		$segment = new Segments\Segment(
			$data['name'],
			[
				'rules'   => $data['rules'],
				'order'   => $data['order'],
				'orderby' => $data['orderby'],
			]
		);

		$this->segments
			->add_segment( $segment )
			->save();

		$this->handle_segments_response( [
			'segment' => $this->get_segment_response( $segment ),
		] );
	}

	public function delete_action() {
		$name = $this->request->filter( 'name', FILTER_SANITIZE_STRING );

		$this->segments
			->remove_segment( $name )
			->save();

		$this->handle_segments_response();
	}

}