<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @author YITH
 * @package YITH License & Upgrade Framework
 */

$renew_url = 'https://yithemes.com/my-account/my-subscriptions/';
?>

<tr class="<?php echo esc_attr( $info['TextDomain'] ); ?>">
	<td class="product-name">
		<span class="show-if-is-mobile field-name"><?php esc_html_e( 'License:', 'yith-plugin-upgrade-fw' ); ?></span>
		<?php echo esc_html( $this->display_product_name( $info['Name'] ) ); ?>
	</td>
	<td class="product-licence-email">
		<span class="show-if-is-mobile field-name"><?php esc_html_e( 'Email:', 'yith-plugin-upgrade-fw' ); ?></span>
		<?php
		$license_email      = '';
		$split              = explode( '@', $info['licence']['email'] );
		$len                = strlen( $split[0] );
		$email_start_lenght = 0;

		if ( $len > 3 ) {
			$email_start_lenght = 3;
		} elseif ( $len > 2 ) {
			$email_start_lenght = 2;
		} elseif ( $len > 1 ) {
			$email_start_lenght = 1;
		}

		$email_start      = substr( $split[0], 0, $email_start_lenght );
		$email_anonymized = '';
		for ( $i = 0; $i < ( $len - $email_start_lenght ); $i ++ ) {
			$email_anonymized .= '*';
		}
		$license_email = $email_start . $email_anonymized . '@' . $split[1];
		echo esc_html( $license_email );
		?>
	</td>
	<td class="product-licence-key">
		<span class="show-if-is-mobile field-name"><?php esc_html_e( 'License Key:', 'yith-plugin-upgrade-fw' ); ?></span>
		<?php echo esc_html( sprintf( '%s-****-****-****-************', substr( $info['licence']['licence_key'], 0, 8 ) ) ); ?>
	</td>
	<td class="product-licence-remaining">
		<span class="show-if-is-mobile field-name"><?php esc_html_e( 'Licenses used:', 'yith-plugin-upgrade-fw' ); ?></span>
		<?php
		$license_remaining = sprintf(
			/* translators: %1$1s: Number of activations for the licence. %2$2s: The activations number limit */
			esc_html__( '%1$1s out of %2$2s', 'yith-plugin-upgrade-fw' ),
			( $info['licence']['activation_limit'] - $info['licence']['activation_remaining'] ),
			$info['licence']['activation_limit']
		);
		echo esc_html( $license_remaining );
		?>
	</td>

	<?php
	$error_message = '';
	if ( ! empty( $info['licence']['status_code'] ) && 200 !== intval( $info['licence']['status_code'] ) ) {

		switch ( $info['licence']['status_code'] ) {
			case 106:
				$error_message = esc_html_x( 'expired', 'License status', 'yith-plugin-upgrade-fw' );
				break;
			case 107:
				$error_message = esc_html_x( 'banned', 'License status', 'yith-plugin-upgrade-fw' );
				break;
			default:
				$error_message = esc_html( $info['licence']['status_code'] );
				break;
		}
	}
	?>

	<td class="product-licence-expire-on <?php echo esc_attr( $error_message ); ?>">
		<span class="show-if-is-mobile field-name"><?php esc_html_e( 'Expires on:', 'yith-plugin-upgrade-fw' ); ?></span>
		<div class="yith-license-expire-on-wrapper <?php echo esc_attr( $error_message ); ?>">
			<?php
			if ( ! empty( $info['licence']['status_code'] ) && 200 !== intval( $info['licence']['status_code'] ) ) {
				printf( '<span class="yith-license-status-message %s">%s</span>', $error_message, $error_message ); //@codingStandardsIgnoreLine

				if ( 106 === intval( $info['licence']['status_code'] ) ) {
					// Expired.
					$buy_again = ! empty( $info['PluginURI'] ) ? $info['PluginURI'] : 'https://yithemes.com';
					printf(
						'<a href="%s" class="yith-renew-expired-license button-primary" target="_blank"  rel="nofollow noopener">%s</a>',
						esc_html( $renew_url ),
						esc_html_x( 'renew', 'button label', 'yith-plugin-upgrade-fw' )
					); //@codingStandardsIgnoreLine

					printf(
						'<a href="%s" class="yith-buy-again-license" target="_blank"  rel="nofollow noopener">%s &gt;</a>',
						esc_html( $buy_again ),
						esc_html_x( 'Or get a new license', 'this is a short text for a link', 'yith-plugin-upgrade-fw' )
					); //@codingStandardsIgnoreLine
				}
			} else {
				echo esc_html( gmdate( 'F j, Y', $info['licence']['licence_expires'] ) );
			}
			?>
		</div>
	</td>
	<td class="product-deactivate-button">
		<?php $is_expired_status_code = ! empty( $info['licence']['status_code'] ) && 106 === intval( $info['licence']['status_code'] ); ?>
		<?php $button_class = $is_expired_status_code ? 'remove-expired-plugin' : 'licence-deactive'; ?>
		<?php $button_action = $is_expired_status_code ? 'yith_remove-' . $this->get_product_type() : 'yith_deactivate-' . $this->get_product_type(); ?>
		<input
				type="button"
				value="<?php echo esc_html_x( 'Remove', 'Button label', 'yith-plugin-upgrade-fw' ); ?>"
				class="button-licence button-primary <?php echo esc_html( $button_class ); ?>"
				href="#"
				data-product-init="<?php echo esc_html( $info['init'] ); ?>"
				data-product-id="<?php echo esc_html( $info['product_id'] ); ?>"
				data-textdomain="<?php echo esc_html( $info['TextDomain'] ); ?>"
				data-marketplace="<?php echo esc_html( $info['marketplace'] ); ?>"
				data-displayname="<?php echo esc_html( $this->display_product_name( $info['Name'] ) ); ?>"
				data-action="<?php echo esc_html( $button_action ); ?>">
	</td>
</tr>
