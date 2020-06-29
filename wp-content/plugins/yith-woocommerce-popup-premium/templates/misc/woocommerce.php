<?php

$args = array(
	'post_type'        => 'product',
	'posts_per_page'   => 1,
	'orderby'          => 'rand',
	'suppress_filters' => false,
);


switch ( $product_from ) {
	case 'product':
		if ( ! empty( $products ) ) {
			$args['post__in'] = $products;
		}
		break;
	case 'category':
		if ( ! empty( $category ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $category,
					'operator' => 'IN',
				),
			);
		}
		break;
	case 'onsale':
		$args['post__in'] = wc_get_product_ids_on_sale();
		break;
	case 'featured':
		$args['post__in'] = wc_get_featured_product_ids();
		break;

	default:
}
global $yit_current_post;
$products = get_posts( $args );
if ( empty( $products ) ) {
	return;
}

$product_id = 0;
foreach ( $products as $product ) {
	$product_id = $product->ID;
}

$product      = wc_get_product( $product_id );
$redirect_url = '';

if ( $redirect_opt == 'home' ) {
	$redirect_url = home_url();
} elseif ( $redirect_opt == 'same_page' ) {
	$redirect_url = get_the_permalink( $yit_current_post );
} elseif ( $redirect_opt == 'product_page' ) {
	$redirect_url = get_the_permalink( $product_id );
}


if ( $product->is_type( 'variable' ) ) {
	$add_to_cart_label = $product->add_to_cart_text();
}

$classes = implode(
	' ',
	array_filter(
		array(
			'btn btn-flat btn-yit-popup',
			'button',
			$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
			$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
		)
	)
);

$yit_addtocart_url = sprintf(
	'<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" data-page_redirect="%s" class="%s">%s</a>',
	esc_url( $product->add_to_cart_url() ),
	1,
	esc_attr( $product_id ),
	esc_attr( $product->get_sku() ),
	$redirect_url,
	esc_attr( $classes ),
	esc_html( $add_to_cart_label )
);


$image_id = $product->get_image_id();
$image    = '';
if ( $image_id == 0 ) {
	$image = sprintf( '<img src="%s" alt="%s" />', esc_url( wc_placeholder_img_src() ), esc_html( __( 'Placeholder', 'woocommerce' ) ) );
} else {

	$image = sprintf( '<img src="%s" alt="%s" />', esc_url( wp_get_attachment_url( $image_id ) ), wp_kses_post( $product->get_title() ) );
}
?>

<div class="ypop-product-wrapper woocommerce">
	<?php if ( $show_title ) : ?>
		<h4><a href="<?php echo esc_url( get_the_permalink( $product_id ) ); ?>"><?php echo wp_kses_post( $product->get_title() ); ?></a></h4>
	<?php endif ?>
	<?php if ( $show_thumbnail ) : ?>
		<div class="ypop-woo-thumb">
			<figure id="yit-popup-image"><a href="<?php echo esc_url( get_the_permalink( $product_id ) ); ?>"><?php echo $image; //phpcs:ignore ?></a></figure>
		</div>
	<?php endif ?>
	<div class="product-info">
	<?php if ( $show_price ) : ?>
		<div class="price"><?php echo $product->get_price_html(); //phpcs:ignore ?></div>
	<?php endif ?>
	<?php if ( $show_add_to_cart ) : ?>
		<div class="add_to_cart"><?php echo $yit_addtocart_url; //phpcs:ignore ?></div>
	<?php endif ?>

	<?php if ( $show_summary ) : ?>
		<div class="summary"><?php echo method_exists( $product, 'get_short_description' ) ? $product->get_short_description() : $product->post->post_excerpt; //phpcs:ignore ?></div>
	<?php endif ?>
	</div>
</div>
