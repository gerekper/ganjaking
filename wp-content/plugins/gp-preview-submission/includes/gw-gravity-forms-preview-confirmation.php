<?php
/**
* Better Pre-submission Confirmation
* http://gravitywiz.com/2012/08/04/better-pre-submission-confirmation/
*/
class GWPreviewConfirmation {

	/** @deprecated */
	private static $entry;
	private static $entries = array();

	public static function init() {

		add_filter( 'gform_pre_render', array( __class__, 'prepop_merge_tags' ) );
		add_filter( 'gform_pre_render', array( __class__, 'replace_merge_tags' ) );
		add_filter( 'gform_replace_merge_tags', array( __class__, 'product_summary_merge_tag' ), 10, 3 );
		add_filter( 'gform_merge_tag_filter', array( __class__, 'global_modifiers' ), 10, 5 );

		add_filter( 'gform_pre_validation', array( __class__, 'replace_field_label_merge_tags' ) );
		add_filter( 'gform_admin_pre_render', array( __class__, 'replace_entry_detail_merge_tags' ) );

	}

	public static function replace_merge_tags( $form ) {

		// safeguard for when this filter is called via a Partial Entries AJAX call
		if ( ! class_exists( 'GFFormDisplay' ) ) {
			return $form;
		}

		$current_page = isset( GFFormDisplay::$submission[ $form['id'] ] ) ? rgar( GFFormDisplay::$submission[ $form['id'] ], 'page_number' ) : 1;

		// get all HTML fields on the current page
		foreach ( $form['fields'] as &$field ) {

			self::replace_field_label_merge_tags( $form );

			// skip all fields on the first page
			if ( rgar( $field, 'pageNumber' ) <= 1 ) {
				continue;
			}

			$default_value = rgar( $field, 'defaultValue' );
			if ( gp_preview_submission()->has_any_merge_tag( $default_value ) ) {
				$field['defaultValue'] = rgar( $field, 'pageNumber' ) != $current_page ? '' : self::preview_replace_variables( $default_value, $form );
			}

			// only run 'content' filter for fields on the current page
			if ( rgar( $field, 'pageNumber' ) != $current_page ) {
				continue;
			}

			$html_content = rgar( $field, 'content' );
			if ( gp_preview_submission()->has_any_merge_tag( $html_content ) ) {
				$field['content'] = self::preview_replace_variables( $html_content, $form );
			}
		}

		return $form;
	}

	public static function replace_field_label_merge_tags( $form, $entry = null ) {

		$replace_merge_tags_in_labels = apply_filters( 'gppc_replace_merge_tags_in_labels', false, $form );
		if ( ! $replace_merge_tags_in_labels ) {
			return $form;
		}

		foreach ( $form['fields'] as &$field ) {

			$label = GFCommon::get_label( $field );

			if ( gp_preview_submission()->has_any_merge_tag( $label ) ) {
				$field->label = self::preview_replace_variables( $label, $form, $entry );
			}

			// the label of the Single Product field is captured and retrieved from the post on subsequent
			// page loads; update it in the $_POST as well to account for this.
			if ( in_array( $field->get_input_type(), array( 'singleproduct', 'calculation' ) ) ) {
				$_POST[ sprintf( 'input_%d_%d', $field->id, 1 ) ] = $field->label;
			}

			$choices = $field->choices;
			if ( is_array( $choices ) ) {
				foreach ( $choices as &$choice ) {
					if ( gp_preview_submission()->has_any_merge_tag( $choice['text'] ) ) {
						$is_same_choice_and_value = $choice['text'] == $choice['value'];
						$choice['text']           = self::preview_replace_variables( $choice['text'], $form, $entry );
						$choice['value']          = $is_same_choice_and_value ? $choice['text'] : self::preview_replace_variables( $choice['value'], $form, $entry );
					}
				}
			}
			$field->choices = $choices;

		}

		return $form;
	}

	public static function replace_entry_detail_merge_tags( $form ) {

		if ( GFForms::get_page() == 'entry_detail' || GFForms::get_page() == 'entry_detail_edit' ) {

			remove_filter( 'gform_admin_pre_render', array( __class__, 'replace_entry_detail_merge_tags' ) );
			$entry = is_callable( array( 'GFEntryDetail', 'get_current_entry' ) ) ? GFEntryDetail::get_current_entry() : null;
			add_filter( 'gform_admin_pre_render', array( __class__, 'replace_entry_detail_merge_tags' ) );

			$form = GWPreviewConfirmation::replace_field_label_merge_tags( $form, $entry );

		}

		return $form;
	}

