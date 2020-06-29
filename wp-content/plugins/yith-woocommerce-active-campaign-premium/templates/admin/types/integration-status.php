<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
    </th>
    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo sanitize_title( $value['type'] ) ?>">
        <div class="account-banner">
            <div class="account-avatar">
                <div class="account-thumb">
					<?php if ( ! empty( $avatar ) ): ?>
                        <img src="<?php echo $avatar ?>" alt="<?php echo $username; ?>" width="96" heigth="96" />
						<?php
					else:
						echo get_avatar( 0, 96 );
					endif;
					?>
                </div>
                <div class="account-name tips" data-tip="<?php echo ! empty( $username ) ? __( 'Active Campaign user',
                    'yith-woocommerce-active-campaign' ) : __( 'No user found with this API key', 'yith-woocommerce-active-campaign' ) ?>">
					<?php echo ! empty( $username ) ? $username : __( '&lt; Not Found &gt;' ); ?>
                </div>
            </div>
            <div class="account-details">
                <p class="account-info">
                    <span class="label"><b><?php _e( 'Status:', 'yith-woocommerce-active-campaign' ) ?></b></span>

					<?php if ( ! empty( $username ) ): ?>
                        <mark class="completed tips" data-tip="<?php _e( 'Correctly synchronized', 'yith-woocommerce-active-campaign' ) ?>"><?php _ex( 'OK', 'Correct API provided', 'yith-woocommerce-active-campaign' ) ?></mark>
					<?php else: ?>
                        <mark class="cancelled tips" data-tip="<?php _e( 'Wrong API key', 'yith-woocommerce-active-campaign' ) ?>"><?php _ex( 'KO', 'wrong API provided', 'yith-woocommerce-active-campaign' ) ?></mark>
					<?php endif; ?>
                </p>

                <p class="account-info">
                    <span class="label"><b><?php _ex( 'Name:', 'Active Campaign account details', 'yith-woocommerce-active-campaign' ) ?></b></span>

					<?php echo ! empty( $name ) ? $name : _x( '&lt; Not Found &gt;', 'Active Campaign account details', 'yith-woocommerce-active-campaign' ) ?>
                </p>

                <p class="account-info">
                    <span class="label"><b><?php _ex( 'Email:', 'Active Campaign account details', 'yith-woocommerce-active-campaign' ) ?></b></span>

					<?php echo ! empty( $email ) ? $email : _x( '&lt; Not Found &gt;', 'Active Campaign account details', 'yith-woocommerce-active-campaign' ) ?>
                </p>
            </div>
        </div>
    </td>
</tr>