<?php

if ( !class_exists('Puc_v4p4_DebugBar_ThemePanel', false) ):

	/**
	 * Class Puc_v4p4_DebugBar_ThemePanel
	 */
	class Puc_v4p4_DebugBar_ThemePanel extends Puc_v4p4_DebugBar_Panel {
		/**
		 * @var Puc_v4p4_Theme_UpdateChecker
		 */
		protected $updateChecker;

		protected function displayConfigHeader() {
			$this->row('Theme directory', htmlentities($this->updateChecker->directoryName));
			parent::displayConfigHeader();
		}

		/**
		 * @return array
		 */
		protected function getUpdateFields() {
			return array_merge(parent::getUpdateFields(), array('details_url'));
		}
	}

endif;