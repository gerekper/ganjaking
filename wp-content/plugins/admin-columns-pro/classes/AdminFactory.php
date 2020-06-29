<?php

namespace ACP;

use AC;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use ACP\Admin;
use ACP\Editing\Admin\CustomFieldEditing;
use ACP\Migrate\Admin\Section\Export;
use ACP\Migrate\Admin\Section\Import;
use ACP\Type\SiteUrl;

class AdminFactory {

	/**
	 * @var AC\Admin
	 */
	private $admin;

	/**
	 * @var Location\Absolute
	 */
	private $location;

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
	 * @var bool
	 */
	private $is_network_active;

	public function __construct(
		AC\Admin $admin,
		Location\Absolute $location,
		Storage $storage,
		LicenseRepository $license_repository,
		LicenseKeyRepository $license_key_repository,
		SiteUrl $site_url,
		$is_network_active
	) {
		$this->admin = $admin;
		$this->location = $location;
		$this->storage = $storage;
		$this->license_repository = $license_repository;
		$this->license_key_repository = $license_key_repository;
		$this->site_url = $site_url;
		$this->is_network_active = (bool) $is_network_active;
	}

	protected function get_tools_page() {
		$page = new Admin\Page\Tools( $this->location );
		$page->add_section( new Export( $this->storage ) )
		     ->add_section( new Import() );

		return $page;
	}

	protected function get_license_section() {
		return new Admin\Section\License(
			$this->location,
			$this->license_repository,
			$this->license_key_repository,
			$this->site_url,
			$this->is_network_active
		);
	}

	protected function get_license_network_message() {
		return new Admin\Section\LicenseNetworkMessage();
	}

	private function display_license() {
		return (bool) apply_filters( 'acp/display_licence', true );
	}

	public function create() {
		$this->admin->add_page( $this->get_tools_page() );

		if ( $this->display_license() ) {

			$license_section = ! is_network_admin() && $this->is_network_active
				? $this->get_license_network_message()
				: $this->get_license_section();

			$this->admin->get_page( 'settings' )->add_section( $license_section );
		}

		/** @var AC\Admin\Section\General $general */
		$general = $this->admin->get_page( 'settings' )->get_section( 'general' );
		$general->add_option( new CustomFieldEditing() );

		return $this->admin;
	}

}