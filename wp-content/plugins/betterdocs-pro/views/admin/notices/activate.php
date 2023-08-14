<div class="error notice">
	<p>
		<?php
            printf(
                __( '<strong>BetterDocs Pro</strong> plugin requires <strong>BetterDocs</strong> plugin to be installed. Please <strong><em>Install & Activate</em></strong> the BetterDocs plugin to access all the features.', 'betterdocs-pro' )
            )
        ?>
        <br />
        <a
            href="<?php esc_attr_e( esc_url( $button_url )); ?>"
            id="betterdocs-install-core" style="margin-top: 10px" class="button button-primary">
            <?php echo $button_text; ?>
        </a>
    </p>
</div>
