<?php

namespace ACP\Admin;

use AC;
use AC\Asset\Location;
use AC\DefaultColumnsRepository;
use AC\Integrations;
use AC\ListScreenRepository\Storage;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\Migrate\Admin\Section\Export;
use ACP\Migrate\Admin\Section\Import;
use ACP\Type\SiteUrl;

class NetworkMainFactory implements AC\Admin\MainFactoryInterface {

	/**
	 * @var Storage
	 */
	protected $storage;

	/**
	 * @var Location\Absolute
	 */
	protected $location_core;

	/**
	 * @var Location\Absolute
	 */
	protected $location_pro;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	public function __construct( Storage $storage, Location\Absolute $location_core, Location\Absolute $location_pro, SiteUrl $site_url ) {
		$this->storage = $storage;
		$this->location_core = $location_core;
		$this->location_pro = $location_pro;
		$this->site_url = $site_url;
	}

	protected function get_license_section() {
		return new Section\License(
			$this->location_pro,
			new LicenseRepository( true ),
			new LicenseKeyRepository( true ),
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
			case AC\Admin\Main\Settings::NAME :
				$page = new AC\Admin\Main\Settings();

				if ( $this->show_license_section() ) {
					$page->add_section( $this->get_license_section() );
				}

				return $page;
			case AC\Admin\Main\Addons::NAME :
				return new AC\Admin\Main\Addons( $this->location_core, new Integrations() );
			case Main\Tools::NAME :
				$page = new Main\Tools( $this->location_pro );
				$page->add_section( new Export( $this->storage, true ) )
				     ->add_section( new Import() );

				return $page;
			case AC\Admin\Main\Columns::NAME :
				return new AC\Admin\Main\Columns(
					$this->location_core,
					new DefaultColumnsRepository(),
					new AC\Admin\Section\Partial\Menu( true ),
					$this->storage,
					new AC\Admin\Preference\ListScreen(),
					true
				);
		}

		return null;
	}

}