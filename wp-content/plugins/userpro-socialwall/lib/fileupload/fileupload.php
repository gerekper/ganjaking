<?php

require_once("../../../../../wp-load.php");

global $userpro;

// Secure file uploads
if( isset($_FILES["userpro_file"]) ) {

	if (empty($_FILES["userpro_file"]["name"])){
		die();
	} else {
		if ($_FILES["userpro_file"]["error"] > 0){
			die();
		} else {
			if(!is_uploaded_file($_FILES["userpro_file"]["tmp_name"])){
				die();
			} elseif( $_FILES["userpro_file"]["size"] > userpro_get_option('max_file_size') ){
				die();
			} else {
                $ret = array();
				if(class_exists('finfo'))
				{
					$finfo = new finfo();
				$fileinfo = $finfo->file($_FILES["userpro_file"]["tmp_name"], FILEINFO_MIME_TYPE);
				}
				else
				{
					$fileinfo = $_FILES['userpro_file']['type'];
				}
				$accepted_file_mime_types = array('image/gif','image/jpg','image/jpeg','image/png','application/pdf','application/zip','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/msword','text/plain','audio/wav','audio/mp3','audio/mp4','video/mp4','video/mkv','video/avi');
                $file_extension = strtolower(strrchr($_FILES["userpro_file"]["name"], "."));
				if( !in_array($file_extension, array( '.gif','.jpg','.jpeg','.png','.pdf','.txt','.zip','.doc','.docx','.wav','.mp3','.mp4' , '.mkv', '.avi' )  ) || !in_array($fileinfo,$accepted_file_mime_types) ){
                	$ret['status'] = 0;
                	echo json_encode($ret);
					die();
                }else{
					if(!is_array($_FILES["userpro_file"]["name"])) {
						$wp_filetype = wp_check_filetype_and_ext($_FILES["userpro_file"]["tmp_name"], $_FILES["userpro_file"]["name"]);

						$ext = empty( $wp_filetype['ext'] ) ? '' : $wp_filetype['ext'];
						$type = empty( $wp_filetype['type'] ) ? '' : $wp_filetype['type'];
						$proper_filename = empty( $wp_filetype['proper_filename'] ) ? '' : $wp_filetype['proper_filename'];

						if ( $proper_filename ) {
							$file['name'] = $proper_filename;
						}

						if (! $type || !$ext ) die();

						if ( ! $type ) {
							$type = $file['type'];
						}

						$unique_id = uniqid();
						$ret = array();
						$target_file = $userpro->get_uploads_dir() . $unique_id . $file_extension;
						move_uploaded_file( $_FILES["userpro_file"]["tmp_name"], $target_file );
						$ret['target_file'] = $target_file;
						$ret['target_file_uri'] = $userpro->get_uploads_url() . basename($target_file);
						$ret['status'] = 1;
						echo json_encode($ret);
					}
				}
			}
		}
	}
}
