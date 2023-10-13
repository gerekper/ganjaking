<div class="wrap">
    <h2><?php esc_html_e( 'Manage product feeds', 'woocommerce_gpf' ); ?>
        <a href="{add_link}" class="add-new-h2"><?php esc_html_e( 'Add New', 'woocommerce_gpf' ); ?></a>
    </h2>
    <p>
		<?php esc_html_e( "Use this page to manage your feed URLs, including the types of feeds you want to generate, and any restrictions on those feeds.", 'woocommerce_gpf' ); ?>
    </p>
    <p>
		<?php printf(
			esc_html(
                    'To control how data is populated into feeds, use the %1$smain extension settings%2$s page.',
                    'woocommerce_gpf'
            ),
            '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=gpf' ) . '">', // phpcs:ignore
            '</a>'
		); ?>
    </p>