	public static function prepop_merge_tags( $form ) {

		$has_applicable_field = false;

		foreach ( $form['fields'] as &$field ) {

			if ( ! rgar( $field, 'allowsPrepopulate' ) ) {
				continue;
			}

			$has_applicable_field = true;

			// complex fields store inputName in the "name" property of the inputs array
			if ( is_array( rgar( $field, 'inputs' ) ) && $field['type'] != 'checkbox' ) {
				foreach ( $field['inputs'] as $input ) {
					if ( rgar( $input, 'name' ) ) {
						self::add_dynamic_field_value_filter( rgar( $input, 'name' ), $field, $input['id'] );
					}
				}
			} else {
				self::add_dynamic_field_value_filter( rgar( $field, 'inputName' ), $field );
			}
		}

		if ( $has_applicable_field ) {
			add_filter( "gform_form_tag_{$form['id']}", array( __class__, 'add_page_progression_input' ), 99, 3 );
		}

		return $form;
	}

	public static function add_page_progression_input( $form_tag, $form ) {
		$input = sprintf( '<input type="hidden" value="%s" name="gpps_page_progression_%s" />', esc_html( self::get_page_progression( $form['id'] ) ), $form['id'] );
		return $form_tag . $input;
	}

	public static function get_page_progression( $form_id ) {

		$source_page = rgpost( "gform_source_page_number_{$form_id}" );
		$progression = rgpost( "gpps_page_progression_{$form_id}" );

		if ( $source_page > $progression ) {
			$progression = $source_page;
		}

		return intval( $progression );
	}

	public static function add_dynamic_field_value_filter( $name, $field, $input_id = false ) {

		// safeguard for when this filter is called via a Partial Entries AJAX call
		if ( ! class_exists( 'GFFormDisplay' ) ) {
			return;
		}

		$form = GFAPI::get_form( $field['formId'] );

		$value = self::preview_replace_variables( $name, $form );
		if ( $value == $name ) {
			return;
		}

		$is_submit    = ! empty( $_POST[ "is_submit_{$form['id']}" ] );
		$current_page = GFFormDisplay::get_current_page( $form['id'] );
		$field_page   = rgar( $field, 'pageNumber' );

		$input_id_bits = explode( '.', $input_id );
		$input_id      = array_pop( $input_id_bits );
		$key           = implode( '_', array_filter( array( 'input', $field['id'], $input_id ) ) );

		$on_field_page = $current_page == $field_page;
		$has_value     = ! rgempty( $key );

		if ( $is_submit && $on_field_page ) {
			if ( ! $has_value || self::get_page_progression( $form['id'] ) < $current_page ) {
				$_POST[ $key ] = $value;
			}
		} else {
			$value = str_replace( "'", '&#39;', $value );
			//add_filter( "gform_field_value_{$name}", create_function( "", "return '$value';" ) );
			add_filter( "gform_field_value_{$name}", array( new GP_Late_Static_Binding( array( 'value' => $value ) ), 'Perk_value_pass_through' ) );
		}

	}

