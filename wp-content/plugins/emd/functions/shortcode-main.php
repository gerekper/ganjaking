<?php

	/* Filter shortcodes args */
	add_filter('userpro_shortcode_args', 'userpro_ed_shortcodes_arg', 99);
	function userpro_ed_shortcodes_arg($args){
		$args['emd_filters'] = 1;
		$args['emd_thumb'] = 200;
		$args['emd_social'] = 1;
		$args['emd_bio'] = 1;
		$args['emd_fields'] = 'first_name,last_name,gender,country';
		$args['emd_layout'] = userpro_ed_get_option('emd_layout');
		$args['emd_per_page'] = userpro_ed_get_option('emd_per_page');
		$args['emd_col_width'] = userpro_ed_get_option('emd_col_width');
		$args['emd_col_margin'] = userpro_ed_get_option('emd_col_margin');
		$args['emd_accountstatus'] = __('Search by account status','userpro');
		$args['emd_photopreference'] = __('Photo Preference','userpro');
		$args['emd_country'] = __('Search by country', 'userpro').',dropdown';
		$args['emd_gender'] = __('Gender', 'userpro').',radio';
		$args['emd_paginate'] = 1;
		$args['emd_paginate_top'] = 1;
		$args['collage_per_page'] = 20;
		return $args;
	}

	/* Add extension shortcodes */
	add_action('userpro_custom_template_hook', 'userpro_ed_shortcodes', 99 );
	function userpro_ed_shortcodes($args) {
		global $userpro, $userpro_emd;
		$userpro->up_enqueue_scripts_styles();
		/* Removed from index.php */
		require_once userpro_path . "functions/member-search-filters.php";
		require_once userpro_path . "functions/memberlist-functions.php";
		$template = $args['template'];
		if ($template == 'emd') {
			enqueue_emd_scripts();
			if (locate_template('userpro/' . $template . '.php') != '') {
				include get_stylesheet_directory() . '/userpro/'. $template . '.php';
			} else {
				include userpro_ed_path . "templates/$template.php";
			}
			
		}
		elseif($template == 'collage'){
			enqueue_emd_scripts();
		if (locate_template('userpro/' . $template . '.php') != '') {
				include get_stylesheet_directory() . '/userpro/'. $template . '.php';
			} else {
				
				include_once userpro_ed_path . "templates/$template.php";
			}
		}
	
	}

	function enqueue_emd_scripts()
	{
		wp_register_script('userpro_ed', userpro_ed_url . 'scripts/userpro-emd.min.js');
		wp_enqueue_script('userpro_ed');
	}
