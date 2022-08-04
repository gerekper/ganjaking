<?php
/**
 * Credit notes list.
 *
 * @package YITH\PDFInvoice\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WCPI_Documents_List_Table' ) || ! class_exists( 'YITH_WCPI_Credit_Notes_Table' ) ) {
	require_once YITH_YWPI_INC_DIR . 'admin/class-yith-ywpi-documents-list-table.php';
	require_once YITH_YWPI_INC_DIR . 'admin/class-yith-ywpi-credit-notes-list-table.php';

}
$empty_class = 'ywpi_empty_state';

$list_table = new YITH_WCPI_Credit_Notes_Table();
$list_table->prepare_items();
if ( ! empty( $list_table->items ) ) {

	$download_url    = add_query_arg( 'download_all', true, admin_url( 'admin.php' . yith_ywpi_get_panel_url( 'documents_type', 'documents_type-credit-notes' ) ) );
	$download_button = esc_html__( 'Download all', 'yith-woocommerce-pdf-invoice' );

	$empty_class = '';
}
?>
<div id="yith-ywpi-list-table" class="yith-plugin-fw-panel-custom-tab-container">
	<div class="yith-ywpi-list-table-container">
		<div class="yith-ywpi-list-table-elements <?php echo esc_attr( $empty_class ); ?>">
			<?php
			if ( ! empty( $list_table->items ) ) {
				?>
				<form id="posts-filter"
					class="ywpi-documents-table credit-notes-table yith-plugin-ui--classic-wp-list-style"
					method="get">
					<h1 class="wp-heading-inline"><?php esc_html_e( 'Credit Notes', 'yith-woocommerce-pdf-invoice' ); ?></h1>
					<a href="<?php echo esc_url( $download_url ); ?>"
					class="page-title-action yith-plugin-fw__button--primary">
						<?php echo esc_html( $download_button ); ?>
					</a>
					<hr class="wp-header-end">
					<?php
					$list_table->search_box( 'Search credit note', 'search_credit_note' );
					$list_table->display();
					?>
					<input type="hidden" name="page" value="yith_woocommerce_pdf_invoice_panel"/>
					<input type="hidden" name="tab" value="documents_type"/>
					<input type="hidden" name="sub_tab" value="documents_type-credit-notes"/>
				</form>
				<?php
			} else {
				yith_plugin_fw_get_component(
					array(
						'type'     => 'list-table-blank-state',
						'class'    => 'ywpi_empty_state_documents',
						'icon_url' => YITH_YWPI_ASSETS_IMAGES_URL . 'list-tables/empty-credit-notes.svg',
						'message'  => wp_kses_post( __( 'You have no credit notes generated yet!', 'yith-woocommerce-pdf-invoice' ) ) . '<br>' . wp_kses_post( __( 'Check it later!', 'yith-woocommerce-pdf-invoice' ) ),
					)
				);
			}
			?>
		</div>
	</div>
</div>
