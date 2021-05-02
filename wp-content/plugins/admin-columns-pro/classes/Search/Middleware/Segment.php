<?php

namespace ACP\Search\Middleware;

use AC;
use AC\Middleware;
use AC\Table\TableFormView;
use ACP\Bookmark\Setting\PreferredSegment;

class Segment implements Middleware {

	/**
	 * @var PreferredSegment
	 */
	private $preferred_segment;

	public function __construct( PreferredSegment $preferred_segment ) {
		$this->preferred_segment = $preferred_segment;
	}

	public function handle( AC\Request $request ) {
		$rules_key = 'rules';

		if ( $request->get_method() === AC\Request::METHOD_GET ) {
			$rules_key = 'ac-' . $rules_key;
		}

		// When rules request is empty and the action form has not been submitted we will insert the preferred segment into the request.
		if ( $request->get( $rules_key ) ) {
			return;
		}

		if ( null !== $request->get( TableFormView::PARAM_ACTION ) ) {
			return;
		}

		$segment = $this->preferred_segment->get_segment();

		if ( ! $segment ) {
			return;
		}

		$url_parameters = $segment->get_url_parameters();

		if ( ! isset( $url_parameters['ac-rules'] ) ) {
			return;
		}

		$request->get_parameters()->merge( [
			$rules_key => $url_parameters['ac-rules'],
		] );
	}

}