	/**
	 * Adds special support for file upload, post image and multi input merge tags.
	 */
	public static function preview_special_merge_tags( $value, $input_id, $modifiers, $field ) {

		$input_type            = GFFormsModel::get_input_type( $field );
		$is_upload_field       = in_array( $input_type, array( 'post_image', 'fileupload', 'signature' ), true );
		$is_multi_upload_field = $is_upload_field && rgar( $field, 'multipleFiles' );

		$excluded_field_types   = array( 'survey' );
		$is_excluded_field_type = in_array( $field->type, $excluded_field_types ) || in_array( $input_type, $excluded_field_types );

		if ( $is_excluded_field_type ) {
			return $value;
		}

		// add support for Gravity Forms Slider add-on by WP Gurus
		if ( $input_type == 'slider' && defined( 'GF_SLIDER_FIELD_ADDON_VERSION' ) ) {
			// pulled straight of the GF Slider add-on code
			if ( ! empty( $field->strValue ) && ( ! empty( $field->stri_value_setting ) && $field->stri_value_setting == 1 ) ) {
				$strings = explode( ',', $field->strValue );
				$value   = $strings[ $_POST[ 'input_' . $field->id ] - 1 ];
				return $value;
			}
		}

		// added to prevent overriding :noadmin filter (and other filters that remove fields)
		// added exception for multi upload file fields which have an empty $value at this stage
		if ( ! $value && ! $is_multi_upload_field ) {
			return $value;
		}

		$is_multi_input = is_array( rgar( $field, 'inputs' ) );
		$is_input       = intval( $input_id ) != $input_id;

		if ( ! $is_upload_field && ! $is_multi_input ) {
			return $value;
		}

		// if is individual input of multi-input field, return just that input value
		if ( $is_input ) {
			return $value;
		}

		$form     = GFFormsModel::get_form_meta( $field['formId'] );
		$currency = GFCommon::get_currency();

		// Support entry modifier to allow fetching values from specific entry (used by GPNF when rendering child
		// entries via the Nested Form field merge tag and {all_fields}).
		$_modifiers = self::parse_modifiers( $modifiers );
		$entry_id   = rgar( $_modifiers, 'entry' );
		$entry      = $entry_id ? GFAPI::get_entry( $entry_id ) : self::create_lead( $form );

		if ( is_array( rgar( $field, 'inputs' ) ) ) {
			$value = GFFormsModel::get_lead_field_value( $entry, $field );
			$value = GFCommon::get_lead_field_display( $field, $value, $currency, ! rgar( $_modifiers, 'value' ) );
		} else {

			switch ( $input_type ) {
				case 'fileupload':
					$value = self::preview_image_value( "input_{$field['id']}", $field, $form, $entry );

					if ( $is_multi_upload_field ) {

						if ( is_a( $field, 'GF_Field' ) ) {
							$value = $field->get_value_entry_detail( json_encode( array_filter( (array) $value ) ) );
						} else {
							$value = GFCommon::get_lead_field_display( $field, json_encode( $value ) );
						}

						$input_name = 'input_' . str_replace( '.', '_', $field['id'] );
						$file_info  = self::get_uploaded_file_info( $form['id'], $input_name, $field );

						if ( $file_info ) {
							foreach ( $file_info as $file ) {
								$value = str_replace( '>' . $file['temp_filename'], '>' . $file['uploaded_filename'], $value );
							}
						}
					} else {
						$value = $input_id == 'all_fields' || rgar( $_modifiers, 'link' ) ? self::preview_image_display( $field, $form, $value ) : $value;
					}
					break;
				case 'signature':
					if ( $input_id !== 'all_fields' ) {
						$sig_title = $field->get_field_label( true, $value );
						$value     = sprintf( '<a href="%1$s"><img src="%1$s" alt="%2$s" title="%2$s"></a>', $value, $sig_title );
					}
					break;
				default:
					$value = self::preview_image_value( "input_{$field['id']}", $field, $form, $entry );
					$value = GFCommon::get_lead_field_display( $field, $value, $currency );
					break;
			}
		}

		$value = apply_filters( 'gpps_special_merge_tags_value', $value, $field, $input_id, $modifiers, $form, $entry );
		$value = apply_filters( sprintf( 'gpps_special_merge_tags_value_%s', $form['id'] ), $value, $field, $input_id, $modifiers, $form, $entry );
		$value = apply_filters( sprintf( 'gpps_special_merge_tags_value_%s_%s', $form['id'], $field['id'] ), $value, $field, $input_id, $modifiers, $form, $entry );
		$value = apply_filters( sprintf( 'gpps_special_merge_tags_value_%s', $input_type ), $value, $field, $input_id, $modifiers, $form, $entry );
		$value = apply_filters( sprintf( 'gpps_special_merge_tags_value_%s_%s', $form['id'], $input_type ), $value, $field, $input_id, $modifiers, $form, $entry );

		return $value;
	}

