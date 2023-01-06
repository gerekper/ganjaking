<?php

class OTGS_Installer_WPML_Core_Plugin {
	/** @var bool */
	private $is_installed = false;

	/** @var bool */
	private $is_active;

	public function __construct() {
		$this->is_active = defined( 'ICL_SITEPRESS_VERSION' );

		if( $this->is_active ) {
			$this->is_installed = true;
			return;
		}

		$name = 'WPML Multilingual CMS';
		$slug = 'sitepress-multilingual-cms';
		$plugins = get_plugins();
		foreach ( $plugins as $plugin_id => $plugin ) {
			if ( dirname( $plugin_id ) == $slug || $plugin['Name'] == $name || $plugin['Title'] == $name ) {
				$this->is_installed = true;
			}
		}
	}

	public function is_installed() {
		return $this->is_installed;
	}

	public function is_active() {
		return $this->is_active;
	}
}
