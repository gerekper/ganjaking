<?php
add_filter( 'cmb_meta_boxes', 'theplus_testimonial_setting_metaboxes' );


function theplus_testimonial_setting_metaboxes( array $meta_boxes ) {

	$prefix = 'theplus_testimonial_';
	$post_name=pt_plus_testimonial_post_name();
	$meta_boxes[] = array(
		'id'         => 'testimonial_setting_metaboxes',
		'title'      => __('ThePlus Testimonial Options', 'pt_theplus'),
		'pages'      => array($post_name),
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, 
		'fields'     => array(
				array(
			       'name'	=> __('Author Text', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'author_text',
         		       'type'	=> 'textarea',
           		),
				array(
			       'name'	=> __('Website Url', 'pt_theplus'),
         		       'desc'	=> __('Enter full URL of the author website', 'pt_theplus'),
        		       'id'	=> $prefix . 'website_url',
         		       'type'	=> 'text_url',
           		),
				array(
			       'name'	=> __('Background Image', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'bg_img',
         		       'type'	=> 'file',
           		),
				array(
			       'name'	=> __('Designation', 'pt_theplus'),
         		       'desc'	=>  __('Enter full URL of the author Designation', 'pt_theplus'),
        		       'id'	=> $prefix . 'designation',
         		       'type'	=> 'text',
           		),
		),
	);	

	return $meta_boxes;
}
