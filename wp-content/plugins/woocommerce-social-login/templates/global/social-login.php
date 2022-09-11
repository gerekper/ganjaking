<?php
/**
 * WooCommerce Social Login
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Renders login buttons for available social login providers.
 *
 * @type string $login_text social login prompt text
 * @type array $providers providers to be rendered
 * @type string $return_url return URL
 *
 * @version 2.6.2
 * @since 1.0.0
 */

if ( $providers ) :

	?>
	<div class="wc-social-login form-row-wide">

		<p><?php echo wp_kses_post( $login_text ); ?></p>

		<?php foreach ( $providers as $provider ) : ?>
			<?php printf( '<a href="%1$s" class="button-social-login button-social-login-%2$s"><span class="si si-%2$s"></span>%3$s</a> ', esc_url( $provider->get_auth_url( $return_url ) ), esc_attr( $provider->get_id() ), esc_html( $provider->get_login_button_text() ) ); ?>
		<?php endforeach; ?>

	</div>
	<?php

endif;
