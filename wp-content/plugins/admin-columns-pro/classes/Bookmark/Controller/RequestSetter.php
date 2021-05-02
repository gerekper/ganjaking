<?php

namespace ACP\Bookmark\Controller;

use AC\Request;
use AC\Table\TableFormView;
use ACP\Bookmark;
use ACP\Sorting;

/**
 * Fill the $_GET and $_REQUEST params with the preferred segment query parameters.
 */
class RequestSetter {

	/**
	 * @var Bookmark\Setting\PreferredSegment
	 */
	private $setting;

	public function __construct( Bookmark\Setting\PreferredSegment $setting ) {
		$this->setting = $setting;
	}

	public function handle( Request $request ) {

		// Ignore when switching to another segment or when the filter form is submitted.
		if ( $request->filter( 'ac-segment' ) || null !== $request->get( TableFormView::PARAM_ACTION ) ) {
			return;
		}

		$segment = $this->setting->get_segment();

		if ( ! $segment ) {
			return;
		}

		$params = $segment->get_url_parameters();

		$ignored_params = [
			Sorting\Request\Sort::PARAM_ORDERBY,
			Sorting\Request\Sort::PARAM_ORDER,
			'layout',
			'ac-rules',
			'ac-rules-raw',
		];

		foreach ( $params as $key => $value ) {
			if ( in_array( $key, $ignored_params, true ) ) {
				continue;
			}

			if ( isset( $_GET[ $key ], $_REQUEST[ $key ] ) ) {
				continue;
			}

			$_REQUEST[ $key ] = $_GET[ $key ] = $value;
		}
	}

}