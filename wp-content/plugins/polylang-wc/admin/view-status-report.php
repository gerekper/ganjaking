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

			if ( $verified_page->page_id ) {
				/* translators: %s is a page name */
				$_page_name = '<a href="' . esc_url( get_edit_post_link( $verified_page->page_id ) ) . '" title="' . esc_attr( sprintf( __( 'Edit %s page', 'polylang-wc' ), $verified_page->page_name ) ) . '">' . esc_html( $verified_page->page_name ) . '</a>';
			} else {
				$_page_name = esc_html( $verified_page->page_name );
			}
			?>
			<tr>
				<td data-export-label="<?php echo esc_attr( $verified_page->page_name ); ?>">
					<?php echo $_page_name; // PHPCS:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>:
				</td>
				<td class="help">
					<?php echo wc_help_tip( $verified_page->help ); // PHPCS:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
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
