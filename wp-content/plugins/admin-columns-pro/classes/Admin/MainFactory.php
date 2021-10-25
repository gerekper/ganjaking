<?php

namespace ACP\Admin;

use AC;
use AC\Admin\MainFactoryInterface;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use AC\PluginInformation;
use ACP\Editing\Admin\CustomFieldEditing;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\Migrate\Admin\Section\Export;
use ACP\Migrate\Admin\Section\Import;
use ACP\Sorting\Admin\Section\ResetSorting;
use ACP\Sorting\Admin\ShowAllResults;
use ACP\Type\SiteUrl;

class MainFactory implements MainFactoryInterface {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var PluginInformation
	 */
	private $plugin;

	/**
	 * @var AC\Admin\MainFactory
	 */
	private $main_factory;

	public function __construct( Storage $storage, Location\Absolute $location, SiteUrl $site_url, PluginInformation $plugin, AC\Admin\MainFactory $main_factory ) {
		$this->storage = $storage;
		$this->location = $location;
		$this->site_url = $site_url;
		$this->plugin = $plugin;
		$this->main_factory = $main_factory;
	}

	protected function get_license_section() {
		return new Section\License(
			$this->location,
			new LicenseRepository(),
			new LicenseKeyRepository(),
			$this->site_url
		);
	}

	protected function get_license_network_message() {
		return new Section\LicenseNetworkMessage();
	}

	private function show_license_section() {
		return (bool) apply_filters( 'acp/display_licence', true );
	}

	public function create( $slug ) {
		switch ( $slug ) {

			case Main\Tools::NAME :
				$main = new Main\Tools( $this->location );
				$main->add_section( new Export( $this->storage, false ) )
				     ->add_section( new Import() );

				return $main;
			default:
				$main = $this->main_factory->create( $slug );

				switch ( true ) {
					case $main instanceof AC\Admin\Main\Settings :
						if ( $this->show_license_section() ) {
							$main->add_section(
								$this->plugin->is_network_active()
									? $this->get_license_network_message()
									: $this->get_license_section()
							);
						}

						$main->add_section( new ResetSorting() );

						$general_section = $main->get_section( AC\Admin\Section\General::NAME );

						if ( $general_section instanceof AC\Admin\Section\General ) {
							$general_section->add_option( new CustomFieldEditing() );
							$general_section->add_option( new ShowAllResults() );
						}
				}

				return $main;
		}
	}

}