<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Renders the available checkout add-ons.
 *
 * @type array $add_on_fields add-on fields to be rendered by woocommerce_form_field
 *
 * @version 1.0
 * @since 1.0
 */

if ( $add_on_fields ) :

	?>
	<div id="wc_checkout_add_ons">
		<?php

		foreach ( $add_on_fields as $key => $field ) :

			woocommerce_form_field( $key, $field, WC()->checkout()->get_value( $key ) );

		endforeach;

		?>
	</div>
	<?php

endif;
