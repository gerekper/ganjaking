<?php

namespace ACP\Admin;

use AC;
use AC\Admin\MenuFactoryInterface;
use AC\Asset\Location;
use AC\DefaultColumnsRepository;
use AC\IntegrationRepository;
use AC\ListScreenRepository\Storage;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\Migrate\Admin\Section\Export;
use ACP\Migrate\Admin\Section\Import;
use ACP\Type\SiteUrl;

class NetworkPageFactory implements AC\Admin\PageFactoryInterface {

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

	/**
	 * @var MenuFactoryInterface
	 */
	protected $menu_factory;

	public function __construct( Storage $storage, Location\Absolute $location_core, Location\Absolute $location_pro, SiteUrl $site_url, MenuFactoryInterface $menu_factory ) {
		$this->storage = $storage;
		$this->location_core = $location_core;
		$this->location_pro = $location_pro;
		$this->site_url = $site_url;
		$this->menu_factory = $menu_factory;
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
		return new Section\LicenseNetworkMessage( new LicenseKeyRepository( true ), new LicenseRepository( true ) );
	}

	private function show_license_section() {
		return (bool) apply_filters( 'acp/display_licence', true );
	}

	public function create( $slug ) {
		switch ( $slug ) {
			case AC\Admin\Page\Settings::NAME :
				$page = new AC\Admin\Page\Settings( new AC\Admin\View\Menu( $this->menu_factory->create( $slug ) ), $this->location_core );

				if ( $this->show_license_section() ) {
					$page->add_section( $this->get_license_section() );
				}

				return $page;
			case Page\Addons::NAME :
				return new Page\Addons(
					$this->location_core,
					new IntegrationRepository(),
					new LicenseKeyRepository( true ),
					new LicenseRepository( true ),
					new AC\Admin\View\Menu( $this->menu_factory->create( $slug ) )
				);
			case Page\Tools::NAME :
				$page = new Page\Tools( $this->location_pro, new AC\Admin\View\Menu( $this->menu_factory->create( $slug ) ) );
				$page->add_section( new Export( $this->storage, true ) )
				     ->add_section( new Import() );

				return $page;
			case AC\Admin\Page\Columns::NAME :
				return new AC\Admin\Page\Columns(
					$this->location_core,
					new DefaultColumnsRepository(),
					new AC\Admin\Section\Partial\Menu( true ),
					$this->storage,
					new AC\Admin\View\Menu( $this->menu_factory->create( $slug ) ),
					new AC\Admin\Preference\ListScreen(),
					true
				);
		}

		return null;
	}

}