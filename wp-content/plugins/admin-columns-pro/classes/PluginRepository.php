<?php

namespace ACP;

use AC\IntegrationRepository;
use AC\PluginInformation;
use ACP\Integration\Filter\IsActive;

class PluginRepository {

	/**
	 * @var string
	 */
	private $basename;

	/**
	 * @var IntegrationRepository
	 */
	private $integration_repository;

	public function __construct( $basename, IntegrationRepository $integration_repository ) {
		$this->basename = (string) $basename;
		$this->integration_repository = $integration_repository;
	}

	/**
	 * @return Plugins
	 */
	public function find_all() {
		$plugins = [
			new PluginInformation( $this->basename ),
		];

		$addons = $this->integration_repository->find_all( [
			IntegrationRepository::ARG_FILTER => [ new IsActive() ],
		] );

		foreach ( $addons as $addon ) {
			$plugins[] = new PluginInformation( $addon->get_basename() );
		}

		return new Plugins( $plugins );
	}

}