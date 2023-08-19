<?php

class WC_GFPA_Submission_Helpers {

	public static function capture_post_keys(): array {
		return array_keys( $_POST );
	}

	public static function reset_gravity_forms_post_fields( $post_keys ) {
		$_POST = array_intersect_key( $_POST, array_flip( $post_keys ) );
	}

	public static function revalidate_entry( $form_id, $entry_data ) {
		if ( empty( $form_id ) || empty( $entry_data ) ) {
			return false;
		}

		$form      = GFAPI::get_form( $form_id );
		$post_data = [];
		foreach ( $entry_data as $key => $value ) {
			$post_data[ 'input_' . str_replace( '.', '_', $key ) ] = $value;
		}

		//Find any upload fields in the form.
		$upload_fields = array_filter( $form['fields'], function ( $field ) {
			return $field->type === 'fileupload';
		} );

		//If there are any upload_fields, hydrate the globals. This is necessary for the file upload field to work and validate properly.
		if ( ! empty( $upload_fields ) ) {
			foreach ( $upload_fields as $upload_field ) {
				self::hydrate_field_file_upload( $upload_field, $entry_data );
			}
		}

		// We have to ignore some fields to avoid validation errors.
		self::setup_ignored_fields();

		// Capture the original post keys allowing us to reset them after the submission. This is just to be on the safe side in case other plugins are using $_POST.
		$original_post_fields = self::capture_post_keys();

		// Submit the form and capture the entry.
		$entry = GFAPI::submit_form( $form_id, $post_data );

		// Reset the $_POST to the original values.
		self::reset_gravity_forms_post_fields( $original_post_fields );

		// Reset the ignored fields.
		self::reset_ignored_fields();

		if ( is_wp_error( $entry ) ) {
			throw new Exception( $entry->get_error_message() );
		}

		return $entry;
	}

	public static function resubmit_entry( $form_id, $entry_data, $hydrate_defaults = false ) {
		// Get the form.  Do this first so that Gravity Forms loads required files.
		$form = GFAPI::get_form( $form_id );

		// Prepare to hydrate the post data.
		$post_data = [];
		foreach ( $entry_data as $key => $value ) {
			$post_data[ 'input_' . str_replace( '.', '_', $key ) ] = $value;
		}

		// If we are hydrating the defaults, we need to get the default values for the form.
		if ( $hydrate_defaults ) {
			$defaults = [];

			foreach ( $form['fields'] as $field ) {
				$f                                 = GFAPI::get_field( $form, $field->id );
				$defaults[ 'input_' . $field->id ] = WC_GFPA_Field_Helpers::get_lead_field_value_or_default( $entry_data, $f );
			}

			// Merge the defaults with the post data.
			$post_data = array_merge( $defaults, $post_data );
		}

		// Find any upload fields in the form.
		$upload_fields = array_filter( $form['fields'], function ( $field ) {
			return $field->type === 'fileupload';
		} );

		// If there are any upload_fields, hydrate the globals. This is necessary for the file upload field to work and validate properly.
		if ( ! empty( $upload_fields ) ) {
			foreach ( $upload_fields as $upload_field ) {
				self::hydrate_field_file_upload( $upload_field, $entry_data );
			}
		}

		self::setup_ignored_fields();
		$original_post_fields = self::capture_post_keys();

		// Reset the current lead and submission if there are any. Usually there will not be, but let's be safe.
		// Check that the classes exist first, because these won't be loaded until GFAPI::submit_form() is called, if there were no submissions processed already.
		if ( class_exists( 'GFFormsModel' ) ) {
			GFFormsModel::flush_current_lead();
		}

		if ( class_exists( 'GFFormDisplay' ) ) {
			GFFormDisplay::$submission = array();
		}

		$entry = GFAPI::submit_form( $form_id, $post_data );
		self::reset_gravity_forms_post_fields( $original_post_fields );
		self::reset_ignored_fields();

		// Reset the current lead and submission.
		if ( class_exists( 'GFFormsModel' ) ) {
			GFFormsModel::flush_current_lead();
		}

		if ( class_exists( 'GFFormDisplay' ) ) {
			GFFormDisplay::$submission = array();
		}

		if ( is_wp_error( $entry ) ) {
			throw new Exception( $entry->get_error_message() );
		}

		return $entry;
	}

	public static function setup_ignored_fields() {
		add_filter( 'gform_field_validation', array( __CLASS__, 'ignore_field_validation_rules' ), 9999, 4 );
	}

	public static function reset_ignored_fields() {
		remove_filter( 'gform_field_validation', array( __CLASS__, 'ignore_field_validation_rules' ), 9999 );
	}

	public static function ignore_field_validation_rules( $result, $value, $form, $field ) {
		$fields_to_ignore = apply_filters( 'woocommerce_gforms_field_validation_to_ignore', [
			'signature',
			'creditcard',
			'password',
			// 'fileupload',
			'captcha',
		] );

		$field_type = is_array( $field ) ? $field['type'] : $field->type;
		if ( in_array( $field_type, $fields_to_ignore, true ) ) {
			$result['is_valid'] = true;
			$result['message']  = '';
		}

		return $result;
	}

	public static function hydrate_field_file_upload( GF_Field_FileUpload $field, $entry_data ) {
		if ( $field->multipleFiles ) {
			$field_data = $entry_data[ $field->id ] ?? '';
			if ( $field->multipleFiles ) {
				$previous_files = rgar( $entry_data, $field->id );
				$files          = GFCommon::json_decode( stripslashes( $previous_files ) ) ?? array();
				//This is a multi file upload field, so we need to set $_POST['gform_uploaded_files'] to field's value from $entry_data.
				//Loop though entry_data's field value and insert the basename of each file that was uploaded into the array
				$uploaded_files[ 'input_' . $field->id ] = array();
				foreach ( $files as $file ) {
					$filename                                  = basename( $file );
					$uploaded_files[ 'input_' . $field->id ][] = [
						'uploaded_filename' => $filename,
					];
				}

				$_POST['gform_uploaded_files'] = GFCommon::json_encode( $uploaded_files );
			} else {
				$_POST[ 'input_' . $field->id ] = self::get_file_path_from_url( $entry_data[ $field->id ] ?? '' );
			}
		}
	}

	public static function get_file_path_from_url( $url ) {
		$uploads_dir = wp_upload_dir();
		$uploads_url = $uploads_dir['baseurl'];

		if ( strpos( $url, $uploads_url ) !== 0 ) {
			// URL is not within uploads directory
			return false;
		}

		$file_path = str_replace( $uploads_url, $uploads_dir['basedir'], $url );

		return $file_path;
	}

}
