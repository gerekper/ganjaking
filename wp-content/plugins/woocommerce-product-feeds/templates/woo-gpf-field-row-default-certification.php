<small class="help-text"><?php esc_html_e( 'Provide a valid code for this product as per the EU EPREL database.', 'woocommerce_gpf' ); ?></small>
</p>
<p><select name="_woocommerce_gpf_data[{key}][0][certification_authority]" class="woocommerce-gpf-store-default woocommerce-gpf-store-default-{raw_key}">
    <option value="EC"><?php esc_html_e( 'European commission (EC)', 'woocommerce_gpf' ); ?></option>
</select>
<select name="_woocommerce_gpf_data[{key}][0][certification_name]" class="woocommerce-gpf-store-default woocommerce-gpf-store-default-{raw_key}">
    <option value="EPREL"><?php esc_html_e( 'European Registry for Energy Labeling (EPREL)', 'woocommerce_gpf' ); ?></option>
</select>
<input type="text" name="_woocommerce_gpf_data[{key}][0][certification_code]" class="woocommerce-gpf-store-default" value="{certification_code}" {placeholder}>
