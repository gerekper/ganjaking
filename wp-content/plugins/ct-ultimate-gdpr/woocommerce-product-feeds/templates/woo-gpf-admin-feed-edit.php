<div class="wrap">
    <h2>{page_header}</h2>
    <form action="admin.php?page=woocommerce-gpf-manage-feeds&gpf_action=update" method="POST">
		<?php wp_nonce_field( 'gpf_update_feed' ); ?>
        <input type="hidden" name="feed_id" value="{feed_id}">
        <p>
            <label><?php _e( 'Your name for this feed', 'woocommerce_gpf' ); ?></label><br>
            <input type="text" class="widefat" name="name" value="{name}"
                   placeholder="<?php _e( 'Enter a name for this feed (for your use only)', 'woocommerce_gpf' ); ?>"
                   required>
        </p>
        <p>
            <label><?php _e( 'Feed type', 'woocommerce_gpf' ); ?></label><br>
            <select name="type" id="feed_type">
				<?php foreach ( $args['types'] as $type_id => $type_def ) : ?>
                    <option value="<?php echo esc_attr( $type_id ); ?>" <?php selected( $args['type'], $type_id ); ?>><?php echo esc_html( $type_def['name'] ); ?></option>
				<?php endforeach ?>
            </select>
        </p>
        <p id="category_filter_container">
            <label for="category_filter"><?php _e( 'Category filtering', 'woocommerce_gpf' ); ?></label><br>
            <select name="category_filter" id="category_filter">
                <option value="" <?php selected( $args['category_filter'], '' ); ?>><?php _e( 'Include products from ALL categories', 'woocommerce_gpf' ); ?></option>
                <option value="only" <?php selected( $args['category_filter'], 'only' ); ?>><?php _e( 'Include products ONLY from&hellip;', 'woocommerce_gpf' ); ?></option>
                <option value="except" <?php selected( $args['category_filter'], 'except' ); ?>><?php _e( 'Include all products EXCEPT from&hellip;', 'woocommerce_gpf' ); ?></option>
            </select>
        </p>
        <p id="category_container">
            <select name="categories[]" id="category_selector" style="width: 75%" multiple></select>
        </p>
        <p id="prf_limit_container">
            <label for="limit"><?php _e( 'Which reviews to include', 'woocommerce_gpf' ); ?></label><br>
            <select id="limit">
                <option value="" <?php selected( $args['limit'], '' ); ?>><?php _e( 'All reviews', 'woocommerce_gpf' ); ?></option>
                <option value="week" <?php selected( $args['limit'], 'week' ); ?>><?php _e( 'Reviews in the last 7 days', 'woocommerce_gpf' ); ?></option>
                <option value="yesterday" <?php selected( $args['limit'], 'yesterday' ); ?>><?php _e( 'Reviews yesterday', 'woocommerce_gpf' ); ?></option>
            </select>
        </p>
		<?php do_action( 'woocommerce_gpf_feed_edit_page', $args['feed'], $args ); ?>
        <p>
            <input type="submit" name="save" value="<?php _e( 'Save', 'woocommerce_gpf' ); ?>"
                   class="button button-primary">
        </p>
    </form>
    <script>
		jQuery( function () {
			function woocommerce_gpf_visibility_updates() {
				var feed_type = jQuery( '#feed_type' ).val();
				var category_filter = jQuery( '#category_filter' ).val();

				// Hide everything, and remove all INPUT names & required flags first.
				jQuery( '#category_filter_container' ).hide();
				jQuery( '#category_filter' ).attr( 'name', '' );

				jQuery( '#category_container' ).hide();
				jQuery( '#category_selector' ).attr( 'name', '' );
				jQuery( '#category_selector' ).removeAttr( 'required' );

				jQuery( '#prf_limit_container' ).hide();
				jQuery( 'select#limit' ).attr( 'name', '' );

				// Show #category_filter_container for relevant feed types.
				if ( feed_type !== 'googlereview' ) {
					jQuery( '#category_filter_container' ).show();
					jQuery( '#category_filter' ).attr( 'name', 'category_filter' );
					// Also show category_container if category filter is selected.
					if ( category_filter !== '' ) {
						jQuery( '#category_container' ).show();
						jQuery( '#category_selector' ).attr( 'name', 'categories[]' );
						jQuery( '#category_selector' ).attr( 'required', 'required' );
					}
				}

				// Show the prf_limit_container for review feeds.
				if ( feed_type === 'googlereview' ) {
					jQuery( '#prf_limit_container' ).show();
					jQuery( 'select#limit' ).attr( 'name', 'limit' );
				}
			}

			jQuery( '#category_selector' ).selectWoo( {
														  data: <?php echo wp_json_encode( $args['categories'] ); ?>,
														  placeholder: <?php echo json_encode( __( 'Choose one or more categories', 'woocommerce_gpf' ) ); ?>
													  } );

			jQuery( document ).on( 'change', '#feed_type, #category_filter', woocommerce_gpf_visibility_updates );
			woocommerce_gpf_visibility_updates();
		} );
    </script>
</div>
