<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access.');

if (version_compare(phpversion(), '5.3.3', '>=')) {

	if (class_exists('UpdraftPlus_Addons_RemoteStorage_onedrive')) {

		class UpdraftPlus_BackupModule_onedrive extends UpdraftPlus_Addons_RemoteStorage_onedrive {
			public function __construct() {
				parent::__construct('onedrive', 'Microsoft OneDrive', '5.3.3', 'onedrive.png');
			}
		}
		
	} else {
	
		updraft_try_include_file('methods/addon-not-yet-present.php', 'include_once');
		/**
		 * N.B. UpdraftPlus_BackupModule_AddonNotYetPresent extends UpdraftPlus_BackupModule
		 */
		class UpdraftPlus_BackupModule_onedrive extends UpdraftPlus_BackupModule_AddonNotYetPresent {
			public function __construct() {
				parent::__construct('onedrive', 'Microsoft OneDrive', '5.3.3', 'onedrive.png');
			}
		}
	
	}
	
} else {
	updraft_try_include_file('methods/insufficient.php', 'include_once');
	class UpdraftPlus_BackupModule_onedrive extends UpdraftPlus_BackupModule_insufficientphp {
		public function __construct() {
			parent::__construct('onedrive', 'Microsoft OneDrive', '5.3.3', 'onedrive.png');
		}
	}
}
