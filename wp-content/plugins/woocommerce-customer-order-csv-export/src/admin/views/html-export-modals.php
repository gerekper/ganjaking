<?php
/**
 * WooCommerce Customer/Order/Coupon Export
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Export modal templates
 *
 * @since 4.0.0
 * @version 4.0.0
 */

global $current_tab, $output_type, $export_type;

?>

<script type="text/template" id="tmpl-wc-customer-order-csv-export-modal">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1>{{{data.title}}}</h1>
					<# if ( ! data.batch_enabled ) { #>
						<button class="modal-close modal-close-link dashicons dashicons-no-alt">
							<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce-customer-order-csv-export' ); ?></span>
						</button>
					<# } #>
				</header>
				<article>{{{data.body}}}</article>
				<footer>
					<div class="inner">
						<# if ( data.cancel ) { #>
						<button id="btn-cancel" class="button button-large modal-close">{{{data.cancel}}}</button>
						<# } #>
						<# if ( data.action ) { #>
							<button id="btn-ok" class="button button-large {{{data.button_class}}}">{{{data.action}}}</button>
						<# } #>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>

<script type="text/template" id="tmpl-wc-customer-order-csv-export-modal-body-export-started">

	<# if ( data.batch_enabled ) { #>
		<section>
			<progress class="wc-customer-order-csv-export-progress" max="100" value="0"></progress>
		</section>
	<# } #>

	<p>
		<span class="dashicons dashicons-update wc-customer-order-csv-export-dashicons-spin"></span>
		<?php esc_html_e( 'Your data is being exported now.', 'woocommerce-customer-order-csv-export' ); ?>
		<# if ( 'download' === data.export_method ) { #>
		<?php esc_html_e(' When the export is complete, the download will start automatically.', 'woocommerce-customer-order-csv-export' ); ?>
		<# } #>
	</p>

	<# if ( data.batch_enabled ) { #>
		<p class="batch-warning">
			<?php esc_html_e(' Do not navigate away from this page or use the back button until the export is complete.', 'woocommerce-customer-order-csv-export' ); ?>
		</p>
	<# } #>

	<p>
		<?php
			/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
			printf( esc_html__( 'When completed, the exported file will also be available under %1$sExport List%2$s for the next 14 days.', 'woocommerce-customer-order-csv-export' ), '<a href="' . admin_url( 'admin.php?page=wc_customer_order_csv_export&tab=export_list' ) . '">', '</a>' );
		?>
		<# if ( ! data.batch_enabled ) { #>
				<?php /* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
				esc_html_e( ' You can safely leave this screen and return to the Export List later.', 'woocommerce-customer-order-csv-export' ); ?>
		<# } #>
	</p>
</script>

<script type="text/template" id="tmpl-wc-customer-order-csv-export-modal-body-export-completed">
	<p><span class="dashicons dashicons-yes"></span>
		<?php esc_html_e( 'Your export is ready!', 'woocommerce-customer-order-csv-export' ); ?>
		<# if ( 'download' === data.export_method ) { #>
		<?php
			/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
			printf( esc_html__( '%1$sClick here%2$s if your download does not start automatically. ', 'woocommerce-customer-order-csv-export' ), '<a class="js-export-download-link" href="{{{data.download_url}}}">', '</a>' );
		?>
		<# } #>
		<?php
			/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
			printf( esc_html__( 'The exported file will be available under %1$sExport List%2$s for the next 14 days.', 'woocommerce-customer-order-csv-export' ), '<a href="' . admin_url( 'admin.php?page=wc_customer_order_csv_export&tab=export_list' ) . '">', '</a>' );
		?>
		<# if ( data.mark_as_exported ) { #>
		<?php
			esc_html_e( ' Please note it may take a few minutes for all items to be marked as exported.', 'woocommerce-customer-order-csv-export' );
		?>
		<# } #>
	</p>
</script>

<script type="text/template" id="tmpl-wc-customer-order-export-modal-body-automated-action">

	<?php

	$automations = \SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory::get_automations( [
		'export_type' => $export_type,
	] );

	foreach ( $automations as $key => $automation ) {

		if ( 'local' === $automation->get_method_type() ) {
			unset( $automations[ $key ] );
		}
	}

	$add_url = SkyVerge\WooCommerce\CSV_Export\Admin\Automations::get_automation_add_url();

	if ( ! empty( $automations ) ) : ?>

	<form>

		<p class="description"><?php esc_html_e( 'Choose an automated export to use that transfer method for this export. Only exports with FTP, HTTP post, or email transfer methods can be selected.', 'woocommerce-customer-order-csv-export' ); ?></p>

		<p>
			<label for="js-automation-action-select"><strong><?php esc_html_e( 'Transfer method', 'woocommerce-customer-order-csv-export' ); ?></strong></label>
			<select id="js-automation-action-select" name="automation_action_id">
				<?php foreach ( $automations as $automation ) : ?>
					<option value="<?php echo esc_attr( $automation->get_id() ); ?>"><?php echo esc_html( $automation->get_name() ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="description"><?php printf(
			/* translators: %1$s - <a> tag, %2$s - </a> tag */
			esc_html__( 'Or %1$screate new automated export.%2$s', 'woocommerce-customer-order-csv-export' ),
			'<a href="' . esc_url( $add_url ) . '">', '</a>'
		); ?></p>

	</form>

	<?php else : ?>

		<p>
			<?php printf(
				/* translators: %1$s - export type, such as orders or customers, %2$s - <a> tag, %3$s - </a> tag */
				esc_html__( 'This export method uses the transfer methods setup in automated exports, but there are no automated %1$s exports configured with an eligible transfer method (FTP, HTTP POST, or email). %2$sCreate a new automated export now &raquo;%3$s', 'woocommerce-customer-order-csv-export' ),
				$export_type,
				'<a href="' . esc_url( $add_url ) . '">', '</a>'
			); ?>
		</p>

	<?php endif; ?>

