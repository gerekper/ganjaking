<p id="gpf_currency_container">
    <label for="gpf_currency"><?php _e( 'Currency', 'woocommerce_gpf' ); ?></label><br>
    <select id="gpf_currency">
        <?php foreach ($args['currencies'] as $currency_code => $currency_name) : ?>
            <option value="<?php echo esc_attr($currency_code); ?>" <?php selected ( $args['currency'] ?? '', $currency_code ); ?>><?php echo esc_html( $currency_name ); ?></option>
        <?php endforeach; ?>
    </select>
</p>

