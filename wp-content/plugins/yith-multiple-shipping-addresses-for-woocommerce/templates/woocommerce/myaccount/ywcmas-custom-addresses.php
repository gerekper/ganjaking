<?php
$params = array(
	'ajax'   => 'true',
	'action' => 'ywcmas_shipping_address_form',
);
$url = add_query_arg( $params, admin_url( 'admin-ajax.php' ) );

?>
<div>
    <div style="margin-bottom: 10px;">
        <a class="button ywcmas_shipping_address_button_new" data-rel="prettyPhoto"
           href="<?php echo $url; ?>"><?php esc_html_e( 'Add new shipping address', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></a>
    </div>
	<?php $addresses = yith_wcmas_get_user_custom_addresses( get_current_user_id() ); ?>
    <div id="ywcmas_default_address_block">
		<?php
		ob_start();
		wc_get_template( 'ywcmas-default-address.php', '', '', YITH_WCMAS_WC_TEMPLATE_PATH . 'myaccount/' );
		echo ob_get_clean();
		?>
    </div>
	<?php if ( $addresses ) : ?>
        <div style="margin-top: 40px;">
			<?php
			$params_delete_all = array(
				'ajax'       => 'true',
				'action'     => 'ywcmas_delete_shipping_address_window',
				'delete_all' => 'true'
			);
			$url_delete_all = add_query_arg( $params_delete_all, admin_url( 'admin-ajax.php' ) );
			?>
            <div>
                <h3 style="display: inline-block;"><?php esc_html_e( 'Additional shipping addresses', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></h3>
                <a class="ywcmas_shipping_address_button_delete" style="float: right;" data-rel="prettyPhoto"
                   href="<?php echo $url_delete_all; ?>"><?php esc_html_e( 'Delete all', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></a>
            </div>
            <div class="addresses">
				<?php foreach ( $addresses as $address_id => $address ) : ?>
                    <div class="ywcmas_single_address" id="<?php echo $address_id ?>" style="border: 1px solid lightgrey; margin-bottom: 20px; padding: 20px;">
                        <h5><?php echo $address_id ?></h5>
                        <address>
							<?php
							$address = array(
								'first_name'  => $address['shipping_first_name'],
								'last_name'   => $address['shipping_last_name'],
								'company'     => $address['shipping_company'],
								'address_1'   => $address['shipping_address_1'],
								'address_2'   => $address['shipping_address_2'],
								'city'        => $address['shipping_city'],
								'state'       => $address['shipping_state'],
								'postcode'    => $address['shipping_postcode'],
								'country'     => $address['shipping_country'],
							);

							$formatted_address = WC()->countries->get_formatted_address( $address );

							if ( ! $formatted_address )
								_e( 'You have not set up this type of address yet.', 'yith-multiple-shipping-addresses-for-woocommerce' );
							else
								echo $formatted_address;
							?>
                        </address>
						<?php
						$params_edit = array(
							'ajax'    => 'true',
							'action'  => 'ywcmas_shipping_address_form',
							'address_id' => $address_id
						);
						$url_edit = add_query_arg( $params_edit, admin_url( 'admin-ajax.php' ) );
						$params_delete = array(
							'ajax'   => 'true',
							'action' => 'ywcmas_delete_shipping_address_window',
							'address_id' => $address_id
						);
						$url_delete = add_query_arg( $params_delete, admin_url( 'admin-ajax.php' ) );
						?>
                        <div>
                            <a class="ywcmas_shipping_address_button_edit" data-rel="prettyPhoto" data-address_id="<?php echo $address_id; ?>"
                               href="<?php echo $url_edit; ?>"><?php esc_html_e( 'Edit', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></a>
                            <a class="ywcmas_shipping_address_button_delete" data-rel="prettyPhoto" data-address_id="<?php echo $address_id; ?>"
                               href="<?php echo $url_delete; ?>"><?php esc_html_e( 'Delete', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></a>
                        </div>
                    </div>
				<?php endforeach; ?>
            </div>
        </div>
	<?php endif; ?>
</div>