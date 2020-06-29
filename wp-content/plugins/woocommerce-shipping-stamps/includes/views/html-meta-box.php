<?php
/**
 * View template to display meta box content.
 *
 * @package WC_Stamps_Integration/View
 */

$needs_customs = $this->needs_customs_step( $order );
?>
<ul class="steps<?php echo $needs_customs ? ' needs-customs' : '';  ?>">
	<li class="step-address <?php echo 'address' === $step ? 'active' : ''; ?>"><?php _e( 'Address', 'woocommerce-shipping-stamps' ); ?></li>
	<li class="step-rates <?php echo 'rates' === $step ? 'active' : ''; ?>"><?php _e( 'Rate', 'woocommerce-shipping-stamps' ); ?></li>
	<?php if ( $needs_customs ) : ?>
		<li class="step-customs <?php echo 'customs' === $step ? 'active' : ''; ?>"><?php _e( 'Customs', 'woocommerce-shipping-stamps' ); ?></li>
	<?php endif; ?>
	<li class="step-labels <?php echo 'labels' === $step ? 'active' : ''; ?>"><?php _e( 'Label', 'woocommerce-shipping-stamps' ); ?></li>
</ul>
<div class="stamps_result">
	<?php
	switch ( $step ) {
		case 'address' :
			echo $this->get_address_verification_html( $order );
		break;
		case 'rates' :
			echo $this->get_packages_html( $order );
		break;
		case 'labels' :
			echo $this->get_labels_html( $labels );
		break;
	}
	?>
</div>
<script type="text/javascript">
	jQuery(function() {
		var stamps_package_types = jQuery.parseJSON( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( array_map( 'esc_js', $this->package_types ) ) ); ?>' ) );

		jQuery('#wc_stamps_get_label')
			.on( 'click', '.stamps-action', function() {
				if ( jQuery(this).data( 'confirm' ) ) {
					if ( ! window.confirm( jQuery(this).data( 'confirm' ) ) ) {
						return false;
					}
				}

				jQuery('#wc_stamps_get_label').trigger( 'block' );

				var action = jQuery(this).data( 'stamps_action' );
				var data   = {
					order_id:  <?php echo $post->ID; ?>,
					action:    'wc_stamps_' + action,
					security:  '<?php echo wp_create_nonce( "stamps" ); ?>',
					data:      jQuery('#wc_stamps_get_label').find('input, select').serialize(),
					action_id: jQuery(this).data('id')
				};
				jQuery.ajax({
					url:  '<?php echo admin_url( 'admin-ajax.php' ); ?>',
					data: data,
					type: 'POST',
					success: function( response ) {
						jQuery('#wc_stamps_get_label').trigger( 'unblock' );
						if ( response.html ) {
							jQuery('div.stamps_result').html( response.html );

							jQuery('#wc_stamps_get_label').trigger( 'init' );
						}
						if ( response.reload ) {
							window.location.reload();
						}
						if ( response.step ) {
							jQuery('#wc_stamps_get_label ul.steps li.active').removeClass('active');
							jQuery('#wc_stamps_get_label ul.steps li.step-' + response.step ).addClass('active');
						}
						if ( response.error ) {
							alert( response.error );
						}
					},
					error: function( xhr, errorcode, error ) {
						if ( typeof console !== "undefined" || typeof console.log !== "undefined" ) {
							console.log( errorcode + ': ' + error );
						}
					}
				});

				return false;
			})
			.on( 'block', function() {
				jQuery('#wc_stamps_get_label').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
				return false;
			})
			.on( 'unblock', function() {
				jQuery('#wc_stamps_get_label').unblock();
				return false;
			})
			.on( 'init', function() {
				jQuery('input.stamps-date-picker').datepicker({
					dateFormat: "yy-mm-dd",
					numberOfMonths: 1,
					showButtonPanel: true,
					minDate: 0
				});
				jQuery( 'select#stamps_package_type, select[name=stamps_customs_content_type]' ).change();
				return false;
			})
			.on( 'change', 'select#stamps_package_type', function() {
				if ( jQuery(this).val() ) {
					jQuery(this).closest('td').find('span').html( stamps_package_types[ jQuery(this).val() ] );
				} else {
					jQuery(this).closest('td').find('span').html('');
				}
			})
			.on( 'change', 'select[name=stamps_customs_content_type]', function() {
				if ( jQuery(this).val() == 'Other' ) {
					jQuery(this).closest('td').find('.other_describe').show();
				} else {
					jQuery(this).closest('td').find('.other_describe').hide();
				}
			})
			.on( 'click', '.wc-stamps-customs-add-line', function() {
				jQuery('.wc-stamps-customs-line-intro').remove();
				jQuery('.wc-stamps-customs-items').prepend( jQuery(this).data( 'line_html' ) );
				return false;
			})
			.on( 'click', '.wc-stamps-customs-remove-line', function() {
				jQuery(this).closest('.wc-stamps-customs-item').remove();
				return false;
			})
			.on( 'change', '.wc-stamps-rates input[type=radio]', function() {
				jQuery('.wc-stamps-rates .addons:visible').hide();
				if ( jQuery(this).is(':checked') ) {
					jQuery(this).closest('tr').next('.addons').show();
				}
			})
			.on( 'change', '.wc-stamps-rates input[type=checkbox]', function() {
				if ( jQuery(this).data('disable_addons') ) {
					var disable = jQuery(this).data('disable_addons');

					jQuery( disable ).each(function( index, value ){
						jQuery('.wc-stamps-rates input[type=checkbox][data-type="' + value + '"]').removeAttr('checked');
					});

					// Disable sub addons
					if ( ! jQuery(this).is(':checked') ) {
						jQuery(this).closest('li').find('input[type=checkbox]').removeAttr('checked');
						jQuery(this).closest('li').find('ul').hide();
					} else {
						jQuery(this).closest('li').find('ul').show();
					}
				}
			});

		jQuery('#wc_stamps_get_label').trigger( 'init' );
	});
</script>
