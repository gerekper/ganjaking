<?php

namespace ACP\Admin;

use AC;
use AC\Asset\Location;
use ACP\ActivationTokenFactory;
use ACP\Admin\Page\License;
use ACP\Admin\Page\Tools;

class MenuFactory extends AC\Admin\MenuFactory {

	/**
	 * @var ActivationTokenFactory
	 */
	private $activation_token_factory;

	public function __construct( $url, Location\Absolute $location, ActivationTokenFactory $activation_token_factory ) {
		parent::__construct( (string) $url, $location );

		$this->activation_token_factory = $activation_token_factory;
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