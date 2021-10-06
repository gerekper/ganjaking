<?php

namespace ACP\Admin;

use AC;
use AC\Admin\MenuFactoryInterface;
use AC\Asset\Location;
use AC\IntegrationRepository;
use AC\ListScreenRepository\Storage;
use AC\PluginInformation;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\Migrate\Admin\Section\Export;
use ACP\Migrate\Admin\Section\Import;
use ACP\Sorting\Admin\Section\ResetSorting;
use ACP\Sorting\Admin\ShowAllResults;
use ACP\Type\SiteUrl;

class PageFactory extends AC\Admin\PageFactory {

	/**
	 * @var Location\Absolute
	 */
	private $location_pro;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var PluginInformation
	 */
	private $plugin;

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var LicenseRepository
	 */
	private $license_repository;

	public function __construct(
		Storage $storage,
		Location\Absolute $location_core,
		Location\Absolute $location,
		SiteUrl $site_url,
		PluginInformation $plugin,
		MenuFactoryInterface $menu_factory,
		LicenseKeyRepository $license_key_repository,
		LicenseRepository $license_repository
	) {
		parent::__construct( $storage, $location_core, $menu_factory );
		$this->storage = $storage;
		$this->location_pro = $location;
		$this->site_url = $site_url;
		$this->plugin = $plugin;
		$this->license_key_repository = $license_key_repository;
		$this->license_repository = $license_repository;
	}

	protected function get_license_section() {
		return new Section\License(
			$this->location_pro,
			$this->license_repository,
			$this->license_key_repository,
			$this->site_url
		);
	}

	protected function get_license_network_message() {
		return new Section\LicenseNetworkMessage( $this->license_key_repository, $this->license_repository );
	}

	private function show_license_section() {
		return (bool) apply_filters( 'acp/display_licence', true );
	}

	public function create( $slug ) {
		switch ( $slug ) {
			case Page\Addons::NAME :
				return new Page\Addons(
					$this->location,
					new IntegrationRepository(),
					$this->license_key_repository,
					$this->license_repository,
					new AC\Admin\View\Menu( $this->menu_factory->create( $slug ) )
				);
			case Page\Tools::NAME :
				$page = new Page\Tools( $this->location_pro, new AC\Admin\View\Menu( $this->menu_factory->create( $slug ) ) );
				$page->add_section( new Export( $this->storage, false ) )
				     ->add_section( new Import() );

				return $page;
			case AC\Admin\Page\Settings::NAME :
				$page = parent::create( $slug );

				if ( $this->show_license_section() ) {
					$page->add_section(
						$this->plugin->is_network_active()
							? $this->get_license_network_message()
							: $this->get_license_section()
						, 20 );
				}

				$page->add_section( new ResetSorting(), 30 );

				$general_section = $page->get_section( AC\Admin\Section\General::NAME );

				if ( $general_section instanceof AC\Admin\Section\General ) {
					$general_section->add_option( new ShowAllResults() );
				}

				return $page;
			default:
				return parent::create( $slug );
		}
	}

}