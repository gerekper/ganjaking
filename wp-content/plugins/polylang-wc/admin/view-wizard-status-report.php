<?php
/**
 * Adds status report for translations of the default pages.
 *
 * @package Polylang-WC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Don't access directly.
}
?>
<table class="wc_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="3" data-export-label="WC Pages Translations"><h2><?php esc_html_e( 'WooCommerce pages translations', 'polylang-wc' ); ?></h2></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $this->get_woocommerce_pages_status()->pages as $verified_page ) {
			?>
			<tr>
				<td><?php echo esc_html( $verified_page->page_name ); ?>:</td>
				<td>
				<?php if ( $verified_page->is_error ) : ?>
					<mark class="error"><span class="dashicons dashicons-warning"></span> <?php echo esc_html( $verified_page->error_message ); ?></mark>
				<?php else : ?>
						<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
				<?php endif; ?>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
