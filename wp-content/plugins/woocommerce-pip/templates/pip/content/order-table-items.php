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
 * PIP Template Body before content
 *
 * @type \WC_Order $order Order object
 * @type int $order_id Order ID
 * @type \WC_PIP_Document Document object
 * @type string $type Document type
 * @type string $action Current document action
 *
 * @version 3.6.2
 * @since 3.0.0
 */

					?>
					<tbody class="order-table-body">

						<?php $table_rows = $document->get_table_rows(); ?>

						<?php foreach( $table_rows as $rows ) : ?>

							<?php if ( ! empty( $rows['headings'] ) && is_array( $rows['headings'] ) ) : ?>

								<tr class="row heading">

									<?php foreach ( $rows['headings'] as $cell_id => $cell ) : ?>

										<?php if ( ! empty( $cell['content'] ) ) : ?>

											<th class="<?php echo sanitize_html_class( $cell_id ); ?>" <?php if ( ! empty( $cell['colspan'] ) ) { echo 'colspan="' . (int) $cell['colspan'] . '"'; } ?>>
												<?php echo $cell['content']; ?>
											</th>

										<?php endif; ?>

									<?php endforeach; ?>

								</tr>

							<?php endif; ?>

							<?php if ( ! empty( $rows['items'] ) ) : $i = 0; ?>

								<?php foreach ( $rows['items'] as $items ) : ?>

									<?php if ( ! empty( $items ) && is_array( $items ) ) : $i++; ?>

										<tr class="row item <?php echo $i % 2 === 0 ? 'even' : 'odd'; ?>">

											<?php foreach ( $items as $cell_id => $cell_content ) : ?>

												<td class="<?php echo sanitize_html_class( $cell_id ); ?>">
													<?php echo $cell_content; ?>
												</td>

											<?php endforeach; ?>

										</tr>

									<?php endif; ?>

								<?php endforeach; ?>

							<?php endif; ?>

						<?php endforeach; ?>

					</tbody>
					<?php
