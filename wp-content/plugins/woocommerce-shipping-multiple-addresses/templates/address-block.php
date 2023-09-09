<div class="address-block">
<?php
$page_id   = wc_get_page_id( 'multiple_addresses' );
$form_link = get_permalink( $page_id );
$addr      = array(
	'first_name' => $address['shipping_first_name'],
	'last_name'  => $address['shipping_last_name'],
	'company'    => $address['shipping_company'],
	'address_1'  => $address['shipping_address_1'],
	'address_2'  => $address['shipping_address_2'],
	'city'       => $address['shipping_city'],
	'state'      => $address['shipping_state'],
	'postcode'   => $address['shipping_postcode'],
	'country'    => $address['shipping_country'],
);

$formatted_address = wcms_get_formatted_address( $address );
if ( $formatted_address ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Escaped in wcms_get_formatted_address()
    echo '<address>'.$formatted_address.'</address>';
    $ref = '';
    if ( is_account_page() ) {
        $ref = 'account';
    }
    $edit_link = add_query_arg( array('edit' =>  $idx, 'address-form' => 1, 'ref' => $ref,
		),
		$form_link
	);

	if ( empty( $address['default_address'] ) || true !== $address['default_address'] ) {
		?>
		<div class="buttons">
			<a class="button" href="<?php echo esc_url( $edit_link ); ?>#shipping_address"><?php esc_html_e( 'Edit', 'wc_shipping_multiple_address' ); ?></a>
			<a class="button ms_delete_address" data-idx="<?php echo esc_attr( $idx ); ?>" href="#"><?php esc_html_e( 'Delete', 'wc_shipping_multiple_address' ); ?></a>
		</div>
		<?php
	}
}
?>
</div>
