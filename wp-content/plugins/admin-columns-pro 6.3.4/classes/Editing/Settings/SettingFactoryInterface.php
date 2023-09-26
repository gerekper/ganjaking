<?php

namespace ACP\Editing\Settings;

use ACP\Editing\Settings;

interface SettingFactoryInterface {

	/**
	 * @return Settings
	 */
	public function create();

}