</script>

<script type="text/template" id="tmpl-wc-customer-order-export-modal-body-download-action">

	<?php if ( ! empty( $export_type ) ) :

		$user_id = get_current_user_id();

		// get saved settings for manual exports of this type for each output type
		$csv_saved_export_format   = $user_id ? get_user_meta( $user_id, "_wc_customer_order_export_csv_{$export_type}_manual_export_format", true ) : '';
		$xml_saved_export_format   = $user_id ? get_user_meta( $user_id, "_wc_customer_order_export_xml_{$export_type}_manual_export_format", true ) : '';
		$csv_saved_filename        = $user_id ? get_user_meta( $user_id, "_wc_customer_order_export_csv_{$export_type}_manual_export_filename", true ) : '';
		$xml_saved_filename        = $user_id ? get_user_meta( $user_id, "_wc_customer_order_export_xml_{$export_type}_manual_export_filename", true ) : '';
		$csv_saved_add_order_notes = $user_id ? get_user_meta( $user_id, "_wc_customer_order_export_csv_manual_export_add_order_notes", true ) : '';
		$xml_saved_add_order_notes = $user_id ? get_user_meta( $user_id, "_wc_customer_order_export_xml_manual_export_add_order_notes", true ) : '';

		// get saved general setting for manual exports
		$saved_batch_enabled = $user_id ? get_user_meta( $user_id, "_wc_customer_order_export_manual_export_batch_enabled", true ) : '';

		$use_legacy_formats        = 'yes' === get_option( 'wc_customer_order_export_keep_legacy_formats' );
		$csv_export_formats        = SkyVerge\WooCommerce\CSV_Export\Admin\Export_Formats_Helper::get_export_formats( 'csv', $export_type, $use_legacy_formats );
		$xml_export_formats        = SkyVerge\WooCommerce\CSV_Export\Admin\Export_Formats_Helper::get_export_formats( 'xml', $export_type, $use_legacy_formats );
		$format_field_tooltip      = __( 'Default is a new format for v3.0, Import matches the Customer/Order CSV Import plugin format.', 'woocommerce-customer-order-csv-export' );
		$format_field_tooltip_html = wc_help_tip( $format_field_tooltip );

		$csv_filename_field_default  = $csv_saved_filename ? $csv_saved_filename : "{$export_type}-export-%%timestamp%%.csv";
		$xml_filename_field_default  = $xml_saved_filename ? $xml_saved_filename : "{$export_type}-export-%%timestamp%%.xml";
		$filename_field_tooltip_html = '';

		switch ( $export_type ) {

			case \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS:
				$filename_field_tooltip = __( 'The filename for exported orders. Merge variables: %%timestamp%%, %%order_ids%%', 'woocommerce-customer-order-csv-export' );
			break;
			case \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS:
				$filename_field_tooltip = __( 'The filename for exported customers. Merge variables: %%timestamp%%', 'woocommerce-customer-order-csv-export' );
			break;
			case \WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS :
				$filename_field_tooltip = __( 'The filename for exported coupons. Merge variables: %%timestamp%%', 'woocommerce-customer-order-csv-export' );
			break;
		}

		if ( ! empty( $filename_field_tooltip ) ) {

			$filename_field_tooltip_html = wc_help_tip( $filename_field_tooltip );
		}

		$csv_add_notes_field_default  = '' !== $csv_saved_add_order_notes ? $csv_saved_add_order_notes : true;
		$xml_add_notes_field_default  = '' !== $xml_saved_add_order_notes ? $xml_saved_add_order_notes : true;
		$add_notes_field_tooltip      = __( 'Enable to add a note to exported orders.', 'woocommerce-customer-order-csv-export' );
		$add_notes_field_tooltip_html = wc_help_tip( $add_notes_field_tooltip );

		$batch_processing_field_default      = ! empty( $saved_batch_enabled ) ? $saved_batch_enabled : false;
		$batch_processing_field_tooltip      = __( 'Use batch processing for manual exports. Only enable this setting when notified that your site does not support background processing.', 'woocommerce-customer-order-csv-export' );
		$batch_processing_field_tooltip_html = wc_help_tip( $batch_processing_field_tooltip );

	?>

	<form>

		<table class="wc-customer-order-csv-export-download-action-modal widefat" cellspacing="0">
			<tbody>
				<tr>
					<td>
						<label for="js-download-action-export-format"><strong><?php esc_html_e( 'Format', 'woocommerce-customer-order-csv-export' ); ?></strong><?php echo $format_field_tooltip_html ?></label>
					</td>
					<td style="text-align: left !important;">
						<select id="js-download-action-export-format" name="download_action_export_format">
							<# if ( 'csv' === data.output_type ) { #>
							<?php
								wc_customer_order_csv_export()->get_admin_instance()->render_select_with_optgroup_options( $csv_export_formats, $csv_saved_export_format );
							?>
							<# } else { #>
							<?php
								wc_customer_order_csv_export()->get_admin_instance()->render_select_with_optgroup_options( $xml_export_formats, $xml_saved_export_format );
							?>
							<# } #>
						</select>
					</td>
				</tr>

				<tr>
					<td>
						<label for="js-download-action-filename"><strong><?php esc_html_e( 'Filename', 'woocommerce-customer-order-csv-export' ); ?></strong><?php echo $filename_field_tooltip_html ?></label>
					</td>
					<td style="text-align: left !important;">
						<input type="text" id="js-download-action-filename" name="download_action_filename" style="min-width: 300px;"
							<# if ( 'csv' === data.output_type ) { #>
								value="<?php echo $csv_filename_field_default; ?>"
							<# } else { #>
								value="<?php echo $xml_filename_field_default; ?>"
							<# } #>
						/>
					</td>
				</tr>

				<?php if ( \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export_type ) : ?>
				<tr>
					<td>
						<label for="js-download-action-add-notes"><strong><?php esc_html_e( 'Add order notes', 'woocommerce-customer-order-csv-export' ); ?></strong><?php echo $add_notes_field_tooltip_html; ?></label>
					</td>
					<td style="text-align: left !important;">
						<input type="checkbox" id="js-download-action-add-notes" name="download_action_add_notes" value="1"
							<# if ( 'csv' === data.output_type ) { #>
								<?php echo $csv_add_notes_field_default ? 'checked="checked"' : ''; ?>
							<# } else { #>
								<?php echo $xml_add_notes_field_default ? 'checked="checked"' : ''; ?>
							<# } #>
						>
					</td>
				</tr>
				<?php endif; ?>

				<tr>
					<td>
						<label for="js-download-action-batch-processing"><strong><?php esc_html_e( 'Batch processing', 'woocommerce-customer-order-csv-export' ); ?></strong><?php echo $batch_processing_field_tooltip_html; ?></label>
					</td>
					<td style="text-align: left !important;">
						<input type="checkbox" id="js-download-action-batch-processing" name="download_action_enable_batch_processing" value="1"
							<?php echo $batch_processing_field_default ? 'checked="checked"' : ''; ?>
						>
					</td>
				</tr>

			</tbody>
		</table>

	</form>

	<?php endif; ?>

