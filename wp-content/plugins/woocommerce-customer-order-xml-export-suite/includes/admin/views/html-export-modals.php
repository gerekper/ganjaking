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
 * Export modal templates
 *
 * @since 2.0.0
 * @version 2.0.0
 */

global $current_tab, $current_section;
?>

<script type="text/template" id="tmpl-wc-customer-order-xml-export-suite-modal">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1>{{{data.title}}}</h1>
					<?php if ( ! wc_customer_order_xml_export_suite()->is_batch_processing_enabled() ) : ?>
						<button class="modal-close modal-close-link dashicons dashicons-no-alt">
							<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce-customer-order-xml-export-suite' ); ?></span>
						</button>
					<?php endif; ?>
				</header>
				<article>{{{data.body}}}</article>
				<footer>
					<div class="inner">
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

<script type="text/template" id="tmpl-wc-customer-order-xml-export-suite-modal-body-export-started">

	<?php $batch_enabled = wc_customer_order_xml_export_suite()->is_batch_processing_enabled(); ?>

	<?php if ( $batch_enabled ) : ?>
		<section>
			<progress class="wc-customer-order-xml-export-suite-progress" max="100" value="0"></progress>
		</section>
	<?php endif; ?>

	<p>
		<span class="dashicons dashicons-update wc-customer-order-xml-export-suite-dashicons-spin"></span>
		<?php esc_html_e( 'Your data is being exported now.', 'woocommerce-customer-order-xml-export-suite' ); ?>
		<# if ( 'download' === data.export_method ) { #>
		<?php esc_html_e(' When the export is complete, the download will start automatically.', 'woocommerce-customer-order-xml-export-suite' ); ?>
		<# } #>
	</p>

	<?php if ( $batch_enabled ) : ?>
		<p class="batch-warning">
			<?php esc_html_e(' Do not navigate away from this page or use the back button until the export is complete.', 'woocommerce-customer-order-xml-export-suite' ); ?>
		</p>
	<?php endif; ?>

	<p>
		<?php
			/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
			printf( esc_html__( 'When completed, the exported file will also be available under %1$sExport List%2$s for the next 14 days.', 'woocommerce-customer-order-xml-export-suite' ), '<a href="' . admin_url( 'admin.php?page=wc_customer_order_xml_export_suite&tab=export_list' ) . '">', '</a>' );

			if ( ! $batch_enabled ) :

				/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
				esc_html_e( ' You can safely leave this screen and return to the Export List later.', 'woocommerce-customer-order-xml-export-suite' );

			endif;
		?>
	</p>
</script>

<script type="text/template" id="tmpl-wc-customer-order-xml-export-suite-modal-body-export-completed">
	<p><span class="dashicons dashicons-yes"></span>
		<?php esc_html_e( 'Your export is ready!', 'woocommerce-customer-order-xml-export-suite' ); ?>
		<# if ( 'download' === data.export_method ) { #>
		<?php
			/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
			printf( esc_html__( '%1$sClick here%2$s if your download does not start automatically. ', 'woocommerce-customer-order-xml-export-suite' ), '<a class="js-export-download-link" href="{{{data.download_url}}}">', '</a>' );
		?>
		<# } #>
		<?php
			/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
			printf( esc_html__( 'The exported file will be available under %1$sExport List%2$s for the next 14 days.', 'woocommerce-customer-order-xml-export-suite' ), '<a href="' . admin_url( 'admin.php?page=wc_customer_order_xml_export_suite&tab=export_list' ) . '">', '</a>' );
		?>
	</p>
</script>

<?php if ( isset( $current_tab ) && 'custom_formats' === $current_tab ) : ?>

	<script type="text/template" id="tmpl-wc-customer-order-xml-export-suite-modal-body-load-mapping">

		<form action="" method="post">

		<?php

			$current_section === isset( $current_section ) ? $current_section : 'orders';

			$format_options = array(
				'default' => __( 'Default', 'woocommerce-customer-order-xml-export-suite' ),
				'legacy'  => __( 'Legacy', 'woocommerce-customer-order-xml-export-suite' ),
			);

			/**
			 * Allow actors to change the existing format options in load mapping modal
			 *
			 * @since 2.0.0
			 * @param array $format_options
			 * @param string $export_type
			 */
			$format_options = apply_filters( 'wc_customer_order_xml_export_suite_load_mapping_options', $format_options, $current_section );
		?>

			<div class="wc-customer-order-xml-export-suite-load-mapping-source-selector">
				<select name="source" id="load-mapping-source">
					<optgroup label="<?php esc_attr_e( 'Existing formats', 'woocommerce-customer-order-xml-export-suite' ); ?>">
						<?php foreach ( $format_options as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</optgroup>
					<option value="snippet"><?php esc_html_e( 'JSON snippet', 'woocommerce-customer-order-xml-export-suite' ); ?></option>
				</select>

				<textarea id="load-mapping-snippet" class="large-text" rows="10" name="snippet" style="display: none" placeholder="<?php esc_attr_e( 'Insert or copy & paste format configuration here', 'woocommerce-customer-order-xml-export-suite' ); ?>"></textarea>
			</div>

		</form>
	</script>

<?php endif; ?>
