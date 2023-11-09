<?php
/**
 * Integration with Gravity Forms: Gravity_Forms class
 *
 * This integration will automatically compress images on Gravity Forms upload.
 *
 * @since 3.9.10
 *
 * @package Smush\Core\Integrations
 */

namespace Smush\Core\Integrations;

use GFFormsModel;
use Smush\Core\Core;
use Smush\Core\Helper;
use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Gravity_Forms for Gravity Forms integration.
 *
 * This integration will automatically compress images on Gravity Forms upload.
 *
 * @since 3.9.10
 *
 * @see Abstract_Integration
 */
class Gravity_Forms extends Abstract_Integration {

	/**
	 * Gravity_Forms constructor.
	 *
	 * @since 3.9.10
	 */
	public function __construct() {
		$this->module  = 'gform';
		$this->class   = 'free';
		$this->enabled = defined( 'GF_SUPPORTED_WP_VERSION' ) && class_exists( 'GFForms' );

		parent::__construct();

		// Hook at the end of setting row to output an error div.
		add_action( 'smush_setting_column_right_inside', array( $this, 'additional_notice' ) );

		// Return if Gravity Form integration or auto compression is not enabled.
		if ( ! $this->enabled || ! $this->settings->get( 'gform' ) || ! $this->settings->get( 'auto' ) ) {
			return;
		}

		// Track gravity form submission and validate if there is any image uploaded in Image or File Upload fields.
		add_action( 'gform_after_submission', array( $this, 'smush_gform_after_submission' ), 10, 2 );
	}

	/*
	 * ************************************
	 *
	 * OVERWRITE PARENT CLASS FUNCTIONALITY
	 */

	/**
	 * Filters the setting variable to add Gravity Form setting title and description
	 *
	 * @since 3.9.10
	 *
	 * @param array $settings Settings.
	 *
	 * @return array
	 */
	public function register( $settings ) {
		$settings[ $this->module ] = array(
			'label'       => esc_html__( 'Enable Gravity Forms integration', 'wp-smushit' ),
			'short_label' => esc_html__( 'Gravity Forms', 'wp-smushit' ),
			'desc'        => esc_html__( 'Allow compressing images uploaded with Gravity Forms.', 'wp-smushit' ),
		);

		return $settings;
	}

	/**
	 * Show additional notice if the required plugins are not installed.
	 *
	 * @since 3.9.10
	 *
	 * @param string $name Setting name.
	 */
	public function additional_notice( $name ) {
		if ( $this->module === $name && ! $this->enabled ) {
			?>
			<div class="sui-toggle-content">
				<div class="sui-notice">
					<div class="sui-notice-content">
						<div class="sui-notice-message">
							<i class="sui-notice-icon sui-icon-info" aria-hidden="true"></i>
							<p><?php esc_html_e( 'To use this feature you need be using Gravity Forms.', 'wp-smushit' ); ?></p>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Processing automatic image smush on Gravity Forms upload.
	 *
	 * @since 3.9.10
	 *
	 * @param Object $entry Entry Object.
	 * @param Object $form  Form Object.
	 */
	public function smush_gform_after_submission( $entry, $form ) {
		$fields = $form['fields'];

		foreach ( $fields as $field ) {
			if ( 'fileupload' !== $field->type && 'post_image' !== $field->type ) {
				continue;
			}

			if ( ! function_exists( 'rgar' ) ) {
				continue;
			}

			$uploaded_files = rgar( $entry, $field->id );
			$uploaded_files = $this->smush_parse_files( $uploaded_files, $field );

			if ( ! is_array( $uploaded_files ) || empty( $uploaded_files ) ) {
				continue;
			}

			foreach ( $uploaded_files as $_file ) {
				$dir = $this->get_gform_upload_dir( $form['id'] );

				if ( ! $dir || ! isset( $dir['url'] ) || ! isset( $dir['path'] ) ) {
					continue;
				}

				$file = str_replace( $dir['url'], $dir['path'], $_file );

				// Get mime type from file path.
				$mime = Helper::get_mime_type( $file );

				// If image file not exist or image type not supported.
				if ( ! file_exists( $file ) || ! in_array( $mime, Core::$mime_types, true ) ) {
					continue;
				}

				WP_Smush::get_instance()->core()->mod->smush->do_smushit( $file );
			}
		}
	}

	/**
	 * Get upload directory url and path.
	 *
	 * @since 3.9.10
	 *
	 * @param int $form_id Form ID.
	 * @return bool|array
	 */
	public function get_gform_upload_dir( $form_id ) {
		if ( ! class_exists( 'GFFormsModel' ) ) {
			return false;
		}

		$dir         = GFFormsModel::get_file_upload_path( $form_id, 'PLACEHOLDER' );
		$dir['path'] = dirname( $dir['path'] );
		$dir['url']  = dirname( $dir['url'] );

		return $dir;
	}

	/**
	 * Parsing uploaded files.
	 *
	 * @since 3.9.10
	 *
	 * @param mixed  $files  File path.
	 * @param Object $field  Form field object.
	 *
	 * @return array
	 */
	public function smush_parse_files( $files, $field ) {
		if ( empty( $files ) ) {
			return array();
		}

		if ( $this->smush_is_json( $files ) ) {
			$files = json_decode( $files );
		} elseif ( 'post_image' === $field->get_input_type() ) {
			$file_bits = explode( '|:|', $files );
			$files     = array( $file_bits[0] );
		} else {
			$files = array( $files );
		}

		return $files;
	}

	/**
	 * Check entry files in JSON format.
	 *
	 * @since 3.9.10
	 *
	 * @param String $string File string.
	 *
	 * @return bool
	 */
	public function smush_is_json( $string ) {
		// Duplicate contents of GFCommon::is_json() here to supports versions of GF older than GF 2.5.
		if ( is_string( $string ) && in_array( substr( $string, 0, 1 ), array( '{', '[' ) ) && is_array( json_decode( $string, ARRAY_A ) ) ) {
			return true;
		}

		return false;
	}

}