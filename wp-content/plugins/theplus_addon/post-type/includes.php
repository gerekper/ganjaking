<?php
	require_once THEPLUS_PLUGIN_PATH.'post-type/post-type.php';
	add_action( 'init', 'pt_plus_cmb_initialize_cmb_meta_boxes', 9999 );
	/**
		* Initialize the metabox class.
	*/
	function pt_plus_cmb_initialize_cmb_meta_boxes() {
		if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once THEPLUS_PLUGIN_PATH.'post-type/metabox/init.php';

	}
	$client_post=pt_plus_get_option('post_type','client_post_type');
	if(isset($client_post) && !empty($client_post) && ($client_post=='themes' || $client_post=='plugin' || $client_post=='themes_pro')){
		require_once THEPLUS_PLUGIN_PATH.'post-type/custom-metabox/clients_options.php';
	}
	$testimonial_post=pt_plus_get_option('post_type','testimonial_post_type');
	if(isset($testimonial_post) && !empty($testimonial_post) && ($testimonial_post=='themes' || $testimonial_post=='plugin' || $client_post=='themes_pro')){
		require_once THEPLUS_PLUGIN_PATH.'post-type/custom-metabox/testimonial_option.php';
	}
	$portfolio_post=pt_plus_get_option('post_type','portfolio_post_type');
	if(isset($portfolio_post) && !empty($portfolio_post) && ($portfolio_post=='themes' || $portfolio_post=='plugin' || $client_post=='themes_pro')){
		require_once THEPLUS_PLUGIN_PATH.'post-type/custom-metabox/portfolio_options.php';
	}
	$team_member_post=pt_plus_get_option('post_type','team_member_post_type');
	if(isset($team_member_post) && !empty($team_member_post) && ($team_member_post=='themes' || $team_member_post=='plugin' || $client_post=='themes_pro')){
		require_once THEPLUS_PLUGIN_PATH.'post-type/custom-metabox/teammember_options.php';
	}
?>