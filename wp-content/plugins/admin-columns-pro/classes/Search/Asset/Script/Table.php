<?php

namespace ACP\Search\Asset\Script;

use AC;
use AC\Asset\Location;
use AC\Asset\Script;
use AC\Capabilities;
use AC\Request;
use ACP\Bookmark\Entity\Segment;

final class Table extends Script {

	/**
	 * @var array
	 */
	protected $filters;

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var Segment|null
	 */
	protected $segment;

	public function __construct( $handle, Location $location, array $filters, Request $request, Segment $segment = null ) {
		parent::__construct( $handle, $location, [ 'aca-search-querybuilder', 'wp-pointer' ] );

		$this->filters = $filters;
		$this->request = $request;
		$this->segment = $segment;
	}

	/**
	 * @return int|null
	 */
	private function get_current_segment() {
		$segment_id = $this->request->get( 'ac-segment' );

		if ( ! $segment_id && $this->segment ) {
			$segment_id = $this->segment->get_id()->get_id();
		}

		return $segment_id
			? (int) $segment_id
			: null;
	}

	public function register() {
		parent::register();

		$rules = $this->request->get( 'ac-rules-raw' );

		wp_localize_script( 'aca-search-table', 'ac_search', [
			'current_segment' => $this->get_current_segment(),
			'rules'           => $rules ? json_decode( $rules ) : null,
			'filters'         => $this->filters,
			'sorting'         => [
				'orderby' => isset( $_GET['orderby'] ) ? $_GET['orderby'] : null,
				'order'   => isset( $_GET['order'] ) ? $_GET['order'] : null,
			],
			'segments' => [
				'can_manage' => current_user_can( AC\Capabilities::MANAGE )
			],
			'i18n'            => [
				'select'         => _x( 'Select', 'select placeholder', 'codepress-admin-columns' ),
				'add_filter'     => __( 'Add Filter', 'codepress-admin-columns' ),
				'days_ago'       => __( 'days ago', 'codepress-admin-columns' ),
				'days'           => __( 'days', 'codepress-admin-columns' ),
				'shared_segment' => __( 'Available to all users', 'codepress-admin-columns' ),
				'clear_filters'  => __( 'Clear filters', 'codepress-admin-columns' ),
				'segments'       => [
					'save_filters' => __( 'Save Filters', 'codepress-admin-columns' ),
					'public_filters' => __( 'Public', 'codepress-admin-columns' ),
					'name' => __( 'Name', 'codepress-admin-columns' ),
					'cancel' => __( 'Cancel', 'codepress-admin-columns' ),
					'save' => __( 'Save', 'codepress-admin-columns' ),
					'instructions' => __( 'Instructions', 'codepress-admin-columns' ),
				],
			],
			'capabilities'    => [
				'user_can_manage_shared_segments' => current_user_can( Capabilities::MANAGE ),
			],
		] );
	}

}