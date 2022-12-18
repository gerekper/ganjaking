<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access.');

if (class_exists('UpdraftPlus_Addons_RemoteStorage_onedrive')) {

	class UpdraftPlus_BackupModule_pcloud extends UpdraftPlus_Addons_RemoteStorage_pcloud {
		public function __construct() {
			parent::__construct('pcloud', 'pCloud', false, 'pcloud-logo.png');
		}
	}
	
} else {

	updraft_try_include_file('methods/addon-not-yet-present.php', 'include_once');
	/**
	 * N.B. UpdraftPlus_BackupModule_AddonNotYetPresent extends UpdraftPlus_BackupModule
	 */
	class UpdraftPlus_BackupModule_pcloud extends UpdraftPlus_BackupModule_AddonNotYetPresent {
		public function __construct() {
			parent::__construct('pcloud', 'pCloud', false, 'pcloud-logo.png');
		}
	}

}