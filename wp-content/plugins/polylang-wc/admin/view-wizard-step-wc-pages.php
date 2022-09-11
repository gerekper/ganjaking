<?php
/**
 * Displays the wizard WooCommerce step.
 *
 * @package Polylang-WC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
};

$admin_status_report = Polylang_Woocommerce::instance()->admin_status_reports;

?>
<h2><?php esc_html_e( 'WooCommerce pages', 'polylang-wc' ); ?></h2>
<?php
if ( count( $this->translation_updates ) > 0 ) {
	?>
	<p>
		<?php esc_html_e( 'For your multilingual shop to work correctly, all the WooCommerce pages must be created and translated.', 'polylang-wc' ); ?>
	</p>
	<p>
		<?php
		if ( $admin_status_report->get_woocommerce_pages_status()->is_error ) {
			esc_html_e( 'Before creating these pages in each language, if available, we will try to install the plugin translations.', 'polylang-wc' );
		} else {
			esc_html_e( 'All the WooCommerce pages have already been created and translated, but some plugin translations have not been installed yet. We will try to install the plugin translations, if available.', 'polylang-wc' );
		}
		?>
	</p>
	<table id="translations-to-update" class="wc_status_table widefat">
		<thead>
			<th colspan="2">
				<h2><?php esc_html_e( 'Missing plugin translations', 'polylang-wc' ); ?></h2>
			</th>
		</thead>
		<tbody>
			<?php
			foreach ( $this->translation_updates as $translation ) {
				if ( 'plugin' !== $translation->type ) {
					continue;
				}

				$language_properties = $this->model->get_language( $translation->language );
				if ( ! $language_properties ) {
					continue;
				}
				?>
				<tr>
					<td><?php echo esc_html( $translation->slug ); ?></td>
					<td><?php echo esc_html( $language_properties->name . ' - ' . $translation->language ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php if ( $admin_status_report->get_woocommerce_pages_status()->is_error ) : ?>
	<p>
		<?php esc_html_e( 'Finally, we are going to ensure that all the WooCommerce pages are created and translated.', 'polylang-wc' ); ?>
	</p>
	<?php endif; ?>
	<?php
} else {
	?>
	<p>
	<?php esc_html_e( 'For your multilingual shop to work correctly, we need to ensure that all the WooCommerce pages are created and translated.', 'polylang-wc' ); ?>
	</p>
	<?php
}
if ( $admin_status_report->get_woocommerce_pages_status()->is_error ) {
	$admin_status_report->wizard_status_report();
}
