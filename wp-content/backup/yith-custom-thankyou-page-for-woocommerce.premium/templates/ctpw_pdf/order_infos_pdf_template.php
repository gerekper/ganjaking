<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package       YITH Custom ThankYou Page for Woocommerce
 */

/**
 * PDF Layout Template
 *
 * Override this template by copying it to [your theme folder]/woocommerce/ctpw_pdf/order_infos_pdf_template.php
 *
 * @author        Yithemes
 * @package       YITH Custom ThankYou Page for Woocommerce
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<?php
	/**
	 * yith_ctpw_pdf_template_head hook
	 *
	 * @hooked yith_ctpw_add_pdf_styles - 10
	 */
	// DO_ACTION yith_ctpw_pdf_template_head: hook the PDF head tag: param $order.
	do_action( 'yith_ctpw_pdf_template_head', $order );
	?>
</head>

<body>
<div class="ctpw-pdf-document">
	<?php
	/**
	 * Show the header of the document
	 *
	 * @hooked yith_ctpw_add_pdf_logo - 10
	 */
	// DO_ACTION yith_ctpw_template_document_header: hook the PDF header: param $order.
	do_action( 'yith_ctpw_template_document_header', $order );
	?>

	<?php
	/**
	 * Show the template for the order details
	 *
	 * @hooked yith_ctpw_add_pdf_order_infos - 10
	 * @hooked yith_ctpw_add_pdf_order_infos_table - 15
	 */
	// DO_ACTION yith_ctpw_template_order_content: hook the PDF main content: param $order.
	do_action( 'yith_ctpw_template_order_content', $order );
	?>

	<?php
	/**
	 * Show the template for the order notes
	 */
	// DO_ACTION yith_ctpw_template_notes: hook the PDF notes: param $order.
	do_action( 'yith_ctpw_template_notes', $order );
	?>

	<?php
	/**
	 * Show the template for end of the document
	 *
	 * @hooked yith_ctpw_pdf_footer_text - 10
	 */
	// DO_ACTION yith_ctpw_template_footer: hook the PDF footer: param $order.
	do_action( 'yith_ctpw_template_footer', $order );
	?>
</div>
</body>
</html>
