<?php
	//require_once('stripe-vendor/autoload.php');
    if( get_option( 'pafe-features-stripe-payment', 2 ) == 2 || get_option( 'pafe-features-stripe-payment', 2 ) == 1 ) {
			require_once('stripe-vendor/autoload.php');
		}

	include('MailChimp.php');
	require_once(__DIR__.'/helper/functions.php');
	require_once(__DIR__.'/helper/pdf.php');

	add_action( 'wp_ajax_pafe_ajax_form_builder', 'pafe_ajax_form_builder' );
	add_action( 'wp_ajax_nopriv_pafe_ajax_form_builder', 'pafe_ajax_form_builder' );

	function find_element_recursive( $elements, $submit_button_id ) {
		foreach ( $elements as $element ) {
			if ( $submit_button_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = find_element_recursive( $element['elements'], $submit_button_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	function set_val(&$array,$path,$val) {
		for($i=&$array; $key=array_shift($path); $i=&$i[$key]) {
			if(!isset($i[$key])) $i[$key] = array();
		}
		$i = $val;
	}

	function pafe_merge_string(&$string,$string_add) {
		$string = $string . $string_add;
	}

	function pafe_unset_string(&$string) {
		$string = '';
	}

	function pafe_set_string(&$string,$string_set) {
		$string = $string_set;
	}

	function replace_email($content, $fields, $payment_status = 'succeeded', $payment_id = '', $succeeded = 'succeeded', $pending = 'pending', $failed = 'failed', $submit_id = 0, $content_email = false ) {
		$message = $content;
		$message_all_fields = '';

		// $fields_array = array();

		// foreach ($fields as $field) {
		// 	$repeater_id = $field['repeater_id'];
		// 	$repeater_index = $field['repeater_index'];
		// 	$repeater_label = $field['repeater_label'];

		// 	if (!empty($repeater_id)) {
		// 		$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );

		// 		$path = join(",",$repeater_id_array);
		// 		$path = str_replace('|', ',', $path);
		// 		$path = explode(',',$path);

		// 		set_val($fields_array,$path,$field);
		// 	} else {
		// 		$field['repeater'] = false;
		// 		$fields_array[$field['name']] = $field;
		// 	}
		// }

		if (!empty($fields)) {

			// all fields
			foreach ($fields as $field) {
				$field_value = $field['value'];
				$field_label = isset($field['label']) ? $field['label'] : '';
				if (isset($field['value_label'])) {
					$field_value = $field['value_label'];
				}
				$field_value = str_replace( [ "\r\n", "\n", "\r", "[remove_line_if_field_empty]" ], '<br />', $field_value );
				$repeater_id = !empty($field['repeater_id']) ? $field['repeater_id'] : '';
				$repeater_id_string = '';
				$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );
				foreach ($repeater_id_array as $repeater) {
					$repeater_array = explode('|', $repeater);
					array_pop($repeater_array);
					$repeater_id_string .= join(",",$repeater_array);
				}

				if (isset($field['repeater_index'])) {
					$repeater_index = $field['repeater_index'];
					$repeater_index_1 = $repeater_index + 1;
				}

				if ( !empty($field['repeater_label']) ) {
					$repeater_label = '<span data-id="' . $repeater_id_string . '"><strong>' . $field['repeater_label'] . ' ' . $repeater_index_1 . ': </strong></span><br>';
				}

				$repeater_remove_this_field = false;
				if (isset($field['repeater_remove_this_field'])) {
					$repeater_remove_this_field = true;
				}
				
				if (!empty($repeater_id) && !empty($repeater_label)) {
					if (!$repeater_remove_this_field) {
						if (strpos($message_all_fields, $repeater_label) !== false) {
							$message_all_fields .= $field_label . ': ' . $field_value . '<br />';
						} else {
							$message_all_fields .= $repeater_label;
							if (strpos($field['name'], 'pafe-end-repeater') === false) {
								$message_all_fields .= $field_label . ': ' . $field_value . '<br />';
							}
						}
					}					
				} else {
					if (strpos($field['name'], 'pafe-end-repeater') === false) {
						if($field['image_upload'] == true){
							$message_all_fields .= $field_label . ': ' . str_replace(",", "<br />", $field_value) . '<br />';
						}else{
                            if($content_email){
                                if(!isset($field['remove_in_email']) ){
                                    $message_all_fields .= $field_label . ': ' . $field_value . '<br />';
                                }
                            }else{
                                $message_all_fields .= $field_label . ': ' . $field_value . '<br />';
                            }
						}
					}
				}

			}

			$message = str_replace( '[all-fields]', $message_all_fields, $message );

			// each field

			$repeater_content = '';
			$repeater_id_one = '';
			foreach ($fields as $field) {
				$field_value = $field['value'];
				$field_label = isset($field['label']) ? $field['label'] : '';
				if (isset($field['value_label'])) {
					$field_value = $field['value_label'];
				}

				$field_value = str_replace( [ "\r\n", "\n", "\r", "[remove_line_if_field_empty]" ], '<br />', $field_value );

				$search_remove_line_if_field_empty = '[field id="' . $field['name'] . '"]' . '[remove_line_if_field_empty]';

				if (empty($field_value)) {
					$lines = explode('<br />', $message);
					$lines_found = array();

					foreach($lines as $num => $line){
					    $pos = strpos($line, $search_remove_line_if_field_empty);
					    if($pos !== false) {
					    	$lines_found[] = $line;
					    }
					}

					if (!empty($lines_found)) {
						foreach ($lines_found as $line) {
							$message = str_replace( [ $line . '<br />', '<br />' . $line ], '', $message );
						}
					}
				}

				$search = '[field id="' . $field['name'] . '"]';
				$message = str_replace($search, $field_value, $message);

				$repeater_id = isset($field['repeater_id']) ? $field['repeater_id'] : '';
				$repeater_id_string = '';
				$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );
				foreach ($repeater_id_array as $repeater) {
					$repeater_array = explode('|', $repeater);
					array_pop($repeater_array);
					$repeater_id_string .= join(",",$repeater_array);
				}

				if (isset($field['repeater_index'])) {
					$repeater_index = $field['repeater_index'];
					$repeater_index_1 = $repeater_index + 1;
				}

				if ( !empty($field['repeater_label']) ) {
					$repeater_label = '<span data-id="' . $repeater_id_string . '"><strong>' . $field['repeater_label'] .' '. $repeater_index_1 . ': </strong></span><br>';
				}

				$repeater_remove_this_field = false;
				if (isset($field['repeater_remove_this_field'])) {
					$repeater_remove_this_field = true;
				}
				
				if (!empty($repeater_id) && !empty($repeater_label) && $repeater_remove_this_field == false) {
					if (strpos($repeater_content, $repeater_label) !== false) {
						if (strpos($field['name'], 'pafe-end-repeater') === false) {
							$string_add = $field_label . ': ' . $field_value . '<br />';
						}
						pafe_merge_string($repeater_content,$string_add);
					} else {
						$string_add = $repeater_label . $field['label'] . ': ' . $field_value . '<br />';
						pafe_merge_string($repeater_content,$string_add);
					}
					if (substr_count($field['repeater_id'],'|') == 2) {
						pafe_set_string($repeater_id_one,$field['repeater_id_one']);
					}
				}

				if (isset($field['repeater_id']) && empty($repeater_id)) {
					if (!empty($repeater_id_one) && !empty($repeater_content)) {
						$search_repeater = '[repeater id="' . $repeater_id_one . '"]';
						$message = str_replace($search_repeater, $repeater_content, $message);
						pafe_unset_string($repeater_content);
						pafe_unset_string($repeater_id_one);
					}
				}
				
			}
		}

		$search_remove_line_if_field_empty = '"]' . '[remove_line_if_field_empty]'; // fix alert [

		$lines = explode("\n", $message);
		$lines_found = array();

		foreach($lines as $num => $line){
		    $pos = strpos($line, $search_remove_line_if_field_empty);
		    if($pos !== false) {
		    	$lines_found[] = $line;
		    }
		}

		if (!empty($lines_found)) {
			foreach ($lines_found as $line) {
				$message = str_replace( [ $line . "\n", "\n" . $line ], '', $message );
			}
		}

		$message = str_replace( [ "[remove_line_if_field_empty]" ], '', $message );

		$message = str_replace( [ "\r\n", "\n", "\r" ], '', $message );
		if(!empty($payment_status['type']) && $payment_status['type'] == 'mollie'){
			$message = str_replace( '[payment_status]', $succeeded[$payment_status['status']], $message );
		}elseif($payment_status == 'stripe'){
			$payment_status = pafe_stripe_get_payment_status($payment_id);
			$message = str_replace( '[payment_status]', $payment_status, $message );
		}else{
			if ($payment_status == 'succeeded') {
				$message = str_replace( '[payment_status]', $succeeded, $message );
			}

			if ($payment_status == 'pending') {
				$message = str_replace( '[payment_status]', $pending, $message );
			}

			if ($payment_status == 'failed') {
				$message = str_replace( '[payment_status]', $failed, $message );
			}
		}

		if (!empty($submit_id)) {
			$message = str_replace( '[submit_id]', $submit_id, $message );
		}
		$pafe_submit_id = isset($GLOBALS['pafe_submit_id']) ? $GLOBALS['pafe_submit_id'] : 0;
		$meta_data_shortcode = ['[remote_ip]', '[user_agent]', '[date_submit]', '[time_submit]', '[page_url]', '[submit_id]'];
		$meta_data_shortcode_value = [$_POST['remote_ip'], $_SERVER['HTTP_USER_AGENT'], date_i18n(get_option('date_format')), date_i18n(get_option('time_format')), $_POST['referrer'], $pafe_submit_id];
		$message = str_replace($meta_data_shortcode, $meta_data_shortcode_value, $message);
		$message = pafe_set_payment_status($message, $payment_status, $succeeded, $pending, $failed, $payment_id);
		return $message;
	}

	function get_field_name_shortcode($content) {
		$field_name = str_replace('[field id="', '', $content);
		$field_name = str_replace('[repeater id="', '', $field_name); // fix alert ]
		$field_name = str_replace('"]', '', $field_name);
		return trim($field_name);
	}

	function pafe_get_field_value($field_name,$fields, $payment_status = 'succeeded', $payment_id = '', $succeeded = 'succeeded', $pending = 'pending', $failed = 'failed', $multiple = false ) {

		$field_name_first = $field_name;

		if (strpos($field_name, '[repeater id') !== false) { // ] [ [ fix alert
			$field_name = str_replace('id="', "id='", $field_name);
			$field_name = str_replace('"]', "']", $field_name);
			$message = $field_name;
			$repeater_content = '';
			$repeater_id_one = '';
			foreach ($fields as $field) {
				$field_label = isset($field['label']) ? $field['label'] : '';
				$field_value = $field['value'];
				if (isset($field['value_label'])) {
					$field_value = $field['value_label'];
				}

				$field_value = str_replace( [ "\r\n", "\n", "\r", "[remove_line_if_field_empty]" ], '<br />', $field_value );
				
				$search = '[field id="' . $field['name'] . '"]';
				$message = str_replace($search, $field_value, $message);

				$repeater_id = $field['repeater_id'];
				$repeater_id_string = '';
				$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );
				foreach ($repeater_id_array as $repeater) {
					$repeater_array = explode('|', $repeater);
					array_pop($repeater_array);
					$repeater_id_string .= join(",",$repeater_array);
				}
				$repeater_index = $field['repeater_index']; 
				$repeater_index_1 = $repeater_index + 1;
				$repeater_label = '<span class="piotnet-repeater-label">' . $field['repeater_label'] . ' ' . $repeater_index_1 . '</span>\n';

				$repeater_remove_this_field = false;
                if($succeeded == 'remove_zero' && $field_value == '0'){
                    $remove_zero = true;
                }else{
                    $remove_zero = false;
                }
				if (isset($field['repeater_remove_this_field']) || $remove_zero) {
					$repeater_remove_this_field = true;
				}
				
				if (!empty($repeater_id) && !empty($repeater_label) && $repeater_remove_this_field == false) {
					if (strpos($repeater_content, $repeater_label) !== false) {
						$string_add = '<span class="piotnet-item-label">'. $field_label . '</span>: <span class="piotnet-item-value">' . $field_value . '</span>\n';
						pafe_merge_string($repeater_content,$string_add);
					} else {
						$string_add = '<span class="piotnet-item-label">' . $repeater_label . $field['label'] . '</span>: <span class="piotnet-item-value">' . $field_value . '</span>\n';
						pafe_merge_string($repeater_content,$string_add);
					}
					if (substr_count($field['repeater_id'],'|') == 2) {
						pafe_set_string($repeater_id_one,$field['repeater_id_one']);
					}
				}

				if (isset($field['repeater_id']) && empty($repeater_id)) {
					if (!empty($repeater_id_one) && !empty($repeater_content)) {
						$search_repeater = "[repeater id='" . $repeater_id_one . "']";
						$message = str_replace($search_repeater, $repeater_content, $message);

						pafe_unset_string($repeater_content);
						pafe_unset_string($repeater_id_one);
					}
				}
			}

			$field_value = $message;
		} else {
			$field_name = get_field_name_shortcode($field_name);
			$field_value = '';
			foreach ($fields as $key_field=>$field) {
				if ($fields[$key_field]['name'] == $field_name) {
					// if (!empty($fields[$key_field]['value'])) {
					// 	$field_value = $fields[$key_field]['value'];
					// }

					if (isset($fields[$key_field]['calculation_results'])) {
						$field_value = $fields[$key_field]['calculation_results'];
					} else {
						$field_value = $fields[$key_field]['value'];
						if (isset($fields[$key_field]['value_label'])) {
                            $field_value = $fields[$key_field]['value_label'];
                        }
						if ($multiple && !empty($fields[$key_field]['value_multiple'])) {
							$field_value = $fields[$key_field]['value_multiple'];
						}
					}
				}
			}

			$field_value = str_replace( [ "\r\n", "\n", "\r", "[remove_line_if_field_empty]" ], '<br />', $field_value );
		}
		//Metadata shortcode
		$pafe_submit_id = $GLOBALS['pafe_submit_id'] ? $GLOBALS['pafe_submit_id'] : 0;
		$meta_data_shortcode = ['[remote_ip]', '[user_agent]', '[date_submit]', '[time_submit]', '[page_url]', '[submit_id]'];
		$meta_data_shortcode_value = [$_POST['remote_ip'], $_SERVER['HTTP_USER_AGENT'], date_i18n(get_option('date_format')), date_i18n(get_option('time_format')), $_POST['referrer'], $pafe_submit_id];
		$field_value = str_replace($meta_data_shortcode, $meta_data_shortcode_value, $field_value);
		$field_value = $succeeded != 'remove_zero' ? pafe_set_payment_status($field_value, $payment_status, $succeeded, $pending, $failed, $payment_id) : $field_value;
		return $field_value;		
	}

	function pafe_hexToRgb($hex, $alpha = false) {
		$hex      = str_replace('#', '', $hex);
		$length   = strlen($hex);
		$rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
		$rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
		$rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
		if ( $alpha ) {
		   $rgb['a'] = $alpha;
		}
		return $rgb;
	 }

	function getIndexColumn($column) {
		$columnArray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

		$columnFirstWord = strtoupper( substr($column, 0, 1) );
		$columnSecondWord = strtoupper( substr($column, 1, 2) );
		$index = 0;
		  
		if($columnSecondWord == '') {
		  $index = array_search($columnFirstWord, $columnArray);
		} else {
		  $index = (array_search($columnFirstWord, $columnArray) + 1)*26 + array_search($columnSecondWord, $columnArray);
		}

		return $index;
	}

	function acf_get_field_key( $field_name, $post_id ) {
		global $wpdb;
		$acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID,post_parent,post_name FROM $wpdb->posts WHERE post_excerpt=%s AND post_type=%s" , $field_name , 'acf-field' ) );
		// get all fields with that name.
		switch ( count( $acf_fields ) ) {
			case 0: // no such field
				return false;
			case 1: // just one result. 
				return $acf_fields[0]->post_name;
		}
		// result is ambiguous
		// get IDs of all field groups for this post
		$field_groups_ids = array();
		$field_groups = acf_get_field_groups( array(
			'post_id' => $post_id,
		) );
		foreach ( $field_groups as $field_group )
			$field_groups_ids[] = $field_group['ID'];
		
		// Check if field is part of one of the field groups
		// Return the first one.
		foreach ( $acf_fields as $acf_field ) {
			$acf_field_id = acf_get_field($acf_field->post_parent);
			if ( in_array($acf_field_id['parent'],$field_groups_ids) ) {
				return $acf_field->post_name;
			}
		}
		return false;
	}

	function jetengine_repeater_get_field_object( $field_name, $meta_field_id ) {
		$meta_objects = get_option('jet_engine_meta_boxes');
		foreach ( $meta_objects as $meta_object ) {
			$meta_fields = $meta_object['meta_fields'];
			foreach ( $meta_fields as $meta_field ) {
				if ( ($meta_field['name'] == $meta_field_id) && ($meta_field['type'] == 'repeater') ) {
					$meta_repeater_fields = $meta_field['repeater-fields'];
					foreach ( $meta_repeater_fields as $meta_repeater_field ) {
						if ( $meta_repeater_field['name'] == $field_name ) {
							return $meta_repeater_field;
						}
					}
				}
			}
		}
	}

	function metabox_group_get_field_object( $field_name, $meta_objects ) {
		foreach ( $meta_objects as $meta_object ) {
			$meta_fields = $meta_object['fields'];
			foreach ( $meta_fields as $meta_field ) {
				if ( ($meta_field['type'] == 'group') && ($meta_field['clone']) ) {
					$meta_repeater_fields = $meta_field['fields'];
					foreach ( $meta_repeater_fields as $meta_repeater_field ) {
						if ( $meta_repeater_field['id'] == $field_name ) {
							return $meta_repeater_field;
						}
					}
				}
			}
		}
		return false;
	}

	/**
	 * Save the image on the server.
	 */
	function save_image( $base64_img, $title ) {
		// Replace special characters
		$title = str_replace(array("\\", "/"), "_", $title);
		$title = str_replace(array(":", "*", "?", "\"", "<", ">", "|"), "", $title);

		// Upload dir.
		$upload_dir  = wp_upload_dir();
		$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;

		// $img             = str_replace( 'data:image/png;base64,', '', $base64_img );
		// $img             = str_replace( ' ', '+', $img );
		// $decoded         = base64_decode( $img );
		// $filename        = $title;
		// $file_type       = 'image/png';
		// $hashed_filename = $title . '_' . md5( $filename . microtime() ) .'.png';
		$file_type       = 'image/png';
		$data_uri = $base64_img;
		$encoded_image = explode(",", $data_uri)[1];
		$decoded = base64_decode($encoded_image);
		$hashed_filename = $title . '_' . md5( $title . microtime() ) .'.png';

		// Save the image in the uploads directory.
		$upload_file = file_put_contents( $upload_path . $hashed_filename, $decoded );

		$attachment = array(
			'post_mime_type' => $file_type,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $hashed_filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'guid'           => $upload_dir['url'] . '/' . basename( $hashed_filename )
		);

		$full_path = $upload_dir['path'] . '/' . $hashed_filename;
		$attach_id = wp_insert_attachment( $attachment, $full_path );

		$attach_data = wp_generate_attachment_metadata( $attach_id, $full_path );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		$attachment_url = wp_get_attachment_image_src($attach_id,'full')[0];
		$attachment_url = preg_replace_callback('#://([^/]+)/([^?]+)#', function ($match) {
			return '://' . $match[1] . '/' . join('/', array_map('rawurlencode', explode('/', $match[2])));
		}, $attachment_url);
		return $attachment_url;
	}

	function pafe_ajax_form_builder() {

		global $wpdb;

			if ( !empty($_POST['post_id']) && !empty($_POST['form_id']) && !empty($_POST['fields']) ) {
				$post_id = $_POST['post_id'];
				$submit_button_id = $_POST['form_id'];
				$fields = stripslashes($_POST['fields']);
				$fields = json_decode($fields, true);
				$fields = array_unique($fields, SORT_REGULAR);

				$failed = false;

				$post_url = '';

				$message = '';
				$meta_content = '';
				$meta_content_2 = '';

				$upload = wp_upload_dir();
				$upload_dir = $upload['basedir'];
				$upload_dir = $upload_dir . '/piotnet-addons-for-elementor';
				$upload_dir = apply_filters( 'pafe/form_builder/upload_dir', $upload_dir );
				$elementor = \Elementor\Plugin::$instance;

				if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
					$meta = $elementor->documents->get( $post_id )->get_elements_data();
				} else {
					$meta = $elementor->db->get_plain_editor( $post_id );
				}

				$form = find_element_recursive( $meta, $submit_button_id );

				$widget = $elementor->elements_manager->create_element_instance( $form );
				$form['settings'] = $widget->get_active_settings();

				// Validate pre submission
				$custom_message = false;
				$custom_message = apply_filters( 'pafe/form_builder/validate_pre_submit_form', $custom_message, $fields, $form, $post_id );
				if ( !empty( $custom_message ) ) {
					echo json_encode( [
						'payment_status' => 'succeeded',
						'status' => '',
						'payment_id' => '',
						'post_url' => '',
						'redirect' => '',
						'register_message' => '',
						'failed_status' => 0,
						'custom_message' => $custom_message,
					] );
					wp_die();
					return;
				}

				$limit_entries_message = '';
				$args = array(
					'post_type' => 'pafe-form-database',
					'meta_value' => $form['settings']['form_id'],
					'meta_key' => 'form_id',
				);
				$the_query = new WP_Query( $args );
				$form_database_total = $the_query->found_posts;
				if ( !empty($form['settings']['pafe_limit_form_enable']) && $form_database_total > $form['settings']['pafe_limit_entries_total_post'] ) {
					$limit_entries_message = 'expired';
					$pafe_response = array(
						'limit_entries_status' => $limit_entries_message,
					);
					echo json_encode($pafe_response);
					wp_die();
				}

				$attachment = array();
                $attachment_images_to_email = [];
                $attachment_images_to_email2 = [];

				$not_allowed_extensions = array('php', 'phpt', 'php5', 'php7', 'exe');

				if( !empty($_FILES) ) {
					foreach ($_FILES as $key=>$file) {
						
						for ($i=0; $i < count($file['name']); $i++) {
							$file_name_san = sanitize_file_name($file['name'][$i]);
							$file_extension = pathinfo( $file_name_san, PATHINFO_EXTENSION );

							if(in_array(strtolower($file_extension), $not_allowed_extensions)){
								wp_die();
							}

							$filename_goc = str_replace( '.' . $file_extension, '', $file_name_san);
							$filename = $filename_goc . '-' . uniqid() . '.' . $file_extension;
							$filename = wp_unique_filename( $upload_dir, $filename );
							$filename = apply_filters( 'pafe/form_builder/upload_dir/file_name', $filename );
							$new_file = trailingslashit( $upload_dir ) . $filename;

							if ( is_dir( $upload_dir ) && is_writable( $upload_dir ) ) {
								$move_new_file = @ move_uploaded_file( $file['tmp_name'][$i], $new_file );
								if ( false !== $move_new_file ) {
									// Set correct file permissions.
									$perms = 0644;
									@ chmod( $new_file, $perms );

									$file_url = $upload['baseurl'] . '/piotnet-addons-for-elementor/' . $filename;

									foreach ($fields as $key_field=>$field) {
										if ($key == $field['name']) {
											if ($fields[$key_field]['attach-files'] == 1) {
												$attachment[] = WP_CONTENT_DIR . '/uploads/piotnet-addons-for-elementor/' . $filename;
											} else {
												if($fields[$key_field]['value'] == '' && in_array($file_name_san, $field['file_name'])){
													$fields[$key_field]['value'] = $file_url;
												}else{
													if(in_array($file_name_san, $field['file_name']) && $i != (count($file['name']) - 1)){
														$fields[$key_field]['value'] .= ', ' . $file_url;
													}
												}
											}
										}
									}
								}
							}
						}						
					} 
				}

				foreach ($fields as $key_field=>$field) {
					$field_value = $fields[$key_field]['value'];

					if (isset($fields[$key_field]['value_label'])) {
                        $field_value = $fields[$key_field]['value_label'];
                    }

					if (strpos($field_value, 'data:image/png;base64') !== false) {
						$image_url = save_image( $field_value, $fields[$key_field]['name'] );
						$fields[$key_field]['value'] = $image_url;
					}

					if (isset($fields[$key_field]['attach-files'])) {
                        if ( $fields[$key_field]['type'] == 'text' ) {
                            $images = explode( ',', $fields[$key_field]['value'] );
                            $attachment_images_to = json_decode($fields[$key_field]['attach-files']);
                            foreach ( $images as $image ) {
                                $image_name = basename( $image );
                                if(in_array('email', $attachment_images_to)){
                                    $attachment_images_to_email[] = wp_get_upload_dir()['path'] . '/'. $image_name;
                                }
                                if(in_array('email2', $attachment_images_to)){
                                    $attachment_images_to_email2[] = wp_get_upload_dir()['path'] . '/'. $image_name;
                                }
                            }
                            $fields[$key_field]['attach-files'] = 1;
                        }
						if ($fields[$key_field]['attach-files'] == 1) {
							if (isset($fields[$key_field])) {
								unset($fields[$key_field]);
							}
						}
					}
				}

				$form_id = get_post_type($post_id) == 'pafe-forms' ? $post_id : $form['settings']['form_id'];

				$body = array(); // Webhook

				$meta_data = array(); // Webhook

				$fields_data = array(); // Webhook

				$form_submission = array(); // Webhook

				//Mollie Payment
				if(!empty($_POST['mollie_payment'])){
					$mollie_amount_value = replace_email($form['settings']['mollie_amount'],$fields);
					$mollie_amount_value = preg_replace('/[^0-9.,]+/', '', $mollie_amount_value);
					$mollie_amount_value = str_replace(',', '.', $mollie_amount_value);
					$mollie_amount_value = number_format($mollie_amount_value, 2);
					$mollie_payment_data = [
						'amount' => [
							'currency' => $form['settings']['mollie_currency'],
							'value' => $mollie_amount_value
						],
						'description' => replace_email($form['settings']['mollie_description'],$fields),
						'redirectUrl' => $_POST['mollie_redirect_url'],
						'locale' => replace_email($form['settings']['mollie_locale'],$fields),
					];
					if(!empty($form['settings']['mollie_custom_metadata']) && !empty($form['settings']['mollie_metadata_list'])){
						foreach($form['settings']['mollie_metadata_list'] as $k => $metadata){
							$metadata_key = !empty($metadata['mollie_metadata_label']) ? $metadata['mollie_metadata_label'] : $k;
							$mollie_payment_data['metadata'][$metadata_key] = replace_email($metadata['mollie_metadata_value'],$fields);
						}
					}else{
						foreach ($fields as $key => $value) {
							$mollie_payment_data['metadata'][$value['name']] = $value['value'];
						}
					}
					$mollie_api_key = get_option('piotnet-addons-for-elementor-pro-mollie-api-key');
					$mollie_params = http_build_query($mollie_payment_data);
					$curl = curl_init();
					curl_setopt_array($curl, array(
						CURLOPT_URL => 'https://api.mollie.com/v2/payments',
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'POST',
						CURLOPT_POSTFIELDS => $mollie_params,
						CURLOPT_HTTPHEADER => array(
						'Authorization: Bearer '.$mollie_api_key,
						'Content-Type: application/x-www-form-urlencoded'
						),
					));
					
					$response = curl_exec($curl);
					
					curl_close($curl);
					echo $response;
					wp_die();
					return;
				}
                //Razorpay Payment
                if(! empty( $form['settings']['razorpay_enable'] ) && $_POST['razo_payment'] == 'yes'){
                    $razor_id = get_option( 'piotnet-addons-for-elementor-pro-razorpay-api-key' );
                    $razor_secret = get_option( 'piotnet-addons-for-elementor-pro-razorpay-secret-key' );
                    $razor_notes = [];
                    if(!empty($form['settings']['razorpay_notes']) && !empty($form['settings']['razorpay_note_list'])){
						foreach($form['settings']['razorpay_note_list'] as $k => $metadata){
							$razor_note_key = !empty($metadata['razorpay_metadata_label']) ? $metadata['razorpay_metadata_label'] : $k;
							$razor_notes[$razor_note_key] = replace_email($metadata['razorpay_metadata_value'],$fields);
						}
					}
                    $razor_amount = replace_email($form['settings']['razorpay_amount'],$fields);
                    $razor_amount = floatval($razor_amount) * 100;
                    $razor_prefill = [];
                    if(!empty($form['settings']['razorpay_prefill'])){
                        $razor_prefill = [
                            'name' => replace_email($form['settings']['razorpay_customer_name'],$fields),
                            'email' => replace_email($form['settings']['razorpay_customer_email'],$fields),
                            'contact' => replace_email($form['settings']['razorpay_customer_contact'],$fields),
                        ];
                    }
                    $razor_payment_data = [
                        'key' => $razor_id,
                        'amount' => $razor_amount,
                        'currency' => $form['settings']['razorpay_currency'],
                        'name' => replace_email($form['settings']['razorpay_name'],$fields),
                        'description' => replace_email($form['settings']['razorpay_description'],$fields),
                        'image' => replace_email($form['settings']['razorpay_image'],$fields),
                        'prefill' => $razor_prefill,
                        'notes' => $razor_notes
                    ];
                    
                    $razor_first_pay = !empty($form['settings']['razorpay_first_payment_min_amount']) ? $form['settings']['razorpay_first_payment_min_amount'] : 1;
                    $razor_order_data = [
                        'amount' => $razor_amount,
                        'currency' => $form['settings']['razorpay_currency'],
                        'partial_payment' => !empty($form['settings']['razorpay_partial_payment']) ? true : false,
                        'first_payment_min_amount' => floatval($razor_first_pay) * 100,
                        'notes' => $razor_notes
                    ];
                    $razor_order_url = 'https://api.razorpay.com/v1/orders';
                    $razorpay_args = [
                        'method' => 'POST',
                        'headers' => [
                            'content-type' => 'application/json',
                            'Authorization' => 'Basic ' . base64_encode( $razor_id . ':' . $razor_secret ),
                        ],
                        'body' => json_encode($razor_order_data)
                    ];
                    $razor_order_res = wp_remote_post($razor_order_url, $razorpay_args);
                    if(!is_wp_error( $razor_order_res ) && !is_wp_error( wp_remote_retrieve_body( $razor_order_res ) )){
                        $razor_order_body = json_decode( wp_remote_retrieve_body( $razor_order_res ) );
                        $razor_payment_data['order_id'] = $razor_order_body->id;
                        echo json_encode([
                            'razor_payment_data'=> $razor_payment_data,
                            'razorpayment'=> true
                        ]);
                    }else{
                        echo json_encode([
                            'status'=> 'error',
                            'razorpayment'=> true
                        ]);
                    }
                    wp_die();
                    return;
                }
                //Razorpay Subcriptions
                if(! empty( $form['settings']['razorpay_sub_enable'] ) && $_POST['razo_sub'] == 'yes'){
                    $razorpay_id = get_option( 'piotnet-addons-for-elementor-pro-razorpay-api-key' );
                    $razorpay_secret = get_option( 'piotnet-addons-for-elementor-pro-razorpay-secret-key' );
                    $razor_quantity = replace_email($form['settings']['razor_subcription_quantity'],$fields);
                    $razor_total_count = replace_email($form['settings']['razor_subcription_total_count'],$fields);
                    $razor_sub_prefill = [];
                    if(!empty($form['settings']['razor_sub_prefill'])){
                        $razor_sub_prefill = [
                            'name' => replace_email($form['settings']['razorpay_sub_customer_name'],$fields),
                            'email' => replace_email($form['settings']['razorpay_sub_customer_email'],$fields),
                            'contact' => replace_email($form['settings']['razorpay_sub_customer_contact'],$fields),
                        ];
                    }
                    $razor_sub_notes = [];
                    if(!empty($form['settings']['razorpay_sub_notes']) && !empty($form['settings']['razorpay_sub_note_list'])){
						foreach($form['settings']['razorpay_sub_note_list'] as $k => $metadata){
							$razor_note_key = !empty($metadata['razorpay_sub_metadata_label']) ? $metadata['razorpay_sub_metadata_label'] : $k;
							$razor_sub_notes[$razor_note_key] = replace_email($metadata['razorpay_sub_metadata_value'],$fields);
						}
					}
                    $razorpay_sub_url = 'https://api.razorpay.com/v1/subscriptions';
                    $razor_sub = [
                        'plan_id' => replace_email($form['settings']['razor_subcription_plan_id'],$fields),
                        'total_count' => !empty($razor_total_count) ? (int) $razor_total_count : 1,
                        'quantity' => !empty($razor_quantity) ? (int) $razor_quantity : 1,
                    ];
                    $razor_sub_args = [
                        'method' => 'POST',
                        'headers' => [
                            'content-type' => 'application/json',
                            'Authorization' => 'Basic ' . base64_encode( $razorpay_id . ':' . $razorpay_secret ),
                        ],
                        'body' => json_encode($razor_sub)
                    ];
                    $razor_sub_response = wp_remote_post($razorpay_sub_url, $razor_sub_args);
                    if ( !is_wp_error( $razor_sub_response ) && !is_wp_error(wp_remote_retrieve_body($razor_sub_response)) ) {
                        $razor_sub_body = json_decode( wp_remote_retrieve_body( $razor_sub_response ) );
                        $razor_sub_data = [
                            'key' => $razorpay_id,
                            'subscription_id' => $razor_sub_body->id,
                            'name' => replace_email($form['settings']['razor_subcription_name'],$fields),
                            'description' => replace_email($form['settings']['razor_subcription_desc'],$fields),
                            'image' => replace_email($form['settings']['razor_subcription_image'],$fields),
                            'prefill' => $razor_sub_prefill,
                            'notes' => $razor_sub_notes,
                        ];
                        echo json_encode([
                            'subscription_razor_data' => $razor_sub_data,
                            'razorpay_sub'=> true
                        ]);
                    }else{
                        echo json_encode([
                            'status'=> 'error',
                            'razorpay_sub'=> true
                        ]);
                    }
                    wp_die();
                    return;
                }
				if ( ! empty( $form['settings']['form_metadata'] ) ) {
					$form_metadata = $form['settings']['form_metadata'];
					$meta_content .= '<br>---<br><br>';
					foreach ($form_metadata as $meta) {
						if ($meta == 'date') {
							$meta_content .= __('Date','pafe') . ': ' . date_i18n( get_option( 'date_format' ) ) . '<br>';
						}
						if ($meta == 'time') {
							$meta_content .= __('Time','pafe') . ': ' . date_i18n( get_option( 'time_format' ) ) . '<br>';
						}
						if ($meta == 'page_url') {
							$meta_content .= __('Page URL','pafe') . ': ' . $_POST['referrer'] . '<br>';
						}
						if ($meta == 'user_agent') {
							$meta_content .= __('User Agent','pafe') . ': ' . $_SERVER['HTTP_USER_AGENT'] . '<br>';
						}
						if ($meta == 'remote_ip') {
							$meta_content .= __('Remote IP','pafe') . ': ' . $_POST['remote_ip'] . '<br>';
						}
					}
				}

				if ( ! empty( $form['settings']['form_metadata_2'] ) ) {
					$form_metadata_2 = $form['settings']['form_metadata_2'];
					$meta_content_2 .= '<br>---<br><br>';
					foreach ($form_metadata_2 as $meta) {
						if ($meta == 'date') {
							$meta_content_2 .= __('Date','pafe') . ': ' . date_i18n( get_option( 'date_format' ) ) . '<br>';
						}
						if ($meta == 'time') {
							$meta_content_2 .= __('Time','pafe') . ': ' . date_i18n( get_option( 'time_format' ) ) . '<br>';
						}
						if ($meta == 'page_url') {
							$meta_content_2 .= __('Page URL','pafe') . ': ' . $_POST['referrer'] . '<br>';
						}
						if ($meta == 'user_agent') {
							$meta_content_2 .= __('User Agent','pafe') . ': ' . $_SERVER['HTTP_USER_AGENT'] . '<br>';
						}
						if ($meta == 'remote_ip') {
							$meta_content_2 .= __('Remote IP','pafe') . ': ' . $_POST['remote_ip'] . '<br>';
						}
					}
				}

				$meta_data['date']['title'] = __('Date','pafe');
				$meta_data['date']['value'] = date_i18n( get_option( 'date_format' ) );
				$meta_data['time']['title'] = __('Time','pafe');
				$meta_data['time']['value'] = date_i18n( get_option( 'time_format' ) );
				$meta_data['page_url']['title'] = __('Page URL','pafe');
				$meta_data['page_url']['value'] = $_POST['referrer'];
				$meta_data['user_agent']['title'] = __('User Agent','pafe');
				$meta_data['user_agent']['value'] = $_SERVER['HTTP_USER_AGENT'];
				$meta_data['remote_ip']['title'] = __('Remote IP','pafe');
				$meta_data['remote_ip']['value'] = $_POST['remote_ip'];

				if( in_array('webhook', $form['settings']['submit_actions']) && !empty($form['settings']['webhooks_advanced_data']) ) {
					if ($form['settings']['webhooks_advanced_data'] == 'yes') {
						$body['meta'] = $meta_data;
					}
				}

				$status = '';

				$payment_status = 'succeeded';
				$payment_id = '';

				if (!empty($_POST['payment_intent_id'])) {
					
					\Stripe\Stripe::setApiKey(get_option('piotnet-addons-for-elementor-pro-stripe-secret-key'));

					$intent = \Stripe\PaymentIntent::retrieve(
						$_POST['payment_intent_id']
					);

					$charge = $intent;

					$payment_id = $intent->id;
					$payment_status = $intent->status;

					// Webhook
					$form_submission['payment_id'] = $payment_id;
					$form_submission['payment_status'] = $payment_status;
				}

				// Paypal

				if (!empty($_POST['paypal_transaction_id'])) {
					$payment_id = $_POST['paypal_transaction_id'];
					$payment_status = 'succeeded';

					// Webhook
					$form_submission['payment_id'] = $payment_id;
					$form_submission['payment_status'] = $payment_status;
				}

				//Mollie
				if(!empty($_POST['mollie_payment_id'])){
					$payment_id = $_POST['mollie_payment_id'];
					$mollie_api_key = get_option('piotnet-addons-for-elementor-pro-mollie-api-key');
					$payment_status = pafe_get_mollie_payment_status($payment_id, $mollie_api_key);
					$form_submission['payment_status'] = $payment_status;
                    if(!empty($form['settings']['mollie_send_email'])){
                        $failed = $payment_status != 'paid' ? true : $failed;
                    }
				}
				// Recaptcha

				$recaptcha_check = 1;

				if (!empty($_POST['recaptcha'])) {

					// Build POST request:
				    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
				    $recaptcha_secret = get_option('piotnet-addons-for-elementor-pro-recaptcha-secret-key');
				    $recaptcha_response = $_POST['recaptcha'];

					$recaptcha_request = [
						'body' => [
							'secret' => $recaptcha_secret,
							'response' => $recaptcha_response,
							'remoteip' => $_POST['remote_ip'],
						],
					];

					$recaptcha = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', $recaptcha_request );

					$recaptcha = json_decode( wp_remote_retrieve_body( $recaptcha ) );
                    $recaptcha_score = !empty($form['settings']['pafe_recaptcha_score_value']) ? floatval($form['settings']['pafe_recaptcha_score_value']) : 0.5;
				    if ($recaptcha->score < $recaptcha_score) {
                        $recaptcha_check = 0;
				    }
				}

				// Honeypot

				foreach ($fields as $key_field => $field) {
					if ( isset($fields[$key_field]['type']) && $fields[$key_field]['type'] == 'honeypot') {
						if (!empty($fields[$key_field]['value'])) {
							$recaptcha_check = 0;
						}
					}
				}

				$fields_db = $fields;
				if (!empty($form['settings']['remove_empty_form_input_fields'])) {
					$fields_new = array();
					$field_remove = [];
				    foreach ($fields as $field) {
				    	if (!isset($field['calculation_results'])) {
				    		if (!empty($field['value']) || $field['value'] == '0' || strpos($field['name'], 'pafe-end-repeater') !== false) {
					    		$fields_new[] = $field;
					    	}else{
								$field_remove[] = '[field id="'.$field['name'].'"]';
							}
				    	} else {
				    		if (!empty($field['calculation_results']) || $field['calculation_results'] == '0') {
					    		$fields_new[] = $field;
					    	}else{
								$field_remove[] = '[field id="'.$field['name'].'"]';
							}
				    	}
				    }
				    $fields = $fields_new;
				}
				// Filter Hook
					
				$fields = apply_filters( 'pafe/form_builder/fields', $fields );
				$form['settings'] = apply_filters( 'pafe/form_builder/form_settings', $form['settings'] );
				// repeater

				$fields_array = array();

				foreach ($fields as $field) {
					$repeater_id = isset($field['repeater_id']) ? $field['repeater_id'] : '';
					$repeater_index = isset($field['repeater_index']) ? $field['repeater_index'] : '';
					$repeater_label = isset($field['repeater_label']) ? $field['repeater_label'] : '';

					if (!empty($repeater_id)) {
						$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );
						$repeater_id_array_new = array();

						if (strpos(rtrim($repeater_id, ','), ',') !== false) {
							for ($i=0; $i < count($repeater_id_array); $i++) { 
								if ($i != count($repeater_id_array) - 1) {
									$repeater_id_array_new[] = str_replace('|' . $field['name'], '', $repeater_id_array[$i]);
								} else {
									$repeater_id_array_new[] = $repeater_id_array[$i];
								}
							}
						} else {
							$repeater_id_array_new = $repeater_id_array;
						}

						$path = join(",",$repeater_id_array_new);
						$path = str_replace('|', ',', $path);
						$path = explode(',',$path);

						set_val($fields_array,$path,$field['value']);
					} else {
						$field['repeater'] = false;
						$fields_array[$field['name']] = $field;
					}
				}

				array_walk($fields_array, function (& $item) {
					foreach ($item as $key => $value) {
						if (strpos($key, 'index') === 0) {
							$key_new = str_replace('index', '', $key);
							$item[$key_new] = $item[$key];
							unset($item[$key]);
						}
					}
				});

				$form_database_post_id = 0;

				if ($recaptcha_check == 1) {

					// Add to Form Database

					if (empty($form['settings']['form_database_disable'])) {
						$my_post = array(
							'post_title'    => wp_strip_all_tags( 'Piotnet Addons Form Database ' . $submit_button_id ),
							'post_status'   => 'publish',
							'post_type'		=> 'pafe-form-database',
						);

						$form_database_post_id = wp_insert_post( $my_post );
						global $pafe_submit_id;
						$pafe_submit_id = $form_database_post_id;

						if (!empty($form_database_post_id)) {

							$my_post_update = array(
								'ID'           => $form_database_post_id,
								'post_title'   => '#' . $form_database_post_id,
							);
							wp_update_post( $my_post_update );

							$fields_database = array();

							$fields_database['form_id'] = array(
								'name' => 'form_id',
								'value' => $form_id,
								'label' => 'Form ID',
							);
							$fields_database['form_id_elementor'] = array(
								'name' => 'form_id_elementor',
								'value' => $submit_button_id,
								'label' => '',
							);
							$fields_database['post_id'] = array(
								'name' => 'post_id',
								'value' => $post_id,
								'label' => '',
							);

							$repeater = array();

							foreach ($fields_db as $field) {

								if (!empty($field['repeater_id'])) {
									if (substr_count($field['repeater_id'],',') == 1) {
										$repeater_id = explode('|', $field['repeater_id']);

										if (!in_array($repeater_id[0], $repeater)) {
											$repeater[$repeater_id[0]] = array(
												'repeater_id' => $repeater_id[0],
												'repeater_label' => $field['repeater_label'],
											);
										}
									}
								} else {
									if (strpos($field['name'], 'pafe-end-repeater') === false) {
										$fields_database[$field['name']] = array(
											'name' => $field['name'],
											'value' => $field['value'],
											'label' => $field['label'],
										);
									}
								}
							}

							foreach ($repeater as $repeater_item) {

								$repeater_value = replace_email( '[repeater id="' . $repeater_item['repeater_id'] . '"]', $fields );
		            			$repeater_value = str_replace("<br />","\n", $repeater_value);
								$repeater_value = str_replace("<br/>","\n", $repeater_value);
								$repeater_value = str_replace("<br>","\n", $repeater_value);

								$fields_database[$repeater_item['repeater_id']] = array(
									'name' => $repeater_item['repeater_id'],
									'value' => nl2br($repeater_value),
									'label' => $repeater_item['repeater_label'],
								);
							}

							foreach ($fields_database as $field) {
								$fields_database[$field['name']] = array(
									'name' => $field['name'],
									'value' =>rtrim( str_replace('\n', '
', $field['value']) ),
									'label' => $field['label'],
								);
							}

							if (!empty($charge)) {
								$fields_database['payment_id'] = array(
									'name' => 'payment_id',
									'value' => $charge->id,
									'label' => 'Payment ID',
								);
								$fields_database['payment_customer_id'] = array(
									'name' => 'payment_customer_id',
									'value' => $charge->customer,
									'label' => 'Payment Customer ID',
								);
								$fields_database['payment_description'] = array(
									'name' => 'payment_description',
									'value' => $charge->description,
									'label' => 'Payment Description',
								);
								$fields_database['payment_amount'] = array(
									'name' => 'payment_amount',
									'value' => $charge->amount,
									'label' => 'Payment Amount',
								);
								$fields_database['payment_currency'] = array(
									'name' => 'payment_currency',
									'value' => $charge->currency,
									'label' => 'Payment Currency',
								);
								$fields_database['payment_status'] = array(
									'name' => 'payment_status',
									'value' => $charge->status,
									'label' => 'Payment Status',
								);
							}

							if (!empty($_POST['paypal_transaction_id'])) {
								$fields_database['payment_id'] = array(
									'name' => 'payment_id',
									'value' => $payment_id,
									'label' => 'Payment ID',
								);
								$fields_database['transaction_id'] = array(
									'name' => 'transaction_id',
									'value' => $payment_id,
									'label' => 'Payment ID',
								);
								$fields_database['payment_status'] = array(
									'name' => 'payment_status',
									'value' => $payment_status,
									'label' => 'Payment Status',
								);
							}
							
							if($form['settings']['form_database_hidden_field_option'] && !empty($form['settings']['form_database_list_hidden_field'])){
								$field_database_hidden = $form['settings']['form_database_list_hidden_field'];
								foreach($field_database_hidden as $hidden_value){
									if(!empty($fields_database[$hidden_value['form_database_hidden_field']]['value'])){
										$fields_database[$hidden_value['form_database_hidden_field']]['value'] = '********';
									}
								}
							}
							update_post_meta( $form_database_post_id, '_pafe_form_builder_fields_database', json_encode($fields_database, JSON_UNESCAPED_UNICODE) );

							foreach ($fields_database as $field) {
								// Remove HTML Tag Repeater
								$field_value = strip_tags($field['value']);
								update_post_meta( $form_database_post_id, $field['name'], $field_value);
							}
						}
					}

					// End add to Form Database

					// Google Calendar

					if (!empty($form['settings']['google_calendar_enable'])) {
						pafe_process_google_calendar($form, $fields, $payment_id);
					}

					// Submit Post

					if( in_array('submit_post', $form['settings']['submit_actions']) ) {
						$sp_user_id = get_current_user_id();
						if(!$sp_user_id && in_array("register", $form['settings']['submit_actions'])){
							$sp_user_data = get_user_by( 'email', replace_email($form['settings']['register_email'], $fields));
							$sp_user_id = $sp_user_data->id;
						}
						$sp_post_type = $form['settings']['submit_post_type'];
						$sp_post_taxonomy = $form['settings']['submit_post_taxonomy'];
						$sp_terms = $form['settings']['submit_post_terms_list'];
						$sp_term_slug = $form['settings']['submit_post_term_slug'];
						$sp_status = $form['settings']['submit_post_status'];
						$sp_title = $form['settings']['submit_post_title'];
						$sp_content = get_field_name_shortcode( $form['settings']['submit_post_content'] );
						$sp_term = get_field_name_shortcode( $form['settings']['submit_post_term'] );
						$sp_featured_image = get_field_name_shortcode( $form['settings']['submit_post_featured_image'] );
						$sp_custom_fields = $form['settings']['submit_post_custom_fields_list'];

						$post_title = $post_content = $post_tags = $post_term = $post_featured_image = '';

						$post_title = replace_email($sp_title, $fields);

						foreach ($fields as $field) {
							// if ($field['name'] == $sp_title) {
							// 	$post_title = $field['value'];
							// }
							if ($field['name'] == $sp_content) {
								$post_content = $field['value'];
							}
							if ($field['name'] == $sp_term) {
								$post_term = $field['value'];
							}
							if ($field['name'] == $sp_featured_image) {
								$post_featured_image = $field['value'];
							}
						}

						if ( !empty($post_title) ) {
							$submit_post = array(
								'post_type'		=> $sp_post_type,
								'post_status'   => $sp_status,
								'post_title'    => wp_strip_all_tags( $post_title ),
								'post_content'  => $post_content,
							);
							if($sp_user_id){
								$submit_post['post_author'] = $sp_user_id;
							}

							if (empty($_POST['edit'])) {
								$submit_post_id = wp_insert_post( $submit_post );
							} else {
								$submit_post_id = intval($_POST['edit']);

								$submit_post = array(
									'ID'            => $submit_post_id,
									'post_type'		=> $sp_post_type,
									'post_title'    => wp_strip_all_tags( $post_title ),
									'post_content'  => $post_content,
								);

								wp_update_post( $submit_post );
							}

							if (!empty($post_featured_image)) {
								$post_featured_image_array = explode(',', $post_featured_image);
								$post_featured_image_id = attachment_url_to_postid( $post_featured_image_array[0] );
								if (!empty($post_featured_image_id)) {
									set_post_thumbnail( $submit_post_id, intval( $post_featured_image_id ) );
								} else {
									// Gives us access to the download_url() and wp_handle_sideload() functions
									require_once( ABSPATH . 'wp-admin/includes/file.php' );

									// URL to the WordPress logo
									$url = $post_featured_image_array[0];
									$timeout_seconds = 15;

									// Download file to temp dir
									$temp_file = download_url( $url, $timeout_seconds );

									if ( !is_wp_error( $temp_file ) ) {

										// Array based on $_FILE as seen in PHP file uploads
										$file = array(
											'name'     => basename($url), // ex: wp-header-logo.png
											'type'     => 'image/png',
											'tmp_name' => $temp_file,
											'error'    => 0,
											'size'     => filesize($temp_file),
										);

										$overrides = array(
											// Tells WordPress to not look for the POST form
											// fields that would normally be present as
											// we downloaded the file from a remote server, so there
											// will be no form fields
											// Default is true
											'test_form' => false,

											// Setting this to false lets WordPress allow empty files, not recommended
											// Default is true
											'test_size' => true,
										);

										// Move the temporary file into the uploads directory
										$results = media_handle_sideload( $file, $submit_post_id );

										if ( !is_wp_error($results) ) {
											$post_featured_image_id = $results;
											if (!empty($post_featured_image_id)) {
												set_post_thumbnail( $submit_post_id, intval( $post_featured_image_id ) );
											}
										}

									}
								}
							}

							if (!empty($sp_post_taxonomy) && empty($sp_terms)) {
								$sp_post_taxonomy = explode('-', $sp_post_taxonomy);
								$sp_post_taxonomy = $sp_post_taxonomy[0];
								if (!empty($sp_term_slug)) {
									wp_set_object_terms( $submit_post_id, $sp_term_slug, $sp_post_taxonomy );
								}
								if (!empty($sp_term)) {
									wp_set_object_terms( $submit_post_id, $post_term, $sp_post_taxonomy );
								}
							}

							if (!empty($sp_terms)) {
								foreach ($sp_terms as $sp_terms_item) {
									$sp_post_taxonomy = explode('|', $sp_terms_item['submit_post_taxonomy']);
									$sp_post_taxonomy = $sp_post_taxonomy[0];
									$sp_term_slug = $sp_terms_item['submit_post_terms_slug'];
									$sp_term = get_field_name_shortcode( $sp_terms_item['submit_post_terms_field_id'] );
									$post_term = '';
									foreach ($fields as $field) {
										if ($field['name'] == $sp_term) {
											if (strpos($field['value'], ',') !== false) {
												$post_term = explode(',', $field['value']);
											} else {
												$post_term = $field['value'];
											}
										}
									}
									
									$terms_array = array();

									if (!empty($sp_term_slug)) {
										$terms_array[] = $sp_term_slug;
									}

									if (!empty($post_term)) {
										if (is_array($post_term)) {
											$terms_array = array_merge($terms_array,$post_term);
										} else {
											$terms_array[] = $post_term;
										}
									}

									wp_set_object_terms( $submit_post_id, $terms_array, $sp_post_taxonomy );
								}
							}

							foreach ($sp_custom_fields as $sp_custom_field) {
								if ( !empty( $sp_custom_field['submit_post_custom_field'] ) ) {
									$custom_field_value = '';
									$meta_type = $sp_custom_field['submit_post_custom_field_type'];

									foreach ($fields as $field) {
										if ($field['name'] == get_field_name_shortcode( $sp_custom_field['submit_post_custom_field_id'] )) {
											$custom_field_value = $field['value'];
											$custom_field_value_array = $field;
										}
									}

									if ($meta_type == 'repeater') {
										foreach ($fields_array as $field_key => $value) {
											if ($field_key == get_field_name_shortcode( $sp_custom_field['submit_post_custom_field_id'] )) {
												$custom_field_value = $value;
											}
										}

										if (!empty($custom_field_value)) {
											array_walk($custom_field_value, function (& $item, $custom_field_value_key, $submit_post_id_value) {
												foreach ($item as $key => $value) {
													$field_object = get_field_object(acf_get_field_key( $key, $submit_post_id_value ));
													if (!empty($field_object)) {
														$field_type = $field_object['type'];
														$item_value = $value;

														if ($field_type == 'repeater') {
															foreach ($item_value as $item_value_key => $item_value_element) {
																foreach ($field_object['sub_fields'] as $item_sub_field) {
																	foreach ($item_value_element as $item_value_element_key => $item_value_element_value) {
																		if ($item_sub_field['name'] == $item_value_element_key) {
																			if ($item_sub_field['type'] == 'image') {
																				$image_array = explode(',', $item_value_element_value);
																				$image_id = attachment_url_to_postid($image_array[0]);

																				if (!empty($image_id)) {
																					$item_value[$item_value_key][$item_value_element_key] = $image_id;
																				}
																			}
																		}
																	}
																}
															}
														}

														if ($field_type == 'image') {
															$image_array = explode(',', $item_value);
															$image_id = attachment_url_to_postid( $image_array[0] );
															if (!empty($image_id)) {
																$item_value = $image_id;
															}
														}

														if ($field_type == 'gallery') {
															$images_array = explode(',', $item_value);
															$images_id = array();
															foreach ($images_array as $images_item) {
																if (!empty($images_item)) {
																	$image_id = attachment_url_to_postid( $images_item );
																	if (!empty($image_id)) {
																		$images_id[] = $image_id;
																	}
																}
															}
															if (!empty($images_id)) {
																$item_value = $images_id;
															}
														}

														if ($field_type == 'select' && strpos($item_value, ',') !== false || $field_type == 'checkbox') {
															$item_value = explode(',', $item_value);
														}

														if ($field_type == 'true_false') {
															$item_value = !empty($item_value) ? 1 : 0;
														}

														if ($field_type == 'date_picker') {
															$time = strtotime( $item_value );

															if (empty($item_value)) {
																$item_value = '';
															} else {
																$item_value = date($field_object['return_format'],$time);
															}
														}

														if ($field_type == 'time') {
															$time = strtotime( $item_value );
															$item_value = date($field_object['return_format'],$time);
														}

														// if ($meta_type == 'google_map') {
														// 	$custom_field_value = array('address' => $custom_field_value_array['value'], 'lat' => $custom_field_value_array['lat'], 'lng' => $custom_field_value_array['lng'], 'zoom' => $custom_field_value_array['zoom']);
														// }

														$item[$key] = $item_value;
													}
												}
											}, $submit_post_id);
										}
									}
									// Jetengine MetaBoxes
									if ($meta_type == 'jet_engine_repeater') {
										foreach ($fields_array as $field_key => $value) {
											if ($field_key == get_field_name_shortcode( $sp_custom_field['submit_post_custom_field_id'] )) {
												$custom_field_value = $value;
											}
										}

										if (!empty($custom_field_value)) {
											foreach ($custom_field_value as $item_key => $custom_field_item) {
												foreach ($custom_field_item as $key => $value) {
													$field_object = jetengine_repeater_get_field_object( $key, $sp_custom_field['submit_post_custom_field'] );
													if (!empty($field_object)) {
														$field_type = $field_object['type'];
														$item_value = $value;

														if ($field_type == 'media') {
															$image_array = explode(',', $item_value);
															$image_id = attachment_url_to_postid( $image_array[0] );
															if (!empty($image_id)) {
																$item_value = $image_id;
															}
														}

														if ($field_type == 'gallery') {
															$images_array = explode(',', $item_value);
															$images_id = '';
															foreach ($images_array as $images_item) {
																if (!empty($images_item)) {
																	$image_id = attachment_url_to_postid( $images_item );
																	if (!empty($image_id)) {
																		$images_id .= $image_id . ',';
																	}
																}
															}
															if (!empty($images_id)) {
																$item_value = rtrim($images_id, ',');
															}
														}

														if ($field_type == 'checkbox') {
															$item_value = explode(',', $item_value);
															foreach ($item_value as $itemx) {
																$item_value[$itemx] = 'true';
															}
														}

														if ($field_type == 'date') {
															$time = strtotime( $item_value );
															if (empty($item_value)) {
																$item_value = '';
															} else {
																$item_value = date('Y-m-d',$time);
															}
														}

														if ($field_type == 'time') {
															$time = strtotime( $item_value );
															$item_value = date('H:i',$time);
														}

														$custom_field_item[$key] = $item_value;
													}
												}
												$custom_field_value[$item_key] = $custom_field_item;
											}
										}
									}

									// Metabox Group
									if ($meta_type == 'meta_box_group') {
										foreach ($fields_array as $field_key => $value) {
											if ($field_key == get_field_name_shortcode($sp_custom_field['submit_post_custom_field_id'])) {
												$custom_field_value = $value;
											}
										}
										$custom_field_group_id = $sp_custom_field['submit_post_custom_field_group_id'];
										$agrs = array(
											'name' => $custom_field_group_id,
											'post_type' => 'meta-box',
										);

										$custom_field_post_id = get_posts($agrs)[0]->ID;
										$custom_field_objects = get_post_meta($custom_field_post_id, 'meta_box');

										if (!empty($custom_field_value)) {
											array_walk($custom_field_value, function (& $item, $custom_field_value_key, $custom_field_object_value) {
												foreach ($item as $key => $value) {
													$field_object = metabox_group_get_field_object( $key, $custom_field_object_value );
													if (!empty($field_object)) {
														$field_type = $field_object['type'];
														$item_value = $value;

														if ( ($field_type == 'group') && ($field_object['clone']) ) {
															foreach ($item_value as $item_value_key => $item_value_element ) {
																foreach ($field_object['fields'] as $fields_items) {
																	foreach ($item_value_element as $item_value_element_key => $item_value_element_value) {
																		if ( $fields_items['id'] == $item_value_element_key ) {
																			if ($fields_items['type'] == 'single_image') {
																				$image_array = explode(',', $item_value_element_value);
																				$image_id = attachment_url_to_postid($image_array[0]);
																				if (!empty($image_id)) {
																					$item_value[$item_value_key][$item_value_element_key] = $image_id;
																				}
																			}
																		}
																	}
																}
															}
														}

														if ($field_type == 'single_image') {
															$image_array = explode(',', $item_value);
															$image_id = attachment_url_to_postid( $image_array[0] );
															if (!empty($image_id)) {
																$item_value = $image_id;
															}
														}

														if ($field_type == 'image') {
															$images_array = explode(',', $item_value);
															$images_id = '';
															foreach ($images_array as $images_item) {
																if (!empty($images_item)) {
																	$image_id = attachment_url_to_postid( $images_item );
																	if (!empty($image_id)) {
																		$images_id .= $image_id . ',';
																	}
																}
															}
															if (!empty($images_id)) {
																$item_value = explode(',', $images_id);
															}
														}

														if ($field_type == 'date') {
															$time = strtotime( $item_value );
															if (empty($item_value)) {
																$item_value = '';
															} else {
																$item_value = date('Y-m-d',$time);
															}
														}

														if ($field_type == 'time') {
															$time = strtotime( $item_value );
															$item_value = date('H:i',$time);
														}

														if ($field_type == 'select') {
															if (strpos($item_value, ',') !== false) {
																$item_value = explode(',', $item_value);
															}
														}

														if ($field_type == 'checkbox') {
															$item_value = explode(',', $item_value);
														}

														$item[$key] = $item_value;
													}
												}
											}, $custom_field_objects);
										}
									}

									//if (!empty($custom_field_value)) {
										if (function_exists('update_field') && $form['settings']['submit_post_custom_field_source'] == 'acf_field') {

											if ($meta_type == 'image') {
												$image_array = explode(',', $custom_field_value);
												$image_id = attachment_url_to_postid( $image_array[0] );
												if (!empty($image_id)) {
													$custom_field_value = $image_id;
												}
											}

											if ($meta_type == 'gallery') {
												$images_array = explode(',', $custom_field_value);
												$images_id = array();
												foreach ($images_array as $images_item) {
													if (!empty($images_item)) {
														$image_id = attachment_url_to_postid( $images_item );
														if (!empty($image_id)) {
															$images_id[] = $image_id;
														}
													}
												}
												if (!empty($images_id)) {
													$custom_field_value = $images_id;
												}
											}

											if ($meta_type == 'select' && strpos($custom_field_value, ',') !== false || $meta_type == 'checkbox' || $meta_type == 'acf_relationship') {
												$custom_field_value = explode(',', $custom_field_value);
											}

											if ($meta_type == 'true_false') {
												$custom_field_value = !empty($custom_field_value) ? 1 : 0;
											}

											if ($meta_type == 'date') {
												$time = strtotime( $custom_field_value );

												if (empty($custom_field_value)) {
													$custom_field_value = '';
												} else {
													$custom_field_value = date('Ymd',$time);
												}
											}

											if ($meta_type == 'time') {
												$time = strtotime( $custom_field_value );
												
												if (empty($custom_field_value)) {
													$custom_field_value = '';
												} else {
													$custom_field_value = date('H:i:s',$time);
												}
											}

											if ($meta_type == 'google_map') {
												$custom_field_value = array('address' => $custom_field_value_array['value'], 'lat' => $custom_field_value_array['lat'], 'lng' => $custom_field_value_array['lng'], 'zoom' => $custom_field_value_array['zoom']);
											}

											update_field( $sp_custom_field['submit_post_custom_field'], $custom_field_value, $submit_post_id );

										} elseif ($form['settings']['submit_post_custom_field_source'] == 'toolset_field') {

											$meta_key = 'wpcf-' . $sp_custom_field['submit_post_custom_field'];

											if ($meta_type == 'image') {
												$image_array = explode(',', $custom_field_value);
												if (!empty($image_array)) {
													update_post_meta( $submit_post_id, $meta_key, $image_array[0] );
												}
											} elseif ($meta_type == 'gallery') {
												$images_array = explode(',', $custom_field_value);
												delete_post_meta( $submit_post_id, $meta_key);
												foreach ($images_array as $images_item) {
													if (!empty($images_item)) {
														add_post_meta( $submit_post_id, $meta_key, $images_item );
													}
												}
											} elseif ($meta_type == 'checkbox') {
												$custom_field_value = explode(',', $custom_field_value);

												$field_toolset = wpcf_admin_fields_get_field($sp_custom_field['submit_post_custom_field']);

												if (isset($field_toolset['data']['options'])){
											        $res = array();
												    foreach ($field_toolset['data']['options'] as $key => $option){
												        if (in_array($option['set_value'], $custom_field_value)){
												            $res[$key] = $option['set_value'];
												        }
												    }   
											        update_post_meta( $submit_post_id, $meta_key , $res );
												}
											} elseif ($meta_type == 'date') {
												$custom_field_value = strtotime( $custom_field_value );
												update_post_meta( $submit_post_id, $meta_key, $custom_field_value );
											} else {

												update_post_meta( $submit_post_id, $meta_key, $custom_field_value );

											}

										} elseif ($form['settings']['submit_post_custom_field_source'] == 'jet_engine_field') {
											if ($meta_type == 'image') {
												$image_array = explode(',', $custom_field_value);
												$image_id = attachment_url_to_postid( $image_array[0] );
												if (!empty($image_id)) {
													$custom_field_value = $image_id;
												}
											}

											if ($meta_type == 'gallery') {
												$images_array = explode(',', $custom_field_value);
												$images_id = '';
												foreach ($images_array as $images_item) {
													if (!empty($images_item)) {
														$image_id = attachment_url_to_postid( $images_item );
														if (!empty($image_id)) {
															$images_id .= $image_id . ',';
														}
													}
												}
												if (!empty($images_id)) {
													$custom_field_value = rtrim($images_id, ',');
												}
											}

											if ($meta_type == 'date') {
												$time = strtotime( $custom_field_value );

												if (empty($custom_field_value)) {
													$custom_field_value = '';
												} else {
                                                    if(!empty($sp_custom_field['submit_post_save_timestamp'])){
                                                        $custom_field_value = $time;
                                                    }else{
                                                        $custom_field_value = date('Y-m-d',$time);
                                                    }
												}
											}

											if ($meta_type == 'select') {
												if (strpos($custom_field_value, ',') !== false) {
													$custom_field_value = explode(',', $custom_field_value);
												}
											}

											if ($meta_type == 'checkbox') {
												$value_array = array();
												$custom_field_value = explode(',', $custom_field_value);
												foreach ($custom_field_value as $item) {
													$value_array[$item] = true;
												}
												$custom_field_value = $value_array;
											}

											if ($meta_type == 'time') {
												$time = strtotime( $custom_field_value );
												$custom_field_value = date('H:i',$time);
											}

											update_post_meta( $submit_post_id, $sp_custom_field['submit_post_custom_field'], $custom_field_value );
										//PODS
										} else if (function_exists('pods_field_update') && $form['settings']['submit_post_custom_field_source'] == 'pods_field') {
											if ($meta_type == 'image') {
												$image_array = explode(',', $custom_field_value);
												$image_id = attachment_url_to_postid( $image_array[0] );
												if (!empty($image_id)) {
													$custom_field_value = $image_id;
												}
											}

											if ($meta_type == 'gallery') {
												$images_array = explode(',', $custom_field_value);
												$images_id = array();
												foreach ($images_array as $images_item) {
													if (!empty($images_item)) {
														$image_id = attachment_url_to_postid( $images_item );
														if (!empty($image_id)) {
															$images_id[] = $image_id;
														}
													}
												}
												if (!empty($images_id)) {
													$custom_field_value = $images_id;
												}
											}

											if ($meta_type == 'true_false') {
												$custom_field_value = !empty($custom_field_value) ? 1 : 0;
											}

											if ($meta_type == 'date') {
												$time = strtotime( $custom_field_value );
												if (empty($custom_field_value)) {
													$custom_field_value = '';
												} else {
													$custom_field_value = date('Y-m-d',$time);
												}
											}

											if ($meta_type == 'time') {
												$time = strtotime( $custom_field_value );
												
												if (empty($custom_field_value)) {
													$custom_field_value = '';
												} else {
													$custom_field_value = date('H:i:s',$time);
												}
											}

											pods_field_update( $sp_post_type, $submit_post_id, $sp_custom_field['submit_post_custom_field'], $custom_field_value );
										//META BOX
										} elseif (function_exists('rwmb_set_meta') && $form['settings']['submit_post_custom_field_source'] == 'metabox_field') {
											if ($meta_type == 'image') {
												$image_array = explode(',', $custom_field_value);
												$image_id = attachment_url_to_postid( $image_array[0] );
												if (!empty($image_id)) {
													$custom_field_value = $image_id;
												}
											}

											if ($meta_type == 'gallery') {
												$images_array = explode(',', $custom_field_value);
												$images_id = '';
												foreach ($images_array as $images_item) {
													if (!empty($images_item)) {
														$image_id = attachment_url_to_postid( $images_item );
														if (!empty($image_id)) {
															$images_id .= $image_id . ',';
														}
													}
												}
												if (!empty($images_id)) {
													$custom_field_value = explode(',', $images_id);
												}
											}

											if ($meta_type == 'date') {
												$time = strtotime( $custom_field_value );
												if (empty($custom_field_value)) {
													$custom_field_value = '';
												} else {
													$custom_field_value = date('Y-m-d',$time);
												}
											}

											if ($meta_type == 'time') {
												$time = strtotime( $custom_field_value );
												$custom_field_value = date('H:i',$time);
											}

											if ($meta_type == 'select') {
												if (strpos($custom_field_value, ',') !== false) {
													$custom_field_value = explode(',', $custom_field_value);
												}
											}

											if ($meta_type == 'checkbox') {
												$custom_field_value = explode(',', $custom_field_value);
											}

											if ($meta_type == 'metabox_google_map') {
												$custom_field_value = $custom_field_value_array['value'];
											}

											rwmb_set_meta( $submit_post_id, $sp_custom_field['submit_post_custom_field'], $custom_field_value, $custom_field_value );

										} else {
											update_post_meta( $submit_post_id, $sp_custom_field['submit_post_custom_field'], $custom_field_value );
										}
									//}
								}
							}

							update_post_meta( $submit_post_id, '_submit_button_id', $submit_button_id );
							update_post_meta( $submit_post_id, '_submit_post_id', $post_id );

							$post_url = get_permalink( $submit_post_id );
						}							
					}

					// End Submit Post

					// Webhook

					$repeater = array();

					foreach ($fields as $field) {
						$field_name = $field['name'];

						if (strpos($field['name'], 'pafe-end-repeater') === false && empty($field['repeater_id'])) {
							$fields_data[$field_name]['id'] = $field['name'];
							$fields_data[$field_name]['title'] = $field['label'];
							$fields_data[$field_name]['value'] = $field['value'];

							if (isset($field['value_label'])) {
	                            $fields_data[$field_name]['value_label'] = $field['value_label'];
	                        }
						}

						if (!empty($field['repeater_id'])) {
							if (substr_count($field['repeater_id'],',') == 1) {
								$repeater_id = explode('|', $field['repeater_id']);

								if (!in_array($repeater_id[0], $repeater)) {
									$repeater[$repeater_id[0]] = array(
										'repeater_id' => $repeater_id[0],
										'repeater_label' => $field['repeater_label'],
									);
								}
							}

						}

					}

					foreach ($repeater as $repeater_item) {
						$fields_data[$repeater_item['repeater_id']]['id'] = $repeater_item['repeater_id'];
						$fields_data[$repeater_item['repeater_id']]['title'] = $repeater_item['repeater_label'];
						$fields_data[$repeater_item['repeater_id']]['value'] = $fields_array[$repeater_item['repeater_id']];
					}

					if (!empty($submit_post_id)) {
						$form_submission['submit_post_id'] = $submit_post_id;
					}

					$form_submission['fields'] = $fields_data;

					$form_submission['form']['id'] = $form_id;

					$form_submission['submission_id'] = $form_database_post_id;

					if( in_array('webhook', $form['settings']['submit_actions']) && !empty($form['settings']['webhooks'])) {

						$body['fields'] = $form_submission['fields'];
						$body['form'] = $form_submission['form'];
						$body['submission_id'] = $form_submission['submission_id'];
						$args = [
							'body' => $body,
						];
						$webhook_response = wp_remote_post( replace_email($form['settings']['webhooks'], $fields), $args );
					}else{
						$webhook_response = false;
					}

					// Google Sheets

					if (!empty($form['settings']['pafe_form_google_sheets_connector_enable']) && !empty($form['settings']['pafe_form_google_sheets_connector_field_list']) && !empty($form['settings']['pafe_form_google_sheets_connector_id'])) {
						$row = array();
						$fieldList = $form['settings']['pafe_form_google_sheets_connector_field_list'];
						$columnArray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
						$fieldColumns = array();

						for ($i = 0; $i < count($fieldList); $i++) {
			            	$fieldColumns[] = getIndexColumn($fieldList[$i]['pafe_form_google_sheets_connector_field_column']);
				        }

						for ($z = 0; $z < (max($fieldColumns) + 1); $z++) {
							$value = '';

						 	for ($i = 0; $i < count($fieldList); $i++) {
					            $fieldID = $fieldList[$i]['pafe_form_google_sheets_connector_field_id'];
					            $fieldColumn = $fieldList[$i]['pafe_form_google_sheets_connector_field_column'];

				            	if ($z == getIndexColumn($fieldColumn)) {
				            		for($j=0; $j < count($fieldList); $j++) {
				            			$value = replace_email($fieldID,$fields,$payment_status, $payment_id, 'succeeded', 'pending', 'failed', $form_database_post_id );
				            			$value = str_replace("<br />","\n", $value);
										$value = str_replace("<br/>","\n", $value);
										$value = str_replace("<br>","\n", $value);
										$value = str_replace("<strong>","", $value);
										$value = str_replace("</strong>","", $value);
										$value = strip_tags($value);

					        		}
				            	}

					        }

					        $row[] = strpos($value, '[field id="') === false ? $value : '';
				        }
					    // Submission
					    //$row = rtrim($row, ',');
					    // Config
					    $gs_sid = $form['settings']['pafe_form_google_sheets_connector_id'];
					    $gs_tab = !empty($form['settings']['pafe_form_google_sheets_connector_tab']) ? $form['settings']['pafe_form_google_sheets_connector_tab'] . '!' : '';
					    $gs_clid = get_option('piotnet-addons-for-elementor-pro-google-sheets-client-id');
					    $gs_clis = get_option('piotnet-addons-for-elementor-pro-google-sheets-client-secret');
					    $gs_rtok = get_option('piotnet-addons-for-elementor-pro-google-sheets-refresh-token');
					    //$gs_atok = false;
					    $gs_url = 'https://sheets.googleapis.com/v4/spreadsheets/' . $gs_sid . '/values/' . $gs_tab . 'A1:append?includeValuesInResponse=false&insertDataOption=INSERT_ROWS&responseDateTimeRenderOption=SERIAL_NUMBER&responseValueRenderOption=FORMATTED_VALUE&valueInputOption=USER_ENTERED';
					    //$gs_body = '{"majorDimension":"ROWS", "values":[[' . $row . ']]}';

					    $gs_body = array(
     						"majorDimension" => "ROWS",
     						"values" => array($row),
					    );

					    $gs_body = json_encode($gs_body);
					    // HTTP Request Token Refresh

						$google_sheet_expired_token = get_option('piotnet-addons-for-elementor-pro-google-sheets-expired-token');
						$google_sheet_expired_token = (int)$google_sheet_expired_token;
						$google_sheet_current_time = time();

						if ($google_sheet_expired_token < $google_sheet_current_time) {
							$google_sheets_request = [
								'body' => [],
								'headers' => array(
									'Content-type' => 'application/x-www-form-urlencoded',
								),
							];
							$google_sheets = wp_remote_post('https://www.googleapis.com/oauth2/v4/token?client_id=' . $gs_clid . '&client_secret=' . $gs_clis . '&refresh_token=' . $gs_rtok . '&grant_type=refresh_token', $google_sheets_request);
							$google_sheets = json_decode(wp_remote_retrieve_body($google_sheets));
							if (!empty($google_sheets->access_token)) {
								$gs_atok = $google_sheets->access_token;
								$gg_sheet_newexpired = get_option('piotnet-addons-for-elementor-pro-google-sheets-expires');
								$gg_sheet_newexpired = (int)$gg_sheet_newexpired;

								update_option( 'piotnet-addons-for-elementor-pro-google-sheets-access-token', $gs_atok );

								$google_gheet_new_expired_token = time() + $gg_sheet_newexpired;

								update_option( 'piotnet-addons-for-elementor-pro-google-sheets-expired-token', $google_gheet_new_expired_token );
							}
						}
						$gs_atok = get_option('piotnet-addons-for-elementor-pro-google-sheets-access-token');

						$google_sheets_request_send = [
							'body' => $gs_body,
							'headers' => array(
								'Content-length' => strlen($gs_body),
								'Content-type' => 'application/json',
								'Authorization' => 'OAuth ' . $gs_atok,
							),
						];
						$google_sheets_send = wp_remote_post($gs_url, $google_sheets_request_send);
					}

					// Mailchimp V3
					if (in_array("mailchimp_v3", $form['settings']['submit_actions'])) {
						$mailchimp_acceptance = true;
					
						if (!empty($form['settings']['mailchimp_acceptance_field_shortcode_v3'])) {
							$mailchimp_acceptance_value = pafe_get_field_value($form['settings']['mailchimp_acceptance_field_shortcode_v3'],$fields);
							if (empty($mailchimp_acceptance_value)) {
								$mailchimp_acceptance = false;
							}
						}
						if($mailchimp_acceptance){
							$mailchimp_source = $form['settings']['mailchimp_api_key_source_v3'];
							$list_id = $form['settings']['mailchimp_list_id'];
							if($mailchimp_source == 'default'){
								$mailchimp_api_key = get_option('piotnet-addons-for-elementor-pro-mailchimp-api-key');
							}else{
								$mailchimp_api_key = $form['settings']['mailchimp_api_key_v3'];
							}
							$mailchimp_data = [];
							$mailchimp_field_mapping = $form['settings']['mailchimp_field_mapping_list_v3'];
							$interests = !empty($form['settings']['mailchimp_group_id']) ? explode(',', $form['settings']['mailchimp_group_id']) : [];
							if(!empty($list_id)){
								foreach($mailchimp_field_mapping as $field){
									if($field['mailchimp_field_mapping_tag_name_v3'] == 'email_address'){
										$memberId =  md5(strtolower(replace_email($field['mailchimp_field_mapping_field_shortcode_v3'],$fields,$payment_status, $payment_id)));
										$mailchimp_data[$field['mailchimp_field_mapping_tag_name_v3']] = replace_email($field['mailchimp_field_mapping_field_shortcode_v3'],$fields,$payment_status, $payment_id);
									}elseif($field['mailchimp_field_mapping_tag_name_v3'] == 'ADDRESS'){
										$mailchimp_data['merge_fields']['ADDRESS']['addr1'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_address_1'],$fields,$payment_status, $payment_id);
										$mailchimp_data['merge_fields']['ADDRESS']['addr2'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_address_1'],$fields,$payment_status, $payment_id);
										$mailchimp_data['merge_fields']['ADDRESS']['city'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_city'],$fields,$payment_status, $payment_id);
										$mailchimp_data['merge_fields']['ADDRESS']['state'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_state'],$fields,$payment_status, $payment_id);
										$mailchimp_data['merge_fields']['ADDRESS']['zip'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_zip'],$fields,$payment_status, $payment_id);
										$mailchimp_data['merge_fields']['ADDRESS']['country'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_country'],$fields,$payment_status, $payment_id);
									}elseif (empty($field['mailchimp_field_mapping_tag_name_v3'])) {
										$mailchimp_groups = replace_email($field['mailchimp_field_mapping_field_shortcode_v3'],$fields,$payment_status, $payment_id);
										$mailchimp_groups = explode(',', $mailchimp_groups);
										$interests = array_merge($interests, $mailchimp_groups);
									}elseif ($field['mailchimp_field_mapping_tag_name_v3'] == 'tags') {
										$mailchimp_data['tags'] = [];
										$mailchimp_tags = explode(',', $field['mailchimp_field_mapping_field_shortcode_v3']);
										foreach($mailchimp_tags as $tag){
											array_push($mailchimp_data['tags'], replace_email($tag,$fields,$payment_status, $payment_id));
										}
									}
									else{
										$mailchimp_data['merge_fields'][$field['mailchimp_field_mapping_tag_name_v3']] = replace_email($field['mailchimp_field_mapping_field_shortcode_v3'],$fields,$payment_status, $payment_id);
									}
								}
								$mailchimp_data['status'] = !empty($form['settings']['mailchimp_confirm_email_v3']) ? 'pending' : 'subscribed';
								if(!empty($interests)){
									foreach($interests as $interest){
										$mailchimp_data['interests'][$interest] = true;
									}
								}
								$helper = new PAFE_Helper();
								$mailchimp_url = 'https://' . substr($mailchimp_api_key,strpos($mailchimp_api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/'.$list_id.'/members/'.$memberId.'';
								if(!empty($mailchimp_data['merge_fields']['ADDRESS'])){
									if(empty($mailchimp_data['merge_fields']['ADDRESS']['addr1']) || empty($mailchimp_data['merge_fields']['ADDRESS']['zip']) || empty($mailchimp_data['merge_fields']['ADDRESS']['state']) || empty($mailchimp_data['merge_fields']['ADDRESS']['city'])){
										echo "Please enter a valid address.";
									}else{
										$mailchimp_res = $helper->mailchimp_curl_put_member($mailchimp_url, $mailchimp_api_key, $mailchimp_data);
									}
								}else{
									$mailchimp_res = $helper->mailchimp_curl_put_member($mailchimp_url, $mailchimp_api_key, $mailchimp_data);
								}
							}else{
								echo "Please enter list ID.";
							}
						}
					}

					// Mailchimp

					if (in_array("mailchimp", $form['settings']['submit_actions'])) {

						$mailchimp_acceptance = true;

						if (!empty($form['settings']['mailchimp_acceptance_field_shortcode'])) {
							$mailchimp_acceptance_value = pafe_get_field_value($form['settings']['mailchimp_acceptance_field_shortcode'],$fields);
							if (empty($mailchimp_acceptance_value)) {
								$mailchimp_acceptance = false;
							}
						}

						if ($mailchimp_acceptance) {

							$mailchimp_api_key_source = $form['settings']['mailchimp_api_key_source'];

							if ($mailchimp_api_key_source == 'default') {
								$mailchimp_api_key = get_option('piotnet-addons-for-elementor-pro-mailchimp-api-key');
							} else {
								$mailchimp_api_key = $form['settings']['mailchimp_api_key'];
							}

							$mailchimp_audience_id = $form['settings']['mailchimp_audience_id'];

							$mailchimp_field_mapping_list = $form['settings']['mailchimp_field_mapping_list'];

							if (!empty($mailchimp_api_key) && !empty($mailchimp_audience_id) && !empty($mailchimp_field_mapping_list)) {

								$MailChimp = new MailChimpPAFE($mailchimp_api_key);
								
								$merge_fields = array();

								foreach ($mailchimp_field_mapping_list as $item) {
									$key = $item['mailchimp_field_mapping_tag_name'];
									$shortcode = $item['mailchimp_field_mapping_field_shortcode'];
									if (!empty($key)) {

										if (!empty($shortcode)) {
											$merge_fields[$key] = pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id);
											if ($key == 'EMAIL' || $key == 'MERGE0') {
												$mailchimp_email = pafe_get_field_value($shortcode,$fields);
											}
										}

										if (!empty($item['mailchimp_field_mapping_address'])) {
											$merge_fields[$key] = array(
												'addr1' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_address_1'],$fields,$payment_status, $payment_id),
												'addr2' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_address_2'],$fields,$payment_status, $payment_id),
												'city' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_city'],$fields,$payment_status, $payment_id),
												'state' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_state'],$fields,$payment_status, $payment_id),
												'zip' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_zip'],$fields,$payment_status, $payment_id),
												'country' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_country'],$fields,$payment_status, $payment_id),
											);
										}

									}
								}

								if (!empty($merge_fields) && !empty($mailchimp_email)) {
									$mailchimp_result = $MailChimp->post("lists/$mailchimp_audience_id/members", [
										'email_address' => $mailchimp_email,
										'merge_fields'  => $merge_fields,
										'status'        => 'subscribed',
									]);

									if ($MailChimp->success()) {
										// print_r($mailchimp_result);	
									} else {
										// echo $MailChimp->getLastError();
									}
								}
							}
						}
					}

					//Mailpoet
					if (in_array("mailpoet", $form['settings']['submit_actions'])) {
						$mailpoet_acceptance = true;
						if (!empty($form['settings']['mailpoet_acceptance_field_shortcode'])) {
							
							$mailpoet_acceptance_value = pafe_get_field_value($form['settings']['mailpoet_acceptance_field_shortcode'],$fields);
							if (empty($mailpoet_acceptance_value)) {
								$mailpoet_acceptance = false;
							}
						}
						if (class_exists(\MailPoet\API\API::class)) {
							if($mailpoet_acceptance == true){
								$mailpoet_api = \MailPoet\API\API::MP('v1');
								$mailpoet_field_mapping_list = $form['settings']['mailpoet_field_mapping_list'];
								$mailpoet_list = $form['settings']['mailpoet_select_list'];
								foreach($mailpoet_field_mapping_list as $item){
									$mailpoet_data[$item['mailpoet_field_mapping_tag_name']] = pafe_get_field_value($item['mailpoet_field_mapping_field_shortcode'],$fields,$payment_status, $payment_id);
								}
								$mailpoet_send_confirmation_email = !empty($form['settings']['mailpoet_send_confirmation_email']) ? true : false;
								$mailpoet_schedule_welcome_email = !empty($form['settings']['mailpoet_send_welcome_email']) ? true : false;
								$mailpoet_skip_subscriber_notification = !empty($form['settings']['skip_subscriber_notification']) ? true : false;
								$options = [
									'send_confirmation_email' => $mailpoet_send_confirmation_email,
									'schedule_welcome_email' => $mailpoet_schedule_welcome_email,
									'skip_subscriber_notification' => $mailpoet_skip_subscriber_notification
								];

								$get_subscriber = null;
								try {
									$get_subscriber = $mailpoet_api->getSubscriber($mailpoet_data['email']);
								} catch (\Exception $e) {
									//$error_message = $e->getMessage();
								}
								
								try {
									if (!$get_subscriber) {
										$mailpoet_api->addSubscriber($mailpoet_data, $mailpoet_list, $options);
									}
								} catch (\Exception $e) {
									//$error_message = $e->getMessage(); 
								}
							}
						}else{
							echo "Please install Mailpoet plugin.";
						}
					}

					//Mailerlite V2
					if (in_array("mailerlite_v2", $form['settings']['submit_actions'])) {
						$mailerlite_acceptance = true;
						if (!empty($form['settings']['mailerlite_api_acceptance_field_shortcode'])) {
							$mailerlite_acceptance_value = pafe_get_field_value($form['settings']['mailerlite_api_acceptance_field_shortcode'],$fields);
							if (empty($mailerlite_acceptance_value)) {
								$mailerlite_acceptance = false;
							}
						}
						if ($mailerlite_acceptance == true) {
							$mailerlite_api = $form['settings']['mailerlite_api_key_source_v2'];
							if ($mailerlite_api == 'default') {
								$mailerlite_api_key = get_option('piotnet-addons-for-elementor-pro-mailerlite-api-key');
							}else{
								$mailerlite_api_key = $form['settings']['mailerlite_api_key_v2'];
							}
							$mailerlite_api_group = $form['settings']['mailerlite_api_group'];
							$mailerlite_api_url = !empty($mailerlite_api_group) ? 'https://api.mailerlite.com/api/v2/groups/'.$mailerlite_api_group.'/subscribers' : 'https://api.mailerlite.com/api/v2/subscribers';
							$mailerlite_field_mapping_list = $form['settings']['mailerlite_api_field_mapping_list_v2'];
							if(!empty($mailerlite_field_mapping_list)){
								$mailerlite_data = [];
								foreach($mailerlite_field_mapping_list as $item){
									if($item['mailerlite_api_field_mapping_tag_name_v2'] == 'name' || $item['mailerlite_api_field_mapping_tag_name_v2'] == 'email'){
										$mailerlite_data[$item['mailerlite_api_field_mapping_tag_name_v2']] = pafe_get_field_value($item['mailerlite_api_field_mapping_field_shortcode_v2'],$fields,$payment_status, $payment_id);
									}else{
										$mailerlite_data['fields'][$item['mailerlite_api_field_mapping_tag_name_v2']] =  pafe_get_field_value($item['mailerlite_api_field_mapping_field_shortcode_v2'],$fields,$payment_status, $payment_id);
									}
								}
							}
							$curl = curl_init();
							curl_setopt_array($curl, array(
								CURLOPT_URL => $mailerlite_api_url,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => "",
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 30,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => "POST",
								CURLOPT_POSTFIELDS => json_encode($mailerlite_data),
								CURLOPT_HTTPHEADER => array(
									"content-type: application/json",
									"x-mailerlite-apikey: ".$mailerlite_api_key.""
								),
							));
	
							$response = curl_exec($curl);
							$err = curl_error($curl);
	
							curl_close($curl);
						}
					}
					//Convertkit
					if (in_array("convertkit", $form['settings']['submit_actions'])) {
						$convertkit_acceptance = true;
						if (!empty($form['settings']['convertkit_acceptance_field']) && !empty($form['settings']['convertkit_acceptance_field_shortcode'])) {
							$convertkit_acceptance_value = pafe_get_field_value($form['settings']['convertkit_acceptance_field_shortcode'],$fields);
							if (empty($convertkit_acceptance_value)) {
								$convertkit_acceptance = false;
							}
						}
						if($convertkit_acceptance == true) {
							$convertkit_api = $form['settings']['convertkit_api_key_source'];
							if ($convertkit_api == 'default') {
								$convertkit_api_key = get_option('piotnet-addons-for-elementor-pro-convertkit-api-key');
							}else{
								$convertkit_api_key = $form['settings']['convertkit_api_key'];
							}
							$convertkit_form_id = $form['settings']['convertkit_form_id'];
							$convertkit_fields = $form['settings']['convertkit_field_mapping_list'];
							if(!empty($convertkit_fields) && !empty($convertkit_form_id)){
								$data_convertkit = ['api_key' => $convertkit_api_key];
								foreach($convertkit_fields as $index => $convertkit){
									if(in_array($convertkit['convertkit_tag_name'], ['first_name', 'email', 'tags'])){
										$data_convertkit[$convertkit['convertkit_tag_name']] = pafe_get_field_value($convertkit['convertkit_shortcode'],$fields,$payment_status, $payment_id);
									}else{
										$data_convertkit['fields'][$convertkit['convertkit_tag_name']] = pafe_get_field_value($convertkit['convertkit_shortcode'],$fields,$payment_status, $payment_id);
									}
								}
								if(isset($data_convertkit['tags'])){
									$data_convertkit['tags'] = explode(',', $data_convertkit['tags']);
								}
								$data_convertkit['state'] = 'active';
								$helper = new PAFE_Helper();
								$convertkit_result = $helper->pafe_convertkit_add_subscriber($data_convertkit, $convertkit_form_id);
							}
						}
					}
					//Sendinblue
					if (in_array("sendinblue", $form['settings']['submit_actions'])) {
						$sendinblue_acceptance = true;
						if (!empty($form['settings']['sendinblue_api_acceptance_field_shortcode'])) {
							$sendinblue_acceptance_value = pafe_get_field_value($form['settings']['sendinblue_api_acceptance_field_shortcode'],$fields);
							if (empty($sendinblue_acceptance_value)) {
								$sendinblue_acceptance = false;
							}
						}
						if($sendinblue_acceptance == true) {
							$sendinblue_api = $form['settings']['sendinblue_api_key_source'];
							if ($sendinblue_api == 'default') {
								$sendinblue_api_key = get_option('piotnet-addons-for-elementor-pro-sendinblue-api-key');
							}else{
								$sendinblue_api_key = $form['settings']['sendinblue_api_key'];
							}
							$sendinblue_lists = explode(',', $form['settings']['sendinblue_list_ids']);
							foreach($sendinblue_lists as $key => $list){
								$sendinblue_lists[$key] = intval($list);
							}
							$sendinblue_field_mapping_list = $form['settings']['sendinblue_fields_map'];
							if(!empty($sendinblue_field_mapping_list)){
								$data_sendinblue = [];
								$helper = new PAFE_Helper();
								foreach($sendinblue_field_mapping_list as $key => $val){
									if($val['sendinblue_tagname'] == 'email'){
										$data_sendinblue['email'] = pafe_get_field_value($val['sendinblue_shortcode'],$fields,$payment_status, $payment_id);
									}else{
										$data_sendinblue['attributes'][$val['sendinblue_tagname']] = pafe_get_field_value($val['sendinblue_shortcode'],$fields,$payment_status, $payment_id);
									}
								}
								$data_sendinblue['updateEnabled'] = !empty($form['settings']['sendinblue_api_update_contact']) ? true : false;
								$data_sendinblue['listIds'] = $sendinblue_lists;
								$data_sendinblue = json_encode($data_sendinblue);
								$sendinblue_result = $helper->sendinblue_create_contact($sendinblue_api_key, $data_sendinblue);
							}
						}
					}
					//PDF Genrenator
					if (in_array("pdfgenerator", $form['settings']['submit_actions'])) {
						$pdf_generator_list = $form['settings']['pdfgenerator_field_mapping_list'];
						$pdf_page_size = $form['settings']['pdfgenerator_size'];
						$pdf_content_font_size = !empty($form['settings']['pdfgenerator_set_custom']) ? $form['settings']['pdfgenerator_font_size']['size'] : $form['settings']['pdfgenerator_heading_field_mapping_font_size']['size'];
						$pdf = new PDF_Template();
						if($form['settings']['pdfgenerator_font_family'] != 'default' && $form['settings']['pdfgenerator_font_family'] != 'Courier' && $form['settings']['pdfgenerator_font_family'] != 'Helvetica' && $form['settings']['pdfgenerator_font_family'] != 'Times'){
							$pdf_font_family = substr($form['settings']['pdfgenerator_font_family'], strrpos($form['settings']['pdfgenerator_font_family'], '/')+1);
							$pdf_font_name = strtolower(substr($pdf_font_family ,0,(strpos($pdf_font_family ,'.'))));
							$pdf->AddFont($pdf_font_name,'',$form['settings']['pdfgenerator_font_family'],true);
						}
						$pfd_font_ratio = 0.9;
						$pdf->AddPage('', $pdf_page_size);
						if($form['settings']['pdfgenerator_import_template'] == 'yes' && !empty($form['settings']['pdfgenerator_template_url'])){
							$pdf_template =  realpath($_SERVER['DOCUMENT_ROOT'] . parse_url($form['settings']['pdfgenerator_template_url'], PHP_URL_PATH ));
							$pdf->setSourceFile($pdf_template);
							$tplIdx = $pdf->importPage(1);
							$pdf->useTemplate($tplIdx);
						}

						$pdf_color = pafe_hexToRgb($form['settings']['pdfgenerator_color']);
						
						$pdf->AddFont('dejavu','','DejaVuSans.ttf',true);
						$pdf->AddFont('dejavubold','','DejaVuSans-Bold.ttf',true);
						$pdf->AddFont('dejavuitalic','','DejaVuSerif-Italic.ttf',true);
						$pdf->AddFont('dejavu-bolditalic','','DejaVuSerif-BoldItalic.ttf',true);
						if($form['settings']['pdfgenerator_font_family'] == 'default' || $form['settings']['pdfgenerator_font_family'] == ''){
							$pdf->SetFont('dejavu','',$pdf_content_font_size* $pfd_font_ratio);
						}
						elseif ($form['settings']['pdfgenerator_font_family'] == 'Courier' || $form['settings']['pdfgenerator_font_family'] == 'Helvetica' || $form['settings']['pdfgenerator_font_family'] == 'Times') {
							$pdf->SetFont($form['settings']['pdfgenerator_font_family'],'',$pdf_content_font_size);
						}else{
							$pdf->SetFont($pdf_font_name,'',$pdf_content_font_size);
						}
						$pdf->SetTextColor($pdf_color['r'], $pdf_color['g'], $pdf_color['b']);
						
						if( $form['settings']['pdfgenerator_background_image_enable'] == 'yes'){
							if(isset( $form['settings']['pdfgenerator_background_image']['url'])){
								$pdf_generator_image =  $form['settings']['pdfgenerator_background_image']['url'];
							}
						}
						
						if(!empty($pdf_generator_image)){
							wp_http_validate_url($pdf_generator_image) ? $pdf->Image($pdf_generator_image,0,0,210) : '';
						}
						if(!empty($form['settings']['pdfgenerator_title'])){
							$pdf->Cell(0,5,replace_email($form['settings']['pdfgenerator_title'],$fields,$payment_status, $payment_id),0,1,strtoupper(substr($form['settings']['pdfgenerator_title_text_align'],0,1)));
							$pdf->Ln(15);
						}
						
						if($form['settings']['pdfgenerator_set_custom']=='yes'){
							foreach($pdf_generator_list as $item){
								if($item['custom_font'] == 'yes'){
									if(($form['settings']['pdfgenerator_font_family'] == 'default' || $form['settings']['pdfgenerator_font_family'] == 'Courier' || $form['settings']['pdfgenerator_font_family'] == 'Helvetica' || $form['settings']['pdfgenerator_font_family'] == 'Times') && ($item['font_weight'] == 'N' || $item['font_weight'] == 'I' || $item['font_weight'] == 'B' || $item['font_weight'] == 'BI')){
										if($item['font_weight'] == 'I'){
											if($form['settings']['pdfgenerator_font_family'] == 'default'){
												$pdf->SetFont('dejavuitalic','',$item['font_size']['size'] * $pfd_font_ratio);	
											}else{
												$pdf->SetFont($form['settings']['pdfgenerator_font_family'],'I',$form['settings']['pdfgenerator_font_size']['size']);
											}
										}elseif($item['font_weight'] == 'B'){
											if($form['settings']['pdfgenerator_font_family'] == 'default'){
												$pdf->SetFont('dejavubold','',$item['font_size']['size'] * $pfd_font_ratio);	
											}else{
												$pdf->SetFont($form['settings']['pdfgenerator_font_family'],'B',$item['font_size']['size']);
											}
										}else{
											if($form['settings']['pdfgenerator_font_family'] == 'default'){
												$pdf->SetFont('dejavu', '', $item['font_size']['size'] * $pfd_font_ratio);
											}else{
												$pdf->SetFont($form['settings']['pdfgenerator_font_family'],'BI',$item['font_size']['size']);
											}
										}
									}else{
										if($item['font_weight'] != 'N' && $item['font_weight'] != 'I' && $item['font_weight'] != 'B' && $item['font_weight'] != 'BI'){
											$pdf_item_font_family = substr($item['font_weight'], strrpos($item['font_weight'], '/')+1);
											$pdf_item_font_name = strtolower(substr($pdf_item_font_family ,0,(strpos($pdf_item_font_family ,'.'))));
											$pdf->AddFont($pdf_item_font_name,'',$item['font_weight'],true);
											$pdf->SetFont($pdf_item_font_name,'',$item['font_size']['size']);
										}else{
											if($form['settings']['pdfgenerator_font_family'] != 'default' && $form['settings']['pdfgenerator_font_family'] != 'Courier' && $form['settings']['pdfgenerator_font_family'] != 'Helvetica' && $form['settings']['pdfgenerator_font_family'] != 'Times'){
												$pdf_font_family = substr($form['settings']['pdfgenerator_font_family'], strrpos($form['settings']['pdfgenerator_font_family'], '/')+1);
												$pdf_font_name = strtolower(substr($pdf_font_family ,0,(strpos($pdf_font_family ,'.'))));
												$pdf->AddFont($pdf_font_name,'',$form['settings']['pdfgenerator_font_family'],true);
												$pdf->SetFont($pdf_font_name,'',$item['font_size']['size']);
											}else{
												$pdf->SetFont($form['settings']['pdfgenerator_font_family'],'',$item['font_size']['size']);
											}
										}
									}
									$pdf_color = pafe_hexToRgb($item['color']);
									$pdf->SetTextColor($pdf_color['r'], $pdf_color['g'], $pdf_color['b']);
								}
								else{
									if($form['settings']['pdfgenerator_font_family'] == 'default' || $form['settings']['pdfgenerator_font_family'] == ''){
										$pdf->SetFont('dejavu','',$pdf_content_font_size* $pfd_font_ratio);
									}
									elseif ($form['settings']['pdfgenerator_font_family'] == 'Courier' || $form['settings']['pdfgenerator_font_family'] == 'Helvetica' || $form['settings']['pdfgenerator_font_family'] == 'Times') {
										$pdf->SetFont($form['settings']['pdfgenerator_font_family'],'',$pdf_content_font_size);
									}else{
										$pdf->SetFont($pdf_font_name,'',$pdf_content_font_size);
									}
									$pdf_color = pafe_hexToRgb($form['settings']['pdfgenerator_color']);
									$pdf->SetTextColor($pdf_color['r'], $pdf_color['g'], $pdf_color['b']);
								}
								if($form['settings']['pdfgenerator_size'] == 'a3'){
									$item_x = (float)$item['pdfgenerator_set_x']['size'] * 2.97;
									$item_y = (float)$item['pdfgenerator_set_y']['size'] * 4.2;
									$item_width = (float)$item['pdfgenerator_width']['size'] * 2.97;
									$image_height = (float)$item['pdfgenerator_height']['size']* 4.2;
									$item_image_x = (float)$item['pdfgenerator_image_set_x']['size'] * 2.97;
									$item_image_y = (float)$item['pdfgenerator_image_set_y']['size'] * 4.2;
								}elseif($form['settings']['pdfgenerator_size'] == 'a4'){
									$item_x = (float)$item['pdfgenerator_set_x']['size'] * 2.1;
									$item_y = (float)$item['pdfgenerator_set_y']['size'] * 2.97;
									$item_width = (float)$item['pdfgenerator_width']['size'] * 2.1;
									$image_height = (float)$item['pdfgenerator_height']['size']* 2.97;
									$item_image_x = (float)$item['pdfgenerator_image_set_x']['size'] * 2.1;
									$item_image_y = (float)$item['pdfgenerator_image_set_y']['size'] * 2.97;
								}elseif($form['settings']['pdfgenerator_size'] == 'a5'){
									$item_x = (float)$item['pdfgenerator_set_x']['size'] * 1.48;
									$item_y = (float)$item['pdfgenerator_set_y']['size'] * 2.1;
									$item_width = (float)$item['pdfgenerator_width']['size'] * 1.48;
									$image_height = (float)$item['pdfgenerator_height']['size']* 2.1;
									$item_image_x = (float)$item['pdfgenerator_image_set_x']['size'] * 1.48;
									$item_image_y = (float)$item['pdfgenerator_image_set_y']['size'] * 2.1;
								}elseif($form['settings']['pdfgenerator_size'] == 'letter'){
									$item_x = (float)$item['pdfgenerator_set_x']['size'] * 2.159;
									$item_y = (float)$item['pdfgenerator_set_y']['size'] * 2.794;
									$item_width = (float)$item['pdfgenerator_width']['size'] * 2.159;
									$image_height = (float)$item['pdfgenerator_height']['size']* 2.794;
									$item_image_x = (float)$item['pdfgenerator_image_set_x']['size'] * 2.159;
									$item_image_y = (float)$item['pdfgenerator_image_set_y']['size'] * 2.794;
								}else{
									$item_x = (float)$item['pdfgenerator_set_x']['size'] * 2.159;
									$item_y = (float)$item['pdfgenerator_set_y']['size'] * 3.556;
									$item_width = (float)$item['pdfgenerator_width']['size'] * 2.159;
									$image_height = (float)$item['pdfgenerator_height']['size']* 3.556;
									$item_image_x = (float)$item['pdfgenerator_image_set_x']['size'] * 2.159;
									$item_image_y = (float)$item['pdfgenerator_image_set_y']['size'] * 3.556;
								}
								$type = $item['pdfgenerator_field_type'];
								if($type == 'image'){
									$pdf_image_url = !empty(pafe_get_field_value($item['pdfgenerator_field_shortcode'],$fields,$payment_status, $payment_id)) ? pafe_get_field_value($item['pdfgenerator_field_shortcode'],$fields,$payment_status, $payment_id) : false;
									if(wp_http_validate_url($pdf_image_url)){
										$pdf->Image($pdf_image_url, $item_image_x, $item_image_y, $item_width, $image_height);
									}
								}elseif($type == 'image-upload'){
									$pdf_image_url = !empty($item['pdfgenerator_image_field']['url']) ? $item['pdfgenerator_image_field']['url'] : false;
									wp_http_validate_url($pdf_image_url) ? $pdf->Image($pdf_image_url, $item_image_x, $item_image_y, $item_width) : '';
								}
								else{
									if($item['auto_position'] == 'yes'){
										$pdf_txt = pafe_get_field_value($item['pdfgenerator_field_shortcode'],$fields,$payment_status, $payment_id);
										$pdf->WriteHTML($pdf_txt,false);
									}else{
										$payment_status = !empty($form['settings']['pafe_stripe_enable']) ? 'stripe' : $payment_status;
										$pdf_txt = replace_email($item['pdfgenerator_field_shortcode'],$fields, $payment_status, $payment_id, 'Success	', '', '', $form_database_post_id);
										$pdf_text_align = !empty($item['text_align']) ? strtoupper(substr($item['text_align'], 0, 1)) : '';
										if($form['settings']['pdfgenerator_content_html'] == 'yes'){
											if(!empty($form['settings']['remove_empty_form_input_fields'])){
												if(strpos($pdf_txt, '[field id="') !== false){
													continue;
												}else{
													$pdf->WriteHTML2($pdf_txt,$item_width,$item_x, $item_y, $pdf_color, $pdf_text_align);
												}
											}else{
												$pdf->WriteHTML2($pdf_txt,$item_width,$item_x, $item_y, $pdf_color, $pdf_text_align);
											}
										}else{
											if(!empty($form['settings']['remove_empty_form_input_fields'])){
												if(strpos($pdf_txt, '[field id="') !== false){
													continue;
												}else{
													$pdf->WriteCell($pdf_txt,$item_width,$item_x, $item_y, $pdf_text_align);
												}
											}else{
												$pdf->WriteCell($pdf_txt,$item_width,$item_x, $item_y, $pdf_text_align);
											}
										}
									}
								}
							}
						}else{
							$pdf_color = pafe_hexToRgb($form['settings']['pdfgenerator_heading_field_mapping_color']);
							$pdf->SetTextColor($pdf_color['r'], $pdf_color['g'], $pdf_color['b']);
							if(strtoupper(substr($form['settings']['pdfgenerator_heading_field_mapping_text_align'],0,1)) == 'L'){
								$image_alignment = 0;
							}elseif(strtoupper(substr($form['settings']['pdfgenerator_heading_field_mapping_text_align'],0,1)) == 'C'){
								$image_alignment = 50;
							}else{
								$image_alignment = 100;
							}
							foreach($fields as $item){
								if($form['settings']['pdfgenerator_heading_field_mapping_show_label'] == 'yes' && !empty($item['label'])){
									$pdf_text = $item['label'] .': '. replace_email($item['value'],$fields,$payment_status, $payment_id, '', '', '', $form_database_post_id).'<br>';
								}else{
									$pdf_text = replace_email($item['value'],$fields,$payment_status, $payment_id, '', '', '', $form_database_post_id).'<br>';
								}
								if($item['type'] == 'signature' || $item['type'] == 'image'){
									if($form['settings']['pdfgenerator_heading_field_mapping_show_label'] == 'yes' && !empty($item['label'])){
										$pdf->Cell(0,5,$item['label'],0,2, strtoupper(substr($form['settings']['pdfgenerator_heading_field_mapping_text_align'],0,1)));
										wp_http_validate_url($item['value']) ? $pdf->Image($item['value'], $image_alignment) : '';
									}else{
										wp_http_validate_url($item['value']) ? $pdf->Image($item['value'], $image_alignment) : '';
									}
									
								}else{
									if(!empty($form['settings']['remove_empty_form_input_fields'])){
										if(!empty($item['value'])){
											$pdf->WriteHTML($pdf_text,false);
										}
									}else{
										$pdf->WriteHTML($pdf_text,false);
									}
								}
							}
						}
						if($form['settings']['pdfgenerator_custom_export_file'] == 'yes' && !empty($form['settings']['pdfgenerator_export_file_name'])){
							$pdf_file_name = replace_email($form['settings']['pdfgenerator_export_file_name'],$fields,$payment_status, $payment_id, '', '', '', $form_database_post_id);
							if(!empty($form['settings']['pdfgenerator_save_file'])){
								$pdf_file_name = $pdf_file_name . '_'. uniqid();
							}
						}else{
							$pdf_file_name = $form_database_post_id;
						}
						$pdf->Output('F', $pdf_file_name . '.pdf', true, $upload_dir . '/');

						$attachment[] = WP_CONTENT_DIR . '/uploads/piotnet-addons-for-elementor/' . $pdf_file_name . '.pdf';
					}

					// MailerLite

					if (in_array("mailerlite", $form['settings']['submit_actions'])) {

						$mailerlite_api_key_source = $form['settings']['mailerlite_api_key_source'];

						if ($mailerlite_api_key_source == 'default') {
							$mailerlite_api_key = get_option('piotnet-addons-for-elementor-pro-mailerlite-api-key');
						} else {
							$mailerlite_api_key = $form['settings']['mailerlite_api_key'];
						}

						$mailerlite_group_id = $form['settings']['mailerlite_group_id'];

						$mailerlite_email = pafe_get_field_value( $form['settings']['mailerlite_email_field_shortcode'], $fields );

						$mailerlite_field_mapping_list = $form['settings']['mailerlite_field_mapping_list'];

						if ( !empty($mailerlite_email) && !empty($mailerlite_api_key) && !empty($mailerlite_group_id) ) {

							$mailerlite_url = 'https://api.mailerlite.com/api/v2/groups/' . $mailerlite_group_id . '/subscribers';

							$mailerlite_body = array(
								'email' => $mailerlite_email,
							);

							if (!empty($mailerlite_field_mapping_list)) {
								$mailerlite_fields = array();
								foreach ($mailerlite_field_mapping_list as $item) {
									$key = $item['mailerlite_field_mapping_tag_name'];
									$shortcode = $item['mailerlite_field_mapping_field_shortcode'];
									if (!empty($key) && !empty($shortcode)) {
										if ($key != 'email') {
											$mailerlite_fields[$key] = pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id);
										}
									}
								}

								$mailerlite_body['fields'] = $mailerlite_fields;
							}

							$mailerlite_request_data = [
								'headers' => array(
									'X-MailerLite-ApiKey' => $mailerlite_api_key,
									'Content-Type' => 'application/json',
							    ),
							    'body' => json_encode( $mailerlite_body ),
							];

							$mailerlite_request = wp_remote_post( $mailerlite_url, $mailerlite_request_data );

						}
					}

					//Get Response

					if(in_array("getresponse", $form['settings']['submit_actions'])){
						$getresponse_api_key_source = $form['settings']['getresponse_api_key_source'];
						$form['settings']['pafe_getresponse_list'];
						if($getresponse_api_key_source == 'default'){
							$getresponse_api_key = get_option('piotnet-addons-for-elementor-pro-getresponse-api-key');
						}else{
							$getresponse_api_key = $form['settings']['getresponse_api_key'];
						}
						$getresponse_url_add_contact = "https://api.getresponse.com/v3/contacts/";
						$items = $form['settings']['getresponse_field_mapping_list'];
						if(!empty($items)){
							$get_response_fields = array();
							foreach($items as $item){
								$key = $item['getresponse_field_mapping_tag_name'];
								$shortcode = $item['getresponse_field_mapping_field_shortcode'];
								if (!empty($key) && !empty($shortcode)) {
									if($key == 'email' || $key ==  "name"){
										$get_response_fields[$key] = pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id);
									}elseif($item['getresponse_field_mapping_multiple'] == 'yes'){
										$get_response_fields['customFieldValues'][] = array(
											'customFieldId' => $key,
											'values' =>  pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id, 'succeeded', 'pending', 'failed', true),
										);
									}
									else{
										$get_response_fields['customFieldValues'][] = array(
											'customFieldId' => $key,
											'value' =>  array(
												pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id)
											)
										);
									}
								}
							}
							$get_response_fields['ipAddress'] = $_POST['remote_ip'];
							$get_response_fields['campaign'] = [
								'campaignId' => $form['settings']['getresponse_campaign_id']
							];
							if(!empty($form['settings']['getresponse_day_of_cycle'])){
                                $get_response_fields['dayOfCycle'] = $form['settings']['getresponse_day_of_cycle'];
                            }
							$getresponse_data = json_encode($get_response_fields);
							$ch = curl_init($getresponse_url_add_contact);
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
							curl_setopt($ch, CURLOPT_POSTFIELDS, $getresponse_data);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
								'Content-Type: application/json',
								'X-Auth-Token: api-key '.$getresponse_api_key,
							));
							
							$result =  curl_exec($ch);
						}
						
					}
					//Zoho CRM
					if (in_array("zohocrm", $form['settings']['submit_actions'])) {
						$zoho_acceptance = true;
						$helper = new PAFE_Helper();
						if (!empty($form['settings']['zoho_acceptance_field_shortcode'])) {
							
							$zoho_acceptance_value = pafe_get_field_value($form['settings']['zoho_acceptance_field_shortcode'],$fields);
							if (empty($zoho_acceptance_value)) {
								$zoho_acceptance = false;
							}
						}
						if($zoho_acceptance == true){
							$zoho_access_token = get_option('zoho_access_token');
							$zoho_refresh_token = get_option('zoho_refresh_token');
							$zoho_api_domain = get_option('zoho_api_domain');
							$zoho_api_module = $form['settings']['zohocrm_module'] == 'custom' ? $form['settings']['zohocrm_custom_module'] : $form['settings']['zohocrm_module'];
							$zoho_request_url = $zoho_api_domain.'/crm/v2/'.$zoho_api_module;
							$zoho_mapping_fields = $form['settings']['zohocrm_fields_map'];
							$zoho_data = [];
							$zoho_all_field = '';
							if(!empty($zoho_mapping_fields)){
								foreach($zoho_mapping_fields as $item){
									if($item['zohocrm_shortcode'] == '["all_field"]'){
										$zoho_all_field_tag_name = $item['zohocrm_tagname'];
									}else{
										if(strpos($item['zohocrm_tagname'], '@')){
											$zoho_field_type = substr($item['zohocrm_tagname'],strpos($item['zohocrm_tagname'], '@')+1);
											$zoho_tag_name = substr($item['zohocrm_tagname'],0,strpos($item['zohocrm_tagname'],"@"));
											if($zoho_field_type == 'date'){
												$zoho_data[$zoho_tag_name] = date_format(date_create(replace_email($item['zohocrm_shortcode'],$fields,$payment_status, $payment_id)), 'Y-m-d');
												$zoho_all_field .= $item[$zoho_tag_name].': '.date_format(date_create(replace_email($item['zohocrm_shortcode'],$fields,$payment_status, $payment_id)), 'Y-m-d').', ';
											}elseif($zoho_field_type == 'boolean'){
												$zoho_value = replace_email($item['zohocrm_shortcode'],$fields,$payment_status, $payment_id);
												if(!empty($zoho_value)){
													$zoho_data[$zoho_tag_name] = true;
												}else{
													$zoho_data[$zoho_tag_name] = false;
												}
												$zoho_all_field .= $item[$zoho_tag_name].': '.$zoho_data[$zoho_tag_name].', ';
											}elseif($zoho_field_type== 'multiselectpicklist'){
												$zoho_value = replace_email($item['zohocrm_shortcode'],$fields,$payment_status, $payment_id);
												$zoho_data[$zoho_tag_name] = explode(',', $zoho_value);
												$zoho_all_field .= $item['zohocrm_tagname'].': '.$zoho_value.', ';
											}
										}else{
											$zoho_data[$item['zohocrm_tagname']] = replace_email($item['zohocrm_shortcode'],$fields,$payment_status, $payment_id);
											$zoho_all_field .= $item['zohocrm_tagname'].': '.replace_email($item['zohocrm_shortcode'],$fields,$payment_status, $payment_id).', ';
										}
									}
								}
							}
							if(!empty($zoho_all_field_tag_name)){
								$zoho_data[$zoho_all_field_tag_name] = rtrim($zoho_all_field, ', ');
							}
							$zoho_result = $helper->zohocrm_post_record($zoho_data, $zoho_request_url, $zoho_access_token);
							$zoho_result = json_decode($zoho_result);
							if(!empty($zoho_result->code) && $zoho_result->code == 'INVALID_TOKEN'){
								$helper->zoho_refresh_token();
								$zoho_access_token = get_option('zoho_access_token');
								$zoho_result = $helper->zohocrm_post_record($zoho_data, $zoho_request_url, $zoho_access_token);
							}
						}
					}

					//Activecampaign
					if (in_array("activecampaign", $form['settings']['submit_actions'])) {
						$activecampaign_api_key_source = $form['settings']['activecampaign_api_key_source'];

						if ($activecampaign_api_key_source == 'default') {
							$activecampaign_api_key = get_option('piotnet-addons-for-elementor-pro-activecampaign-api-key');
							$activecampaign_api_url = get_option('piotnet-addons-for-elementor-pro-activecampaign-api-url');
						} else {
							$activecampaign_api_key = $form['settings']['activecampaign_api_key'];
							$activecampaign_api_url = $form['settings']['activecampaign_api_url'];
						}

						if (!empty($form['settings']['activecampaign_field_mapping_list'])) {
							$activecampaign_fields = array();
							foreach ($form['settings']['activecampaign_field_mapping_list'] as $item) {
								$key = $item['activecampaign_field_mapping_tag_name'];
								$shortcode = $item['activecampaign_field_mapping_field_shortcode'];
								if (!empty($key) && !empty($shortcode)) {
									if (strlen(strstr($key, '%')) > 0) {
										if(strlen(strstr($key, '@multiple')) > 0){
											$key = str_replace('@multiple', '', $key);
											$activecampaign_fields['field[' . $key . ']'] = '||'.str_replace(',', '||', replace_email($shortcode,$fields,$payment_status, $payment_id)).'||';
										}else{
											$activecampaign_fields['field[' . $key . ']'] = replace_email($shortcode,$fields,$payment_status, $payment_id);
										}
									} else {
										$activecampaign_fields[$key] = replace_email($shortcode,$fields,$payment_status, $payment_id);
									}

								}
							}

							$activecampaign_list_id = 'p[' . $form['settings']['activecampaign_list'] . ']';
							$activecampaign_fields[$activecampaign_list_id] = $form['settings']['activecampaign_list'];
							$activecampaign_status = 'status[' . $form['settings']['activecampaign_list'] . ']';
							$activecampaign_fields[$activecampaign_status] = 1;
							$activecampaign_instantresponders = 'instantresponders[' . $form['settings']['activecampaign_list'] . ']';
							$activecampaign_fields[$activecampaign_instantresponders] = 1;
						}

						$activecampaign_params = array(
							'api_key'      => $activecampaign_api_key,
							'api_action'   => 'contact_add',
							'api_output'   => 'serialize',
						);

						$activecampaign_query = "";
						foreach( $activecampaign_params as $key => $value ) $activecampaign_query .= urlencode($key) . '=' . urlencode($value) . '&';
						$activecampaign_query = rtrim($activecampaign_query, '& ');

						$activecampaign_data = "";
						foreach( $activecampaign_fields as $key => $value ) $activecampaign_data .= urlencode($key) . '=' . urlencode($value) . '&';
						$activecampaign_data = rtrim($activecampaign_data, '& ');

						$activecampaign_api_url = rtrim($activecampaign_api_url, '/ ');

						if ( !function_exists('curl_init') ) die('CURL not supported. (introduced in PHP 4.0.2)');

						if ( $activecampaign_params['api_output'] == 'json' && !function_exists('json_decode') ) {
							die('JSON not supported. (introduced in PHP 5.2.0)');
						}

						$activecampaign_api = $activecampaign_api_url . '/admin/api.php?' . $activecampaign_query;

						$helper = new PAFE_Helper();
						$activecampaign_res = $helper->activecampaign_add_contact($activecampaign_api, $activecampaign_data);
						if(!empty($form['settings']['activecampaign_edit_contact']) && !empty($activecampaign_res[0]['id']) && !$activecampaign_res['result_code']){
							$activecampaign_data = 'id='.$activecampaign_res[0]['id'].'&'.$activecampaign_data;
							$activecampaign_api = str_replace('api_action=contact_add', 'api_action=contact_edit', $activecampaign_api);
							$activecampaign_res = $helper->activecampaign_edit_contact($activecampaign_api, $activecampaign_data);
						}
					}
					// MailerLite

					if (in_array("mailerlite", $form['settings']['submit_actions'])) {

						$mailerlite_api_key_source = $form['settings']['mailerlite_api_key_source'];

						if ($mailerlite_api_key_source == 'default') {
							$mailerlite_api_key = get_option('piotnet-addons-for-elementor-pro-mailerlite-api-key');
						} else {
							$mailerlite_api_key = $form['settings']['mailerlite_api_key'];
						}

						$mailerlite_group_id = $form['settings']['mailerlite_group_id'];

						$mailerlite_email = pafe_get_field_value( $form['settings']['mailerlite_email_field_shortcode'], $fields );

						$mailerlite_field_mapping_list = $form['settings']['mailerlite_field_mapping_list'];

						if ( !empty($mailerlite_email) && !empty($mailerlite_api_key) && !empty($mailerlite_group_id) ) {

							$mailerlite_url = 'https://api.mailerlite.com/api/v2/groups/' . $mailerlite_group_id . '/subscribers';

							$mailerlite_body = array(
								'email' => $mailerlite_email,
							);

							if (!empty($mailerlite_field_mapping_list)) {
								$mailerlite_fields = array();
								foreach ($mailerlite_field_mapping_list as $item) {
									$key = $item['mailerlite_field_mapping_tag_name'];
									$shortcode = $item['mailerlite_field_mapping_field_shortcode'];
									if (!empty($key) && !empty($shortcode)) {
										if ($key != 'email') {
											$mailerlite_fields[$key] = pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id);
										}
									}
								}

								$mailerlite_body['fields'] = $mailerlite_fields;
							}

							$mailerlite_request_data = [
								'headers' => array(
									'X-MailerLite-ApiKey' => $mailerlite_api_key,
									'Content-Type' => 'application/json',
							    ),
							    'body' => json_encode( $mailerlite_body ),
							];

							$mailerlite_request = wp_remote_post( $mailerlite_url, $mailerlite_request_data );

						}
					}

					//Webhook Slack
					if (in_array("webhook_slack", $form['settings']['submit_actions'])) {
						$slack_webhook_url = $form['settings']['slack_webhook_url'];
						$slack_username = $form['settings']['slack_username'];
						$slack_icon_url = $form['settings']['slack_icon_url'];
						$slack_channel = $form['settings']['slack_channel'];
						$slack_pre_text = $form['settings']['slack_pre_text'];
						$slack_title = $form['settings']['slack_title'];
						$slack_description = $form['settings']['slack_description'];
						$slack_color = $form['settings']['slack_color'];

						$payload = array();
						if (!empty($form['settings']['slack_username'])) {
							$payload['username'] = $slack_username;
						}
						if (!empty($form['settings']['slack_icon_url'])) {
							$payload['icon_url'] = $slack_icon_url;
						}
						if (!empty($form['settings']['slack_channel'])) {
							$payload['channel'] = $slack_channel;
						}

						$slack_message = $form['settings']['slack_message'];
						$slack_text = replace_email($slack_message, $fields);

						$slack_text = str_replace("<br />","\n", $slack_text);
						$slack_text = str_replace("<br/>","\n", $slack_text);
						$slack_text = str_replace("<br>","\n", $slack_text);
						$slack_text = str_replace("<strong>","*", $slack_text);
						$slack_text = str_replace("</strong>","*", $slack_text);

						$slack_text = strip_tags($slack_text);

						$attachment = [
							'color' => $slack_color,
							'title' => $slack_title,
							'title_link' => get_site_url(),
							'pretext' => $slack_pre_text,
							'text' => $slack_text,
							'footer'=> 'PAFE Pro',
						];

						if ($form['settings']['slack_timestamp'] == 'yes') {
							$attachment['ts'] = time();
						}

						$payload['attachments'] = [$attachment];
						
						$curl = curl_init();
						curl_setopt_array($curl, array(
						  CURLOPT_URL => $slack_webhook_url,
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => '',
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => 'POST',
						  CURLOPT_POSTFIELDS => json_encode($payload),
						  CURLOPT_SSL_VERIFYPEER =>false,
						  CURLOPT_HTTPHEADER => array(
						    'Content-Type: application/json'
						  ),
						));

						$response = curl_exec($curl);

						curl_close($curl);
					}

					//SendGrid 
					if (in_array("twilio_sendgrid", $form['settings']['submit_actions'])) {
						// $sendgrid_url = $form['settings']['twilio_sendgrid_url'];
						$sendgrid_api_key = $form['settings']['twilio_sendgrid_api_key'];
						$sendgrid_list_ids = $form['settings']['twilio_sendgrid_list_ids'];
						$sendgrid_email = pafe_get_field_value( $form['settings']['twilio_sendgrid_email_field_shortcode'], $fields );
						$sendgrid_field_mapping_list = $form['settings']['twilio_sendgrid_field_mapping_list'];
						$sendgrid_custom_field_mapping_list = $form['settings']['twilio_sendgrid_field_mapping_custom_field_list'];

						// $sendgrid_api_key_source = $form['settings']['sendgrid_api_key_source'];
						// if ($sendgrid_api_key_source == 'default') {
						// 	$sendgrid_api_key = get_option('piotnet-addons-for-elementor-pro-sendgrid-api-key');
						// } else {
						// 	$sendgrid_api_key = $form['settings']['twilio_sendgrid_api_key'];
						// }

						if ( !empty($sendgrid_email) && !empty($sendgrid_api_key) && !empty($sendgrid_list_ids) ) {

							$post_fields = array();
							$sendgrid_fields = array();
							$custom_field = array();

							$sendgrid_list_ids = explode(',', $sendgrid_list_ids);
							
							foreach ($sendgrid_list_ids as $sendgrid_list_id) {
								$sendgrid_list_id = trim($sendgrid_list_id);
								$post_fields['list_ids'][] = $sendgrid_list_id;
							}

							$sendgrid_fields['email'] = $sendgrid_email;

							if ( !empty($sendgrid_field_mapping_list) ) {
								foreach ($sendgrid_field_mapping_list as $item) {
									$key = $item['twilio_sendgrid_field_mapping_tag_name'];
									$shortcode = $item['twilio_sendgrid_field_mapping_field_shortcode'];
									if ( !empty($key) && !empty($shortcode) ) {
										$sendgrid_fields[$key] = pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id);
									}
								}
							}

							$sendgrid_api_key = 'authorization: Bearer ' . $form['settings']['twilio_sendgrid_api_key'];

							if ( !empty($sendgrid_custom_field_mapping_list) ) {
								$curl = curl_init();
								curl_setopt_array($curl, array(
									CURLOPT_URL => "https://api.sendgrid.com/v3/marketing/field_definitions",
									CURLOPT_RETURNTRANSFER => true,
									CURLOPT_ENCODING => '',
									CURLOPT_MAXREDIRS => 10,
									CURLOPT_TIMEOUT => 0,
									CURLOPT_FOLLOWLOCATION => true,
									CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
									CURLOPT_CUSTOMREQUEST => 'GET',
									CURLOPT_SSL_VERIFYPEER => false,
									CURLOPT_HTTPHEADER => array(
										$sendgrid_api_key
									),
								));

								$response = curl_exec($curl);
								curl_close($curl);

								$response = json_decode($response, true);
								$custom_fields = $response["custom_fields"];

								foreach ($sendgrid_custom_field_mapping_list as $item) {
									foreach ($custom_fields as $field_item) {
										if ( $item['twilio_sendgrid_field_mapping_custom_field_name'] == $field_item['name'] ) {
											$key = $field_item['id'];
											$shortcode = $item['twilio_sendgrid_field_mapping_custom_field_shortcode'];
											if (!empty($key) && !empty($shortcode)) {
												if($field_item['field_type'] == 'Number'){
                                                    $custom_field[$key] = intval(pafe_get_field_value($shortcode, $fields, $payment_status, $payment_id));
                                                }else{
                                                    $custom_field[$key] = pafe_get_field_value($shortcode, $fields, $payment_status, $payment_id);
                                                }
											}
										}
									}
								}
							}

							if(!empty($custom_field)) {
								$sendgrid_fields['custom_fields'] = $custom_field;
							}

							if (!empty($sendgrid_fields)) {
							   $post_fields['contacts'] = [$sendgrid_fields];
							}

							$curl = curl_init();
							curl_setopt_array($curl, array(
								CURLOPT_URL => "https://api.sendgrid.com/v3/marketing/contacts",
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => '',
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 0,
								CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => 'PUT',
								CURLOPT_POSTFIELDS => json_encode($post_fields),
								CURLOPT_SSL_VERIFYPEER =>false,
								CURLOPT_HTTPHEADER => array(
								    $sendgrid_api_key,
								    'content-type: application/json'
								  ),
							));

							$response = curl_exec($curl);

							curl_close($curl);
						}
					}

					// Booking

					if (in_array("booking", $form['settings']['submit_actions'])) {

						$pafe_form_booking = array();

						foreach ($fields as $key => $field) {
							if (!empty($field['booking'])) {

								$booking = $field['booking'];
								foreach ($booking as $booking_key => $booking_item) {
									$booking_item = json_decode($booking_item, true);
									if ( !empty($booking_item['pafe_form_booking_date_field']) ) {
										$date = date( "Y-m-d", strtotime( replace_email($booking_item['pafe_form_booking_date_field'], $fields) ) );
									}
									$pafe_form_booking = array_merge($pafe_form_booking, array($booking_item) );
								}
							}
						}

						$pafe_form_booking = array_unique($pafe_form_booking, SORT_REGULAR);

						foreach ($pafe_form_booking as $booking) {

							if ( empty($booking['pafe_form_booking_date_field']) ) {
								$date = date( "Y-m-d", strtotime( $booking['pafe_form_booking_date'] ) );
							} else {									
								$date = date( "Y-m-d", strtotime( replace_email($booking['pafe_form_booking_date_field'], $fields) ) );
							}

							$slot_availble = 0;
							$slot = $booking['pafe_form_booking_slot'];
							$slot_query = new WP_Query(array(  
								'posts_per_page' => -1 , 
								'post_type' => 'pafe-form-booking',
								'meta_query' => array(                  
							       'relation' => 'AND',                 
								        array(
								            'key' => 'pafe_form_booking_id',                
								            'value' => $booking['pafe_form_booking_id'],                  
								            'type' => 'CHAR',                  
								            'compare' => '=',                  
								        ),
								        array(
								            'key' => 'pafe_form_booking_slot_id',                  
								            'value' => $booking['pafe_form_booking_slot_id'],                  
								            'type' => 'CHAR',                  
								            'compare' => '=',                  
								        ),
								        array(
								            'key' => 'pafe_form_booking_date',                  
								            'value' => $date,                  
								            'type' => 'CHAR',                  
								            'compare' => '=',                
								        ),
								        array(
								            'key' => 'payment_status',                  
								            'value' => 'succeeded',                  
								            'type' => 'CHAR',                  
								            'compare' => '=',                
								        ),
								),	
							));

							$slot_reserved = 0;

							if ($slot_query->have_posts()) {
								while($slot_query->have_posts()) {
									$slot_query->the_post();
									$slot_reserved += intval( get_post_meta(get_the_ID(), 'pafe_form_booking_quantity', true) );
								}
							}

							wp_reset_postdata();

							$slot_availble = $slot - $slot_reserved;

							$booking_slot = 1;

							if (!empty($booking['pafe_form_booking_slot_quantity_field'])) {
								$booking_slot = intval( replace_email($booking['pafe_form_booking_slot_quantity_field'], $fields) );
							}

							if ($slot_availble >= $booking_slot && !empty($slot_availble) && !empty($booking_slot)) {
								$booking_post = array( 
									'post_title'    =>  '#' . $form_database_post_id . ' ' . $booking['pafe_form_booking_title'],
									'post_status'   => 'publish',
									'post_type'		=> 'pafe-form-booking',
								);

								$form_booking_posts_id = wp_insert_post( $booking_post );

								if (empty($form_database_post_id)) {
									$form_database_post_id = $form_booking_posts_id;
									$booking_post = array(
										'ID' => $form_booking_posts_id,
										'post_title' =>  '#' . $form_booking_posts_id . ' ' . $booking['pafe_form_booking_title'],
									);
									wp_update_post( $booking_post );
								}

								foreach ($booking as $key_booking => $booking_data) {
									update_post_meta( $form_booking_posts_id, $key_booking, $booking_data );
								}

								update_post_meta( $form_booking_posts_id, 'pafe_form_booking_date', $date );
								update_post_meta( $form_booking_posts_id, 'pafe_form_booking_quantity', $booking_slot );
								update_post_meta( $form_booking_posts_id, 'order_id', $form_database_post_id );
								update_post_meta( $form_booking_posts_id, 'payment_status', $payment_status );
							} else {
								$failed = true;
							}
						}
					}

					// Replace redirect

					$redirect = '';

					if (in_array("redirect", $form['settings']['submit_actions'])) {
						$redirect = replace_email($form['settings']['redirect_to'], $fields, '', '', '', '', '', $form_database_post_id);
						$redirect = apply_filters( 'pafe/form_builder/redirect', $redirect, $fields );
					}

					// Woocommerce Add to Cart

					if (in_array("woocommerce_add_to_cart", $form['settings']['submit_actions'])) {
                        if ( class_exists( 'WooCommerce' ) ) {  
                            if (!empty($_POST['product_id']) && !empty($form['settings']['woocommerce_add_to_cart_price'])) {
                                if (strpos($_POST['product_id'], 'field id') !== false) {
                                    $product_id = intval( pafe_get_field_value( str_replace('\"', '"', $_POST['product_id']),$fields ) );
                                } else {
                                    $product_id = intval( $_POST['product_id'] );
                                }
                                
                                $cart_item_data = array();
                                $cart_item_data['fields'] = array();
                    
                                $fields_cart = $fields;
                    
                                if (!empty($form['settings']['woocommerce_add_to_cart_custom_order_item_meta_enable'])) {
                                    $fields_cart = array();
                                    foreach ($form['settings']['woocommerce_add_to_cart_custom_order_item_list'] as $item) {
                                        if (!empty($item['woocommerce_add_to_cart_custom_order_item_field_shortcode'])) {
                                            foreach ($fields as $key_field=>$field) {
                                                if (strpos($item['woocommerce_add_to_cart_custom_order_item_field_shortcode'], '[repeater id') !== false) {
                                                    if ($fields[$key_field]['repeater_id_one'] == get_field_name_shortcode( $item['woocommerce_add_to_cart_custom_order_item_field_shortcode'] )) {
                                                        if (!isset($fields_cart[$fields[$key_field]['repeater_id_one']])) {
                                                            $fields_cart[$fields[$key_field]['repeater_id_one']] = array(
                                                                'label' => $fields[$key_field]['repeater_label'],
                                                                'name' => $fields[$key_field]['repeater_id_one'],
                                                                'value' => str_replace( '\n', '<br>', pafe_get_field_value( '[repeater id="' . $fields[$key_field]['repeater_id_one'] . '"]',$fields,$payment_status, $payment_id, 'remove_zero') ),
                                                            );
                                                        }
                                                    }
                                                } else {
                                                    if ($fields[$key_field]['name'] == get_field_name_shortcode( $item['woocommerce_add_to_cart_custom_order_item_field_shortcode'] )) {
                                                        if (empty($item['woocommerce_add_to_cart_custom_order_item_remove_if_field_empty']) && $field['value'] != '0') {
                                                            $fields_cart[] = $field;
                                                        } else {
                                                            if (!empty($field['value']) && empty($item['woocommerce_add_to_cart_custom_order_item_remove_if_value_zero']) && !empty($field['value'])) {
                                                                $fields_cart[] = $field;
                                                            }elseif($field['value_not_prefix'] != '0' && !empty($field['value'])){
                                                                $fields_cart[] = $field;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                foreach ($fields as $key_field=>$field) {
                                    if ($fields[$key_field]['name'] == get_field_name_shortcode( $form['settings']['woocommerce_add_to_cart_price'] )) {
                                        if (isset($fields[$key_field]['calculation_results'])) {
                                            $cart_item_data['pafe_custom_price'] = $fields[$key_field]['calculation_results'];
                                        } else {
                                            $cart_item_data['pafe_custom_price'] = $fields[$key_field]['value'];
                                        }
                                    }
                                }
                    
                                foreach ($fields_cart as $key_field=>$field) {
                                    $field_value = $fields_cart[$key_field]['value'];
                                    if (isset($fields_cart[$key_field]['value_label'])) {
                                        $field_value = $fields_cart[$key_field]['value_label'];
                                    }
                    
                                    $cart_item_data['fields'][] = array(
                                        'label' => $fields_cart[$key_field]['label'],
                                        'name' => $fields_cart[$key_field]['name'],
                                        'value' => $field_value,
                                    );
                                }
                    
                                global $woocommerce;
                    
                                $woocommerce->cart->add_to_cart( $product_id, 1, 0, array(), $cart_item_data );
                    
                            }
                        }
                    }

					// Remote Request

					if (in_array('remote_request', $form['settings']['submit_actions']) && !empty($form['settings']['remote_request_url'])) {

						$wp_args = array();

						if (!empty($form['settings']['remote_request_arguments_list'])) {
							foreach ($form['settings']['remote_request_arguments_list'] as $item) {
								if (!empty($item['remote_request_arguments_parameter']) && !empty($item['remote_request_arguments_value'])) {
									$wp_args[$item['remote_request_arguments_parameter']] = replace_email($item['remote_request_arguments_value'], $fields);
								}
							}
						}

						if (!empty($form['settings']['remote_request_body_list'])) {
							$wp_args['body'] = array();
							foreach ($form['settings']['remote_request_body_list'] as $item) {
								if (!empty($item['remote_request_body_parameter']) && !empty($item['remote_request_body_value'])) {
									$wp_args['body'][$item['remote_request_body_parameter']] = replace_email($item['remote_request_body_value'], $fields);
									if(strpos($wp_args['body'][$item['remote_request_body_parameter']], '[post_url]') !== false && !empty($post_url)){
										$wp_args['body'][$item['remote_request_body_parameter']] = str_replace( '[post_url]', $post_url, $wp_args['body'][$item['remote_request_body_parameter']] );
									}
								}
							}
						}

		        		if (!empty($form['settings']['remote_request_header']) && !empty($form['settings']['remote_request_header_list'])) {
							$wp_args['headers'] = array();
							foreach ($form['settings']['remote_request_header_list'] as $item) {
								if (!empty($item['remote_request_header_parameter']) && !empty($item['remote_request_header_value'])) {
									$wp_args['headers'][$item['remote_request_header_parameter']] = replace_email($item['remote_request_header_value'], $fields);
								}
							}
						}

						if(!empty($wp_args['headers']['Content-Type']) && $wp_args['headers']['Content-Type'] == 'application/json'){
							$wp_args['body'] = json_encode($wp_args['body']);
						}
						$remote_request_response = wp_remote_retrieve_body(wp_remote_request(replace_email($form['settings']['remote_request_url'], $fields), $wp_args));

					}else{
						$remote_request_response = false;
					}

					//Hubspot integration

					if (in_array('hubspot', $form['settings']['submit_actions'])) {
						$hubspot_acceptance = true;
						$hubspot_access_token = get_option('piotnet-addons-for-elementor-pro-hubspot-access-token');
						if (!empty($form['settings']['pafe_hubspot_acceptance_field_shortcode'])) {
							$hubspot_acceptance_value = pafe_get_field_value($form['settings']['pafe_hubspot_acceptance_field_shortcode'],$fields);
							if (empty($hubspot_acceptance_value)) {
								$hubspot_acceptance = false;
							}
						}
						if ( $hubspot_acceptance == true ) {
							$hubspot_data = [];
							$hubspot_properties = $form['settings']['pafe_hubspot_property_list'];
							foreach ( $hubspot_properties as $item ) {
								$hubspot_data['properties'][] = [
									'property' => $item['pafe_hubspot_property_name'],
									'value' => replace_email($item['pafe_hubspot_field_shortcode'], $fields),
								];
							}
							$curl = curl_init();
							curl_setopt_array($curl, array(
								CURLOPT_URL => 'https://api.hubapi.com/contacts/v1/contact/',
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => '',
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 0,
								CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => 'POST',
								CURLOPT_POSTFIELDS =>json_encode($hubspot_data),
								CURLOPT_HTTPHEADER => array(
									'Authorization: Bearer '.$hubspot_access_token,
									'Content-Type: application/json'
								),
							));
							$response = curl_exec($curl);
							curl_close($curl);
						}
					}

					// Sendy

					if (in_array('sendy', $form['settings']['submit_actions'])) {
						if (!empty($form['settings']['sendy_url']) && !empty($form['settings']['sendy_api_key']) && !empty($form['settings']['sendy_name_field_shortcode']) && !empty($form['settings']['sendy_email_field_shortcode']) && !empty($form['settings']['sendy_list_id'])) {
							
							$sendy_url = $form['settings']['sendy_url'];
							$sendy_api = $form['settings']['sendy_api_key'];
							$sendy_name = replace_email($form['settings']['sendy_name_field_shortcode'], $fields);
							$sendy_email = replace_email($form['settings']['sendy_email_field_shortcode'], $fields);
							$sendy_list = $form['settings']['sendy_list_id'];
                            $sendy_custom_fields = count($form['settings']['sendy_custom_fields']);
					        // Set GDPR Value
					        //$gdpr = !empty($form['settings']['sendy_gdpr_shortcode']) ? 'true' : 'false';
					        $gdpr = false;
                            if(!empty($form['settings']['sendy_gdpr_shortcode'])){
                            	$gdpr_value = pafe_get_field_value($form['settings']['sendy_gdpr_shortcode'],$fields);
					        	$gdpr = !empty($gdpr_value) ? 'true' : 'false';
                            }

					        $sendy_data = [
					            'name' => $sendy_name,
					            'email' => $sendy_email,
					            'list' => $sendy_list,
					            'gdpr' => $gdpr,
					            'api_key' => $sendy_api,
					            'ipaddress' => \ElementorPro\Core\Utils::get_client_ip(),
					            'referrer' => isset( $_POST['referrer'] ) ? $_POST['referrer'] : '',
					      
					        ];

					        for($i = 0; $i < $sendy_custom_fields; $i++){
					            $custom_field_name = $form['settings']['sendy_custom_fields'][$i]['custom_field_name'];
					            $custom_field_id = $form['settings']['sendy_custom_fields'][$i]['custom_field_shortcode'];
					            $sendy_data[$custom_field_name] = replace_email($custom_field_id , $fields);
					        }

					       
					    }
                        // Send the request
					    $pafe_sendy = wp_remote_post( $sendy_url . 'subscribe', [
					        'body' => $sendy_data,
					    ] );
					}
					// Constantcontact
					if (in_array("constantcontact", $form['settings']['submit_actions'])) {
						$constant_contact_accept = true;
						if (!empty($form['settings']['constant_contact_acceptance_field']) && !empty($form['settings']['constant_contact_acceptance_field_shortcode'])) {
							$constantcontact_acceptance_value = pafe_get_field_value($form['settings']['constant_contact_acceptance_field_shortcode'],$fields);
							if (empty($constantcontact_acceptance_value)) {
								$constant_contact_accept = false;
							}
						}
						if($constant_contact_accept){
							$helper = new PAFE_Helper();
							$constant_contact_token = get_option('piotnet-constant-contact-access-token');
							$constant_time_get_token = get_option('piotnet-constant-contact-time-get-token');
							$constant_contact_fields = $form['settings']['constant_contact_fields_map'];
							$kind = replace_email($form['settings']['constant_contact_kind'],$fields,$payment_status, $payment_id);
							$kind = !empty($kind) ? $kind : 'home';
							$data_constant_contact = [];
							$constant_contact_street_addresses = [];
							if(!empty($constant_contact_fields)){
								foreach($constant_contact_fields as $item){
									$constant_contact_tag = $item['constant_contact_tagname'];
									$constant_contact_shorcode = $item['constant_contact_shortcode'];
									switch($constant_contact_tag){
										case 'email_address':
											$data_constant_contact['email_address'] = [
												'address' => replace_email($constant_contact_shorcode,$fields,$payment_status, $payment_id),
												'permission_to_send' => 'implicit'
											];
											break;
										case 'phone_number':
											$data_constant_contact['phone_numbers'] = [
												[
													'phone_number' => replace_email($constant_contact_shorcode,$fields,$payment_status, $payment_id),
													'kind' => $kind
												]
											];
											break;
										case 'taggings':
											$taggings = replace_email($constant_contact_shorcode,$fields,$payment_status, $payment_id);
											$data_constant_contact['taggings'] = explode(',', $taggings);
											break;
										case 'street':
										case 'city':
										case 'state':
										case 'postal_code':
										case 'country':
											$constant_contact_street_addresses['street_addresses'][$constant_contact_tag] = replace_email($constant_contact_shorcode,$fields,$payment_status, $payment_id);
											$constant_contact_street_addresses['street_addresses']['kind'] = $kind;
											break;
										default:
											if(strlen($constant_contact_tag) > 32){
												$data_constant_contact['custom_fields'] = [
													[
														'custom_field_id' => $constant_contact_tag,
														'value' => replace_email($constant_contact_shorcode,$fields,$payment_status, $payment_id)
													]
												];
											}else{
												$data_constant_contact[$constant_contact_tag] = replace_email($constant_contact_shorcode,$fields,$payment_status, $payment_id);
											}
									}
								}
								$data_constant_contact['create_source'] = 'Contact';
								$data_constant_contact['list_memberships'] = explode(',', $form['settings']['constant_contact_list_id']);
								if(time() > intval($constant_time_get_token + 7000)){
									$constant_contact_key = get_option('piotnet-addons-for-elementor-pro-constant-contact-client-id');
									$constant_contact_secret = get_option('piotnet-addons-for-elementor-pro-constant-contact-app-secret-id');
									$constant_contact_refresh_token = get_option('piotnet-constant-contact-refresh-token');
									$constant_contact_token = $helper->pafe_constant_contact_refresh_token($constant_contact_key, $constant_contact_secret, $constant_contact_refresh_token);
								}
								$constant_contact_res = $helper->pafe_constant_contact_create_contact($constant_contact_token, json_encode($data_constant_contact));
							}
						}

					}
					// Twilio Whatsapp
					if (in_array("twilio_whatsapp", $form['settings']['submit_actions'])) {
						if(!empty($form['settings']['whatsapp_form']) && !empty($form['settings']['whatsapp_to']) && !empty($form['settings']['whatsapp_message'])) {

	                        $whatsapp_account_sid= get_option('piotnet-addons-for-elementor-pro-twilio-account-sid');
	                        $whatsapp_auth_token = get_option('piotnet-addons-for-elementor-pro-twilio-author-token');
	                        $whatsapp_url =  "https://api.twilio.com/2010-04-01/Accounts/$whatsapp_account_sid/Messages.json";
	                        $whatsapp_from = replace_email($form['settings']['whatsapp_form'] , $fields);
	                        $whatsapp_to =  replace_email($form['settings']['whatsapp_to'] , $fields);
                            $whatsapp_to = explode(',', $whatsapp_to);
	                        $whatsapp_header = base64_encode( $whatsapp_account_sid.':'.$whatsapp_auth_token);
							$form['settings']['whatsapp_message'] = str_replace( [ "\r\n", "\n", "\r", "[remove_line_if_field_empty]" ], '<br />', $form['settings']['whatsapp_message'] );

							$whatsapp_message = replace_email($form['settings']['whatsapp_message'], $fields, '', '', '', '', '', $form_database_post_id );

							$whatsapp_message = str_replace("<br />","\n", $whatsapp_message);
							$whatsapp_message = str_replace("<br/>","\n", $whatsapp_message);
							$whatsapp_message = str_replace("<br>","\n", $whatsapp_message);
							$whatsapp_message = str_replace("<strong>","",$whatsapp_message);
							$whatsapp_message = str_replace("</strong>","", $whatsapp_message);
							$whatsapp_message = strip_tags($whatsapp_message);
                            foreach($whatsapp_to as $number){
                                $whatsapp_data = array (
								    'To' => 'whatsapp:'.trim($number),
								    'From' => 'whatsapp:'.$whatsapp_from,
								    'Body' => $whatsapp_message,
						        );
                                $helper = new PAFE_Helper();
                                $whatsapp_response = $helper->pafe_twilio_whatsapp_send_msg($whatsapp_data, $whatsapp_url, $whatsapp_header);
                            }
						}

                    }

					// Twilio SMS
					if (in_array("twilio_sms", $form['settings']['submit_actions'])) {

						if(!empty($form['settings']['twilio_sms_to']) && !empty($form['settings']['twilio_sms_messaging_service_id']) && !empty($form['settings']['twilio_sms_message'])) {

							$twilio_account_sid = get_option('piotnet-addons-for-elementor-pro-twilio-account-sid');
							$twilio_auth_token = get_option('piotnet-addons-for-elementor-pro-twilio-author-token');
							$twilio_sms_url = "https://api.twilio.com/2010-04-01/Accounts/$twilio_account_sid/Messages.json";
							$twilio_sms_to =  replace_email($form['settings']['twilio_sms_to'] , $fields);
							$twilio_sms_mesid = replace_email($form['settings']['twilio_sms_messaging_service_id'] , $fields);
							$twilio_sms_header = base64_encode( $twilio_account_sid.':'.$twilio_auth_token);
							$form['settings']['twilio_sms_message'] = str_replace( [ "\r\n", "\n", "\r", "[remove_line_if_field_empty]" ], '<br />', $form['settings']['twilio_sms_message'] );
							$twilio_sms_message = replace_email($form['settings']['twilio_sms_message'], $fields, '', '', '', '', '', $form_database_post_id );

							$twilio_sms_message = str_replace("<br />","\n", $twilio_sms_message);
							$twilio_sms_message = str_replace("<br/>","\n", $twilio_sms_message);
							$twilio_sms_message = str_replace("<br>","\n", $twilio_sms_message);
							$twilio_sms_message = str_replace("<strong>","",$twilio_sms_message);
							$twilio_sms_message = str_replace("</strong>","", $twilio_sms_message);
							$twilio_sms_message = strip_tags($twilio_sms_message);
							$twilio_sms_data = array (
								'To' => $twilio_sms_to,
								'MessagingServiceSid' => $twilio_sms_mesid,
								'Body' => $twilio_sms_message,
							);

							$twilio_sms_body = http_build_query($twilio_sms_data);

							$twilio_sms_curl = curl_init();

							curl_setopt_array($twilio_sms_curl, array(
								CURLOPT_URL => $twilio_sms_url ,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => '',
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 0,
								CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => 'POST',
								CURLOPT_POSTFIELDS => $twilio_sms_body,
								CURLOPT_HTTPHEADER => array(
									"Authorization: Basic ".$twilio_sms_header."",
									"Content-Type: application/x-www-form-urlencoded"
								),
							));

							$twilio_sms_response = curl_exec($twilio_sms_curl);

							curl_close($twilio_sms_curl);
						}

					}

					// Sendfox
					if (in_array("sendfox", $form['settings']['submit_actions'])) {

                        if(!empty($form['settings']['sendfox_email_field_shortcode'])) {

	                       $sendfox_access_token = get_option('piotnet-addons-for-elementor-pro-sendfox-access-token');
						   $sendfox_email = replace_email($form['settings']['sendfox_email_field_shortcode'] , $fields);
						   $sendfox_first_name = !empty($form['settings']['sendfox_first_name_field_shortcode']) ? replace_email($form['settings']['sendfox_first_name_field_shortcode'] , $fields) : '' ;

						   $sendfox_last_name = !empty($form['settings']['sendfox_first_name_field_shortcode']) ? replace_email($form['settings']['sendfox_last_name_field_shortcode'] , $fields) : '';
						   $sendfox_list_id = !empty($form['settings']['sendfox_list_id']) ? $form['settings']['sendfox_list_id'] : '';
						   $sendfox_ip = $_POST['remote_ip'];
						   $sendfox_url = "https://api.sendfox.com/contacts?email=$sendfox_email&first_name=$sendfox_first_name&last_name=$sendfox_last_name&ip_address=$sendfox_ip&lists[]=$sendfox_list_id";

	                       $sendfox_curl = curl_init();

						    curl_setopt_array($sendfox_curl, array(
							  CURLOPT_URL => $sendfox_url,
							  CURLOPT_RETURNTRANSFER => true,
							  CURLOPT_ENCODING => '',
							  CURLOPT_MAXREDIRS => 10,
							  CURLOPT_TIMEOUT => 0,
							  CURLOPT_FOLLOWLOCATION => true,
							  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							  CURLOPT_CUSTOMREQUEST => 'POST',
							  CURLOPT_HTTPHEADER => array(
							    "Authorization: Bearer ".$sendfox_access_token."",
							    "Content-Type: application/x-www-form-urlencoded"
							  ),
							));

							$sendfox_response = curl_exec($sendfox_curl);

							curl_close($sendfox_curl);
							//echo $sendfox_response;
						}
                    }
					//Open popup
					$popup = in_array("open_popup", $form['settings']['submit_actions']) ? 'true' : 'false';
	
					// Register
					$register_message = '';

					if (in_array("register", $form['settings']['submit_actions'])) {
						if (!empty($form['settings']['register_email']) && !empty($form['settings']['register_username']) && !empty($form['settings']['register_password'])) {
							$register_email = replace_email($form['settings']['register_email'], $fields);
							$register_username = replace_email($form['settings']['register_username'], $fields);
							$register_password = replace_email($form['settings']['register_password'], $fields);
							$register_password_confirm = replace_email($form['settings']['register_password_confirm'], $fields);
							$register_first_name = replace_email($form['settings']['register_first_name'], $fields);
							$register_last_name = replace_email($form['settings']['register_last_name'], $fields);
							$register_message = '';

							if (!empty($register_password_confirm) && $register_password != $register_password_confirm) {
								$register_message = replace_email($form['settings']['register_password_confirm_message'], $fields);
								$failed = true;
							} else {
								if (!empty($register_email) && !empty($register_username) && !empty($register_password)) {
									$register_user = wp_create_user($register_username,$register_password,$register_email);
									if (is_wp_error($register_user)){ // if there was an error creating a new user
										$failed = true;
								        $register_message = $register_user->get_error_message();
										if (empty($form['settings']['form_database_disable'])) {
											$a = delete_post_meta($form_database_post_id, '_pafe_form_builder_fields_database');
											wp_delete_post($form_database_post_id, true);
											$popup = false;
										}
								    } else {
								    	wp_update_user( array(
								    		'ID' => $register_user,
								    		'role' => $form['settings']['register_role']
							    		));

							    		if (!empty($form['settings']['register_user_meta_list'])) {
							    			foreach ($form['settings']['register_user_meta_list'] as $user_meta_item) {
							    				if (!empty($user_meta_item['register_user_meta_key']) && !empty($user_meta_item['register_user_meta_field_id'])) {
							    					update_user_meta( $register_user, $user_meta_item['register_user_meta_key'], replace_email($user_meta_item['register_user_meta_field_id'], $fields));
							    				}

												if (!empty($user_meta_item['register_user_meta']) && !empty($user_meta_item['register_user_meta_field_id'])) {
													$register_user_meta = $user_meta_item['register_user_meta'];
													$register_user_meta_value = '';

													if ($user_meta_item['register_user_meta'] == 'meta' || $user_meta_item['register_user_meta'] == 'acf' || $user_meta_item['register_user_meta'] == 'metabox') {
														if (!empty($user_meta_item['register_user_meta_key'])) {
															$register_user_meta_key = $user_meta_item['register_user_meta_key'];
														}
													}

													if ($user_meta_item['register_user_meta'] == 'toolset') {
														if (!empty($user_meta_item['register_user_meta_key'])) {
															$register_user_meta_key = 'wpcf-' . $user_meta_item['register_user_meta_key'];
														}
													}

													if ( $register_user_meta == 'acf' ) {
														$meta_type = $user_meta_item['register_user_meta_type'];
														$custom_field_value = pafe_get_field_value($user_meta_item['register_user_meta_field_id'], $fields);

														if ($meta_type == 'image') {
															$image_array = explode(',', $custom_field_value);
															$image_id = attachment_url_to_postid( $image_array[0] );
															if (!empty($image_id)) {
																$custom_field_value = $image_id;
															}
														}

														if ($meta_type == 'gallery') {
															$images_array = explode(',', $custom_field_value);
															$images_id = array();
															foreach ($images_array as $images_item) {
																if (!empty($images_item)) {
																	$image_id = attachment_url_to_postid( $images_item );
																	if (!empty($image_id)) {
																		$images_id[] = $image_id;
																	}
																}
															}
															if (!empty($images_id)) {
																$custom_field_value = $images_id;
															}
														}

														if ($meta_type == 'select' && strpos($custom_field_value, ',') !== false || $meta_type == 'checkbox') {
															$custom_field_value = explode(',', $custom_field_value);
														}

														if ($meta_type == 'true_false') {
															$custom_field_value = !empty($custom_field_value) ? 1 : 0;
														}

														if ($meta_type == 'date') {
															$time = strtotime( $custom_field_value );

															if (empty($custom_field_value)) {
																$custom_field_value = '';
															} else {
																$custom_field_value = date('Ymd',$time);
															}
														}

														if ($meta_type == 'time') {
															$time = strtotime( $custom_field_value );

															if (empty($custom_field_value)) {
																$custom_field_value = '';
															} else {
																$custom_field_value = date('H:i:s',$time);
															}
														}
														update_field( $register_user_meta_key, $custom_field_value, 'user_' . $register_user );
													} elseif (function_exists('rwmb_set_meta') && $register_user_meta == 'metabox') {
														$meta_type = $user_meta_item['register_user_meta_type'];
														$custom_field_value = pafe_get_field_value($user_meta_item['register_user_meta_field_id'], $fields);

														if ($meta_type == 'image') {
															$image_array = explode(',', $custom_field_value);
															$image_id = attachment_url_to_postid( $image_array[0] );
															if (!empty($image_id)) {
																$custom_field_value = $image_id;
															}
															update_user_meta( $register_user, $register_user_meta_key, $custom_field_value );
														}

														if ($meta_type == 'gallery') {
															$images_array = explode(',', $custom_field_value);
															$images_id = '';
															foreach ($images_array as $images_item) {
																if (!empty($images_item)) {
																	$image_id = attachment_url_to_postid( $images_item );
																	if (!empty($image_id)) {
																		$images_id .= $image_id . ',';
																	}
																}
															}
															if (!empty($images_id)) {
																$custom_field_value = explode(',', $images_id);
															}
														}

														if ($meta_type == 'date') {
															$time = strtotime( $custom_field_value );
															if (empty($custom_field_value)) {
																$custom_field_value = '';
															} else {
																$custom_field_value = date('Y-m-d',$time);
															}
														}

														if ($meta_type == 'time') {
															$time = strtotime( $custom_field_value );
															$custom_field_value = date('H:i',$time);
														}

														if ($meta_type == 'select') {
															if (strpos($custom_field_value, ',') !== false) {
																$custom_field_value = explode(',', $custom_field_value);
															}
														}

														if ($meta_type == 'checkbox') {
															$custom_field_value = explode(',', $custom_field_value);
														}

														rwmb_set_meta( $register_user, $register_user_meta_key, $custom_field_value, $custom_field_value );

													} elseif ( function_exists('wpcf_admin_fields_get_field') && $register_user_meta == 'toolset' ) {
														$meta_type = $user_meta_item['register_user_meta_type'];
														$custom_field_value = pafe_get_field_value($user_meta_item['register_user_meta_field_id'], $fields);

														if ($meta_type == 'image') {
															$image_array = explode(',', $custom_field_value);
															if (!empty($image_array)) {
																update_user_meta( $register_user, $register_user_meta_key, $image_array[0] );
															}
														} elseif ($meta_type == 'gallery') {
															$images_array = explode(',', $custom_field_value);
															delete_user_meta( $register_user, $register_user_meta_key);
															foreach ($images_array as $images_item) {
																if (!empty($images_item)) {
																	add_user_meta( $register_user, $register_user_meta_key, $images_item );
																}
															}
														} elseif ($meta_type == 'checkbox') {
															$custom_field_value = explode(',', $custom_field_value);

															$field_toolset = wpcf_admin_fields_get_field($user_meta_item['register_user_meta_key']);

															if (isset($field_toolset['data']['options'])){
																$res = array();
																foreach ($field_toolset['data']['options'] as $key => $option){
																	if (in_array($option['set_value'], $custom_field_value)){
																		$res[$key] = $option['set_value'];
																	}
																}
																update_post_meta( $register_user, $register_user_meta_key , $res );
															}
														} elseif ($meta_type == 'date') {
															$custom_field_value = strtotime( $custom_field_value );
															update_user_meta( $register_user, $register_user_meta_key, $custom_field_value );
														} else {

															update_user_meta( $register_user, $register_user_meta_key, $custom_field_value );

														}
													} else {
														update_user_meta( $register_user, $register_user_meta_key, pafe_get_field_value($user_meta_item['register_user_meta_field_id'], $fields) );
													}
												}
											}
							    		}

								    	if (!empty($register_first_name) && !empty($register_last_name)) {
									    	wp_update_user( array(
									    		'ID' => $register_user,
									    		'first_name' => $register_first_name,
									    		'last_name' => $register_last_name
								    		)); // Update the user with the first name and last name
									    }

									    /* Automatically log in the user and redirect the user to the home page */
										$register_creds = array( // credientials for newley created user
										    'user_login' => $register_username,
										    'user_password' => $register_password,
										    'remember' => true,
										);

										$register_signon = wp_signon($register_creds); //sign in the new user
								    }
								} else {
									$failed = true;
								}
							}
						} else {
							$failed = true;
						}
					}

					if (in_array("login", $form['settings']['submit_actions'])) {
						if (!empty($form['settings']['login_username']) && !empty($form['settings']['login_username']) && !empty($form['settings']['login_password'])) {
							$login_username = replace_email($form['settings']['login_username'], $fields);
							$login_password = replace_email($form['settings']['login_password'], $fields);
							$login_remember = replace_email($form['settings']['login_remember'], $fields);
							$register_message = '';

							if (!empty($login_username) && !empty($login_password)) {
								$login_creds = array(
								    'user_login' => $login_username,
								    'user_password' => $login_password,
								);

								if (!empty($login_remember)) {
									$login_creds['remember'] = true;
								}

								$login_signon = wp_signon($login_creds);

								if (is_wp_error($login_signon)){
									$failed = true;
							        $register_message = $login_signon->get_error_message();
							    }

							} else {
								$failed = true;
							}
						} else {
							$failed = true;
						}
					}

					//Update user profile
					if (in_array("update_user_profile", $form['settings']['submit_actions'])) {
						if (is_user_logged_in()) {
							if (!empty($form['settings']['update_user_meta_list'])) {
								$user_id = get_current_user_id();

								foreach ($form['settings']['update_user_meta_list'] as $user_meta) {
									if (!empty($user_meta['update_user_meta']) && !empty($user_meta['update_user_meta_field_shortcode'])) {

										$user_meta_key = $user_meta['update_user_meta'];
										$user_meta_value = '';

										if ($user_meta['update_user_meta'] == 'meta' || $user_meta['update_user_meta'] == 'acf' || $user_meta['update_user_meta'] == 'metabox') {
											if (!empty($user_meta['update_user_meta_key'])) {
												$user_meta_key = $user_meta['update_user_meta_key'];
											}
										}

										if ($user_meta['update_user_meta'] == 'toolset') {
											if (!empty($user_meta['update_user_meta_key'])) {
												$user_meta_key = 'wpcf-' . $user_meta['update_user_meta_key'];
											}
										}

										if ($user_meta_key == 'email') {
											$user_email = pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields);
											if( !empty($user_email) && is_email($user_email)) {
											        wp_update_user( array(
											            'ID' => $user_id,
											            'user_email' => $user_email,
											    ) );
											}
										}

										if ($user_meta_key == 'display_name') {
											$user_display_name = pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields);
											if(!empty($user_display_name)) {
												wp_update_user( array(
													'ID' => $user_id,
													'display_name' => $user_display_name,
												) );
											}
										}

										if ($user_meta_key == 'password') {
											if (!empty($user_meta['update_user_meta_field_shortcode_confirm_password'])) {
												if (pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields) != pafe_get_field_value($user_meta['update_user_meta_field_shortcode_confirm_password'], $fields)) {
													$failed = true;
													$register_message = $user_meta['wrong_password_message'];
												} else {
													$login_password = pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields);

													if (!empty($login_password)) {
														wp_set_password( $login_password, $user_id );

														$current_user = wp_get_current_user();

														$login_creds = array(
														    'user_login' => $current_user->user_login,
														    'user_password' => $login_password,
														);

														$login_signon = wp_signon($login_creds);
													}
												}
											}
										} else {

											if ( $user_meta['update_user_meta'] == 'acf' ) {
												$meta_type = $user_meta['update_user_meta_type'];

												$custom_field_value = pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields);

												if ($meta_type == 'image') {
													$image_array = explode(',', $custom_field_value);
													$image_id = attachment_url_to_postid( $image_array[0] );
													if (!empty($image_id)) {
														$custom_field_value = $image_id;
													}
												}

												if ($meta_type == 'gallery') {
													$images_array = explode(',', $custom_field_value);
													$images_id = array();
													foreach ($images_array as $images_item) {
														if (!empty($images_item)) {
															$image_id = attachment_url_to_postid( $images_item );
															if (!empty($image_id)) {
																$images_id[] = $image_id;
															}
														}
													}
													if (!empty($images_id)) {
														$custom_field_value = $images_id;
													}
												}

												if ($meta_type == 'select' && strpos($custom_field_value, ',') !== false || $meta_type == 'checkbox') {
													$custom_field_value = explode(',', $custom_field_value);
												}

												if ($meta_type == 'true_false') {
													$custom_field_value = !empty($custom_field_value) ? 1 : 0;
												}

												if ($meta_type == 'date') {
													$time = strtotime( $custom_field_value );

													if (empty($custom_field_value)) {
														$custom_field_value = '';
													} else {
														$custom_field_value = date('Ymd',$time);
													}
												}

												if ($meta_type == 'time') {
													$time = strtotime( $custom_field_value );
													
													if (empty($custom_field_value)) {
														$custom_field_value = '';
													} else {
														$custom_field_value = date('H:i:s',$time);
													}
												}

												// if ($meta_type == 'google_map') {
												// 	$custom_field_value = array('address' => $custom_field_value_array['value'], 'lat' => $custom_field_value_array['lat'], 'lng' => $custom_field_value_array['lng'], 'zoom' => $custom_field_value_array['zoom']);
												// }

												update_field( $user_meta_key, $custom_field_value, 'user_' . $user_id );
											} elseif (function_exists('rwmb_set_meta') && $user_meta['update_user_meta'] == 'metabox') {
												$meta_type = $user_meta['update_user_meta_type'];
												$custom_field_value = pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields);

												if ($meta_type == 'image') {
													$image_array = explode(',', $custom_field_value);
													$image_id = attachment_url_to_postid( $image_array[0] );
													if (!empty($image_id)) {
														$custom_field_value = $image_id;
													}
													update_user_meta( $user_id, $user_meta_key, $custom_field_value );
												}

												if ($meta_type == 'gallery') {
													$images_array = explode(',', $custom_field_value);
													$images_id = '';
													foreach ($images_array as $images_item) {
														if (!empty($images_item)) {
															$image_id = attachment_url_to_postid( $images_item );
															if (!empty($image_id)) {
																$images_id .= $image_id . ',';
															}
														}
													}
													if (!empty($images_id)) {
														$custom_field_value = explode(',', $images_id);
													}
												}

												if ($meta_type == 'date') {
													$time = strtotime( $custom_field_value );
													if (empty($custom_field_value)) {
														$custom_field_value = '';
													} else {
														$custom_field_value = date('Y-m-d',$time);
													}
												}

												if ($meta_type == 'time') {
													$time = strtotime( $custom_field_value );
													$custom_field_value = date('H:i',$time);
												}

												if ($meta_type == 'select') {
													if (strpos($custom_field_value, ',') !== false) {
														$custom_field_value = explode(',', $custom_field_value);
													}
												}

												if ($meta_type == 'checkbox') {
													$custom_field_value = explode(',', $custom_field_value);
												}

												rwmb_set_meta( $user_id, $user_meta_key, $custom_field_value, $custom_field_value );

											} elseif ( function_exists('wpcf_admin_fields_get_field') && $user_meta['update_user_meta'] == 'toolset' ) {
												$meta_type = $user_meta['update_user_meta_type'];
												$custom_field_value = pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields);

												if ($meta_type == 'image') {
													$image_array = explode(',', $custom_field_value);
													if (!empty($image_array)) {
														update_user_meta( $user_id, $user_meta_key, $image_array[0] );
													}
												} elseif ($meta_type == 'gallery') {
													$images_array = explode(',', $custom_field_value);
													delete_user_meta( $user_id, $user_meta_key);
													foreach ($images_array as $images_item) {
														if (!empty($images_item)) {
															add_user_meta( $user_id, $user_meta_key, $images_item );
														}
													}
												} elseif ($meta_type == 'checkbox') {
													$custom_field_value = explode(',', $custom_field_value);

													$field_toolset = wpcf_admin_fields_get_field( $user_meta['update_user_meta_key']);

													if (isset($field_toolset['data']['options'])){
														$res = array();
														foreach ($field_toolset['data']['options'] as $key => $option){
															if (in_array($option['set_value'], $custom_field_value)){
																$res[$key] = $option['set_value'];
															}
														}
														update_post_meta( $user_id, $user_meta_key , $res );
													}
												} elseif ($meta_type == 'date') {
													$custom_field_value = strtotime( $custom_field_value );
													update_user_meta( $user_id, $user_meta_key, $custom_field_value );
												} else {

													update_user_meta( $user_id, $user_meta_key, $custom_field_value );

												}
											} else {
												update_user_meta( $user_id, $user_meta_key, pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields) );
											}
										}
									}
								}
							}
						}
					}

					// Action Hook

					do_action('pafe/form_builder/new_record',$fields);
					do_action('pafe/form_builder/new_record_v2',$form_submission);

					if ($payment_status == 'succeeded') {
						do_action('pafe/form_builder/payment_status_succeeded',$form_submission);
					}

					do_action('pafe/form_builder/remote_request_response', $form_submission, $remote_request_response, $webhook_response);
					$custom_message = apply_filters('pafe/form_builder/custom_message', false, $form_submission, $remote_request_response, $webhook_response);
					$failed = apply_filters('pafe/form_builder/not_send_email', $failed, $form_submission, $remote_request_response, $webhook_response);

					// Email
					if (in_array("email", $form['settings']['submit_actions']) && $failed == false) {

						$to = replace_email($form['settings']['email_to'], $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$to = replace_email( $form['settings']['email_to'], $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						$subject = replace_email($form['settings']['email_subject'], $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$subject = replace_email($form['settings']['email_subject'], $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						if (empty($form['settings']['email_content_type']) || $form['settings']['email_content_type'] == 'plain') {
							$form['settings']['email_content'] = str_replace( [ "\r\n", "\n", "\r" ], '<br />', $form['settings']['email_content'] );
						}

						$message = replace_email($form['settings']['email_content'], $fields, '', '', '', '', '', $form_database_post_id, true );
						if(!empty($form['settings']['mollie_enable'])){
							$mollie_payment['status'] = $payment_status;
							$mollie_payment['type'] = 'mollie';
							$mollie_status['open'] = !empty($form['settings']['pafe_mollie_message_open']) ? $form['settings']['pafe_mollie_message_open'] : 'Payment open';
							$mollie_status['canceled'] = !empty($form['settings']['pafe_mollie_message_canceled']) ? $form['settings']['pafe_mollie_message_canceled'] : 'Payment canceled';
							$mollie_status['authorized'] = !empty($form['settings']['pafe_mollie_message_authorized']) ? $form['settings']['pafe_mollie_message_authorized'] : 'Payment authorized';
							$mollie_status['pending'] = !empty($form['settings']['pafe_mollie_message_pending']) ? $form['settings']['pafe_mollie_message_pending'] : 'Payment pending';
							$mollie_status['paid'] = !empty($form['settings']['pafe_mollie_message_succeeded']) ? $form['settings']['pafe_mollie_message_succeeded'] : 'Payment succeeded';
							$mollie_status['expired'] = !empty($form['settings']['pafe_mollie_message_expired']) ? $form['settings']['pafe_mollie_message_expired'] : 'Payment expired';
							$message = replace_email($message, $fields, $mollie_payment, '', $mollie_status, '', '', $form_database_post_id );
						}

						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$message = replace_email($message, $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						$reply_to = $form['settings']['email_reply_to'];
						if (empty($reply_to)) {
							$reply_to = $form['settings']['email_from'];
						}
						$reply_to = replace_email($reply_to, $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['email_from'] ) ) {
							$headers[] = 'From: ' . replace_email($form['settings']['email_from_name'], $fields, '', '', '', '', '', $form_database_post_id ) . ' <' . replace_email($form['settings']['email_from'], $fields, '', '', '', '', '', $form_database_post_id ) . '>';
							$headers[] = 'Reply-To: ' . $reply_to;
						}

						if ( ! empty( $form['settings']['email_to_cc'] ) ) {
							$headers[] = 'Cc: ' . replace_email($form['settings']['email_to_cc'], $fields, '', '', '', '', '', $form_database_post_id );
						}

						if ( ! empty( $form['settings']['email_to_bcc'] ) ) {
							$headers[] = 'Bcc: ' . replace_email($form['settings']['email_to_bcc'], $fields, '', '', '', '', '', $form_database_post_id );
						}

						$headers[] = 'Content-Type: text/html; charset=UTF-8';

						if (!empty($post_url)) {
							$subject = str_replace( ['[post_url]', '[post_id]'], [$post_url, $submit_post_id], $subject );
							$message = str_replace( ['[post_url]', '[post_id]'], ['<a href="' . $post_url . '">' . $post_url . '</a>', $submit_post_id], $message );

						}
						//Remove field shortcde when send email
						if (!empty($form['settings']['remove_empty_form_input_fields'])) {
							foreach($field_remove as $field_rm){
								$message = str_replace([$field_rm.'<br />', $field_rm.'<br>', $field_rm], '', $message);
							}
						}
						if(!empty($form['settings']['disable_attachment_pdf_email'])){
							$pdf_dir = WP_CONTENT_DIR . '/uploads/piotnet-addons-for-elementor/' . $pdf_file_name . '.pdf';
							if (($key = array_search($pdf_dir, $attachment)) !== false) {
								unset($attachment[$key]);
							}
						}

						$status = wp_mail( $to, $subject, $message . $meta_content, $headers, array_merge($attachment, $attachment_images_to_email) );

					}

					if (in_array("email2", $form['settings']['submit_actions']) && $failed == false) {

						// $to = replace_email($form['settings']['email_to_2'], $fields);

						// $subject = replace_email($form['settings']['email_subject_2'], $fields);

						// $message = replace_email($form['settings']['email_content_2'], $fields);

						$to = replace_email($form['settings']['email_to_2'], $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$to = replace_email( $form['settings']['email_to_2'], $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						$subject = replace_email($form['settings']['email_subject_2'], $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$subject = replace_email($form['settings']['email_subject_2'], $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						if (empty($form['settings']['email_content_type_2']) || $form['settings']['email_content_type_2'] == 'plain') {
							$form['settings']['email_content_2'] = str_replace( [ "\r\n", "\n", "\r" ], '<br />', $form['settings']['email_content_2'] );
						}

						$message = replace_email($form['settings']['email_content_2'], $fields, '', '', '', '', '', $form_database_post_id, true );
						if(!empty($form['settings']['mollie_enable'])){
							$mollie_payment['status'] = $payment_status;
							$mollie_payment['type'] = 'mollie';
							$mollie_status['open'] = !empty($form['settings']['pafe_mollie_message_open']) ? $form['settings']['pafe_mollie_message_open'] : 'Payment open';
							$mollie_status['canceled'] = !empty($form['settings']['pafe_mollie_message_canceled']) ? $form['settings']['pafe_mollie_message_canceled'] : 'Payment canceled';
							$mollie_status['authorized'] = !empty($form['settings']['pafe_mollie_message_authorized']) ? $form['settings']['pafe_mollie_message_authorized'] : 'Payment authorized';
							$mollie_status['pending'] = !empty($form['settings']['pafe_mollie_message_pending']) ? $form['settings']['pafe_mollie_message_pending'] : 'Payment pending';
							$mollie_status['paid'] = !empty($form['settings']['pafe_mollie_message_succeeded']) ? $form['settings']['pafe_mollie_message_succeeded'] : 'Payment succeeded';
							$mollie_status['expired'] = !empty($form['settings']['pafe_mollie_message_expired']) ? $form['settings']['pafe_mollie_message_expired'] : 'Payment expired';
							$message = replace_email($message, $fields, $mollie_payment, '', $mollie_status, '', '', $form_database_post_id );
						}
						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$message = replace_email($form['settings']['email_content_2'], $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						$reply_to = $form['settings']['email_reply_to_2'];
						if (empty($reply_to)) {
							$reply_to = $form['settings']['email_from_2'];
						}
						$reply_to = replace_email($reply_to, $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['email_from_2'] ) ) {
							$headers_email[] = 'From: ' . replace_email($form['settings']['email_from_name_2'], $fields, '', '', '', '', '', $form_database_post_id ) . ' <' . replace_email($form['settings']['email_from_2'], $fields, '', '', '', '', '', $form_database_post_id ) . '>';
							$headers_email[] = 'Reply-To: ' . $reply_to;
						}

						if ( ! empty( $form['settings']['email_to_cc_2'] ) ) {
							$headers_email[] = 'Cc: ' . replace_email($form['settings']['email_to_cc_2'], $fields, '', '', '', '', '', $form_database_post_id );
						}

						if ( ! empty( $form['settings']['email_to_bcc_2'] ) ) {
							$headers_email[] = 'Bcc: ' . replace_email($form['settings']['email_to_bcc_2'], $fields, '', '', '', '', '', $form_database_post_id );
						}

						$headers_email[] = 'Content-Type: text/html; charset=UTF-8';

						if (!empty($post_url)) {
							$subject = str_replace( '[post_url]', $post_url, $subject );
							$message = str_replace( '[post_url]', '<a href="' . $post_url . '">' . $post_url . '</a>', $message );
						}

						if(!empty($form['settings']['disable_attachment_pdf_email2'])){
							$pdf_dir = WP_CONTENT_DIR . '/uploads/piotnet-addons-for-elementor/' . $pdf_file_name . '.pdf';
							if (($key = array_search($pdf_dir, $attachment)) !== false) {
								unset($attachment[$key]);
							}
						}
                        //Remove field shortcde when send email
						if (!empty($form['settings']['remove_empty_form_input_fields'])) {
							foreach($field_remove as $field_rm){
								$message = str_replace([$field_rm.'<br />', $field_rm.'<br>', $field_rm], '', $message);
							}
						}
						$status = wp_mail( $to, $subject, $message . $meta_content_2, $headers_email, array_merge($attachment, $attachment_images_to_email2) );

					}

                    $attachment = array_merge($attachment, $attachment_images_to_email, $attachment_images_to_email2);
					foreach ($attachment as $attachment_item) {
						if(empty($form['settings']['pdfgenerator_save_file'])){
							pafe_delete_acttachment($attachment_item);
						}else{
							if($pdf_file_name . '.pdf' != basename($attachment_item)){
								pafe_delete_acttachment($attachment_item);
							}
						}
					}

					$failed_status = 0;

					if ($failed) {
						$redirect = '';
						$failed_status = 1;
					}

					if ($failed == false && empty($status)) {
						$status = 1;
					}
					$pafe_response = array(
						'payment_status' => $payment_status,
						'status' => $status,
						'payment_id' => $payment_id,
						'post_url' => $post_url,
						'redirect' => $redirect,
						'register_message' => str_replace(',', '###', $register_message),//$register_message,
						'failed_status' => $failed_status,
						'custom_message' => $custom_message,
						'popup' => $popup
					);
					echo json_encode($pafe_response);

				}else{
                    $pafe_response = [
                        'status' => false,
                        'reacaptcha' => !empty($form['settings']['pafe_recaptcha_msg_error']) ? $form['settings']['pafe_recaptcha_msg_error'] : ''
                    ];
                    echo json_encode($pafe_response);
                }
			}
		wp_die();
	}

	function pafe_process_google_calendar($form, $fields, $payment_id) {
		$gg_calendar_date_end = pafe_get_field_value($form['settings']['google_calendar_date_end'],$fields, $payment_id);
		$gg_calendar_date_start = pafe_get_field_value($form['settings']['google_calendar_date_start'],$fields, $payment_id);
		$gg_calendar_client_secret = get_option('piotnet-addons-for-elementor-pro-google-calendar-client-secret');
		$gg_calendar_client_id = get_option('piotnet-addons-for-elementor-pro-google-calendar-client-id');
		$gg_calendar_rtok = get_option('piotnet-addons-for-elementor-pro-google-calendar-refresh-token');
		$gg_calendar_api = get_option('piotnet-addons-for-elementor-pro-google-calendar-client-api-key');
		$gg_calendar_id = get_option('piotnet-addons-for-elementor-pro-google-calendar-id');


		$data_gg_calendar = [
			'summary' => replace_email($form['settings']['google_calendar_summary'],$fields, $payment_id),
			'location' => pafe_get_field_value($form['settings']['google_calendar_location'],$fields, $payment_id),
			'description' => replace_email($form['settings']['google_calendar_description'], $fields)
		];

		$attendees_email = pafe_get_field_value( $form['settings']['google_calendar_attendees_email'], $fields, $payment_id );
		if ( !empty($attendees_email) ) {
			$data_gg_calendar['attendees'] = [
				[
					'displayName' => pafe_get_field_value($form['settings']['google_calendar_attendees_name'],$fields, $payment_id),
					'email' => $attendees_email,
				]
			];
		}

		$remind_time = $form['settings']['google_calendar_remind_time'];
		$remind_method = $form['settings']['google_calendar_remind_method'];

		if ( !empty($remind_time) && !empty($remind_method) ) {
			$remind_time = (int)$remind_time;
			$data_gg_calendar['reminders'] = [
				'useDefault' => false,
				'overrides' => [
					[
						'method' => $remind_method,
						'minutes' => $remind_time,
					],
				],
			];
		} else if ( $remind_time == 0 ){
			$data_gg_calendar['reminders'] = [
				'useDefault' => false,
				'overrides' => [
					[
						'method' => $remind_method,
						'minutes' => 0,
					],
				],
			];
		} else if ( empty($remind_time) ){
			$data_gg_calendar['reminders'] = [
				'useDefault' => false,
				'overrides' => [
					[
						'method' => $remind_method,
						'minutes' => 60,
					],
				],
			];
		}

		$google_calendar_date_type = $form['settings']['google_calendar_date_type'];
		if ( $google_calendar_date_type == 'date_time') {
			$data_gg_calendar['start'] = [
				'dateTime' => $gg_calendar_date_start,
			];

			$gg_calendar_formatted_date_end = null;
			if (empty($gg_calendar_date_end)) {
				$seconds_duration = (int)$form['settings']['google_calendar_duration'] * 60;
				$gg_calendar_formatted_date_end = date("c",strtotime($gg_calendar_date_start) + $seconds_duration);
			} else {
				$gg_calendar_formatted_date_end = $gg_calendar_date_end;
			}
			$data_gg_calendar['end'] = [
				'dateTime' => $gg_calendar_formatted_date_end,
			];
		} else if ( $google_calendar_date_type == 'date') {
			$gg_calendar_formatted_date_start = date('Y-m-d',strtotime($gg_calendar_date_start));
			$data_gg_calendar['start'] = [
				'date' => $gg_calendar_formatted_date_start,
			];

			$gg_calendar_formatted_date_end = empty($gg_calendar_date_end) ? $gg_calendar_formatted_date_start : date('Y-m-d',strtotime($gg_calendar_date_end));
			$data_gg_calendar['end'] = [
				'date' => $gg_calendar_formatted_date_end,
			];
		}

		$curl = curl_init();

		// Refresh Token
		$google_calendar_expired_token = get_option('piotnet-addons-for-elementor-pro-google-calendar-expired-token');
		$google_calendar_expired_token = (int)$google_calendar_expired_token;
		$google_calendar_current_time = time();

		if ($google_calendar_expired_token < $google_calendar_current_time) {
			$google_calendar_request_token = [
				'body' => [],
				'headers' => array(
					'Content-type' => 'application/x-www-form-urlencoded',
				),
			];

			$google_calendar_refresh_token = wp_remote_post( 'https://www.googleapis.com/oauth2/v4/token?client_id=' . $gg_calendar_client_id . '&client_secret=' . $gg_calendar_client_secret . '&refresh_token=' . $gg_calendar_rtok . '&grant_type=refresh_token', $google_calendar_request_token );
			$google_calendar_refresh_token = json_decode( wp_remote_retrieve_body( $google_calendar_refresh_token) );

			if (!empty($google_calendar_refresh_token->access_token)) {
				$gg_calendar_atok = $google_calendar_refresh_token->access_token;
				$gg_cld_newexpired = get_option('piotnet-addons-for-elementor-pro-google-calendar-expires');
				$gg_cld_newexpired = (int)$gg_cld_newexpired;
				update_option( 'piotnet-addons-for-elementor-pro-google-calendar-access-token', $gg_calendar_atok );
				$google_calendar_new_expired_token = time() + $gg_cld_newexpired;
				update_option( 'piotnet-addons-for-elementor-pro-google-calendar-expired-token', $google_calendar_new_expired_token );
			}
		}

		$gg_calendar_access_token = get_option('piotnet-addons-for-elementor-pro-google-calendar-access-token');

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://www.googleapis.com/calendar/v3/calendars/$gg_calendar_id/events?sendUpdates=all&key=$gg_calendar_api",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($data_gg_calendar),
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer $gg_calendar_access_token",
				"Accept: application/json",
				"Content-Type: application/json"
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);

	}
	function pafe_get_mollie_payment_status($id, $api_key){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.mollie.com/v2/payments/'.$id,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Authorization: Bearer '.$api_key
		),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$response = json_decode($response);
		return $response->status;
	}
	function pafe_stripe_get_payment_status($id){
		$secret_key = get_option( 'piotnet-addons-for-elementor-pro-stripe-secret-key' );
		$url = 'https://api.stripe.com/v1/payment_intents/'.$id;
		$args = [
			'headers' => [
				'Authorization' => 'Basic '. base64_encode($secret_key)
			]
		];
		$payment = json_decode(wp_remote_retrieve_body(wp_remote_get( $url, $args )));
		return $payment->status ? $payment->status : 'failed';
	}
	function pafe_set_payment_status($message, $payment_status, $succeeded, $pending, $failed, $payment_id){
		
		if (!is_array($message)) {
			if (strpos($message, '[payment_status]') !== false || strpos($message, '[payment_id]') !== false) {
				if ($payment_status == 'succeeded') {
					$message = str_replace( '[payment_status]', $succeeded, $message );
				}

				if ($payment_status == 'pending') {
					$message = str_replace( '[payment_status]', $pending, $message );
				}

				if ($payment_status == 'failed') {
					$message = str_replace( '[payment_status]', $failed, $message );
				}

				if (!empty($payment_id) && strpos($message, '[payment_id]') !== false) {
					$message = str_replace( '[payment_id]', $payment_id, $message );
				}
			}
			return trim($message);
		} else {
			return $message;
		}
	}
    function pafe_delete_acttachment( $acttachment ) {
		$img_id = attachment_url_to_postid( ( wp_get_upload_dir()['url'] . '/' . basename( $acttachment ) ) );
		if ( $img_id ) {
			wp_delete_attachment( $img_id, true );
		} else {
			unlink( $acttachment );
		}
		return;
	}
?>
