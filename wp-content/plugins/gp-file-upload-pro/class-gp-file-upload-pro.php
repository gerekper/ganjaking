<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_File_Upload_Pro extends GP_Plugin {

	private static $instance = null;

	/**
	 * Marks which scripts/styles have been localized to avoid localizing multiple times with
	 * Gravity Forms' scripts 'callback' property.
	 *
	 * @var array
	 */
	protected $_localized = array();

	protected $_version     = GPFUP_VERSION;
	protected $_path        = 'gp-file-upload-pro/gp-file-upload-pro.php';
	protected $_full_path   = __FILE__;
	protected $_slug        = 'gp-file-upload-pro';
	protected $_title       = 'Gravity Forms File Upload Pro';
	protected $_short_title = 'File Upload Pro';

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function minimum_requirements() {
		return array(
			'gravityforms' => array(
				'version' => '2.3-rc-1',
			),
			'wordpress'    => array(
				'version' => '4.8',
			),
			'plugins'      => array(
				'gravityperks/gravityperks.php' => array(
					'name'    => 'Gravity Perks',
					'version' => '2.0',
				),
			),
		);
	}

	public function init() {

		parent::init();

		gpfup_compatibility_gravitypdf();

		load_plugin_textdomain( 'gp-file-upload-pro', false, basename( dirname( __file__ ) ) . '/languages/' );

		add_action( 'gperk_field_settings', array( $this, 'field_settings_ui' ) );
		add_filter( 'gform_after_save_form', array( $this, 'maybe_upgrade_to_multi_file_upload' ) );
		add_filter( 'gform_field_content', array( $this, 'maybe_append_rehydration_info' ), 10, 5 );

		add_filter( 'gform_user_registration_is_new_file_upload', array( $this, 'compatibility_gfur_allow_sorting' ), 10, 3 );

	}

	public function init_admin() {
		parent::init_admin();

		GravityPerks::enqueue_field_settings();
	}

	public function scripts() {

		$scripts = array(
			array(
				'handle'  => 'gravityperks-vue-2',
				'src'     => $this->get_base_url() . '/js/built/vue-2.js',
				'version' => '2.6.14',
			),
			array(
				'handle'    => 'gp-file-upload-pro',
				'src'       => $this->get_base_url() . '/js/built/gp-file-upload-pro.js',
				'version'   => $this->_version,
				'deps'      => array( 'gform_gravityforms', 'plupload-all', 'gravityperks-vue-2' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( $this, 'should_enqueue_frontend' ),
				),
				'callback'  => array( $this, 'localize_frontend_scripts' ),
			),
			array(
				'handle'    => 'gp-file-upload-pro-admin',
				'src'       => $this->get_base_url() . '/js/built/gp-file-upload-pro-admin.js',
				'version'   => $this->_version,
				'deps'      => array( 'jquery', 'gravityperks-vue-2' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
				'callback'  => array( $this, 'localize_admin_scripts' ),
			),
		);

		/**
		 * Filter the registered scripts for File Upload Pro.
		 *
		 * @param array $scripts The registered scripts.
		 *
		 * @since 1.1.10
		 */
		return apply_filters( 'gpfup_scripts', array_merge( parent::scripts(), $scripts ) );

	}

	/**
	 * Determine if frontend scripts/styles should be enqueued. We first check for the proper field types and then for
	 * the gpfupEnabled field setting.
	 *
	 * @param $form
	 *
	 * @return bool
	 */
	public function should_enqueue_frontend( $form ) {
		// Disable scripts on post/page edit. Fixes a compatibility issue with the new Gutenberg editor. See HS#26665
		global $pagenow;
		if ( GFForms::get_page() || $pagenow === 'post.php' ) {
			return false;
		}

		if ( empty( $form['fields'] ) ) {
			return false;
		}

		$should_enqueue = false;

		foreach ( $form['fields'] as $field ) {
			if ( $field->get_input_type() === 'fileupload' && rgar( $field, 'gpfupEnable' ) ) {
				if ( rgar( $field, 'gpfupEnableSorting' ) ) {
					// Enqueue Gravity Forms admin icons to get the sort handle icon.
					wp_enqueue_style( 'gform_admin_icons' );

					/**
					 * Return out instead of continuing to loop if we already need to enqueue everything we have.
					 */
					return true;
				}

				$should_enqueue = true;
			}
		}

		return $should_enqueue;
	}


	/**
	 * Output <div> to be used by vue-portal to render the cropper in.
	 *
	 * @param $form array
	 * @param $field GF_Field
	 *
	 * @return void
	 */
	public function output_cropper_vue_portal( $form, $field ) {
		echo "<div id='gpfup-cropper-portal-{$form['id']}-{$field->id}'></div>";
	}

	public function is_localized( $item ) {
		return in_array( $item, $this->_localized );
	}

	/**
	 * Convert aspect ratio with antecedent:consequent to a float.
	 *
	 * @param $field
	 *
	 * @return float
	 */
	public function get_aspect_ratio_float( $field ) {
		$exact_width  = rgar( $field, 'gpfupExactWidth' );
		$exact_height = rgar( $field, 'gpfupExactHeight' );

		if ( $exact_width && $exact_height ) {
			return $exact_width / $exact_height;
		}

		$antecedent = rgar( $field, 'gpfupAspectRatioAntecedent' );
		$consequent = rgar( $field, 'gpfupAspectRatioConsequent' );

		if ( ! $antecedent || ! $consequent ) {
			return null;
		}

		return $antecedent / $consequent;
	}

	/**
	 * Append JSON variable of hydration info if rehydration is necessary.
	 *
	 * This is used in favor of wp_localize_script as localization of scripts happens prior to
	 * GFFormsModel::$uploaded_files being initialized.
	 *
	 * @param $content string Field markup
	 * @param $field GF_Field_FileUpload
	 * @param $value string | array
	 * @param $entry_id number
	 * @param $form_id number
	 *
	 * @return string Field markup
	 */
	public function maybe_append_rehydration_info( $content, $field, $value, $entry_id, $form_id ) {
		if ( $field->type !== 'fileupload' || ! rgar( $field, 'gpfupEnable' ) ) {
			return $content;
		}

		$rehydration_info = $this->get_rehydration_info( $field, $value );

		if ( ! $rehydration_info ) {
			return $content;
		}

		$var_name = 'gpfup_rehydration_' . $form_id . '_' . $field->id;

		$content .= '<script type="text/javascript">
			var ' . $var_name . ' = ' . wp_json_encode( $rehydration_info ) . ';
		</script>';

		return $content;
	}

	/**
	 * Return rehydration info for the files attached to the current value. This is needed for Save & Continue and in
	 * the future, it will be used for other use cases like frontend editing and GravityView in general.
	 *
	 * @param $field GF_Field_FileUpload
	 * @param $value string|array Field value.
	 *
	 * @returns array | null Info to rehydrate the file. Will typically include URL (if image), file type, and size.
	 */
	public function get_rehydration_info( $field, $value ) {
		$rehydration_info = array();

		$form_id = rgar( $field, 'formId' );

		if ( ! $form_id ) {
			return null;
		}

		if ( ! isset( GFFormsModel::$uploaded_files[ $form_id ][ "input_{$field->id}" ] ) || ! is_array( GFFormsModel::$uploaded_files[ $form_id ][ "input_{$field->id}" ] ) ) {
			return null;
		}

		/* Get dynamically populated value. Needed to get the full URL for the GF User Registration Add-On. */
		$value = self::maybe_decode_json( $value );

		if ( empty( $value ) ) {
			$value = RGFormsModel::get_parameter_value( $field->inputName, array(), $field );
		}

		$prepopulated_files = $value;

		if ( is_string( $value ) ) {
			$prepopulated_files = explode( ',', $value );
		}

		foreach ( GFFormsModel::$uploaded_files[ $form_id ][ "input_{$field->id}" ] as $file ) {
			if ( ! empty( $file['temp_filename'] ) ) {
				$path = GFFormsModel::get_upload_path( $form_id ) . '/tmp/' . $file['temp_filename'];
				$url  = GFFormsModel::get_upload_url( $form_id ) . '/tmp/' . $file['temp_filename'];
			} elseif ( ! empty( $file['uploaded_filename'] ) ) {
				foreach ( $prepopulated_files as $prepopulated_file ) {
					$prepopulated_file = trim( $prepopulated_file );

					if ( basename( $prepopulated_file ) === $file['uploaded_filename'] ) {
						$url          = $prepopulated_file;
						$partial_path = str_replace( GFFormsModel::get_upload_url( $form_id ), '', $prepopulated_file );
						$path         = GFFormsModel::get_upload_path( $form_id ) . $partial_path;

						break;
					}
				}

				if ( ! $path || ! $url ) {
					continue;
				}
			} else {
				continue;
			}

			$ext_and_type = wp_check_filetype( $path );
			$size         = null;

			if ( file_exists( $path ) ) {
				$size = filesize( $path );
			}

			if ( ! empty( $file['temp_filename'] ) ) {
				preg_match( '/_(o_[a-z0-9]+)\.[a-zA-Z0-9]{1,4}/', $file['temp_filename'], $file_id_match );

				if ( ! $file_id_match ) {
					continue;
				}

				$file_id = $file_id_match[1];
			} elseif ( ! empty( $file['uploaded_filename'] ) ) {
				$file_id = $file['uploaded_filename'];
			} else {
				continue;
			}

			$file_info = array(
				'size' => $size,
				'type' => $ext_and_type['type'],
			);

			if ( strpos( $ext_and_type['type'], 'image/' ) === 0 ) {
				$file_info['url'] = $field->get_download_url( $url );
			}

			$rehydration_info[ $file_id ] = $file_info;
		}

		return $rehydration_info;
	}

	public function localize_frontend_scripts( $form ) {

		/**
		 * Form-specific localization/init info
		 */
		if ( ! $this->is_localized( 'gp-file-upload-pro-form-' . $form['id'] ) ) {
			$gpfup_fields = array();

			foreach ( $form['fields'] as $field ) {
				if ( $field->get_input_type() === 'fileupload' && rgar( $field, 'gpfupEnable' ) ) {
					$gpfup_fields[] = array(
						'formId'        => $form['id'],
						'fieldId'       => $field['id'],
						'enableCrop'    => rgar( $field, 'gpfupEnableCrop' ),
						'enableSorting' => rgar( $field, 'gpfupEnableSorting' ),
						'cropRequired'  => rgar( $field, 'gpfupCropRequired' ),
						'aspectRatio'   => $this->get_aspect_ratio_float( $field ),
						'maxWidth'      => rgar( $field, 'gpfupMaxWidth' ),
						'maxHeight'     => rgar( $field, 'gpfupMaxHeight' ),
						'minWidth'      => rgar( $field, 'gpfupMinWidth' ),
						'minHeight'     => rgar( $field, 'gpfupMinHeight' ),
						'exactWidth'    => rgar( $field, 'gpfupExactWidth' ),
						'exactHeight'   => rgar( $field, 'gpfupExactHeight' ),
					);
				}
			}

			wp_localize_script( 'gp-file-upload-pro', 'GPFUP_FORM_INIT_' . $form['id'], $gpfup_fields );

			$this->_localized[] = 'gp-file-upload-pro-form-' . $form['id'];
		}

		/**
		 * If a script is enqueued in the footer with in_footer, this script will
		 * be called multiple times and we need to guard against localizing multiple times.
		 */
		if ( $this->is_localized( 'gp-file-upload-pro' ) ) {
			return;
		}

		wp_localize_script( 'gp-file-upload-pro', 'GPFUP_CONSTANTS', array(
			'STRINGS' => array(
				'select_files'                     => __( 'select files', 'gp-file-upload-pro' ),
				'drop_files_here'                  => __( 'Drop files here', 'gp-file-upload-pro' ),
				'or'                               => __( 'or', 'gp-file-upload-pro' ),
				'cancel'                           => __( 'Cancel', 'gp-file-upload-pro' ),
				'crop'                             => __( 'Crop', 'gp-file-upload-pro' ),
				'cropping'                         => __( 'Cropping', 'gp-file-upload-pro' ),
				'croppingOf'                       => __( 'of', 'gp-file-upload-pro' ), // Cropping x _of_ y.
				'does_not_meet_minimum_dimensions' => __( 'This image does not meet the minimum dimensions: {minWidth}x{minHeight}px.', 'gp-file-upload-pro' ),
				'does_not_meet_minimum_width'      => __( 'This image does not meet the minimum width: {minWidth}px.', 'gp-file-upload-pro' ),
				'does_not_meet_minimum_height'     => __( 'This image does not meet the minimum height: {minHeight}px.', 'gp-file-upload-pro' ),
			),
		) );

		$this->_localized[] = 'gp-file-upload-pro';

	}

	public function localize_admin_scripts() {
		wp_localize_script( 'gp-file-upload-pro-admin', 'GPFUP_CONSTANTS', array(
			'STRINGS' => array(
				'enable_cropping'          => __( 'Enable Cropping', 'gp-file-upload-pro' ),
				'enable_sorting'           => __( 'Enable Sorting', 'gp-file-upload-pro' ),
				'enable_file_upload_pro'   => __( 'Enable File Upload Pro', 'gp-file-upload-pro' ),
				'upgrade_multiple_files'   => __( "You are about to enable File Upload Pro on a Single File Upload field.\n\nCompleting this action will convert this field to a Multi-file Upload field. Entry data associated with this field will be converted into the multi-file upload format when the form is saved. This conversion cannot be undone.\n\nWould you like to enable File Upload Pro on this field?", 'gp-file-upload-pro' ),
				'require_crop'             => __( 'Require Crop', 'gp-file-upload-pro' ),
				'constraints'              => __( 'Constraints', 'gp-file-upload-pro' ),
				'aspect_ratio'             => __( 'Aspect Ratio', 'gp-file-upload-pro' ),
				'max_dimensions'           => __( 'Max Dimensions', 'gp-file-upload-pro' ),
				'min_dimensions'           => __( 'Min Dimensions', 'gp-file-upload-pro' ),
				'exact_dimensions'         => __( 'Exact Dimensions', 'gp-file-upload-pro' ),
				'width'                    => __( 'Width', 'gp-file-upload-pro' ),
				'height'                   => __( 'Height', 'gp-file-upload-pro' ),
				'multi_file_requirement'   => sprintf(
				// translators: placeholder is URL to File Upload Pro FAQ
					__( 'This setting is required when File Upload Pro is enabled. <a href="%s" target="_blank">Learn more.</a>', 'gp-file-upload-pro' ),
					'https://gravitywiz.com/documentation/gravity-forms-file-upload-pro/#why-is-the-8220-multiple-files-8221-setting-required-when-file-upload-pro-is-enabled'
				),
				'tooltip_enable'           => sprintf(
					'<h6>%s</h6> %s',
					__( 'File Upload Pro', 'gp-file-upload-pro' ),
					__( 'Greatly improve the uploading experience for your Gravity Forms File Upload fields.', 'gp-file-upload-pro' )
				),
				'tooltip_enable_cropping'  => sprintf(
					'<h6>%s</h6> %s',
					__( 'Enable Cropping', 'gp-file-upload-pro' ),
					__( 'Automatically crop images according to the specified constraints. Users may re-crop the image by clicking on the image thumbnail. Use the Require Crop setting to require the user to manually crop the image.', 'gp-file-upload-pro' )
				),
				'tooltip_enable_sorting'   => sprintf(
					'<h6>%s</h6> %s',
					__( 'Enable Sorting', 'gp-file-upload-pro' ),
					__( 'Allow the order of files to be modified using drag-and-drop sorting in the uploader area.', 'gp-file-upload-pro' )
				),
				'tooltip_require_crop'     => sprintf(
					'<h6>%s</h6> %s',
					__( 'Require Crop on Upload', 'gp-file-upload-pro' ),
					__( 'Require that images be cropped on upload by automatically opening the cropper. If the crop is canceled, the files are not uploaded.', 'gp-file-upload-pro' )
				),
				'tooltip_exact_dimensions' => sprintf(
					'<h6>%s</h6> %s',
					__( 'Exact Dimensions', 'gp-file-upload-pro' ),
					__( 'Require file to be upload with exact dimensions. These dimensions will be used as the minimum dimensions in the cropper and an aspect ratio will be derived from the dimensions. If an image exceeds the exact dimensions after cropping, it will be downscaled.', 'gp-file-upload-pro' )
				),
				'tooltip_min_dimensions'   => sprintf(
					'<h6>%s</h6> %s',
					__( 'Minimum Dimensions', 'gp-file-upload-pro' ),
					__( 'Require cropper to meet the minimum dimensions. If cropper is not used, file dimensions will be validated prior to upload.', 'gp-file-upload-pro' )
				),
				'tooltip_max_dimensions'   => sprintf(
					'<h6>%s</h6> %s',
					__( 'Maximum Dimensions', 'gp-file-upload-pro' ),
					__( 'Images will be downscaled to the maximum dimensions set below.', 'gp-file-upload-pro' )
				),
				'tooltip_aspect_ratio'     => sprintf(
					'<h6>%s</h6> %s',
					__( 'Aspect Ratio', 'gp-file-upload-pro' ),
					__( 'Restrict cropper to aspect ratio.', 'gp-file-upload-pro' )
				),
			),
		) );
	}

	public function styles() {

		$styles = array(
			array(
				'handle'  => 'gp-file-upload-pro',
				'src'     => $this->get_base_url() . '/styles/gp-file-upload-pro.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( $this, 'should_enqueue_frontend' ),
				),
			),
			array(
				'handle'  => 'gp-file-upload-pro-admin',
				'src'     => $this->get_base_url() . '/styles/gp-file-upload-pro-admin.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
			),
		);

		return array_merge( parent::styles(), $styles );

	}

	## Admin Field Settings
	## SETTINGS ##

	public function field_settings_ui( $position ) {
		/*
		 * The class of this root element needs to contain whatever field setting classes are used for the Add-On otherwise GF 2.6 will nuke them due to them
		 * not being present in the initial markup.
		 *
		 * Additionally, it needs to be an <li>, have the class to protect as the first class, and also have the field_setting class.
		 */
		?>
		<!-- Populated with Vue -->
		<li id="gpfup" class="gpfup-field-setting field_setting"></li>
		<li class="gp-field-setting field_setting"></li>
		<?php
	}

	/**
	 * Converts existing entries of single-file upload field to data format for multi-file upload.
	 *
	 * @param array $form_meta      The form meta
	 */
	public function maybe_upgrade_to_multi_file_upload( $form_meta ) {
		global $wpdb;

		$entry_meta_table = GFFormsModel::get_entry_meta_table_name();
		$field_upgraded   = false;

		foreach ( $form_meta['fields'] as &$field ) {
			if ( ! rgar( $field, 'gpfupUpgradeToMultipleFiles' ) || $field->get_input_type() !== 'fileupload' ) {
				continue;
			}

			/**
			 * As a quick aside, forward slashes are escaped with a backslash in PHP's json_encode.
			 *
			 * Due to MySQL 5.6 not having JSON escape functions readily available, we forego a complex string escape
			 * as we expect the uploaded files to be simple paths/URLs.
			 *
			 * That said, not escaping forward slashes _is_ JSON compliant.
			 */
			$query = $wpdb->prepare( "UPDATE {$entry_meta_table}
				SET meta_value = CONCAT('[\"', meta_value, '\"]')
				WHERE meta_key = %s
				AND form_id = %d", $field['id'], $form_meta['id'] );

			$wpdb->query( $query );

			$field_upgraded = true;

			unset( $field['gpfupUpgradeToMultipleFiles'] );
		}

		if ( ! $field_upgraded ) {
			return;
		}

		GFFormsModel::update_form_meta( $form_meta['id'], $form_meta );
	}

	/**
	 * For sorting to work without uploading a new file, we need to tell GFUR that a new file has been uploaded so
	 * the form value is used rather than the existing meta.
	 *
	 * @param boolean $is_new_file_upload
	 * @param number $form_id
	 * @param string $input_name
	 */
	public function compatibility_gfur_allow_sorting( $is_new_file_upload, $form_id, $input_name ) {
		$file_info = GFFormsModel::get_temp_filename( $form_id, $input_name );

		if ( isset( $file_info['uploaded_filename'] ) && is_array( $file_info['uploaded_filename'] ) ) {
			foreach ( $file_info['uploaded_filename'] as $file ) {
				if ( rgar( $file, 'gpfup_order_changed' ) ) {
					return true;
				}
			}
		}

		return $is_new_file_upload;
	}
}

function gp_file_upload_pro() {
	return GP_File_Upload_Pro::get_instance();
}

GFAddOn::register( 'GP_File_Upload_Pro' );
