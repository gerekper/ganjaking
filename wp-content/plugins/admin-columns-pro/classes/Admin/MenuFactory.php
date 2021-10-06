<?php

namespace ACP\Admin;

use AC;
use ACP\Admin\Page\Tools;
use ACP\Entity\License;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;

class MenuFactory extends AC\Admin\MenuFactory {

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repo;

	/**
	 * @var LicenseRepository
	 */
	private $license_repo;

	public function __construct( $url, AC\IntegrationRepository $integrations, LicenseKeyRepository $license_key_repo, LicenseRepository $license_repo ) {
		parent::__construct( $url, $integrations );

		$this->license_key_repo = $license_key_repo;
		$this->license_repo = $license_repo;
	}

	/**
	 * @return License|null
	 */
	private function get_license() {
		$key = $this->license_key_repo->find();

		return $key
			? $this->license_repo->find( $key )
			: null;
	}

	private function has_active_license() {
		$license = $this->get_license();

		return $license && $license->is_active();
	}

	public function create( $current ) {
		$menu = parent::create( $current );

		$menu->remove_item( 'pro' );

		$menu->add_item(
			new AC\Admin\Menu\Item(
				Tools::NAME,
				$this->create_menu_link( Tools::NAME ),
				__( 'Tools', 'codepress-admin-columns' ),
				$current === Tools::NAME ? '-active' : ''
			)
		);

		$item = $menu->get_item_by_slug( AC\Admin\Page\Settings::NAME );

		if ( $item && ! $this->has_active_license() ) {
			$label = sprintf( '%s %s', $item->get_label(), '<span class="ac-badge">' . 1 . '</span>' );

			$menu->add_item( new AC\Admin\Menu\Item( $item->get_slug(), $item->get_url(), $label, $item->get_class() ) );

		}

		do_action( 'acp/admin/page/menu', $menu );

		return $menu;
	}

}