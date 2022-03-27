<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce Local Pickup Plus order pickup details for PIP documents template file.
 *
 * @type string $document_action Whether the document is being printed or emailed
 * @type string $document_type Type of document being displayed
 * @type \WC_PIP_Document $document Invoice, Packing List or Pick List document
 * @type \WC_Order $order Order being displayed
 * @type array $pickup_data Pickup data for given order
 * @type \WC_Shipping_Local_Pickup_Plus $shipping_method Local Pickup Plus Shipping Method instance
 *
 * @version 2.0.0
 * @since 2.0.0
 */

?>
<div class="wc-local-pickup-plus">

	<?php $package_number = 1; ?>
	<?php $packages_count = count( $pickup_data ); ?>

	<h3><?php echo esc_html( $shipping_method->get_method_title() ); ?></h3>

	<?php foreach ( $pickup_data as $pickup_meta ) : ?>

		<div>
			<?php if ( $packages_count > 1 ) : ?>
				<h5><?php echo sprintf( is_rtl() ? '#%2$s %1$s': '%1$s #%2$s', esc_html( $shipping_method->get_method_title() ), $package_number ); ?></h5>
			<?php endif; ?>
			<ul>
				<?php foreach ( $pickup_meta as $label => $value ) : ?>
					<li>
						<?php if ( is_rtl() ) : ?>
							<?php echo wp_kses_post( $value ); ?> <strong>:<?php echo esc_html( $label ); ?></strong>
						<?php else : ?>
							<strong><?php echo esc_html( $label ); ?>:</strong> <?php echo wp_kses_post( $value ); ?>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php $package_number++; ?>
		</div>

	<?php endforeach; ?>
</div>