	public static function preview_image_value( $input_name, $field, $form, $entry ) {

		$file_info = self::get_uploaded_file_info( $form['id'], $input_name, $field );

		if ( ! self::is_multi_file_field( $field ) ) {
			$file_url = GFFormsModel::get_upload_url( $form['id'] ) . '/tmp/' . $file_info['temp_filename'];
			$source   = $field->get_download_url( $file_url );
		}

		if ( ! $file_info ) {
			return '';
		}

		switch ( GFFormsModel::get_input_type( $field ) ) {

			case 'post_image':
				list(,$image_title, $image_caption, $image_description) = explode( '|:|', $entry[ $field['id'] ] );
				$value = ! empty( $source ) ? $source . '|:|' . $image_title . '|:|' . $image_caption . '|:|' . $image_description : '';
				break;

			case 'fileupload':
				if ( rgar( $field, 'multipleFiles' ) ) {
					$file_names = wp_list_pluck( $file_info, 'temp_filename' );
					$value      = array();
					foreach ( $file_names as $file_name ) {
						$value[] = GFFormsModel::get_upload_url( $form['id'] ) . '/tmp/' . basename( $file_name );
					}
				} else {
					$value = $source;
				}
				break;

		}

		return $value;
	}

	public static function preview_image_display( $field, $form, $value ) {

		// need to get the tmp $file_info to retrieve real uploaded filename, otherwise will display ugly tmp name
		$input_name = 'input_' . str_replace( '.', '_', $field['id'] );
		$file_info  = GFFormsModel::get_temp_filename( $form['id'], $input_name );
		$file_path  = $value;

		if ( ! empty( $file_path ) ) {
			$file_path = esc_attr( str_replace( ' ', '%20', $file_path ) );
			$value     = "<a href='$file_path' target='_blank' title='" . __( 'Click to view', 'gravityforms' ) . "'>" . $file_info['uploaded_filename'] . '</a>';
		}

		return $value;
	}

	public static function parse_modifiers( $modifiers_str ) {

		preg_match_all( '/([a-z]+)(?:(?:\[(.+?)\])|,?)/i', $modifiers_str, $modifiers, PREG_SET_ORDER );
		$parsed = array();

		foreach ( $modifiers as $modifier ) {

			list( $match, $modifier, $value ) = array_pad( $modifier, 3, null );
			if ( $value === null ) {
				$value = $modifier;
			}

			// Split '1,2,3' into array( 1, 2, 3 ).
			if ( strpos( $value, ',' ) !== false ) {
				$value = array_map( 'trim', explode( ',', $value ) );
			}

			$parsed[ $modifier ] = $value;

		}

		return $parsed;
	}

	/**
	* Retrieves $entry object from class if it has already been created; otherwise creates a new $entry object.
	*/
	public static function create_lead( $form ) {

		/**
		 * Filter the entry to be used to replace merge tags - before - it has been generated.
		 *
		 * @since 1.2.9
		 *
		 * @param object|bool $entry Entry object or null if this if the first time the function has been called.
		 * @param object      $form  The current form object.
		 */
		$entry = gf_apply_filters( array( 'gpps_entry_pre_create', $form['id'] ), rgar( self::$entries, $form['id'], null ), $form );

		// We will need to create an entry if one has not already been created - or - if there are multiple forms on the
		// same page, we will need to get the entry separately.
		if ( empty( $entry ) ) {

			// flush runtime cache so we have a clean slate (fixes issue with WC GF Product Add-ons plugin)
			GFCache::flush();

			if ( isset( $_GET['gf_token'] ) ) {
				$incomplete_submission_info = GFFormsModel::get_draft_submission_values( $_GET['gf_token'] );
				if ( $incomplete_submission_info['form_id'] == $form['id'] ) {
					$submission_details_json = $incomplete_submission_info['submission'];
					$submission_details      = json_decode( $submission_details_json, true );
					$entry                   = $submission_details['partial_entry'];
				}
			}

			$entry = ! empty( $entry ) ? $entry : GFFormsModel::create_lead( $form );
			self::clear_field_value_cache( $form );

			foreach ( $form['fields'] as $field ) {

				$input_type = GFFormsModel::get_input_type( $field );

				switch ( $input_type ) {
					case 'signature':
						if ( empty( $entry[ $field['id'] ] ) ) {
							$entry[ $field['id'] ] = rgpost( "input_{$form['id']}_{$field['id']}_signature_filename" );
						}
						break;
					// Improves support for GP eCommerce Fields; calculations will be wrong if calculated prior to submission.
					// We'll assume posted values are correct since they will be re-validated on submission regardless.
					case 'calculation':
					case 'number':
						if ( ! $field->has_calculation() ) {
							break;
						}
						$is_product = $field['type'] == 'product';
						$input_id   = $is_product ? sprintf( '%s.%s', $field->id, 2 ) : $field->id;
						if ( empty( $entry[ $field['id'] ] ) ) {
							$entry[ $input_id ] = rgpost( sprintf( 'input_%s', str_replace( '.', '_', $input_id ) ) );
						}
						break;
				}
			}

			// process $entry through 'gform_get_input_value' (specifically added for support with encrypting/decrypting
			foreach ( $form['fields'] as $field ) {
				$inputs = $field->get_entry_inputs();
				if ( is_array( $inputs ) ) {
					foreach ( $inputs as $input ) {
						$entry[ (string) $input['id'] ] = gf_apply_filters( array( 'gform_get_input_value', $form['id'], $field->id, $input['id'] ), rgar( $entry, (string) $input['id'] ), $entry, $field, $input['id'] );
					}
				} else {
					$value = rgar( $entry, (string) $field->id );
					if ( self::is_encrypted_field( rgar( $entry, 'id' ), $field->id ) ) {
						$value = GFCommon::decrypt( $value );
					}
					$entry[ $field->id ] = gf_apply_filters( array( 'gform_get_input_value', $form['id'], $field->id ), $value, $entry, $field, '' );
				}
			}
		}

		/**
		 * Filter the entry to be used to replace merge tags - after - it has been generated.
		 *
		 * @since 1.2.9
		 *
		 * @param object|bool $entry Entry object or null if this if the first time the function has been called.
		 * @param object      $form  The current form object.
		 */
		$entry = gf_apply_filters( array( 'gpps_entry_post_create', $form['id'] ), $entry, $form );

		self::$entries[ $form['id'] ] = $entry;

		return $entry;
	}

