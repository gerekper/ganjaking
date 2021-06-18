<div class="wrap">
    <h2><?php _e( 'Manage product feeds', 'woocommerce_gpf' ); ?>
        <a href="{add_link}" class="add-new-h2"><?php _e( 'Add New', 'woocommerce_gpf' ); ?></a>
    </h2>
    <p>
		<?php _e( "Use this page to manage your feed URLs, including the types of feeds you want to generate, and any restrictions on those feeds.", 'woocommerce_gpf' ); ?>
    </p>
    <p>
		<?php printf(
			__( "To control how data is populated into feeds, use the <a href='%s'>main extension settings</a> page.", 'woocommerce_gpf' ),
			admin_url( 'admin.php?page=wc-settings&tab=gpf' )
		); ?>
    </p>
