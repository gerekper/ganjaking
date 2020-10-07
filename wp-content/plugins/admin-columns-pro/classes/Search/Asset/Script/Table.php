<?php

namespace ACP\Search\Asset\Script;

use AC\Asset\Location;
use AC\Asset\Script;
use AC\Capabilities;
use AC\Request;

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
	 * @param string   $handle
	 * @param Location $location
	 * @param array    $filters
	 * @param Request  $request
	 */
	public function __construct( $handle, Location $location, array $filters, Request $request ) {
		parent::__construct( $handle, $location, [ 'aca-search-querybuilder', 'wp-pointer' ] );

		$this->filters = $filters;
		$this->request = $request;
	}

	public function register() {
		parent::register();

		wp_localize_script( 'aca-search-table', 'ac_search', [
			'rules'          => json_decode( $this->request->get( 'ac-rules-raw' ) ),
			'filters'        => $this->filters,
			'url_parameters' => $this->request->get_query()->all(),
			'i18n'           => [
				'select'         => _x( 'Select', 'select placeholder', 'codepress-admin-columns' ),
				'add_filter'     => __( 'Add Filter', 'codepress-admin-columns' ),
				'days_ago'       => __( 'days ago', 'codepress-admin-columns' ),
				'days'           => __( 'days', 'codepress-admin-columns' ),
				'shared_segment' => __( 'Available to all users', 'codepress-admin-columns' ),
				'clear_filters'  => __( 'Clear filters', 'codepress-admin-columns' ),
			],
			'capabilities'   => [
				'user_can_manage_shared_segments' => current_user_can( Capabilities::MANAGE ),
			],
		] );
	}

}