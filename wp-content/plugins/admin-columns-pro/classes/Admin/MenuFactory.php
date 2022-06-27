<?php

namespace ACP\Admin;

use AC;
use AC\Asset\Location;
use AC\Integration\Filter;
use AC\IntegrationRepository;
use ACP\ActivationTokenFactory;
use ACP\Admin\Page\License;
use ACP\Admin\Page\Tools;

class MenuFactory extends AC\Admin\MenuFactory {

	/**
	 * @var ActivationTokenFactory
	 */
	private $activation_token_factory;

	/**
	 * @var IntegrationRepository
	 */
	private $integration_repository;

	public function __construct( $url, Location\Absolute $location, ActivationTokenFactory $activation_token_factory, IntegrationRepository $integration_repository ) {
		parent::__construct( (string) $url, $location );

		$this->activation_token_factory = $activation_token_factory;
		$this->integration_repository = $integration_repository;
	}

	public function get_inactive_addon_count() {
		return $this->integration_repository->find_all( [
			IntegrationRepository::ARG_FILTER => [
				new Filter\IsNotActive( is_multisite(), is_network_admin() ),
				new Filter\IsInstalled(),
				new Filter\IsPluginActive(),
			],
		] )->count();
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

		$addons = $menu->get_item_by_slug( Page\Addons::NAME );

		$inactive_addons = $this->get_inactive_addon_count();

		if ( $inactive_addons > 0 ) {
			$label = sprintf( '%s <span class="ac-badge">%s</span>', $addons->get_label(), $inactive_addons );

			$menu->add_item( new AC\Admin\Menu\Item(
				$addons->get_slug(),
				$addons->get_url(),
				$label,
				$addons->get_class(),
				$addons->get_target()
			) );
		}

		if ( $this->show_license_section() ) {
			$label = __( 'License', 'codepress-admin-columns' );

			if ( ! $this->activation_token_factory->create() ) {
				$label = sprintf( '%s <span class="ac-badge">%s</span>', $label, '1' );
			}

			$menu->add_item(
				new AC\Admin\Menu\Item(
					License::NAME,
					$this->create_menu_link( License::NAME ),
					$label,
					$current === License::NAME ? '-active' : ''
				)
			);
		}

		do_action( 'acp/admin/page/menu', $menu );

		return $menu;
	}

	private function show_license_section() {
		return (bool) apply_filters( 'acp/display_licence', true );
	}

}