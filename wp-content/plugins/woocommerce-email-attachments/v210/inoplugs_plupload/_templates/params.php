<?php

/*
 * The following structures are needed:
 */

/**
 * Array for configuring pluploader with default values
 */
$params = array(
		'runtimes'				=> 'html5,silverlight,flash,html4',
		'url'					=> admin_url( 'admin-ajax.php' ),
		'max_file_size'			=> wp_max_upload_size() . 'b',
//		'chunk_size'			=> 'remove, if not needed',
//		'unique_names'			=> ???
		'resize'				=> array(
								'width'		=> 320, 
								'height'	=> 240, 
								'quality'	=> 90
						),
		'filters'				=> array( 
									array(	
										'title'           => 'Allowed Image Files',
										'extensions'      => '*.*'
										) 
						),
		'flash_swf_url'			=> includes_url( 'js/plupload/plupload.flash.swf' ),
		'silverlight_xap_url'	=> includes_url( 'js/plupload/plupload.silverlight.xap' ),
		
		'multipart'				=> true,
		'multipart_params'		=> array(
								'nonce' => ''
									),		//	add any variables you want to get returned and are same for all instances
		'required_features'		=> '',
		'headers'				=> array(),
		
//		'preinit'				=> false,
//		'dragdrop'				=> true,
//		'rename'				=> false,
		'multiple_queues'		=> true,
		'urlstream_upload'		=> true,
		'file_data_name'		=> 'async-upload',
		
		
	//	set on client side with JavaScript
		'browse_button'			=>	'ID',
		'drop_element'			=>	'ID',
		'container'				=>	'ID',
	
);


/**
 * Settings for an instance - values are stored in hidden fields and returned.
 * 
 */
$settings = array (
	
	
);





/**
 * Array to be translated before output
 */
$messages = array(
		'delete_this_file' => 'Delete this file',
		'delete' => 'Delete',
		'edit' => 'Edit',
		'uploaded files' => 'Uploaded files',
		'upload files' => 'Upload files',
		'drop images here' => 'Drop images here',
		'or' => 'or',
		'title browse button' => 'Click to select files with browser',
		'text browse button' => 'Select Files'
		);
?>
