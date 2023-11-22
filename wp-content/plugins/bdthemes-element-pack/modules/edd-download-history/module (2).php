<?php

namespace ElementPack\Modules\EddDownloadHistory;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'easy-digital-download-history';
	}

	public function get_widgets() {

		$widgets = [
			'EDD_Download_History',
		];

		return $widgets;
	}
}
