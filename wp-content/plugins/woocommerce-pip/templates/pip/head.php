<?php
/**
 * WooCommerce Print Invoices/Packing Lists
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @package   WC-Print-Invoices-Packing-Lists/Templates
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * PIP Template Head.
 *
 * @type \WC_Order $order order object
 * @type \WC_PIP_Document document object
 * @type string $type document type
 * @type string $action current document action
 *
 * @version 3.6.2
 * @since 3.0.0
 */

?>
<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>">
		<title>
			<?php

			/**
			 * Filters the document's title.
			 *
			 * @since 3.0.0
			 *
			 * @param string $title The title
			 * @param string $type PIP Document type
			 * @param \WC_PIP_Document $document PIP Document object
			 * @param \WC_Order $order Order object
			 */
			echo apply_filters( 'wc_pip_document_title', sprintf( esc_html( '%1$s - %2$s %3$s' ), wc_pip_get_site_name(), $document->name, $document->get_invoice_number() ), $type, $document, $order );

			?>
		</title>
		<?php

		/**
		 * Fires inside the document's `<head>` element.
		 *
		 * @since 3.0.0
		 *
		 * @param string $type PIP Document type
		 * @param \WC_PIP_Document $document PIP Document object
		 * @param \WC_Order $order order object
		 */
		do_action( 'wc_pip_head', $type, $document, $order );

		?>
	</head>
	<?php

	/**
	 * Filters if the print dialog should be shown automatically when the document loads.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $show_print_dialog defaults to true
	 */
	$show_print_dialog = apply_filters( 'wc_pip_show_print_dialog', true );

	?>
	<body id="woocommerce-pip" class="woocommerce-pip <?php echo sanitize_html_class( $type ); ?>" <?php if ( isset( $action ) && 'print' === $action && $show_print_dialog ) { echo 'onload="window.print()"'; } ?> <?php echo is_rtl() ? 'style="direction: rtl;"' : ''; ?>>
		<?php

			// outputs a print button for documents to be printed
			if ( isset( $action ) && 'print' === $action ) {
				wc_pip_print_button();
			}
