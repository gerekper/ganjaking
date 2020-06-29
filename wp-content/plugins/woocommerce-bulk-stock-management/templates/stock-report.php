<?php
if ( ! is_user_logged_in() || ! current_user_can( 'edit_others_products' ) ) {
	wp_die( 'This page is private.' );
}

function show_stock_report_row( $post, $product, $nested = false ) {
	if ( ! $post ) {
		return;
	}
	?>
	<tr>
		<?php if ( wc_product_sku_enabled() ) : ?>
			<td><?php echo $product->get_sku(); ?></td>
		<?php endif; ?>
		<td>
			<?php

			if ( ! $nested ) {
				$post_title = $post->post_title;
			} else {
				$post_title = '';
			}

			// Get variation data
	        if ( $product->is_type( 'variation' ) ) {
	        	$post_title .= ' &mdash; <small><em>';

	        	$list_attributes = array();
	        	$attributes = $product->get_variation_attributes();

	        	foreach ( $attributes as $name => $attribute ) {
	        		if ( function_exists( 'wc_attribute_label' ) ) {
	        			$list_attributes[] = wc_attribute_label( str_replace( 'attribute_', '', $name ) ) . ': <strong>' . $attribute . '</strong>';
	        		} else {
	        			$list_attributes[] = wc_attribute_label( str_replace( 'attribute_', '', $name ) ) . ': <strong>' . $attribute . '</strong>';
	        		}
	        	}

	        	$post_title .= implode( ', ', $list_attributes );

	        	$post_title .= '</em></small>';
	        }

	        echo $post_title;

		?></td>
		<td><?php echo $post->ID; ?></td>
		<td><?php echo 'product' === $post->post_type ? 'Product' : 'Variation'; ?></td>
		<td><?php echo wc_price( $product->get_price() ); ?></td>
		<td><?php echo wc_stock_amount( $product->get_stock_quantity() ); ?></td>
	</tr>
	<?php
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php _e( 'Stock Report' ); ?></title>
		<style>
			body { background:white; color:black; width: 95%; margin: 0 auto; }
			table { border: 1px solid #000; width: 100%; }
			table td, table th { border: 1px solid #000; padding: 6px; }
			p.date { float: right; }
		</style>
	</head>
	<body>
		<header>
			<p class="date"><?php echo date_i18n( get_option( 'date_format' ), current_time( 'timestamp' ) ); ?></p>
			<h1 class="title"><?php _e( 'Stock Report', 'woocommerce-bulk-stock-management' ); ?></h1>
		</header>
		<section>
		<table cellspacing="0" cellpadding="2">
			<thead>
				<tr>
					<?php if ( wc_product_sku_enabled() ) : ?>
						<th scope="col" style="text-align:left;"><?php _e( 'SKU', 'woocommerce-bulk-stock-management' ); ?></th>
					<?php endif; ?>
					<th scope="col" style="text-align:left;"><?php _e( 'Product', 'woocommerce-bulk-stock-management' ); ?></th>
					<th scope="col" style="text-align:left;"><?php _e( 'ID', 'woocommerce-bulk-stock-management' ); ?></th>
					<th scope="col" style="text-align:left;"><?php _e( 'Type', 'woocommerce-bulk-stock-management' ); ?></th>
					<th scope="col" style="text-align:left;"><?php _e( 'Unit Retail Price', 'woocommerce-bulk-stock-management' ); ?></th>
					<th scope="col" style="text-align:left;"><?php _e( 'Stock Qty', 'woocommerce-bulk-stock-management' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$post_ids = array( 0 );
				$variation_ids = array( 0 );

				$meta_query = array();

				$meta_query[] = array(
					'key'	=> '_manage_stock',
					'value'	=> 'yes',
				);

				if ( wc_product_sku_enabled() ) {
					$orderby  = 'meta_value title id';
				} else {
					$orderby  = 'id';
				}

				/**
				 * Find ID's of posts managing stock
				 */
				$product_ids = get_posts( array(
					'post_type'      => 'product',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'fields'         => 'ids',
					'meta_query'     => $meta_query,
					'orderby'        => esc_attr( $orderby ),
					'order'          => 'asc',
				) );

				$meta_query = array();

				$meta_query[] = array(
					'key'     => '_stock',
					'value'   => array( '', null ),
					'compare' => 'NOT IN',
				);

				/**
				 * Find ID's of variations managing stock
				 */
				$variation_ids = get_posts( array(
					'post_type'      => 'product_variation',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'fields'         => 'id=>parent',
					'meta_query'     => $meta_query,
					'orderby'        => esc_attr( $orderby ),
					'order'          => 'asc',
				) );

				foreach ( $product_ids as $post_id ) {

				    $product = wc_get_product( $post_id );

					// In order to keep backwards compatibility it's required to use the parent data for variations.
					if ( $product->is_type( 'variation' ) ) {
						$product_post = get_post( $product->get_parent_id() );
					} else {
						$product_post = get_post( $product->get_id() );
					}

					show_stock_report_row( $product_post, $product );

					foreach ( $variation_ids as $var_id => $parent ) {
						if ( $parent == $product_post->ID ) {

							$variation = wc_get_product( $var_id );
							$variation_post = get_post( $var_id );

							unset( $variation_ids[ $var_id ] );

							show_stock_report_row( $variation_post, $variation, true );
						}
					}
				}

				foreach ( $variation_ids as $var_id => $parent ) {
					$variation = wc_get_product( $var_id );
					$variation_post = get_post( $var_id );

					show_stock_report_row( $variation_post, $variation );
				}
			?>
			</tbody>
		</table>
	</body>
</html>
