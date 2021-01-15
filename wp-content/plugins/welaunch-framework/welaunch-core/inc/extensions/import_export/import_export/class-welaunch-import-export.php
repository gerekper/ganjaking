<?php // phpcs:disable WordPress.WhiteSpace.PrecisionAlignment.Found
/**
 * Import & Export for Option Panel
 *
 * @package     weLaunchFramework
 * @author      Dovy Paukstys
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'weLaunch_Import_Export', false ) ) {

	/**
	 * Main weLaunch_import_export class
	 *
	 * @since       1.0.0
	 */
	class weLaunch_Import_Export extends weLaunch_Field {


		/**
		 * weLaunch_Import_Export constructor.
		 *
		 * @param array  $field  Field array.
		 * @param string $value  Value array.
		 * @param object $parent weLaunchFramework object.
		 *
		 * @throws ReflectionException .
		 */
		public function __construct( $field = array(), $value = '', $parent ) {
			parent::__construct( $field, $value, $parent );

			$this->is_field = $this->parent->extensions['import_export']->is_field;
		}

		/**
		 * Set field defaults.
		 */
		public function set_defaults() {
			// Set default args for this field to avoid bad indexes. Change this to anything you use.
			$defaults = array(
				'options'          => array(),
				'stylesheet'       => '',
				'output'           => true,
				'enqueue'          => true,
				'enqueue_frontend' => true,
			);

			$this->field = wp_parse_args( $this->field, $defaults );
		}

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @return      void
		 * @since       1.0.0
		 * @access      public
		 */
		public function render() {
			$secret = md5( md5( weLaunch_Functions_Ex::hash_key() ) . '-' . $this->parent->args['opt_name'] );

			// No errors please.
			$defaults = array(
				'full_width' => true,
				'overflow'   => 'inherit',
			);

			$this->field = wp_parse_args( $this->field, $defaults );

			$do_close = false;

			$id = $this->parent->args['opt_name'] . '-' . $this->field['id'];
			?>
			<h4><?php esc_html_e( 'Import Options', 'welaunch-framework' ); ?></h4>
			<p>
				<a
					href="javascript:void(0);"
					id="welaunch-import-code-button"
					class="button-secondary">
					<?php esc_html_e( 'Import from Clipboard', 'welaunch-framework' ); ?>
				</a>

				<a
					href="javascript:void(0);"
					id="welaunch-import-link-button"
					class="button-secondary">
					<?php esc_html_e( 'Import from URL', 'welaunch-framework' ); ?>
				</a>

				<a
					href="#"
					id="welaunch-import-upload"
					class="button-secondary">
					<?php esc_html_e( 'Upload file', 'welaunch-framework' ); ?><span></span>
				</a>
				<input type="file" id="welaunch-import-upload-file" size="50">
			</p>
			<div id="welaunch-import-code-wrapper">
				<p class="description" id="import-code-description">
					<?php // phpcs:ignore WordPress.NamingConventions.ValidHookName ?>
					<?php echo esc_html( apply_filters( 'welaunch-import-file-description', esc_html__( 'Paste your clipboard data here.', 'welaunch-framework' ) ) ); ?>
				</p>
				<textarea
					id="import-code-value"
					name="<?php echo esc_attr( $this->parent->args['opt_name'] ); ?>[import_code]"
					class="large-text no-update" rows="3"></textarea>
			</div>
			<div id="welaunch-import-link-wrapper">
				<p class="description" id="import-link-description">
					<?php // phpcs:ignore WordPress.NamingConventions.ValidHookName ?>
					<?php echo esc_html( apply_filters( 'welaunch-import-link-description', esc_html__( 'Input the URL to another sites options set and hit Import to load the options from that site.', 'welaunch-framework' ) ) ); ?>
				</p>
				<input
					class="large-text no-update"
					id="import-link-value"
					name="<?php echo esc_attr( $this->parent->args['opt_name'] ); ?>[import_link]"
					rows="2"/>
			</div>
			<p id="welaunch-import-action">
				<input
					type="submit"
					id="welaunch-import"
					name="import"
					class="button-primary"
					value="<?php esc_html_e( 'Import', 'welaunch-framework' ); ?>">&nbsp;&nbsp;
				<span>
					<?php // phpcs:ignore WordPress.NamingConventions.ValidHookName ?>
					<?php echo esc_html( apply_filters( 'welaunch-import-warning', esc_html__( 'WARNING! This will overwrite all existing option values, please proceed with caution!', 'welaunch-framework' ) ) ); ?>
				</span>
			</p>
			<div class="hr">
				<div class="inner">
					<span>&nbsp;</span>
				</div>
			</div>
			<h4><?php esc_html_e( 'Export Options', 'welaunch-framework' ); ?></h4>
			<div class="welaunch-section-desc">
				<p class="description">
					<?php // phpcs:ignore WordPress.NamingConventions.ValidHookName ?>
					<?php echo esc_html( apply_filters( 'welaunch-backup-description', esc_html__( 'Here you can copy/download your current option settings. Keep this safe as you can use it as a backup should anything go wrong, or you can use it to restore your settings on this site (or any other site).', 'welaunch-framework' ) ) ); ?>
				</p>
			</div>
			<?php $link = admin_url( 'admin-ajax.php?action=welaunch_download_options-' . $this->parent->args['opt_name'] . '&secret=' . $secret ); ?>
			<p>
				<button id="welaunch-export-code-copy" class="button-secondary"
						data-secret="<?php echo esc_attr( $secret ); ?>"
						data-copy="<?php esc_attr_e( 'Copy to Clipboard', 'welaunch-framework' ); ?>"
						data-copied="<?php esc_attr_e( 'Copied!', 'welaunch-framework' ); ?>">
					<?php esc_html_e( 'Copy to Clipboard', 'welaunch-framework' ); ?>
				</button>
				<a href="<?php echo esc_url( $link ); ?>" id="welaunch-export-code-dl" class="button-primary">
					<?php esc_html_e( 'Export File', 'welaunch-framework' ); ?>
				</a>
				<a href="javascript:void(0);" id="welaunch-export-link" class="button-secondary"
				   data-copy="<?php esc_attr_e( 'Copy Export URL', 'welaunch-framework' ); ?>"
				   data-copied="<?php esc_attr_e( 'Copied!', 'welaunch-framework' ); ?>"
				   data-url="<?php echo esc_url( $link ); ?>">
					<?php esc_html_e( 'Copy Export URL', 'welaunch-framework' ); ?>
				</a>
			</p>
			<p></p>
			<textarea class="large-text no-update" id="welaunch-export-code" rows="1"></textarea>
			<?php
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @return      void
		 * @since       1.0.0
		 * @access      public
		 */
		public function enqueue() {
			wp_enqueue_script(
				'welaunch-extension-import-export-js',
				$this->url . 'welaunch-import-export' . weLaunch_Functions::is_min() . '.js',
				array(
					'jquery',
					'welaunch-js',
				),
				weLaunch_Extension_Import_Export::$version,
				true
			);

			wp_enqueue_style( 'welaunch-import-export', $this->url . 'welaunch-import-export.css', array(), weLaunch_Extension_Import_Export::$version, 'all' );
		}
	}
}
