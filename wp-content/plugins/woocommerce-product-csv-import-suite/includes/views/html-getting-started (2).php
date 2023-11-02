<div id="message" class="updated woocommerce-message wc-connect">
	<div class="squeezer">
		<h4>
			<?php
			// translators: $1 and $2: opening and closing string tags, $3: mdash character.
			printf( esc_html__( '%1$sProduct CSV Import Suite%2$s %3$s Before getting started prepare your CSV files', 'woocommerce-product-csv-import-suite' ), '<strong>', '</strong>', '&#8211;' );
			?>
			</h4>

		<p class="submit"><a href="http://docs.woothemes.com/documentation/plugins/woocommerce/woocommerce-extensions/product-csv-import-suite/" class="button-primary" target="_blank"><?php esc_html_e( 'Documentation', 'woocommerce-product-csv-import-suite' ); ?></a> <a class="docs button-primary" href="<?php echo esc_url( plugins_url( 'sample.csv', WC_PCSVIS_FILE ) ); ?>"><?php esc_html_e( 'Sample CSV', 'woocommerce-product-csv-import-suite' ); ?></a></p>
	</div>
</div>
