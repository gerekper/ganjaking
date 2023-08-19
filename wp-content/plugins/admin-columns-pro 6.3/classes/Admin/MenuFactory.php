<?php

namespace ACP\Admin;

use AC;
use AC\Admin\Menu;
use AC\Asset\Location;
use ACP\ActivationTokenFactory;
use ACP\Admin\Page\License;
use ACP\Admin\Page\Tools;

class MenuFactory extends AC\Admin\MenuFactory {

	/**
	 * @var ActivationTokenFactory
	 */
	private $activation_token_factory;

	public function __construct(
		$url,
		Location\Absolute $location,
		ActivationTokenFactory $activation_token_factory
	) {
		parent::__construct( (string) $url, $location );

		$this->activation_token_factory = $activation_token_factory;
	}

	public function create( string $current ): Menu
    {
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

		if ( $addons ) {
			$menu->add_item( new AC\Admin\Menu\Item(
				$addons->get_slug(),
				$addons->get_url(),
				$addons->get_label(),
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

	private function show_license_section(): bool {
		return (bool) apply_filters( 'acp/display_licence', true );
	}

}