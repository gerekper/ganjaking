	</table>

	{include_variations}

	{send_item_group_id}

    {expanded_schema}

    {shop_code}

	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('.woocommerce_gpf_field_selector').change(function(){
				group = jQuery(this).parent('.woocommerce_gpf_field_selector_group');
				defspan = group.children('div');
				defspan.slideToggle('fast');
			});
			jQuery('#woocommerce_gpf_config\\[include_variations\\]').change(function() {
				var variations_included = this.checked;
				if ( variations_included ) {
					jQuery('#woocommerce_gpf_config\\[send_item_group_id\\]').parents('p').slideDown();
				} else {
					jQuery('#woocommerce_gpf_config\\[send_item_group_id\\]').parents('p').slideUp();
				}
			});
			if (jQuery('#woocommerce_gpf_config\\[include_variations\\]').get(0).checked) {
				jQuery('#woocommerce_gpf_config\\[send_item_group_id\\]').parents('p').show();
			} else {
				jQuery('#woocommerce_gpf_config\\[send_item_group_id\\]').parents('p').hide();
			}
		});
	</script>

