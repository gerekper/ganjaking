<input name="_woocommerce_gpf_data[{key}]" class="woocommerce_gpf_product_type_{raw_key} woocommerce-gpf-store-default" value="{current_data}" style="width: 750px;"{placeholder}>
<p class="help-text"><small><?php _e( 'Start typing to see suggestions from the official Google taxonomy', 'woocommerce_gpf' ); ?>. The following localised taxonomies will be searched: {locale_list}</small></p>
<script type="text/javascript">
	jQuery(document).ready(function(){
			jQuery('.woocommerce_gpf_product_type_{raw_key}').wooautocomplete( { minChars: 3, deferRequestBy: 5, serviceUrl: 'index.php?woocommerce_gpf_search=true' } );
	});
</script>
