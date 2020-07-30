<?php

namespace ACP\Search\Controller;

use AC;
use AC\Exception;
use AC\ListScreenRepository;
use AC\Preferences;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Controller;
use ACP\Search\Segments;

class Segment extends Controller {

	/**
	 * @var AC\ListScreen;
	 */
	protected $list_screen;

	/**
	 * @var Segments
	 */
	protected $segments;

	public function __construct( ListScreenRepository\Storage $storage, Request $request ) {
		parent::__construct( $request );

		$id = $request->get( 'layout' );

		if ( ListScreenId::is_valid_id( $id ) ) {
			$this->list_screen = $storage->find( new ListScreenId( $id ) );
		}

		if ( ! $this->list_screen instanceof AC\ListScreen ) {
			throw Exception\RequestException::parameters_invalid();
		}

		$this->segments = new Segments(
			new Preferences\Site( 'segments_' . $id )
		);
	}

	protected function handle_segments_response( array $data = [] ) {
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
		$query_string = array_merge( $segment->get_value( 'url_parameters' ), [
			'ac-segment' => $segment->get_name(),
		] );

		foreach ( $query_string as $k => $v ) {
			$query_string[ $k ] = is_array( $v )
				? urlencode_deep( $v )
				: urlencode( $v );
		}

		$url = add_query_arg(
			$query_string,
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
				'name'                     => FILTER_SANITIZE_STRING,
				'query_string'             => FILTER_DEFAULT,
				'whitelisted_query_string' => FILTER_DEFAULT,
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

		$segment = new Segments\Segment( $data['name'], [
			'rules'          => $data['rules'],
			'url_parameters' => $whitelisted_url_parameters,
		] );

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