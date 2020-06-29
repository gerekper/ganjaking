<?php

	/* Check page exists */
	function userpro_sc_admin_page_exists($template) {
		$pages = get_option('userpro_sc_pages');
		if ($template=='view') $template = 'profile';
		if (isset($pages[$template]))
			$page_id = $pages[$template];
			$page_data = get_page($page_id);
			if($page_data->post_status == 'publish'){
				return true;
			}
		return false;
	}

	/* Get page link for social */
	function userpro_sc_admin_link($template){
		$pages = get_option('userpro_sc_pages');
		if ($template=='view') $template = 'profile';
		if (isset($pages[$template])){
			return get_page_link( $pages[$template] );
		}
	}