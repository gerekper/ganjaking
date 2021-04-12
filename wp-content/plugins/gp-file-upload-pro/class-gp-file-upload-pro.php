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

		load_plugin_textdomain( 'gp-file-upload-pro', false, basename( dirname( __file__ ) ) . '/languages/' );

		add_action( 'gperk_field_settings', array( $this, 'field_settings_ui' ) );
		add_action( 'gform_editor_js', array( $this, 'field_settings_js' ) );

	}

	public function init_admin() {
		parent::init_admin();

		GravityPerks::enqueue_field_settings();
	}

	public function scripts() {

		$scripts = array(
			array(
				'handle'    => 'gp-file-upload-pro',
				'src'       => $this->get_base_url() . '/js/built/gp-file-upload-pro.js',
				'version'   => $this->_version,
				'deps'      => array( 'gform_gravityforms', 'plupload-all' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( $this, 'should_enqueue_frontend' ),
				),
				'callback'  => array( $this, 'localize_frontend_scripts' ),
			),
		);

		return array_merge( parent::scripts(), $scripts );

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
		if ( is_admin() ) {
			return false;
		}

		if ( empty( $form['fields'] ) ) {
			return false;
		}

		foreach ( $form['fields'] as $field ) {
			if ( rgar( $field, 'type' ) === 'fileupload' && rgar( $field, 'gpfupEnable' ) ) {
				return true;
			}
		}

		return false;
	}


	public function is_localized( $item ) {
		return in_array( $item, $this->_localized );
	}

	public function localize_frontend_scripts( $form ) {

		/**
		 * Form-specific localization/init info
		 */
		if ( ! $this->is_localized( 'gp-file-upload-pro-form-' . $form['id'] ) ) {
			$gpfup_fields = array();

			foreach ( $form['fields'] as $field ) {
				if ( $field['type'] === 'fileupload' && rgar( $field, 'gpfupEnable' ) ) {
					$gpfup_fields[] = array(
						'formId'       => $form['id'],
						'fieldId'      => $field['id'],
						'enableCrop'   => rgar( $field, 'gpfupEnableCrop' ),
						'cropRequired' => rgar( $field, 'gpfupCropRequired' ),
					);
				}
			}

			wp_localize_script( 'gp-file-upload-pro', 'GPFUP_FORM_' . $form['id'], $gpfup_fields );

			$this->_localized[] = 'gp-file-upload-pro-form-' . $form['id'];
		}

		/**
		 * If a script is enqueued in the footer with in_footer, this script will
		 * be called multiple times and we need to guard against localizing multiple times.
		 */
		if ( $this->is_localized( 'gp-file-upload-pro' ) ) {
			return;
		}


		wp_localize_script( 'gp-file-upload-pro', 'GPFUP', array(
			'STRINGS' => array(
				'select_files'    => __( 'select files', 'gp-file-upload-pro' ),
				'drop_files_here' => __( 'Drop files here', 'gp-file-upload-pro' ),
				'or'              => __( 'or', 'gp-file-upload-pro' ),
				'cancel'          => __( 'Cancel', 'gp-file-upload-pro' ),
				'crop'            => __( 'Crop', 'gp-file-upload-pro' ),
			),
		) );

		$this->_localized[] = 'gp-file-upload-pro';

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
		);

		return array_merge( parent::scripts(), $styles );

	}

	## Admin Field Settings
	## SETTINGS ##

	public function field_settings_ui( $position ) {
		?>

		<li class="gpfup-field-setting field_setting" style="display:none;">
			<input type="checkbox" value="1" id="gpfup-enable" onchange="SetFieldProperty( 'gpfupEnable', this.checked );jQuery( '.gpfup-show-if-enabled' ).toggle();" />
			<label for="gpfup-enable" class="inline">
				<?php _e( 'Enable File Upload Pro', 'gp-file-upload-pro' ); ?>
				<?php gform_tooltip( $this->_slug . '_enable' ); ?>
			</label>

			<div id="gpfup-multi-file-notice"
				 class="gp-notice gpfup-hide-if-multiple" style="margin: 15px 0 0;"><?php _e( sprintf( 'Enable Multi-File Upload (on the General tab) to use %s.', $this->_short_title ), 'gp-file-upload-pro' ); ?></div>
		</li>

		<div class="gp-child-settings gpfup-child-settings gpfup-show-if-multiple gpfup-show-if-enabled">
			<li>
				<input type="checkbox" value="1" id="gpfup-enable-crop" onchange="SetFieldProperty( 'gpfupEnableCrop', this.checked );jQuery( '.gpfup-show-if-cropping-enabled' ).toggle();" />
				<label for="gpfup-enable-crop" class="inline">
					<?php _e( 'Enable Cropping', 'gp-file-upload-pro' ); ?>
					<?php gform_tooltip( $this->_slug . '_enable_crop' ); ?>
				</label>
			</li>

			<li class="gpfup-show-if-cropping-enabled">
				<input type="checkbox" value="1" id="gpfup-crop-required" onchange="SetFieldProperty( 'gpfupCropRequired', this.checked );" />
				<label for="gpfup-crop-required" class="inline">
					<?php _e( 'Require Crop', 'gp-file-upload-pro' ); ?>
					<?php gform_tooltip( $this->_slug . '_crop_required' ); ?>
				</label>
			</li>
		</div>

		<?php
	}

	public function field_settings_js() {
		?>
		<script type="text/javascript">
			( function( $ ) {

				$( document ).ready( function(){
					for( fieldType in fieldSettings ) {
						if( fieldSettings.hasOwnProperty( fieldType ) && $.inArray( fieldType, [ 'fileupload' ] ) !== -1 ) {
							fieldSettings[ fieldType ] += ', .gpfup-field-setting';
						}
					}
				} );

				$( document ).bind( 'gform_load_field_settings', function( event, field, form ) {
					if (field.type !== 'fileupload') {
						$( '.gpfup-field-setting, .gpfup-child-settings' ).hide();
						return;
					}

					var multipleFiles = !!field['multipleFiles'];
					var gpfupEnabled = !!field['gpfupEnable'];
					var croppingEnabled = !!field['gpfupEnableCrop'];
					var croppingRequired = !!field['gpfupCropRequired'];

					var $enable = $( '#gpfup-enable' );

					$enable.prop( 'checked', gpfupEnabled );
					$enable.prop( 'disabled', !multipleFiles );

					$('#gpfup-enable-crop').prop( 'checked', croppingEnabled );
					$('#gpfup-crop-required').prop( 'checked', croppingRequired );

					$( '.gpfup-hide-if-multiple' ).toggle( !multipleFiles );
					$( '.gpfup-show-if-multiple' ).toggle( multipleFiles );

					$( '.gpfup-show-if-cropping-enabled' ).toggle( croppingEnabled );
					$( '.gpfup-show-if-enabled' ).toggle( gpfupEnabled );
				} );
			} )( jQuery );
		</script>

		<?php
	}

	public function tooltips( $tooltips ) {
		$tooltips[ $this->_slug . '_enable' ] = sprintf(
			'<h6>%s</h6> %s',
			__( 'File Upload Pro', 'gp-file-upload-pro' ),
			__( 'Greatly improve the uploading experience for your Gravity Forms File Upload fields.', 'gp-file-upload-pro' )
		);

		$tooltips[ $this->_slug . '_enable_crop' ] = sprintf(
			'<h6>%s</h6> %s',
			__( 'Enable Cropping', 'gp-file-upload-pro' ),
			__( 'Allow users to crop their uploaded images by clicking on the image thumbnail.', 'gp-file-upload-pro' )
		);

		$tooltips[ $this->_slug . '_crop_required' ] = sprintf(
			'<h6>%s</h6> %s',
			__( 'Require Crop on Upload', 'gp-file-upload-pro' ),
			__( 'Require that images be cropped on upload by automatically opening the cropper. If the crop is canceled, the files are not uploaded.', 'gp-file-upload-pro' )
		);

		return $tooltips;
	}

}

function gp_file_upload_pro() {
	return GP_File_Upload_Pro::get_instance();
}

GFAddOn::register( 'GP_File_Upload_Pro' );
