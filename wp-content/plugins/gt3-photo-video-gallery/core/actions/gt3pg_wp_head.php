<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly
	use GT3\PhotoVideoGallery\Settings;

	function gt3pg_wp_head() {
		$settings = Settings::instance()->getSettings('basic');
		if (is_array($settings) && key_exists('gt3pg_text_before_head', $settings) && !empty($settings['gt3pg_text_before_head'])) {
			echo "<style>" . $settings['gt3pg_text_before_head'] . "</style>\n";
		}
	}
