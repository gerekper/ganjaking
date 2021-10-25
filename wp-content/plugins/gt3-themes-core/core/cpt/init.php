<?php

if(!defined('ABSPATH')) {
	exit;
}

add_action('after_setup_theme', function(){
	$GT3_CPT = apply_filters('gt3/elementor/core/cpt/register', array(
		'team',
		'portfolio',
	));

	if(is_array($GT3_CPT) && !empty($GT3_CPT)) {
		foreach($GT3_CPT as $CPT) {
			$CPT  = strtolower(htmlspecialchars($CPT));
			$file = __DIR__.'/'.$CPT.'/init.php';
			if(file_exists($file)) {
				require_once $file;
			}
		}
	}
});




