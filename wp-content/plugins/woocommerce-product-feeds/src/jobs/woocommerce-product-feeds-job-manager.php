<?php

use Pimple\Container;

class WoocommerceProductFeedsJobManager {
	/**
	 * @var array
	 */
	private static $jobs = [];

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Job types to be managed.
	 */
	private $job_types = [
		'WoocommerceProductFeedsRefreshGoogleTaxonomyJob',
		'WoocommerceProductFeedsClearGoogleTaxonomyJob',
	];

	/**
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
		add_action( 'init', [ $this, 'init_workers' ], 9 );
	}

	/**
	 * @return void
	 */
	public function init_workers() {
		// Bail if we've already created instances.
		if ( ! empty( self::$jobs ) ) {
			return;
		}
		foreach ( $this->job_types as $job_type ) {
			self::$jobs[ $job_type ] = $this->container[ $job_type ];
		}
	}
}
