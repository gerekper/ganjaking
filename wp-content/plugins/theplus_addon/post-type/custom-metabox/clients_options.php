<?php
add_filter( 'cmb_meta_boxes', 'theplus_clients_setting_metaboxes' );


function theplus_clients_setting_metaboxes( array $meta_boxes ) {

	$prefix = 'theplus_clients_';
	$post_name=pt_plus_client_post_name();
	$meta_boxes[] = array(
		'id'         => 'clients_setting_metaboxes',
		'title'      => __('TP Clients options', 'pt_theplus'),
		'pages'      => array($post_name),
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
		'fields'     => array(
				array(
			       'name'	=> __('Sub tilte', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'subtitle',
         		       'type'	=> 'text',
           		),
				array(
			       'name'	=> __('Url', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'url',
         		       'type'	=> 'text_url',
           		),
				array(
			       'name'	=> __('Title color', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'title_color',
         		       'type'	=> 'colorpicker',
           		),
				array(
			       'name'	=> __('Title Hover color', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'title_hvr_color',
         		       'type'	=> 'colorpicker',
           		),
				array(
			       'name'	=> __('Subtitle color', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'sub_color',
         		       'type'	=> 'colorpicker',
           		),
				array(
			       'name'	=> __('Subtitle hover color', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'sub_hvr_color',
         		       'type'	=> 'colorpicker',
           		),
				array(
			       'name'	=> __('Description color', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'desc_color',
         		       'type'	=> 'colorpicker',
           		),
				array(
			       'name'	=> __('Description Hover color', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'desc_hvr_color',
         		       'type'	=> 'colorpicker',
           		),
				
				array(
			       'name'	=> __('Background color', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'bg_color',
         		       'type'	=> 'colorpicker',
           		),
				array(
			       'name'	=> __('Background Hover color', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'bg_hover_color',
         		       'type'	=> 'colorpicker',
           		),
				array(
			       'name'	=> __('Border Hover color', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'border_hover_color',
         		       'type'	=> 'colorpicker',
           		),
				array(
			       'name'	=> __('Hover Image', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'hover_img',
         		       'type'	=> 'file',
           		),
		),
	);	

	return $meta_boxes;
}
