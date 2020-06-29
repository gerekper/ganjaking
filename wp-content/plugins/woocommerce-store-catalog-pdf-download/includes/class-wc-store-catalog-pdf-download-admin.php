<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Store_Catalog_PDF_Download_Admin {
	private static $_this;

	private $_settings_tab_id = 'scpdfd';

	/**
	 * Init
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		add_filter( 'woocommerce_get_sections_products', array( $this, 'add_settings_section' ) );
		
		add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );

		add_action( 'woocommerce_settings_save_products', array( $this, 'save_settings' ) );

		add_action( 'woocommerce_admin_field_woocommerce_store_catalog_pdf_download_settings', array( $this, 'get_settings' ) );
		
		add_action( 'woocommerce_system_status_report', array( $this, 'render_debug_fields' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );

    	return true;
	}

	/**
	 * Get instance
	 *
	 * @access public
	 * @since 1.0.0
	 * @return instance object
	 */
	public static function get_instance() {
		return self::$_this;
	}

	/**
	 * Load admin scripts
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public function load_scripts() {
		$screen = get_current_screen();

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// load script only on settings products tab
		if ( $screen->base === 'woocommerce_page_wc-settings' && isset( $_GET['tab'] ) && $_GET['tab'] === 'products' ) {
			wp_enqueue_script( 'wc-store-catalog-pdf-download-admin-js', plugins_url( 'assets/js/admin' . $suffix . '.js', dirname( __FILE__ ) ) );			

			wp_enqueue_media();

			$localized_vars = array(
				'modalLogoTitle'  => __( 'Add Logo Image', 'woocommerce-store-catalog-pdf-download' ),
				'buttonLogoText'  => __( 'Upload Image', 'woocommerce-store-catalog-pdf-download' ),
				'modalPDFTitle'   => __( 'Add PDF', 'woocommerce-store-catalog-pdf-download' ),
				'buttonPDFText'   => __( 'Upload PDF', 'woocommerce-store-catalog-pdf-download' ),
				'removeImage'     => __( 'Are you sure you want to remove this image?', 'woocommerce-store-catalog-pdf-download' ),
				'removePDF'       => __( 'Are you sure you want to remove this PDF?', 'woocommerce-store-catalog-pdf-download' ),
				'previewLinkText' => __( 'Custom PDF Preview Link', 'woocommerce-store-catalog-pdf-download' ),
			);
			
			wp_localize_script( 'wc-store-catalog-pdf-download-admin-js', 'wc_store_catalog_pdf_download_admin_local', $localized_vars );

			wp_enqueue_style( 'wc-store-catalog-pdf-download-admin-css', plugins_url( 'assets/css/admin-styles.css', dirname( __FILE__ ) ) );
		}

		return true;
	}

	/**
	 * Add settings section to products tab
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $sections existing sections
	 * @return array $sections modified sections
	 */
	public function add_settings_section( $sections ) {

		$sections['wc_store_catalog_pdf_download'] = __( 'Store Catalog PDF', 'woocommerce-store-catalog-pdf-download' );

		return $sections;
	}

	/**
	 * Add admin settings
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $settings existing settings
	 * @param string $current_section current section name
	 * @return array $settings
	 */
	public function add_settings( $settings, $current_section ) {
		if ( 'wc_store_catalog_pdf_download' === $current_section ) {
			$new_settings = array(
				array(
					'title'    => __( 'Store Catalog PDF', 'woocommerce-store-catalog-pdf-download' ),
					'id'       => 'woocommerce_store_catalog_pdf_download_settings',
					'type'     => 'woocommerce_store_catalog_pdf_download_settings',
				),
			);

			return $new_settings;
		} else {
			return $settings;
		}
	}

	/**
	 * Get admin settings
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $settings existing settings
	 * @param string $current_section current section name
	 * @return array $settings
	 */
	public function get_settings() { 
		global $current_section;

		if ( 'wc_store_catalog_pdf_download' !== $current_section ) {
			return;
		}

		$help_icon   = esc_url( WC()->plugin_url() ) . '/assets/images/help.png';
		
		$custom_pdf  = get_option( 'wc_store_catalog_pdf_download_custom_pdf', '' );
		
		$logo        = get_option( 'wc_store_catalog_pdf_download_logo', '' );
		
		$show_header = get_option( 'wc_store_catalog_pdf_download_show_header', 'no' );
		
		$header_text = get_option( 'wc_store_catalog_pdf_download_header_text', '' );
		
		$show_footer = get_option( 'wc_store_catalog_pdf_download_show_footer', 'no' );
		
		$footer_text = get_option( 'wc_store_catalog_pdf_download_footer_text', '' );
		
		$layout      = get_option( 'wc_store_catalog_pdf_download_layout', 'list' );
		
		$link_label  = get_option( 'wc_store_catalog_pdf_download_link_label', __( 'Download Catalog', 'woocommerce-store-catalog-pdf-download' ) );

		$hide_header_text = '';

		if ( 'no' === $show_header ) {
			$hide_header_text = 'display:none;';
		}

		$hide_footer_text = '';

		if ( 'no' === $show_footer ) {
			$hide_footer_text = 'display:none;';
		}

		// custom pdf
		$hide_remove_pdf_link = '';

		if ( ! empty( $custom_pdf ) ) {
			$custom_pdf_url = wp_get_attachment_url( $custom_pdf );
			
		} else {
			$custom_pdf_url = '';
			$hide_remove_pdf_link = 'display:none;';
		}

		// logo image
		$hide_remove_image_link = '';
		$hide_preview_image = '';

		$logo_image_url = wp_get_attachment_image_src( $logo, 'full' );

		if ( empty( $logo_image_url ) ) {
			$hide_remove_image_link = 'display:none;';
			$hide_preview_image = ' hide';
		}
		?>
		<h3><?php _e( 'Store Catalog PDF', 'woocommerce-store-catalog-pdf-download' ); ?></h3>
		
		<h4><?php _e( 'Ready-made PDF', 'woocommerce-store-catalog-pdf-download' ); ?></h4>
		
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th class="titledesc" scope="row"><?php _e( 'Custom PDF', 'woocommerce-store-catalog-pdf-download' ); ?><img class="help_tip" data-tip="<?php echo wc_sanitize_tooltip( __( 'Upload a ready made PDF of your store.', 'woocommerce-store-catalog-pdf-download' ) ); ?>" src="<?php echo $help_icon; ?>" height="16" width="16" /></th>

					<td class="forminp"><a href="#" class="wc-store-catalog-pdf-download-upload-custom-pdf button"><?php _e( 'Upload Custom PDF', 'woocommerce-store-catalog-pdf-download' ); ?></a><p class="description"><?php _e( 'Optional: This is to be used with the shortcode to display a ready made PDF', 'woocommerce-store-catalog-pdf-download' ); ?></p>
						
						<input type="hidden" name="wc_store_catalog_pdf_download_custom_pdf" value="<?php echo esc_attr( $custom_pdf ); ?>" id="wc_store_catalog_pdf_download_custom_pdf" />
						
						<br />

						<?php if ( $custom_pdf ) { ?>
							<a href="<?php echo esc_url( $custom_pdf_url ); ?>" target="_blank" class="custom-pdf-preview"><?php _e( 'Custom PDF Preview Link', 'woocommerce-store-catalog-pdf-download' ); ?></a>

						<?php } else { ?>
							<a href="#" target="_blank" class="custom-pdf-preview"></a>

						<?php } ?>

						<a href="#" class="remove-pdf dashicons dashicons-no" style="<?php echo esc_attr( $hide_remove_pdf_link ); ?>" title="<?php esc_attr_e( 'Click to remove PDF', 'woocommerce-store-catalog-pdf-download' ); ?>"></a>
					</td>
				</tr>
			</tbody>
		</table>

		<h4><?php _e( 'PDF Generator', 'woocommerce-store-catalog-pdf-download' ); ?></h4>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th class="titledesc" scope="row"><?php _e( 'Company Logo', 'woocommerce-store-catalog-pdf-download' ); ?><img class="help_tip" data-tip="<?php echo wc_sanitize_tooltip( __( 'Upload a logo to be shown at the top of the PDF.', 'woocommerce-store-catalog-pdf-download' ) ); ?>" src="<?php echo $help_icon; ?>" height="16" width="16" /></th>

					<td class="forminp"><a href="#" class="wc-store-catalog-pdf-download-upload-logo button"><?php _e( 'Upload Logo', 'woocommerce-store-catalog-pdf-download' ); ?></a>
						
						<input type="hidden" name="wc_store_catalog_pdf_download_logo" value="<?php echo esc_attr( $logo ); ?>" id="wc_store_catalog_pdf_download_logo" />
						
						<br />
						
						<?php if ( is_array( $logo_image_url ) && ! empty( $logo_image_url ) ) { ?>
							<img src="<?php echo esc_url( $logo_image_url[0] ); ?>" class="logo-preview-image" />

						<?php } else { ?>
							<img src="" class="logo-preview-image<?php echo esc_attr( $hide_preview_image ); ?>" />

						<?php } ?>
						
						<a href="#" class="remove-image dashicons dashicons-no" style="<?php echo esc_attr( $hide_remove_image_link ); ?>" title="<?php esc_attr_e( 'Click to remove image', 'woocommerce-store-catalog-pdf-download' ); ?>"></a>
					</td>
				</tr>

				<tr valign="top">
					<th class="titledesc" scope="row"><?php _e( 'PDF Header', 'woocommerce-store-catalog-pdf-download' ); ?></th>
					<td class="forminp">
						<label for="show-header"><input type="checkbox" id="show-header" name="wc_store_catalog_pdf_download_show_header" <?php checked( $show_header, 'yes' ); ?> /> <?php _e( 'Show Header', 'woocommerce-store-catalog-pdf-download' ); ?></label>
						<p class="description"><?php _e( 'Enabling this option will show a header information at the top of the PDF such as any intro verbiage.', 'woocommerce-store-catalog-pdf-download' ); ?></p>
					</td>
				</tr>

				<tr valign="top" class="header-text-row" style="<?php echo esc_attr( $hide_header_text ); ?>">
					<th class="titledesc" scope="row"><?php _e( 'PDF Header Text', 'woocommerce-store-catalog-pdf-download' ); ?></th>
					<td class="forminp">
						<p><textarea name="wc_store_catalog_pdf_download_header_text" style="width:100%;height:200px;"><?php echo wp_kses_post( $header_text ); ?></textarea></p>
					</td>
				</tr>

				<tr valign="top">
					<th class="titledesc" scope="row"><?php _e( 'PDF Footer', 'woocommerce-store-catalog-pdf-download' ); ?></th>
					<td class="forminp">
						<label for="show-footer"><input type="checkbox" id="show-footer" name="wc_store_catalog_pdf_download_show_footer" <?php checked( $show_footer, 'yes' ); ?> /> <?php _e( 'Show Footer', 'woocommerce-store-catalog-pdf-download' ); ?></label>
						<p class="description"><?php _e( 'Enabling this option will show a footer information at the bottom of the PDF such as foot notes or any outro verbiage.', 'woocommerce-store-catalog-pdf-download' ); ?></p>
					</td>
				</tr>

				<tr valign="top" class="footer-text-row" style="<?php echo esc_attr( $hide_footer_text ); ?>">
					<th class="titledesc" scope="row"><?php _e( 'PDF Footer Text', 'woocommerce-store-catalog-pdf-download' ); ?></th>
					<td class="forminp">
						<p><textarea name="wc_store_catalog_pdf_download_footer_text" style="width:100%;height:200px;"><?php echo wp_kses_post( $footer_text ); ?></textarea></p>
					</td>
				</tr>

				<tr valign="top">
					<th class="titledesc" scope="row"><?php _e( 'PDF Layout Format', 'woocommerce-store-catalog-pdf-download' ); ?><img class="help_tip" data-tip="<?php echo wc_sanitize_tooltip( __( 'Set how you want the PDF layout format to display for category products.', 'woocommerce-store-catalog-pdf-download' ) ); ?>" src="<?php echo $help_icon; ?>" height="16" width="16" /></th>
					<td class="forminp">
						<select name="wc_store_catalog_pdf_download_layout" class="wc-enhanced-select">
							<option value="list" <?php selected( $layout, 'list' ); ?>><?php _e( 'List Format', 'woocommerce-store-catalog-pdf-download' ); ?></option>
							<option value="grid" <?php selected( $layout, 'grid' ); ?>><?php _e( 'Grid Format', 'woocommerce-store-catalog-pdf-download' ); ?></option>
						</select>
					</td>
				</tr>

				<tr valign="top">
					<th class="titledesc" scope="row"><?php _e( 'Download Link Label', 'woocommerce-store-catalog-pdf-download' ); ?><img class="help_tip" data-tip="<?php echo wc_sanitize_tooltip( __( 'Set the text you want to display next to the download icon.', 'woocommerce-store-catalog-pdf-download' ) ); ?>" src="<?php echo $help_icon; ?>" height="16" width="16" /></th>
					<td class="forminp">
						<input type="text" name="wc_store_catalog_pdf_download_link_label" value="<?php echo esc_attr( $link_label ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>

		<?php

		return true;
	}

	/**
	 * Save admin settings
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool true
	 */
	public function save_settings() {
		global $current_section;

		if ( 'wc_store_catalog_pdf_download' !== $current_section ) {
			return;
		}
				
		if ( isset( $_POST['wc_store_catalog_pdf_download_custom_pdf'] ) ) {
			$custom_pdf = sanitize_text_field( $_POST['wc_store_catalog_pdf_download_custom_pdf'] );
			update_option( 'wc_store_catalog_pdf_download_custom_pdf', $custom_pdf );
		}

		if ( isset( $_POST['wc_store_catalog_pdf_download_logo'] ) ) {
			$logo = sanitize_text_field( $_POST['wc_store_catalog_pdf_download_logo'] );
			update_option( 'wc_store_catalog_pdf_download_logo', $logo );
		}

		if ( isset( $_POST['wc_store_catalog_pdf_download_show_header'] ) ) {
			update_option( 'wc_store_catalog_pdf_download_show_header', 'yes' );

		} else {
			update_option( 'wc_store_catalog_pdf_download_show_header', 'no' );
		}	

		if ( isset( $_POST['wc_store_catalog_pdf_download_header_text'] ) ) {
			$header_text = wp_kses_post( $_POST['wc_store_catalog_pdf_download_header_text'] );
			update_option( 'wc_store_catalog_pdf_download_header_text', $header_text );
		}

		if ( isset( $_POST['wc_store_catalog_pdf_download_show_footer'] ) ) {
			update_option( 'wc_store_catalog_pdf_download_show_footer', 'yes' );

		} else {
			update_option( 'wc_store_catalog_pdf_download_show_footer', 'no' );
		}	

		if ( isset( $_POST['wc_store_catalog_pdf_download_footer_text'] ) ) {
			$footer_text = wp_kses_post( $_POST['wc_store_catalog_pdf_download_footer_text'] );
			update_option( 'wc_store_catalog_pdf_download_footer_text', $footer_text );
		}

		if ( isset( $_POST['wc_store_catalog_pdf_download_layout'] ) ) {
			$layout = sanitize_text_field( $_POST['wc_store_catalog_pdf_download_layout'] );
			update_option( 'wc_store_catalog_pdf_download_layout', $layout );
		}

		if ( isset( $_POST['wc_store_catalog_pdf_download_link_label'] ) ) {
			$link_label = sanitize_text_field( $_POST['wc_store_catalog_pdf_download_link_label'] );
			update_option( 'wc_store_catalog_pdf_download_link_label', $link_label );
		}

		return true;

	}

	/**
	 * Renders the debug fields
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public function render_debug_fields() {
	?>	
		<table class="wc_status_table widefat" cellspacing="0" id="status">
			<thead>
				<tr>
					<th colspan="3" data-export-label="Store Catalog PDF Download">Store Catalog PDF Download</th>
				</tr>

				<tr>
					<th></th>
					<th><?php _e( 'Required', 'woocommerce-store-catalog-pdf-download' ); ?></th>
					<th><?php _e( 'Present', 'woocommerce-store-catalog-pdf-download' ); ?></th>
				</tr>
			</thead>
			
			<tbody>
				<tr>
					<td data-export-label="System Temp Directory"><?php _e( 'System Temp Directory', 'woocommerce-store-catalog-pdf-download' ); ?></td>
					<td><?php _e( 'Yes', 'woocommerce-store-catalog-pdf-download' ); ?></td>
					<td><?php echo '( ' . sys_get_temp_dir() . ' )'; ?></td>
				</tr>

				<tr>
					<?php
						$upload_dir = wp_upload_dir(); 
			    		$pdf_path = $upload_dir['basedir'] . '/woocommerce-store-catalog-pdf-download/';
					?>
					<td data-export-label="Upload Directory"><?php _e( 'Upload Directory', 'woocommerce-store-catalog-pdf-download' ); ?></td>
					<td><?php _e( 'Yes', 'woocommerce-store-catalog-pdf-download' ); ?></td>
					<td><?php echo ( file_exists( $pdf_path ) && is_writable( $pdf_path ) ) ? '<span style="color:green">' . __( 'Yes', 'woocommerce-store-catalog-pdf-download' ) . '</span>' : '<strong style="color:red;">' . __( 'No', 'woocommerce-store-catalog-pdf-download' ) . '</strong>'; ?><?php echo ' ( ' . $pdf_path . ' )'; ?></td>
				</tr>

				<tr>
					<td data-export-label="DOMDocument extension">DOMDocument extension</td>
					<td><?php _e( 'Yes', 'woocommerce-store-catalog-pdf-download' ); ?></td>
					<td><?php echo extension_loaded( 'DOM' ) ? '<span style="color:green;">' . __( 'Yes', 'woocommerce-store-catalog-pdf-download' ) . '</span>' : '<strong style="color:red;">' . __( 'No', 'woocommerce-store-catalog-pdf-download' ) . '</strong>'; ?> ( <?php echo phpversion( 'DOM' ); ?> )</td>
				</tr>

				<tr>
					<td data-export-label="GD Library">GD Library</td>
					<td><?php _e( 'Yes', 'woocommerce-store-catalog-pdf-download' ); ?></td>
					<td><?php echo ( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ) ? '<span style="color:green;">' . __( 'Yes', 'woocommerce-store-catalog-pdf-download' ) . '</span>' : '<strong style="color:red;">' . __( 'No', 'woocommerce-store-catalog-pdf-download' ) . '</strong>'; ?></td>
				</tr>

				<tr>
					<td data-export-label="MBString">MB String</td>
					<td><?php _e( 'Yes', 'woocommerce-store-catalog-pdf-download' ); ?></td>
					<td><?php echo ( extension_loaded( 'mbstring' ) ) ? '<span style="color:green;">' . __( 'Yes', 'woocommerce-store-catalog-pdf-download' ) . '</span>' : '<strong style="color:red;">' . __( 'No', 'woocommerce-store-catalog-pdf-download' ) . '</strong>'; ?></td>
				</tr>

				<tr>
					<td data-export-label="URL fopen">Allow URL fopen</td>
					<td><?php _e( 'Yes', 'woocommerce-store-catalog-pdf-download' ); ?></td>
					<td><?php echo ( ini_get( 'allow_url_fopen' ) ) ? '<span style="color:green;">' . __( 'Yes', 'woocommerce-store-catalog-pdf-download' ) . '</span>' : '<strong style="color:red;">' . __( 'No', 'woocommerce-store-catalog-pdf-download' ) . '</strong>'; ?></td>
				</tr>

				<tr>
					<td data-export-label="Template Overrides"><?php _e( 'Template Overrides', 'woocommerce-store-catalog-pdf-download' ); ?></td>
					<td colspan="2">
						<?php $theme = wp_get_theme(); ?>
						<?php if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-product-meta-html.php' ) ) {
							echo strtolower( str_replace( ' ', '', $theme->name ) ) . '/woocommerce-store-catalog-pdf-download/pdf-layout-product-meta-html.php' . '<br />';
						} ?>

						<?php if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-list-html.php' ) ) {
							echo strtolower( str_replace( ' ', '', $theme->name ) ) . '/woocommerce-store-catalog-pdf-download/pdf-layout-list-html.php' . '<br />';
						} ?>

						<?php if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-grid-html.php' ) ) {
							echo strtolower( str_replace( ' ', '', $theme->name ) ) . '/woocommerce-store-catalog-pdf-download/pdf-layout-grid-html.php' . '<br />';

						} ?>

						<?php if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-single-html.php' ) ) {
							echo strtolower( str_replace( ' ', '', $theme->name ) ) . '/woocommerce-store-catalog-pdf-download/pdf-layout-single-html.php' . '<br />';
						} ?>

						<?php if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-header-html.php' ) ) {
							echo strtolower( str_replace( ' ', '', $theme->name ) ) . '/woocommerce-store-catalog-pdf-download/pdf-layout-header-html.php' . '<br />';
						} ?>

						<?php if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-footer-html.php' ) ) {
							echo strtolower( str_replace( ' ', '', $theme->name ) ) . '/woocommerce-store-catalog-pdf-download/pdf-layout-footer-html.php' . '<br />';
						} ?>
					</td>
				</tr>
			</tbody>
		</table>
	<?php

	return true;
	}	
}

new WC_Store_Catalog_PDF_Download_Admin();
