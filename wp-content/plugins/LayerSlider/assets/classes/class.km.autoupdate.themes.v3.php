<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

/**
 * Subclass of KM_Updates for themes
 *
 * @package KM_Updates
 * @since 4.6.3
 * @author John Gera
 * @copyright Copyright (c) 2013  John Gera, George Krupa, and Kreatura Media Kft.
 */

require_once dirname(__FILE__) . '/class.km.autoupdate.v3.php';

class KM_ThemeUpdatesV3 extends KM_UpdatesV3 {

	public function __construct($config) {

		// Set up auto updater
		parent::__construct($config);

	}
}
