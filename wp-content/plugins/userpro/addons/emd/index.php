<?php

define('userpro_ed_url',plugin_dir_url(__FILE__ ));
define('userpro_ed_path',plugin_dir_path(__FILE__ ));

	/* functions */
	foreach (glob(userpro_ed_path . 'functions/*.php') as $filename) { require_once $filename; }
	
	/* administration */
	if (is_admin()){
		foreach (glob(userpro_ed_path . 'admin/*.php') as $filename) { include $filename; }
	}