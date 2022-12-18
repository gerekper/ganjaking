<?php
/**
 * Pagination - Show numbered pagination for catalog pages
 *
 * @version     3.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query, $porto_settings, $porto_layout;

$builder_id = porto_check_builder_condition( 'shop' );

if ( $porto_settings['category-item'] ) {
	$per_page = explode( ',', $porto_settings['category-item'] );
} else {
	$per_page = explode( ',', '12,24,36' );
}

if ( $builder_id && empty( $_GET['count'] ) ) {
	$page_count = '';
} else {
	$page_count = porto_loop_shop_per_page();
}

$total = isset( $total ) ? $total : $wp_query->max_num_pages;

echo '<nav class="woocommerce-pagination' . ( isset( $porto_settings['product-infinite'] ) && 'load_more' == $porto_settings['product-infinite'] ? ' pagination load-more' : '' ) . '">';

	?>
	<form class="woocommerce-viewing" method="get">

		<label><?php esc_html_e( 'Show', 'woocommerce' ); ?>: </label>

		<select name="count" class="count">
		<?php if ( $builder_id ) : ?>
			<option value="" <?php selected( $page_count, '' ); ?>><?php esc_html_e( 'Default', 'porto' ); ?></option>
		<?php endif; ?>
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

if ( $total <= 1 || ( isset( $porto_settings['shop_pg_type'] ) && 'none' != $porto_settings['shop_pg_type'] ) ) {
	echo '</nav>';
	return;
}

	$size_count = 3;

if ( in_array( $porto_layout, porto_options_sidebars() ) ) {
	$size_count = 2;
}

	echo paginate_links(
		apply_filters(
			'woocommerce_pagination_args',
			array(
				'base'      => isset( $base ) ? $base : esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
				'format'    => isset( $format ) ? $format : '',
				'add_args'  => false,
				'current'   => max( 1, isset( $current ) ? $current : get_query_var( 'paged' ) ),
				'total'     => $total,
				'prev_text' => '',
				'next_text' => isset( $porto_settings['product-infinite'] ) && 'load_more' == $porto_settings['product-infinite'] ? esc_html__( 'Load More...', 'porto' ) : '',
				'type'      => 'list',
				'end_size'  => $size_count,
				'mid_size'  => floor( $size_count / 2 ),
			)
		)
	);
	?>
</nav>
