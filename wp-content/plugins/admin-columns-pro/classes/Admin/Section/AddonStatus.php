<?php

namespace ACP\Admin\Section;

use AC\PluginInformation;
use AC\Renderable;
use AC\View;
use ACP\Entity\License;

class AddonStatus implements Renderable {

	/**
	 * @var PluginInformation
	 */
	private $addon;

	/**
	 * @var bool
	 */
	private $is_multisite;

	/**
	 * @var bool
	 */
	private $is_network_admin;

	/**
	 * @var License|null
	 */
	private $license;

	public function __construct( PluginInformation $addon, $is_multisite, $is_network_admin, License $license = null ) {
		$this->addon = $addon;
		$this->is_multisite = (bool) $is_multisite;
		$this->is_network_admin = (bool) $is_network_admin;
		$this->license = $license;
	}

	private function render_active_label() {
		return ( new View() )->set_template( 'admin/page/component/addon/active-label' );
	}

	private function render_network_active_label() {
		return ( new View() )->set_template( 'admin/page/component/addon/network-active-label' );
	}

	private function render_network_activate() {
		$view = new View( [
			'label' => __( 'Network Enable', 'codepress-admin-columns' ),
		] );

		return $view->set_template( 'admin/page/component/addon/activate' );
	}

	private function render_network_install() {
		return ( new View() )->set_template( 'admin/page/component/addon/network-install' );
	}

	private function render_network_activate_disabled() {
		return ( new View() )->set_template( 'admin/page/component/addon/network-activate-disabled' );
	}

	private function render_activate_disabled() {
		return ( new View() )->set_template( 'admin/page/component/addon/activate-disabled' );
	}

	private function render_activate() {
		$view = new View( [
			'label' => __( 'Enable', 'codepress-admin-columns' ),
		] );

		return $view->set_template( 'admin/page/component/addon/activate' );
	}

	private function render_install() {
		return ( new View() )->set_template( 'admin/page/component/addon/install' );
	}

	private function render_missing_license() {
		return ( new View() )->set_template( 'admin/page/component/addon/missing-license' );
	}

	public function render() {

		// Network Active
		if ( $this->addon->is_installed() && $this->addon->is_network_active() ) {
			return $this->render_network_active_label();
		}

		// Network Admin
		if ( $this->is_multisite && $this->is_network_admin ) {

			if ( $this->addon->is_installed() ) {
				return current_user_can( 'activate_plugins' )
					? $this->render_network_activate()
					: $this->render_network_activate_disabled();
			}

			if ( $this->license && $this->license->is_active() ) {
				return current_user_can( 'install_plugins' )
					? $this->render_network_install()
					: $this->render_network_activate_disabled();
			}

			return $this->render_missing_license();
		}

		// Single site
		if ( $this->addon->is_installed() && $this->addon->is_active() ) {
			return $this->render_active_label();
		}

		if ( $this->addon->is_installed() ) {
			return current_user_can( 'activate_plugins' )
				? $this->render_activate()
				: $this->render_activate_disabled();
		}

		if ( $this->license && $this->license->is_active() ) {
			return current_user_can( 'install_plugins' )
				? $this->render_install()
				: $this->render_activate_disabled();
		}

		return $this->render_missing_license();
	}

}