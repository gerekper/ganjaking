<?php
$ywot_options     = get_option( 'ywot_carriers' );
$items_per_column = 6;
$carriers         = Carriers::get_instance()->get_carrier_list();
$index            = 0;

?>

<tr>
	<td colspan="2" class="check-all">
		<label>
			<input type="checkbox" name="select_all" id="select_all"
			       value="1" onClick="toggle(this)"/><?php _e( "Select/Unselect all", 'yith-woocommerce-order-tracking' ); ?>
		</label>
	</td>
</tr>

<div class="search-for" style="display: block; float: left; margin: 0 0 15px;">
    <span class="dashicons dashicons-search"></span>
    <input type="text" id="search-for-carriers" placeholder="<?php _e ( 'Type to search...', 'yith-woocommerce-order-tracking' )?>">
</div>

<div style="display:none; margin: 35px 0;" id="noresults"><?php _e( 'Sorry, no carriers matching your terms...', 'yith-woocommerce-order-tracking' ); ?></div>

<?php foreach ( $carriers as $key => $value ) : ?>

	<?php if ( ( $index % $items_per_column ) == 0 ) : ?>
        <tr>
	<?php endif; ?>
    <td class="carrier" style="vertical-align: top;" data-carrier="<?php echo $key; ?>">
        <label for="<?php echo $key; ?>">
            <input type="checkbox" name="ywot_carriers[<?php echo $key; ?>]" id="<?php echo $key; ?>"
                   value="1" <?php is_checked_html( $ywot_options, $key ); ?> /><?php echo $value['name']; ?>
        </label>
    </td>
	<?php if ( ( ( $index + 1 ) % $items_per_column ) == 0 ) : ?>
        </tr>
	<?php endif;
	$index ++;
	?>
<?php endforeach; ?>

<script type="text/javascript">
    function toggle(source) {
        checkboxes = document.getElementsByTagName('input');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }


    (function ($) {
        "use strict";
        var nores = $('#noresults'),
            carrier = $("td.carrier"),
            search = $('input#search-for-carriers'),
            count = 0;
        // Author code here
        search.on('keyup change', function (e) {
            nores.hide();
            var filter = $(this).val(), count = 0;
            carrier.each(function () {
                if ($(this).attr('data-carrier').search(new RegExp(filter, "i")) < 0) {
                    $(this).fadeOut();
                    $('.check-all').fadeOut();
                } else {
                    count++;
                    $(this).fadeIn();
                }
            });

            if (count === 0) {
                nores.fadeIn();
            }
            if (isEmpty(search)) {
                $('.check-all').fadeIn();
            }
        });


    })(jQuery);


</script>