<?php

namespace ACP\Plugin\Update;

use AC\ListScreenFactory;
use AC\ListScreenRepository\Database;
use AC\Plugin\Update;
use AC\Plugin\Version;

class V5104 extends Update {

	/**
	 * @var ListScreenFactory
	 */
	private $list_screen_factory;

	public function __construct( ListScreenFactory $list_screen_factory ) {
		parent::__construct( new Version( '5.1.4' ) );

		$this->list_screen_factory = $list_screen_factory;
	}

	public function apply_update(): void
    {
		$this->set_default_export_option();
	}

	private function set_default_export_option() {
		$repo = new Database( $this->list_screen_factory );

		foreach ( $repo->find_all() as $list_screen ) {
			$settings = $list_screen->get_settings();

			$updated = false;

			foreach ( $settings as $column_name => $setting ) {
				if ( ! isset( $setting['export'] ) ) {
					$settings[ $column_name ]['export'] = 'on';

					$updated = true;
				}
			}

			if ( $updated ) {
				$list_screen->set_settings( $settings );

				$repo->save( $list_screen );
			}
		}
	}

}