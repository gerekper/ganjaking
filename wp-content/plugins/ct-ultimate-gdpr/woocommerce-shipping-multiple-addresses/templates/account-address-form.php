<form action="" method="post" id="address_form">

    <?php if ( $updating ): ?>

        <div id="addresses">

            <div class="shipping_address address_block" id="shipping_address_<?php echo $idx; ?>">
                <?php
                foreach ( $shipFields as $key => $field ) {
                    $val = '';

                    if ( isset( $address[ $key ] ) ) {
                        $val = $address[$key];
                    }

                    woocommerce_form_field( $key, $field, $val );
                }

                do_action( 'woocommerce_after_checkout_shipping_form', $checkout);
                ?>
            </div>

        </div>

    <?php else: ?>

        <div id="addresses" class="address-column">

        <?php
        foreach ( $shipFields as $key => $field ) :
            $val = '';

            woocommerce_form_field( $key, $field, $val );
        endforeach;
        ?>
        </div>
    <?php endif; ?>

    <div class="form-row">
		<?php wp_nonce_field( 'shipping_account_address_action' ); ?>
        <input type="submit" name="set_addresses" value="<?php _e( 'Save Address', 'wc_shipping_multiple_address' ); ?>" class="button alt" />
    </div>
</form>
<script type="text/javascript">
	jQuery( document ).ready( function( $ ) {
		$( '#address_form' ).submit( function() {
			var valid = true;

			$( '.input-text, select, input:checkbox' ).each( function( e ) {
				var $this             = $( this ),
				    $parent           = $this.closest( '.form-row' ),
				    validate_required = $parent.is( '.validate-required' );

				if ( validate_required ) {
					if ( 'checkbox' === $this.attr( 'type' ) && ! $this.is( ':checked' ) ) {
						valid = false;
					} else if ( $this.val() === '' ) {
						valid = false;
					}
				}

				if ( ! valid ) {
					$parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-required-field' );
					$this.focus();
					return false;
				}
			});

			return valid;
		});
	});
</script>