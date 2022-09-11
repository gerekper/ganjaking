<?php
/**
 * WooCommerce Product Documents
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Documents to newer
 * versions in the future. If you wish to customize WooCommerce Product Documents for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-documents/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Renders a set of product documents within sections.
 *
 * @type string $title optional title to display above the set of documents,
 *                     false indicates that the product documents element is a widget
 *                     and the default WordPress widget title will be rendered.
 * @type \WC_Product $product the product to render documents for
 * @type int $product_id The Product ID to render documents for
 * @type \WC_Product_Documents_Collection $documents_collection the collection of product documents
 *
 * @version 1.7.0
 * @since 1.0
 */

// render the title and documents/sections
$title = apply_filters( 'wc_product_documents_title', $title, $product );
if ( $title ) {
	echo apply_filters( 'wc_product_documents_title_html', '<h3 class="woocommerce-product-documents-title">' . esc_html( $title ) . '</h3>', $title, $product );
}

?>
<div class="woocommerce-product-documents-<?php echo $product_id; ?> woocommerce-product-documents">

	<?php foreach ( $documents_collection->get_sections() as $section ) : ?>

		<h3><?php echo $section->get_name(); ?></h3>

		<div>
			<ul>
				<?php foreach ( $section->get_documents() as $document ) : ?>

					<li><a href="<?php echo esc_url( $document->get_file_location() ); ?>" target="<?php echo esc_attr( apply_filters( 'wc_product_documents_link_target', '_self', $product, $section, $document ) ); ?>"><?php echo esc_html( $document->get_label() ); ?></a></li>

				<?php endforeach; ?>
			</ul>
		</div>

	<?php endforeach; ?>

</div>