</script>

<?php if ( isset( $current_tab ) && 'custom_formats' === $current_tab ) : ?>

	<script type="text/template" id="tmpl-wc-customer-order-coupon-export-modal-body-add-custom-format">

		<form action="" method="post">

			<table class="wc-customer-order-coupon-export-add-custom-format-options form-table">
				<tbody>

					<tr valign="top">
						<th>
							<label for="export_type">
								<?php esc_html_e( 'Export type', 'woocommerce-customer-order-csv-export' ); ?>
							</label>
						</th>
						<td>
							<ul>

								<?php $export_types = wc_customer_order_csv_export()->get_export_types(); ?>

								<?php foreach ( $export_types as $export_type_option => $label ) : ?>
									<li>
										<label>
											<input type="radio" name="export_type" value="<?php echo esc_attr( $export_type_option ); ?>" <?php checked( \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS, $export_type_option ); ?> />
											<?php echo esc_html( $label ); ?>
										</label>
									</li>
								<?php endforeach; ?>

							</ul>
						</td>
					</tr>

					<tr valign="top">
						<th>
							<label for="output_type">
								<?php esc_html_e( 'Output type', 'woocommerce-customer-order-csv-export' ); ?>
							</label>
						</th>
						<td>
							<ul>

								<?php $output_types = wc_customer_order_csv_export()->get_output_types(); ?>

								<?php foreach ( $output_types as $output_type_option => $label ) : ?>
									<li>
										<label>
											<input type="radio" name="output_type" value="<?php echo esc_attr( $output_type_option ); ?>" <?php checked( \WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV, $output_type_option ); ?> />
											<?php echo esc_html( $label ); ?>
										</label>
									</li>
								<?php endforeach; ?>

							</ul>
						</td>
					</tr>

				</tbody>
			</table>

		</form>

	</script>

	<script type="text/template" id="tmpl-wc-customer-order-csv-export-modal-body-load-mapping">

		<form action="" method="post">

		<?php

			$export_type = $export_type ?? WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS;

			$format_options = [
				'default' => __( 'Default', 'woocommerce-customer-order-csv-export' ),
			];

			if ( WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV === $output_type ) {

				if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export_type ) {
					$format_options['default_one_row_per_item'] = __( 'Default - One Row per Item', 'woocommerce-customer-order-csv-export' );
				}

				$format_options['import'] = __( 'CSV Import', 'woocommerce-customer-order-csv-export' );

				if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS === $export_type ) {
					/**
					 * There is no `import` format, only `default`.
					 * And the default's name is "CSV Import".
					 * @see \WC_Customer_Order_CSV_Export_Formats::load_formats()
					 */
					$format_options['default'] = $format_options['import'];
					unset( $format_options['import'] );
				}
			}

			/**
			 * Allow actors to change the existing format options in load mapping modal
			 *
			 * @since 4.0.0
			 * @param array $format_options
			 * @param string $export_type
			 */
			$format_options = apply_filters( 'wc_customer_order_export_load_mapping_options', $format_options, $export_type );
		?>

			<div class="wc-customer-order-csv-export-load-mapping-source-selector">
				<select name="source" id="load-mapping-source">
					<optgroup label="<?php esc_attr_e( 'Existing formats', 'woocommerce-customer-order-csv-export' ); ?>">
						<?php foreach ( $format_options as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</optgroup>
					<option value="snippet"><?php esc_html_e( 'JSON snippet', 'woocommerce-customer-order-csv-export' ); ?></option>
					<option value="empty"><?php esc_html_e( 'Build my own', 'woocommerce-customer-order-csv-export' ); ?></option>
				</select>

				<textarea id="load-mapping-snippet" class="large-text" rows="10" name="snippet" style="display: none" placeholder="<?php esc_attr_e( 'Insert or copy & paste mapping configuration here', 'woocommerce-customer-order-csv-export' ); ?>"></textarea>
			</div>

		</form>
	</script>

<?php endif; ?>
