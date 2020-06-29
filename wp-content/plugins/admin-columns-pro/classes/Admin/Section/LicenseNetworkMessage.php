<?php

namespace ACP\Admin\Section;

use AC;
use AC\View;
use ACP;

class LicenseNetworkMessage extends AC\Admin\Section {

	const NAME = 'license-network-message';

	public function __construct() {
		parent::__construct( self::NAME );
	}

	/**
	 * @return string
	 */
	private function render_network_message() {
		return ( new AC\View() )->set_template( 'admin/section-license-network-message' )->render();
	}

	public function render() {
		$view = new View( [
			'title'       => __( 'Updates', 'codepress-admin-columns' ),
			'description' => __( 'Enter your license code to receive automatic updates.', 'codepress-admin-columns' ),
			'content'     => $this->render_network_message(),
			'class'       => 'general',
		] );

		$view->set_template( 'admin/page/settings-section' );

		return $view->render();
	}

}