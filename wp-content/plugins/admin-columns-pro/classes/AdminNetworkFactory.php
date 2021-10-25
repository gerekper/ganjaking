<?php

namespace ACP;

use AC;
use AC\Admin\Page;
use AC\Admin\PageCollection;
use AC\Admin\Preference;
use AC\Admin\SectionCollection;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use ACP\Type\SiteUrl;

class AdminNetworkFactory {

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/**
	 * @var Location\Absolute
	 */
	private $location_core;

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var LicenseRepository
	 */
	private $license_repository;

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var AC\Plugin
	 */
	protected $plugin;

	public function __construct(
		Location\Absolute $location,
		Location\Absolute $location_core,
		Storage $storage,
		LicenseRepository $license_repository,
		LicenseKeyRepository $license_key_repository,
		SiteUrl $site_url,
		$plugin
	) {
		$this->location = $location;
		$this->location_core = $location_core;
		$this->storage = $storage;
		$this->license_repository = $license_repository;
		$this->license_key_repository = $license_key_repository;
		$this->site_url = $site_url;
		$this->plugin = $plugin;
	}

	protected function get_license_section() {
		return new Admin\Section\License(
			$this->location,
			$this->license_repository,
			$this->license_key_repository,
			$this->site_url
		);
	}

	/**
	 * @return Page\Columns
	 */
	protected function create_columns_page() {
		return new Page\Columns(
			$this->location_core,
			new AC\DefaultColumnsRepository(),
			new AC\Admin\Section\Partial\Menu( true ),
			$this->storage,
			new Preference\ListScreen( true ),
			true
		);
	}

	/**
	 * @return Page\Settings
	 */
	protected function create_settings_page() {
		$sections = new SectionCollection();
		$sections->add( $this->get_license_section() );

		return new Page\Settings( $sections );
	}

	/**
	 * @return PageCollection
	 */
	protected function get_pages() {
		$pages = new PageCollection();
		$pages->add( $this->create_columns_page() )
		      ->add( $this->create_settings_page() )
		      ->add( new Page\Addons( $this->location_core, new AC\Integrations() ) );

		return $pages;
	}

	/**
	 * @return AC\Admin
	 */
	public function create() {
		return new AC\Admin(
			'settings.php',
			'network_admin_menu',
			$this->get_pages(),
			$this->location_core
		);
	}

}