<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


$settings = array(
	'survey-conversion' => array(
		'survey-conversion-action'  => array(
			'type' => 'custom_tab',
			'action' => 'yith_wc_survey_conversion_tab'
		)
	)
);

return $settings;