<?php

namespace ACP\Admin\Section;

use AC;
use AC\View;
use ACP;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;

class LicenseNetworkMessage extends AC\Admin\Section {

	const NAME = 'license-network-message';

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var LicenseRepository
	 */
	private $license_repository;

	public function __construct( LicenseKeyRepository $license_key_repository, LicenseRepository $license_repository ) {
		parent::__construct( self::NAME );

		$this->license_key_repository = $license_key_repository;
		$this->license_repository = $license_repository;
	}

	/**
	 * @return string
	 */
	private function render_network_message() {
		return ( new AC\View() )->set_template( 'admin/section-license-network-message' )->render();
	}

	private function is_license_active() {
		$key = $this->license_key_repository->find();

		return $key
			? $this->license_repository->find( $key )
			: null;

	}

	public function render() {
		$view = new View( [
			'title'       => __( 'Updates', 'codepress-admin-columns' ),
			'description' => ! $this->is_license_active() ? sprintf( '%s %s', ac_helper()->icon->dashicon( [ 'icon' => 'info-outline', 'class' => 'orange' ] ), __( 'Enter your license code to receive automatic updates.', 'codepress-admin-columns' ) ) : null,
			'content'     => $this->render_network_message(),
			'class'       => 'general',
		] );

		$view->set_template( 'admin/page/settings-section' );

		return $view->render();
	}

}