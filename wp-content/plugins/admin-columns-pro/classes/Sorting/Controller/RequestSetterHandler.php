<?php

namespace ACP\Sorting\Controller;

use AC;
use ACP\Sorting\ApplyFilter;
use ACP\Sorting\Request;
use ACP\Sorting\Settings;
use ACP\Sorting\Type\SortType;
use ACP\Sorting\UserPreference;

/**
 * When you revisit a page, set the orderby variable so WordPress prints the columns headers properly
 */
class RequestSetterHandler {

	const QUERY_PARAM_MODE = 'mode';

	/**
	 * @var UserPreference\SortType
	 */
	private $user_preference;

	/**
	 * @var Settings\ListScreen\PreferredSort
	 */
	private $setting_sort_default;

	/**
	 * @var Settings\ListScreen\PreferredSegmentSort
	 */
	private $setting_segment_default;

	/**
	 * @var ApplyFilter\DefaultSort
	 */
	private $default_sort_filter;

	public function __construct( UserPreference\SortType $user_preference, Settings\ListScreen\PreferredSort $setting_sort_default, Settings\ListScreen\PreferredSegmentSort $setting_segment_default, ApplyFilter\DefaultSort $default_sort_filter ) {
		$this->user_preference = $user_preference;
		$this->setting_sort_default = $setting_sort_default;
		$this->setting_segment_default = $setting_segment_default;
		$this->default_sort_filter = $default_sort_filter;
	}

	private function get_ajax_request_sort_type() {
		$request = Request\Sort::create_from_globals();

		if ( ! $request->get_order_by() ) {
			return null;
		}

		return SortType::create_by_request( $request );
	}

	public function handle( AC\Request $request ) {
		if ( $request->get( Request\Sort::PARAM_ORDERBY ) ) {
			return;
		}

		// Ignore media grid
		if ( 'grid' === $request->get( self::QUERY_PARAM_MODE ) ) {
			return;
		}

		$sort_type = $this->get_ajax_request_sort_type();

		if ( ! $sort_type ) {
			$sort_type = $this->user_preference->get();
		}

		if ( ! $sort_type ) {
			$sort_type = $this->setting_sort_default->get();
		}

		if ( ! $sort_type ) {
			$sort_type = $this->setting_segment_default->get();
		}

		$sort_type = $this->default_sort_filter->apply_filters( $sort_type );

		if ( ! $sort_type ) {
			return;
		}

		// Set $_GET and $_REQUEST (used by WP_User_Query)
		$_REQUEST[ Request\Sort::PARAM_ORDERBY ] = $_GET[ Request\Sort::PARAM_ORDERBY ] = $sort_type->get_order_by();
		$_REQUEST[ Request\Sort::PARAM_ORDER ] = $_GET[ Request\Sort::PARAM_ORDER ] = $sort_type->get_order();
	}

}