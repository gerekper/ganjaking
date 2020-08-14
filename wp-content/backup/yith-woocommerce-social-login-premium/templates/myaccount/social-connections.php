<?php
/**
 * Show Social Connection in My Account Page
 *
 * @package YITH WooCommerce Social Login
 * @since   1.0.0
 * @author  YITH
 */

$s = '';
if ( apply_filters( 'yit_social_login_show_form', true ) ) {
	?>
    <h2><?php echo apply_filters( 'ywsl_my_account_social_connection_title', __( 'Social Connections', 'yith-woocommerce-social-login' ) ); ?></h2>
	<?php if ( ! empty( $user_connections ) ):
		$s = __( 'also', 'yith-woocommerce-social-login' );
		?>
        <table class="shop_table shop_table_responsive my_account_social">

            <tbody>
			<?php foreach ( $user_connections as $provider => $social ):

				if ( $social['profileURL'] ) {
					$profile = sprintf( '<a href="%s" target="_blank">%s</a>', $social['profileURL'], $social['displayName'] );
				} else {
					$profile = $social['displayName'];
				}

				?>
                <tr class="order">
                    <td class="sl-username" data-title="<?php _e( 'Username', 'yith-woocommerce-social-login' ) ?>">
						<?php echo $social['button'] ?>
						<?php echo $profile ?>
                    </td>
                    <td class="sl-unlink" data-title="<?php _e( 'Unlink', 'yith-woocommerce-social-login' ) ?>"><?php echo $social['unlink_button'] ?></td>
                </tr>
			<?php endforeach ?>
            </tbody>

        </table>
	<?php endif;
	if ( ! empty( $user_unlinked_social ) ):
		?><p><?php printf( __( 'You can %s login with:', 'yith-woocommerce-social-login' ), $s ) ?></p><?php

		foreach ( $user_unlinked_social as $key => $value ) {

			$social_args = array(
				'value'     => $value,
				'image_url' => apply_filters( 'ywsl_custom_icon_' . $key, YITH_YWSL_ASSETS_URL . '/images/' . $key . '.png', $key ),
				'class'     => 'ywsl-social ywsl-' . $key
			);

			$social_args = apply_filters( 'yith_wc_social_login_args', $social_args );

			$image  = sprintf( '<img src="%s" alt="%s"/>', $social_args['image_url'], isset( $value['label'] ) ? $value['label'] : $value );
			$social = sprintf( '<div class="%s" data-social="%s">%s</div>', $social_args['class'], strtolower( $value['label'] ), $image );

			echo apply_filters( 'yith_wc_social_login_icon', $social, $key, $social_args );
		}

		?>
	<?php endif ?>
	<?php
}