	public static function preview_replace_variables( $content, $form, $entry = null ) {

		if ( gp_preview_submission()->has_gppa_parent_merge_tag( $content ) ) {
			return $content;
		}

		if ( $entry == null ) {
			$entry = self::create_lead( $form );
		}

		/**
		 * Filter the content before merge tags are replaced.
		 *
		 * @since 1.2.14
		 *
		 * @param string $content The content that will have merge tag replacement ran on it.
		 * @param array $form The current form object.
		 * @param array $entry The current entry.
		 */
		$content = apply_filters( 'gpps_pre_replace_merge_tags', $content, $form, $entry );

		// add filter that will handle getting temporary URLs for file uploads and post image fields (removed below)
		// beware, the GFFormsModel::create_lead() function also triggers the gform_merge_tag_filter at some point and will
		// result in an infinite loop if not called first above
		add_filter( 'gform_merge_tag_filter', array( 'GWPreviewConfirmation', 'preview_special_merge_tags' ), 10, 4 );

		$content = GFCommon::replace_variables( $content, $form, $entry, false, false, false );

		// remove filter so this function is not applied after preview functionality is complete
		remove_filter( 'gform_merge_tag_filter', array( 'GWPreviewConfirmation', 'preview_special_merge_tags' ) );

		/**
		 * Filter the content after merge tags have been replaced.
		 *
		 * @since 1.2.14
		 *
		 * @param string $content The content that had merge tags replaced in it.
		 * @param array $form The current form object.
		 * @param array $entry The current entry.
		 */
		$content = apply_filters( 'gpps_post_replace_merge_tags', $content, $form, $entry );

		return $content;
	}

	public static function clear_field_value_cache( $form ) {

		if ( ! class_exists( 'GFCache' ) ) {
			return;
		}

		foreach ( $form['fields'] as &$field ) {
			//if( GFFormsModel::get_input_type( $field ) == 'total' )
			GFCache::delete( 'GFFormsModel::get_lead_field_value__' . $field->id );
		}

	}

