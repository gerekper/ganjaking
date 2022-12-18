<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access.');

if (version_compare(phpversion(), '5.3.3', '>=')) {

	if (class_exists('UpdraftPlus_Addons_RemoteStorage_backblaze')) {
		class UpdraftPlus_BackupModule_backblaze extends UpdraftPlus_Addons_RemoteStorage_backblaze {
			public function __construct() {
				parent::__construct('backblaze', 'Backblaze', true, true);
			}
		}
		
	} else {
	
		updraft_try_include_file('methods/addon-not-yet-present.php', 'include_once');
		/**
		 * N.B. UpdraftPlus_BackupModule_AddonNotYetPresent extends UpdraftPlus_BackupModule
		 */
		class UpdraftPlus_BackupModule_backblaze extends UpdraftPlus_BackupModule_AddonNotYetPresent {
			public function __construct() {
				parent::__construct('backblaze', 'Backblaze', '5.3.3', 'backblaze.png');
			}
		}
		
	}
	
} else {

	updraft_try_include_file('methods/insufficient.php', 'include_once');
	/**
	 * N.B. UpdraftPlus_BackupModule_insufficientphp extends UpdraftPlus_BackupModule
	 */
	class UpdraftPlus_BackupModule_backblaze extends UpdraftPlus_BackupModule_insufficientphp {
		public function __construct() {
			parent::__construct('backblaze', 'Backblaze', '5.3.3', 'backblaze.png');
		}
	}
	
}
