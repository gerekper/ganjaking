<?php
extract(
	shortcode_atts(
		array(
			'el_class' => '',
		),
		$atts
	)
);

if ( ! empty( $shortcode_class ) ) {
	$el_class = trim( $shortcode_class . ' ' . $el_class );
}

global $porto_settings;
if ( ! isset( $porto_settings['shop_pg_type'] ) || 'none' == $porto_settings['shop_pg_type'] ) {
	$class_suffix = $el_class;
	if ( isset( $porto_settings['product-infinite'] ) && 'infinite_scroll' == $porto_settings['product-infinite'] ) {
		$class_suffix .= ' d-none';
	} elseif ( isset( $porto_settings['product-infinite'] ) && 'load_more' == $porto_settings['product-infinite'] ) {
		$class_suffix .= ' load-more-wrap';
	}

	$is_preview = apply_filters( 'porto_shop_builder_set_preview', false );
	echo '<div class="' . trim( esc_attr( $el_class ) ) . '">';
		woocommerce_pagination();
	echo '</div>';
	$is_preview ? do_action( 'porto_shop_builder_unset_preview' ) : '';

} else {

	if ( $porto_settings['category-item'] ) {
		$per_page = explode( ',', $porto_settings['category-item'] );
	} else {
		$per_page = explode( ',', '12,24,36' );
	}

	if ( ! empty( $_GET['count'] ) ) {
		$page_count = porto_loop_shop_per_page();
	} else {
		$page_count = '';
	}

	?>

	<form class="woocommerce-viewing<?php echo ! $el_class ? '' : ' ' . esc_attr( $el_class ); ?>" method="get">

		<label><?php esc_html_e( 'Show', 'woocommerce' ); ?>: </label>

		<select name="count" class="count">
			<option value="" <?php selected( $page_count, '' ); ?>><?php esc_html_e( 'Default', 'porto-functionality' ); ?></option>
			<?php foreach ( $per_page as $count ) : ?>
				<option value="<?php echo esc_attr( $count ); ?>" <?php selected( $page_count, $count ); ?>><?php echo esc_html( $count ); ?></option>
			<?php endforeach; ?>
		</select>

		<input type="hidden" name="paged" value=""/>

		<?php

		// Keep query string vars intact
		foreach ( $_GET as $key => $val ) {
			if ( 'count' === $key || 'submit' === $key || 'paged' === $key ) {
				continue;
			}

			if ( is_array( $val ) ) {
				foreach ( $val as $innerVal ) {
					echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
				}
			} else {
				echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
			}
		}
		?>
	</form>
	<?php
}