	/**
	 * Provides an {order_summary} merge tag which outputs only the submitted pricing fields table which is automatically appended to the {all_fields} output.
	 * This differentiates itself from the GF default {pricing_fields} merge tag in that it does not output the pricing data in a table withing a table.
	 * [Example](http://grab.by/ygKq)
	 *
	 * @param $text
	 * @param $form
	 * @param $entry
	 *
	 * @return mixed
	 */
	public static function product_summary_merge_tag( $text, $form, $entry ) {

		if ( ! $text || ! $form || ! $entry ) {
			return $text;
		}

		$tags    = array( '{product_summary}', '{order_summary}' );
		$has_tag = false;

		foreach ( $tags as $tag ) {
			if ( strpos( $text, $tag ) !== false ) {
				$has_tag = true;
				break;
			}
		}

		if ( ! $has_tag ) {
			return $text;
		}

		if ( empty( $entry ) ) {
			$entry = self::create_lead( $form );
		}

		add_filter( 'gform_order_label', '__return_false', 11 );

		$remove          = array( "<tr bgcolor=\"#EAF2FA\">\n                            <td colspan=\"2\">\n                                <font style=\"font-family: sans-serif; font-size:12px;\"><strong>Order</strong></font>\n                            </td>\n                       </tr>\n                       <tr bgcolor=\"#FFFFFF\">\n                            <td width=\"20\">&nbsp;</td>\n                            <td>\n                                ", "\n                            </td>\n                        </tr>" );
		$product_summary = str_replace( $remove, '', GFCommon::get_submitted_pricing_fields( $form, $entry, 'html' ) );

		remove_filter( 'gform_order_label', '__return_false', 11 );

		return str_replace( $tags, $product_summary, $text );
	}

	public static function get_uploaded_file_info( $form_id, $input_name, $field ) {

		// hack alert: force retrieval of unique ID for filenames when continuing from saved entry
		if ( self::is_save_and_continue() && ! isset( $_POST['gform_submit'] ) ) {
			$is_gform_submit_set_manually = true;
			$_POST['gform_submit']        = $form_id;
		}

		$uploaded_files = isset( GFFormsModel::$uploaded_files[ $form_id ][ $input_name ] ) ? GFFormsModel::$uploaded_files[ $form_id ][ $input_name ] : array();
		$file_info      = self::is_multi_file_field( $field ) ? $uploaded_files : GFFormsModel::get_temp_filename( $form_id, $input_name );

		if ( self::is_save_and_continue() && ! isset( $_POST['gform_submit'] ) ) {
			$_POST['gform_submit'] = $form_id;
		}

		// hack alert: force retrieval of unique ID for filenames when continuing from saved entry
		if ( isset( $is_gform_submit_set_manually ) ) {
			unset( $_POST['gform_submit'] );
		}

		return $file_info;
	}

	public static function is_multi_file_field( $field ) {
		return rgar( $field, 'multipleFiles' ) == true;
	}

	public static function global_modifiers( $field_value, $merge_tag, $options, $field, $field_label ) {

		if ( ! $field_value || $options != 'link' ) {
			return $field_value;
		}

		$file_path = esc_attr( str_replace( ' ', '%20', $field_value ) );
		$value     = "<a href='$file_path' target='_blank' title='" . __( 'Click to view', 'gravityforms' ) . "'>" . basename( $file_path ) . '</a>';

		return $value;
	}

	public static function is_save_and_continue() {
		return rgget( 'gf_token' );
	}

	/**
	 * Checks whether the given field was encrypted using GFCommon::encrpyt() and registered using GFCommon::set_encrypted_fields()
	 *
	 * @deprecated In GF 2.3; this is a copy of that method until 3rd-party dependence no longer exists.
	 *
	 * @since unknown
	 *
	 * @param $entry_id
	 * @param $field_id
	 *
	 * @return bool|mixed
	 */
	public static function is_encrypted_field( $entry_id, $field_id ) {

		/**
		 * Determines if an entry field is stored encrypted. Use this hook to change the default behavior of decrypting fields that have been encrypted or to completely disable the
		 * process if checking for encrypted fields.
		 *
		 * @param int $entry_id The current Entry ID
		 * @param int $field_id The current Field ID.
		 */
		$is_encrypted = apply_filters( 'gform_is_encrypted_field', '', $entry_id, $field_id );
		if ( $is_encrypted !== '' ) {
			return $is_encrypted;
		}

		$encrypted_fields = self::get_encrypted_fields( $entry_id );

		return in_array( $field_id, $encrypted_fields );
	}

	/**
	 * Returns an array of field IDs that have been encrypted using GFCommon::encrypt()
	 *
	 * @deprecated In GF 2.3; this is a copy of that method until 3rd-party dependence no longer exists.
	 *
	 * @since unknown
	 *
	 * @param $entry_id
	 *
	 * @return array|bool|mixed
	 */
	public static function get_encrypted_fields( $entry_id ) {

		$encrypted_fields = gform_get_meta( $entry_id, '_encrypted_fields' );
		if ( empty( $encrypted_fields ) ) {
			$encrypted_fields = array();
		}

		return $encrypted_fields;
	}

}

GWPreviewConfirmation::init();
