<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="product_addons_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
	<?php do_action( 'woocommerce-product-addons_panel_start' ); ?>

	<p class="woocommerce-product-add-ons-toolbar woocommerce-product-add-ons-toolbar--open-close toolbar">
		<a href="#" class="close_all"><?php _e( 'Close all', 'woocommerce-product-addons' ); ?></a> / <a href="#" class="expand_all"><?php _e( 'Expand all', 'woocommerce-product-addons' ); ?></a>
	</p>

	<div class="woocommerce_product_addons wc-metaboxes">

		<?php
			$loop = 0;

			foreach ( $product_addons as $addon ) {
				include( dirname( __FILE__ ) . '/html-addon.php' );

				$loop++;
			}
		?>

	</div>

	<div class="woocommerce-product-add-ons-toolbar woocommerce-product-add-ons-toolbar--add-import-export toolbar">
		<button type="button" class="button add_new_addon"><?php _e( 'New add-on', 'woocommerce-product-addons' ); ?></button>

		<div class="woocommerce-product-add-ons-toolbar__import-export">
			<button type="button" class="button import_addons"><?php _e( 'Import', 'woocommerce-product-addons' ); ?></button>
			<button type="button" class="button export_addons"><?php _e( 'Export', 'woocommerce-product-addons' ); ?></button>
		</div>

		<textarea name="export_product_addon" class="export" cols="20" rows="5" readonly="readonly"><?php echo esc_textarea( serialize( $product_addons ) ); ?></textarea>

		<textarea name="import_product_addon" class="import" cols="20" rows="5" placeholder="<?php _e('Paste exported form data here and then save to import fields. The imported fields will be appended.', 'woocommerce-product-addons'); ?>"></textarea>

	</div>
	<?php if ( $exists ) : ?>
		<div class="options_group">
			<p class="form-field">
			<label for="_product_addons_exclude_global"><?php _e( 'Global Addon Exclusion', 'woocommerce-product-addons' ); ?></label>
			<input id="_product_addons_exclude_global" name="_product_addons_exclude_global" class="checkbox" type="checkbox" value="1" <?php checked( $exclude_global, 1 ); ?>/><span class="description"><?php _e( 'Check this to exclude this product from all Global Addons', 'woocommerce-product-addons' ); ?></span>
			</p>
		</div>
	<?php endif; ?>
