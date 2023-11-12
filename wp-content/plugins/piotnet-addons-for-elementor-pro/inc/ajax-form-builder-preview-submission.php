<?php
	add_action( 'wp_ajax_pafe_ajax_form_builder_preview_submission', 'pafe_ajax_form_builder_preview_submission' );
	add_action( 'wp_ajax_nopriv_pafe_ajax_form_builder_preview_submission', 'pafe_ajax_form_builder_preview_submission' );

	function find_element_recursive_preview( $elements, $form_id ) {
		foreach ( $elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = find_element_recursive_preview( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	function set_val_preview(&$array,$path,$val) {
		for($i=&$array; $key=array_shift($path); $i=&$i[$key]) {
			if(!isset($i[$key])) $i[$key] = array();
		}
		$i = $val;
	}

	function pafe_merge_string_preview(&$string,$string_add) {
		$string = $string . $string_add;
	}

	function pafe_unset_string_preview(&$string) {
		$string = '';
	}

	function pafe_set_string_preview(&$string,$string_set) {
		$string = $string_set;
	}

	function replace_email_preview($content, $fields, $payment_status = 'succeeded', $payment_id = '', $succeeded = 'succeeded', $pending = 'pending', $failed = 'failed', $submit_id = 0 ) {
		$message = $content;

		$message_all_fields = '';

		if (!empty($fields)) {

			// all fields
			foreach ($fields as $field) {
				$field_value = $field['value'];
				$field_label = isset($field['label']) ? $field['label'] : '';
				if (isset($field['value_label'])) {
					$field_value = $field['value_label'];
				}

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
				$repeater_label = '<span data-id="' . $repeater_id_string . '"><strong>' . $field['repeater_label'] . ' ' . $repeater_index_1 . ': </strong></span><br>';

				$repeater_remove_this_field = false;
				if (isset($field['repeater_remove_this_field'])) {
					$repeater_remove_this_field = true;
				}
				
				if (!empty($repeater_id) && !empty($repeater_label) && $repeater_remove_this_field == false) {
					if (strpos($message_all_fields, $repeater_label) !== false) {
						$message_all_fields .= '<div class="pafe-form-builder-preview-submission__item"><label class="pafe-form-builder-preview-submission__item-label">' . $field_label . ': </label>' . '<span class="pafe-form-builder-preview-submission__item-value">' . $field_value . '</span>' . '</div>';
					} else {
						$message_all_fields .= $repeater_label;
						$message_all_fields .= '<div class="pafe-form-builder-preview-submission__item"><label class="pafe-form-builder-preview-submission__item-label">' . $field_label . ': </label>' . '<span class="pafe-form-builder-preview-submission__item-value">' . $field_value . '</span>' . '</div>';
					}
					// if ($field['repeater_index'] != ($field['repeater_length'] - 1)) {
					// 	$message .=  '</div>';
					// }
				} else {
					if (strpos($field['name'], 'pafe-end-repeater') === false) {
						$message_all_fields .= '<div class="pafe-form-builder-preview-submission__item"><label class="pafe-form-builder-preview-submission__item-label">' . $field_label . ': </label>' . '<span class="pafe-form-builder-preview-submission__item-value">' . $field_value . '</span>' . '</div>';
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

				$search_remove_line_if_field_empty = '[field id="' . $field['name'] . '"]' . '[remove_line_if_field_empty]';

				if (empty($field_value)) {
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
				}

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
				$repeater_label = '<span data-id="' . $repeater_id_string . '"><strong>' . $field['repeater_label'] . ' ' . $repeater_index_1 . ': </strong></span><br>';

				$repeater_remove_this_field = false;
				if (isset($field['repeater_remove_this_field'])) {
					$repeater_remove_this_field = true;
				}
				
				if (!empty($repeater_id) && !empty($repeater_label) && $repeater_remove_this_field == false) {
					if (strpos($repeater_content, $repeater_label) !== false) {
						$string_add = '<div class="pafe-form-builder-preview-submission__item"><label class="pafe-form-builder-preview-submission__item-label">' . $field_label . ': </label>' . '<span class="pafe-form-builder-preview-submission__item-value">' . $field_value . '</span>' . '</div>';
						pafe_merge_string_preview($repeater_content,$string_add);
					} else {
						$string_add = $repeater_label . '<div class="pafe-form-builder-preview-submission__item"><label class="pafe-form-builder-preview-submission__item-label">' . $field_label . ': </label>' . '<span class="pafe-form-builder-preview-submission__item-value">' . $field_value . '</span>' . '</div>';
						pafe_merge_string_preview($repeater_content,$string_add);
					}
					if (substr_count($field['repeater_id'],'|') == 2) {
						pafe_set_string_preview($repeater_id_one,$field['repeater_id_one']);
					}
				}

				if (empty($repeater_id)) {
					if (!empty($repeater_id_one) && !empty($repeater_content)) {
						$search_repeater = '[repeater id="' . $repeater_id_one . '"]';
						$message = str_replace($search_repeater, $repeater_content, $message);
						pafe_unset_string_preview($repeater_content);
						pafe_unset_string_preview($repeater_id_one);
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

		$message = str_replace( [ "\r\n", "\n", "\r", "[remove_line_if_field_empty]" ], '<br />', $message );

		if ($payment_status == 'succeeded') {
			$message = str_replace( '[payment_status]', $succeeded, $message );
		}

		if ($payment_status == 'pending') {
			$message = str_replace( '[payment_status]', $pending, $message );
		}

		if ($payment_status == 'failed') {
			$message = str_replace( '[payment_status]', $failed, $message );
		}

		if (!empty($payment_id)) {
			$message = str_replace( '[payment_id]', $payment_id, $message );
		}

		if (!empty($submit_id)) {
			$message = str_replace( '[submit_id]', $submit_id, $message );
		}

		return $message;
	}

	function get_field_name_shortcode_preview($content) {
		$field_name = str_replace('[field id="', '', $content);
		$field_name = str_replace('[repeater id="', '', $field_name); // fix alert ]
		$field_name = str_replace('"]', '', $field_name);
		return trim($field_name);
	}

	function pafe_get_field_value_preview($field_name,$fields, $payment_status = 'succeeded', $payment_id = '', $succeeded = 'succeeded', $pending = 'pending', $failed = 'failed' ) {

		$field_name_first = $field_name;

		if (strpos($field_name, '[repeater id') !== false) { // ] [ [ fix alert
			$field_name = str_replace('id="', "id='", $field_name);
			$field_name = str_replace('"]', "']", $field_name);
			$message = $field_name;
			$repeater_content = '';
			$repeater_id_one = '';
			foreach ($fields as $field) {
				$field_value = $field['value'];
				$field_label = isset($field['label']) ? $field['label'] : '';
				if (isset($field['value_label'])) {
					$field_value = $field['value_label'];
				}
				
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
				$repeater_label = $field['repeater_label'] . ' ' . $repeater_index_1 . '</div>';

				$repeater_remove_this_field = false;
				if (isset($field['repeater_remove_this_field'])) {
					$repeater_remove_this_field = true;
				}
				
				if (!empty($repeater_id) && !empty($repeater_label) && $repeater_remove_this_field == false) {
					if (strpos($repeater_content, $repeater_label) !== false) {
						$string_add = '<div class="pafe-form-builder-preview-submission__item"><label class="pafe-form-builder-preview-submission__item-label">' . $field_label . ': </label>' . '<span class="pafe-form-builder-preview-submission__item-value">' . $field_value . '</span>' . '</div>';
						pafe_merge_string_preview($repeater_content,$string_add);
					} else {
						$string_add = $repeater_label . '<div class="pafe-form-builder-preview-submission__item"><label class="pafe-form-builder-preview-submission__item-label">' . $field_label . ': </label>' . '<span class="pafe-form-builder-preview-submission__item-value">' . $field_value . '</span>' . '</div>';
						pafe_merge_string_preview($repeater_content,$string_add);
					}
					if (substr_count($field['repeater_id'],'|') == 2) {
						pafe_set_string_preview($repeater_id_one,$field['repeater_id_one']);
					}
				}

				if (empty($repeater_id)) {
					if (!empty($repeater_id_one) && !empty($repeater_content)) {
						$search_repeater = "[repeater id='" . $repeater_id_one . "']";
						$message = str_replace($search_repeater, $repeater_content, $message);

						pafe_unset_string_preview($repeater_content);
						pafe_unset_string_preview($repeater_id_one);
					}
				}
			}

			$field_value = $message;
		} else {
			$field_name = get_field_name_shortcode_preview($field_name);
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
					}
				}
			}
		}

		if (strpos($field_name_first, '[payment_status]') !== false || strpos($field_name_first, '[payment_id]') !== false) {
			if ($payment_status == 'succeeded') {
				$field_value = str_replace( '[payment_status]', $succeeded, $field_name_first );
			}

			if ($payment_status == 'pending') {
				$field_value = str_replace( '[payment_status]', $pending, $field_name_first );
			}

			if ($payment_status == 'failed') {
				$field_value = str_replace( '[payment_status]', $failed, $field_name_first );
			}

			if (!empty($payment_id) && strpos($field_name_first, '[payment_id]') !== false) {
				$field_value = str_replace( '[payment_id]', $payment_id, $field_name_first );
			}
		}
		
		return trim($field_value);
	}

	function pafe_ajax_form_builder_preview_submission() {
		if ( !empty($_POST['fields']) ) {
			$fields = stripslashes($_POST['fields']);
			$fields = json_decode($fields, true);
			$fields = array_unique($fields, SORT_REGULAR);

			if (isset($_POST['remove_empty_fields'])) {
				$fields_new = array();
			    foreach ($fields as $field) {
			    	if (!empty($field['value'])) {
			    		$fields_new[] = $field;
			    	}
			    }
			    $fields = $fields_new;
			}

			if (isset($_POST['custom_list_fields'])) {
				$fields_name = array();

				$custom_list_fields = json_decode( stripslashes( $_POST['custom_list_fields'] ), true);

				foreach ($custom_list_fields as $shortcode) {
					$fields_name[] = get_field_name_shortcode_preview($shortcode['field_shortcode']);
				}

				$fields_new = array();
			    foreach ($fields as $field) {
			    	if (in_array($field['name'], $fields_name)) {
			    		$fields_new[] = $field;
			    	}
			    }
			    $fields = $fields_new;
			}

			$output = replace_email_preview('[all-fields]',$fields);

			if ($output != '[all-fields]') {
				echo $output;
			} else {
				echo '';
			}
		}

		wp_die();
	}