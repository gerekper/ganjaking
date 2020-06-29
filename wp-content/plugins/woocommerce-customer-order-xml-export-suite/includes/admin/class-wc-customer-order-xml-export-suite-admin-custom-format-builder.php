<?php
/**
 * WooCommerce Customer/Order XML Export Suite
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order XML Export Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order XML Export Suite for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-customer-order-xml-export-suite/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order XML Export Suite Admin Custom Format Builder Class
 *
 * Dedicated class for admin custom format settings
 *
 * @since 2.0.0
 */
class WC_Customer_Order_XML_Export_Suite_Admin_Custom_Format_Builder {


	/**
	 * Setup admin custom format builder class
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Render a custom field/tag mapping form control when using woocommerce_admin_fields()
		add_action( 'woocommerce_admin_field_wc_customer_order_xml_export_suite_field_mapping', array( $this, 'render_field_mapping' ) );
	}


	/**
	 * Get sections
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			'orders'    => __( 'Orders', 'woocommerce-customer-order-xml-export-suite' ),
			'customers' => __( 'Customers', 'woocommerce-customer-order-xml-export-suite' ),
			'coupons'   => __( 'Coupons', 'woocommerce-customer-order-xml-export-suite' ),
		);

		/**
		 * Allow actors to change the sections for custom format builder
		 *
		 * @since 2.0.0
		 * @param array $sections
		 */
		return apply_filters( 'wc_customer_order_xml_export_suite_custom_format_builder_sections', $sections );
	}


	/**
	 * Returns settings array for use by output/save functions
	 *
	 * @since 2.0.0
	 * @param string $section_id
	 * @return array
	 */
	public static function get_settings( $section_id = null ) {

		$settings = array(

			'orders' => array(

				array(
					'name' => __( 'Format Options', 'woocommerce-customer-order-xml-export-suite' ),
					'type' => 'title',
				),

				array(
					'id'      => 'wc_customer_order_xml_export_suite_orders_custom_format_indent',
					'name'    => __( 'Indent output', 'woocommerce-customer-order-xml-export-suite' ),
					'desc'    => __( 'Enable to indent (pretty-print) XML output', 'woocommerce-customer-order-xml-export-suite' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'class'   => 'js-indent',
				),

				array(
					'id'      => 'wc_customer_order_xml_export_suite_orders_custom_format_include_all_meta',
					'name'    => __( 'Include all meta', 'woocommerce-customer-order-xml-export-suite' ),
					'desc'    => __( 'Enable to include all meta in the export', 'woocommerce-customer-order-xml-export-suite' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'class'   => 'js-include-all-meta',
				),

				array( 'type' => 'sectionend' ),

				array(
					'name' => __( 'Field Mapping', 'woocommerce-customer-order-xml-export-suite' ),
					'type' => 'title',
				),

				array(
					'id'          => 'wc_customer_order_xml_export_suite_orders_custom_format_mapping',
					'type'        => 'wc_customer_order_xml_export_suite_field_mapping',
					'export_type' => 'orders',
					'default'     => self::get_default_field_mapping( 'orders' ),
				),


				array( 'type' => 'sectionend' ),
			),

			'customers' => array(

				array(
					'name' => __( 'Format Options', 'woocommerce-customer-order-xml-export-suite' ),
					'type' => 'title',
				),

				array(
					'id'      => 'wc_customer_order_xml_export_suite_customers_custom_format_indent',
					'name'    => __( 'Indent output', 'woocommerce-customer-order-xml-export-suite' ),
					'desc'    => __( 'Enable to indent (pretty-print) XML output', 'woocommerce-customer-order-xml-export-suite' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'class'   => 'js-indent',
				),

				array(
					'id'      => 'wc_customer_order_xml_export_suite_customers_custom_format_include_all_meta',
					'name'    => __( 'Include all meta', 'woocommerce-customer-order-xml-export-suite' ),
					'desc'    => __( 'Enable to include all meta in the export', 'woocommerce-customer-order-xml-export-suite' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'class'   => 'js-include-all-meta',
				),

				array( 'type' => 'sectionend' ),

				array(
					'name' => __( 'Field Mapping', 'woocommerce-customer-order-xml-export-suite' ),
					'type' => 'title',
				),

				array(
					'id'          => 'wc_customer_order_xml_export_suite_customers_custom_format_mapping',
					'type'        => 'wc_customer_order_xml_export_suite_field_mapping',
					'export_type' => 'customers',
					'default'     => self::get_default_field_mapping( 'customers' ),
				),


				array( 'type' => 'sectionend' ),
			),

		);

		// only display coupons export custom format if enabled
		if ( wc_customer_order_xml_export_suite()->is_coupon_export_enabled() ) {

			$settings['coupons'] = array(
				array(
					'name' => __( 'Format Options', 'woocommerce-customer-order-xml-export-suite' ),
					'type' => 'title',
				),

				array(
					'id'      => 'wc_customer_order_xml_export_suite_coupons_custom_format_indent',
					'name'    => __( 'Indent output', 'woocommerce-customer-order-xml-export-suite' ),
					'desc'    => __( 'Enable to indent (pretty-print) XML output', 'woocommerce-customer-order-xml-export-suite' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'class'   => 'js-indent',
				),

				array(
					'id'      => 'wc_customer_order_xml_export_suite_coupons_custom_format_include_all_meta',
					'name'    => __( 'Include all meta', 'woocommerce-customer-order-xml-export-suite' ),
					'desc'    => __( 'Enable to include all meta in the export', 'woocommerce-customer-order-xml-export-suite' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'class'   => 'js-include-all-meta',
				),

				array( 'type' => 'sectionend' ),

				array(
					'name' => __( 'Field Mapping', 'woocommerce-customer-order-xml-export-suite' ),
					'type' => 'title',
				),

				array(
					'id'          => 'wc_customer_order_xml_export_suite_coupons_custom_format_mapping',
					'type'        => 'wc_customer_order_xml_export_suite_field_mapping',
					'export_type' => 'coupons',
					'default'     => self::get_default_field_mapping( 'coupons' ),
				),

				array( 'type' => 'sectionend' ),
			);
		}

		// return all or section-specific settings
		$found_settings = $section_id && isset( $settings[ $section_id ] ) ? $settings[ $section_id ] : $settings;

		/**
		 * Allow actors to add or remove settings from the XML export custom format settings page.
		 *
		 * @since 2.0.0
		 * @param array $settings an array of settings for the given section
		 * @param string $section_id current section ID
		 */
		return apply_filters( 'wc_customer_order_xml_export_suite_custom_format_settings', $found_settings, $section_id );
	}


	/**
	 * Get default field/tag mapping for the given export type
	 *
	 * @since 2.0.0
	 * @param string $export_type Export type
	 * @return array
	 */
	private static function get_default_field_mapping( $export_type ) {

		$default_format = wc_customer_order_xml_export_suite()->get_formats_instance()->get_format( $export_type, 'default' );
		$default_fields = ! empty( $default_format['fields'] ) ? $default_format['fields'] : array();

		$mapping = array();

		foreach ( $default_fields as $field => $name ) {
			$mapping[] = array( 'source' => $field, 'name' => $name );
		}

		return $mapping;
	}


	/**
	 * Output field/tag mapping form control
	 *
	 * @since 2.0.0
	 * @param array $options
	 */
	public function render_field_mapping( $options ) {

		$mapping = get_option( $options['id'] );

		$mapping['__INDEX__'] = array(
			'name'     => '',
			'source'   => '',
			'meta_key' => '',
		);

		$field_data_source_options = wc_customer_order_xml_export_suite()->get_formats_instance()->get_field_data_options( $options['export_type'] );

		?>
		<tr valign="top">
			<td class="forminp wc-customer-order-xml-export-suite-field-mapping-container" colspan="2">

				<input type="hidden" name="<?php echo esc_attr( $options['id'] ); ?>" value="" />

				<table class="wc-customer-order-xml-export-suite-field-mapping widefat" cellspacing="0">
					<thead>
						<tr>
							<?php
								/**
								 * Allow actors to change the field mapping columns
								 *
								 * @since 2.0.0
								 * @param array $columns
								 * @param array $options custom format builder options
								 */
								$columns = apply_filters( 'wc_customer_order_xml_export_suite_field_mapping_columns', array(
									'sort'            => '',
									// this can be anything but `check-column` due to https://core.trac.wordpress.org/changeset/38703
									'sv-check-column' => '<input type="checkbox" class="js-select-all" />',
									'name'            => esc_html__( 'Tag name', 'woocommerce-customer-order-xml-export-suite' ),
									'source'          => esc_html__( 'Data source', 'woocommerce-customer-order-xml-export-suite' ),
								), $options );

								foreach ( $columns as $column => $label ) {
									echo '<th class="' . esc_attr( $column ) . '">' . $label . '</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $mapping as $mapping_key => $field ) {

							echo '<tr class="field-mapping field-mapping-' . esc_attr( $mapping_key ) . '">';

							foreach ( $columns as $column => $label ) {

								switch ( $column ) {

									case 'sort' :
										echo '<td width="1%" class="sort"></td>';
									break;

									case 'sv-check-column' :
										echo '<td width="1%" class="check-column">
											<input type="checkbox" class="js-select-field" />
										</td>';
									break;

									case 'name' :
										echo '<td class="name">
											<input type="text" name="' . esc_attr( $options['id'] ) . '[' . esc_attr( $mapping_key ) . '][' . esc_attr( $column ) . ']" value="' . esc_attr( $field[ $column ] ) . '" class="js-field-name" />
										</td>';
									break;

									case 'source' :

										$value        = isset( $field[ $column ] ) ? $field[ $column ] : '';
										$meta_key     = 'meta'   === $value && isset( $field['meta_key'] )     ? $field['meta_key']     : '';
										$static_value = 'static' === $value && isset( $field['static_value'] ) ? $field['static_value'] : '';

										// trick WC into thinking the hidden placeholder row select is already enhanced.
										// this will allow us to later trigger enhancing the column when a new row is added,
										// so that event bindings work
										$enhanced = '__INDEX__' === $mapping_key ? 'enhanced' : '';

										$html_field_options = '';

										foreach ( $field_data_source_options as $option ) {
											$html_field_options .= '<option value="' . esc_attr( $option ) . '" ' . selected( $value, $option, false ) . '>' . esc_html( $option ) .  '</option>';
										}

										?>

										<td class="data">

											<select name="<?php echo esc_attr( $options['id'] ); ?>[<?php echo esc_attr( $mapping_key ); ?>][source]" class="js-field-key wc-enhanced-select-nostd <?php echo $enhanced; ?>" data-placeholder="<?php esc_attr_e( 'Select a value', 'woocommerce-customer-order-xml-export-suite' ); ?>">
												<?php echo $html_field_options; ?>
												<option value="meta" <?php selected( 'meta', $value ); ?>><?php esc_html_e( 'Meta field...', 'woocommerce-customer-order-xml-export-suite' ); ?></option>
												<option value="static" <?php selected( 'static', $value ); ?>><?php esc_html_e( 'Static value...', 'woocommerce-customer-order-xml-export-suite' ); ?></option>
											</select>

											<label class="js-field-meta-key-label <?php echo ( 'meta' !== $value ? 'hide' : '' ); ?>">
												<?php esc_html_e( 'Meta key:', 'woocommerce-customer-order-xml-export-suite' ); ?>
												<input type="text" name="<?php echo esc_attr( $options['id'] ); ?>[<?php echo esc_attr( $mapping_key ); ?>][meta_key]" value="<?php echo esc_attr( $meta_key ); ?>" class="js-field-meta-key" />
											</label>

											<label class="js-field-static-value-label <?php echo ( 'static' !== $value ? 'hide' : '' ); ?>">
												<?php esc_html_e( 'Value:', 'woocommerce-customer-order-xml-export-suite' ); ?>
												<input type="text" name="<?php echo esc_attr( $options['id'] ); ?>[<?php echo esc_attr( $mapping_key ); ?>][static_value]" value="<?php echo esc_attr( $static_value ); ?>" class="js-field-static-value" />
											</label>

										</td>

										<?php
									break;

									default :
										/**
										 * Allow actors to provide custom columns for field mapping
										 *
										 * @since 2.0.0
										 * @param array $field
										 * @param array $key
										 * @param array $options
										 */
										do_action( 'wc_customer_order_xml_export_suite_field_mapping_column_' . $column, $field, $mapping_key, $options );
									break;
								}
							}

							echo '</tr>';
						}
						?>
						<tr class="no-field-mappings <?php if ( count( $mapping ) > 1 ) { echo 'hide'; } ?>">
							<td colspan="<?php echo count( $columns ); ?>">
								<?php esc_html_e( 'There are no mapped fields. Click the Add Tag button below to start mapping fields.', 'woocommerce-customer-order-xml-export-suite' ); ?>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="<?php echo count( $columns ); ?>">
								<a class="button js-add-field-mapping" href="#"><?php esc_html_e( 'Add tag', 'woocommerce-customer-order-xml-export-suite' ); ?></a>
								<a class="button js-remove-field-mapping <?php if ( count( $mapping ) < 2 ) { echo 'hide'; } ?>" href="#"><?php esc_html_e( 'Remove selected tag(s)', 'woocommerce-customer-order-xml-export-suite' ); ?></a>
								<a class="button js-load-mapping button-secondary" href="#"><?php esc_html_e( 'Load tag mapping', 'woocommerce-customer-order-xml-export-suite' ); ?></a>
							</td>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
		<?php
	}


	/**
	 * Output sections for custom format builder
	 *
	 * @since 2.0.0
	 */
	public function output_sections() {

		global $current_section;

		$sections = $this->get_sections();

		if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
			return;
		}

		echo '<ul class="subsubsub">';

		$section_ids = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=wc_customer_order_xml_export_suite&tab=custom_formats&section=' . sanitize_title( $id ) ) ) . '" class="' . ( $current_section === $id ? 'current' : '' ) . '">' . esc_html( $label ) . '</a> ' . ( end( $section_ids ) === $id ? '' : '|' ) . ' </li>';
		}

		echo '</ul><br class="clear" />';
	}


	/**
	 * Output the export format definitions as JSON for the given export type
	 *
	 * @since 2.0.0
	 * @param string $export_type
	 */
	public function output_formats_json( $export_type ) {

		$formats = wc_customer_order_xml_export_suite()->get_formats_instance()->get_formats( $export_type );

		if ( empty( $formats ) ) {
			return;
		}

		wc_enqueue_js( 'wc_customer_order_xml_export_suite_admin.export_formats = ' . json_encode( $formats ) . ';' );
	}


	/**
	 * Show custom formats page
	 *
	 * @since 2.0.0
	 */
	public function output() {

		global $current_section;

		// default to orders section
		if ( ! $current_section ) {
			$current_section = 'orders';
		}

		$this->output_sections();

		// render settings fields
		woocommerce_admin_fields( self::get_settings( $current_section ) );

		// output JSON settings for formats (used for loading field/tag mapping from existing formats)
		$this->output_formats_json( $current_section );

		wp_nonce_field( __FILE__ );
		submit_button( __( 'Save', 'woocommerce-customer-order-xml-export-suite' ) );
	}


	/**
	 * Save custom format
	 *
	 * @since 2.0.0
	 */
	public function save() {

		global $current_section;

		// default to orders section
		if ( ! $current_section ) {
			$current_section = 'orders';
		}

		// security check
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], __FILE__ ) ) {

			wp_die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce-customer-order-xml-export-suite' ) );
		}

		woocommerce_update_options( self::get_settings( $current_section ), $this->get_posted_data( $current_section ) );

		wc_customer_order_xml_export_suite()->get_message_handler()->add_message( __( 'Your custom format settings have been saved.', 'woocommerce-customer-order-xml-export-suite' ) );
	}


	/**
	 * Sanitizes field names for XML before saving.
	 *
	 * @since 2.2.1
	 *
	 * @param string $current_section the current section for the custom data, 'orders' or 'customers'.
	 * @return array sanitized posted data
	 */
	private function get_posted_data( $current_section ) {

		$data = $_POST;

		foreach ( $data[ "wc_customer_order_xml_export_suite_{$current_section}_custom_format_mapping" ] as $id => $field ) {
			$data[ "wc_customer_order_xml_export_suite_{$current_section}_custom_format_mapping" ][ $id ]['name'] = sanitize_html_class( $field['name'] );
		}

		return $data;
	}


}