</div>
<?php
$empty_name_message = __( 'All addon fields require a name.', 'woocommerce-product-addons' );
?>
<script type="text/javascript">
	jQuery(function( $ ){
		$( '.product_page_global_addons' ).on( 'click', 'input[type="submit"]', function( e ) {
			// Loop through all addons to validate them.
			$( '.woocommerce_product_addons' ).find( '.woocommerce_product_addon' ).each( function() {
				if ( 0 === $( this ).find( '.addon_name input' ).val().length ) {
					e.preventDefault();

					alert( '<?php echo $empty_name_message; ?>' );

					return false;
				}
			});
		});

		jQuery('#product_addons_data')
		.on( 'change', '.addon_name input', function() {
			if ( jQuery(this).val() )
				jQuery(this).closest('.woocommerce_product_addon').find('span.group_name').text( '"' + jQuery(this).val() + '"' );
			else
				jQuery(this).closest('.woocommerce_product_addon').find('span.group_name').text('');
		})
		.on( 'change', 'select.product_addon_type', function() {

			var value = jQuery(this).val();

			if ( value == 'custom' || value == 'custom_price' || value == 'custom_textarea' || value == 'input_multiplier' || value == 'custom_letters_only' || value == 'custom_digits_only' || value == 'custom_letters_or_digits' ) {
				jQuery(this).closest('.woocommerce_product_addon').find('td.minmax_column, th.minmax_column').show();
			} else {
				jQuery(this).closest('.woocommerce_product_addon').find('td.minmax_column, th.minmax_column').hide();
			}

			if ( value == 'custom_price' ) {
				jQuery(this).closest('.woocommerce_product_addon').find('td.price_column, th.price_column').hide();
			} else {
				jQuery(this).closest('.woocommerce_product_addon').find('td.price_column, th.price_column').show();
			}

			// Switch up the column title, based on the field type selected
			switch ( value ) {
				case 'custom_price':
					column_title = '<?php echo esc_js( __( 'Min / max price', 'woocommerce-product-addons' ) ); ?>';
				break;

				case 'input_multiplier':
					column_title = '<?php echo esc_js( __( 'Min / max multiplier', 'woocommerce-product-addons' ) ); ?>';
				break;

				case 'custom_textarea':
				case 'custom_letters_only':
				case 'custom_digits_only':
				case 'custom_letters_or_digits':
				case 'custom_email':
				case 'custom':
					column_title = '<?php echo esc_js( __( 'Min / max characters', 'woocommerce-product-addons' ) ); ?>';
				break;

				default:
					column_title = '<?php echo esc_js( __( 'Min / max', 'woocommerce-product-addons' ) ); ?>';
				break;
			}

			jQuery(this).closest('.woocommerce_product_addon').find('th.minmax_column .column-title').replaceWith( '<span class="column-title">' + column_title + '</span>' );

			// Count the number of options.  If one (or less), disable the remove option buttons
			var removeAddOnOptionButtons = jQuery(this).closest('.woocommerce_product_addon').find('button.remove_addon_option');
			if ( 2 > removeAddOnOptionButtons.length ) {
				removeAddOnOptionButtons.attr('disabled', 'disabled');
			} else {
				removeAddOnOptionButtons.removeAttr('disabled');
			}
		})
		.on( 'click', 'button.add_addon_option', function() {

			var loop = jQuery(this).closest('.woocommerce_product_addon').index('.woocommerce_product_addon');

			var html = '<?php
				ob_start();

				$option = Product_Addon_Admin::get_new_addon_option();
				$loop = "{loop}";

				include( dirname( __FILE__ ) . '/html-addon-option.php' );

				$html = ob_get_clean();
				echo str_replace( array( "\n", "\r" ), '', str_replace( "'", '"', $html ) );
			?>';

			html = html.replace( /{loop}/g, loop );

			jQuery(this).closest('.woocommerce_product_addon .data').find('tbody').append( html );

			jQuery('select.product_addon_type').change();

			return false;
		})
		.on( 'click', '.add_new_addon', function() {

			var loop = jQuery('.woocommerce_product_addons .woocommerce_product_addon').size();
			var total_add_ons = jQuery( '.woocommerce_product_addons .woocommerce_product_addon' ).length;

			if ( total_add_ons >= 1 ) {
				jQuery( '.woocommerce-product-add-ons-toolbar--open-close' ).show();
			}

			var html = '<?php
				ob_start();

				$addon['name']          = '';
				$addon['description']   = '';
				$addon['required']      = '';
				$addon['type']          = 'checkbox';
				$addon['options']       = array(
					Product_Addon_Admin::get_new_addon_option()
				);
				$loop = "{loop}";

				include( dirname( __FILE__ ) . '/html-addon.php' );

				$html = ob_get_clean();
				echo str_replace( array( "\n", "\r" ), '', str_replace( "'", '"', $html ) );
			?>';

			html = html.replace( /{loop}/g, loop );

			jQuery('.woocommerce_product_addons').append( html );

			jQuery('select.product_addon_type').change();

			return false;
		})
		.on( 'click', '.remove_addon', function() {

			var answer = confirm('<?php _e('Are you sure you want remove this add-on?', 'woocommerce-product-addons'); ?>');

			if (answer) {
				var addon = jQuery(this).closest('.woocommerce_product_addon');
				jQuery(addon).find('input').val('');
				jQuery(addon).remove();
			}
			
			jQuery( '.woocommerce_product_addons .woocommerce_product_addon' ).each( function( index, el ) {
				var this_index = index;

				jQuery( this ).find( '.product_addon_position' ).val( this_index );
				jQuery( this ).find( 'select, input, textarea' ).prop( 'name', function( i, val ) {
					var field_name = val.replace( /\[[0-9]+\]/g, '[' + this_index + ']' );

					return field_name;
				} );
			} );

			return false;
		})
		.on( 'click', '.remove_addon_option', function() {

			var answer = confirm( '<?php echo esc_js( __( 'Are you sure you want delete this option?', 'woocommerce-product-addons' ) ); ?>' );

			if ( answer ) {
				var addOn = jQuery( this ).closest( '.woocommerce_product_addon' );
				jQuery( this ).closest( 'tr' ).remove();
				addOn.find( 'select.product_addon_type' ).change();
			}

			return false;

		} )
		.find('select.product_addon_type').change();

		// Import / Export
		jQuery('#product_addons_data').on('click', '.export_addons', function() {

			jQuery('#product_addons_data textarea.import').hide();
			jQuery('#product_addons_data textarea.export').slideToggle('500', function() {
				jQuery(this).select();
			});

			return false;
		});

		jQuery('#product_addons_data').on('click', '.import_addons', function() {

			jQuery('#product_addons_data textarea.export').hide();
			jQuery('#product_addons_data textarea.import').slideToggle('500', function() {
				jQuery(this).val('');
			});

			return false;
		});

		// Sortable
		jQuery('.woocommerce_product_addons').sortable({
			items:'.woocommerce_product_addon',
			cursor:'move',
			axis:'y',
			handle:'h3',
			scrollSensitivity:40,
			helper:function(e,ui){
				return ui;
			},
			start:function(event,ui){
				ui.item.css('border-style','dashed');
			},
			stop:function(event,ui){
				ui.item.removeAttr('style');
				addon_row_indexes();
			}
		});

		function addon_row_indexes() {
			jQuery('.woocommerce_product_addons .woocommerce_product_addon').each(function(index, el){ jQuery('.product_addon_position', el).val( parseInt( jQuery(el).index('.woocommerce_product_addons .woocommerce_product_addon') ) ); });
		};

		// Sortable options
		jQuery('.woocommerce_product_addon .data table tbody').sortable({
			items:'tr',
			cursor:'move',
			axis:'y',
			scrollSensitivity:40,
			helper:function(e,ui){
				ui.children().each(function(){
					jQuery(this).width(jQuery(this).width());
				});
				return ui;
			},
			start:function(event,ui){
				ui.item.css('background-color','#f6f6f6');
			},
			stop:function(event,ui){
				ui.item.removeAttr('style');
			}
		});

		// Remove option
		jQuery('button.remove_addon_option').on('click', function(){

			var answer = confirm('<?php _e('Are you sure you want delete this option?', 'woocommerce-product-addons'); ?>');

			if (answer) {
				var addOn = jQuery(this).closest('.woocommerce_product_addon');
				jQuery(this).closest('tr').remove();
				addOn.find('select.product_addon_type').change();
			}

			return false;

		});

		// Show / hide expand/close
		var total_add_ons = jQuery( '.woocommerce_product_addons .woocommerce_product_addon' ).length;
		if ( total_add_ons > 1 ) {
			jQuery( '.woocommerce-product-add-ons-toolbar--open-close' ).show();
		}

	});
</script>
