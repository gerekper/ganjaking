<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email );

$gmt_offset = get_option( 'gmt_offset' );

if ( 0 <= $gmt_offset )
	$offset_name = '+' . $gmt_offset;
else
	$offset_name = (string)$gmt_offset;

$offset_name = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
$offset_name = '(UTC' . $offset_name . ')';

$body = ! empty( $email->email_body ) ? $email->email_body : '';

// If there is only one product, is not considered as array.
if ( is_array( $email->object ) ) {
	?>
	<p><?php echo $body; ?></p>
	<div>
		<ul>
			<?php
			foreach ( $email->object as $key => $product ) {
                $pre_order_product = new YITH_Pre_Order_Product( $product->id );
				$date = $pre_order_product->get_for_sale_date();
				$id = yit_get_base_product_id( $pre_order_product->product );
				$post = get_post( $id );
				$post_type_object = get_post_type_object( $post->post_type );
				if ( ($post_type_object ) && ( $post_type_object->_edit_link )) {
					$link = admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $id ) );
				} else {
					$link = '';
				}
				?>
				<li>
					<a href="<?php echo $link; ?>"><?php echo $pre_order_product->product->get_title() ?></a> -
                    <span style="font-weight: bold"><?php echo $date . ' ' . $offset_name; ?></span>
					<div>
                        <?php
                        $dimensions = wc_get_image_size( 'shop_thumbnail' );
                        $height     = esc_attr( $dimensions['height'] );
                        $width      = esc_attr( $dimensions['width'] );
                        $src        = ( $pre_order_product->product->get_image_id() ) ? current( wp_get_attachment_image_src( $pre_order_product->product->get_image_id(), 'shop_catalog' ) ) : wc_placeholder_img_src();

                        $image = '<a href="' . $link .'"><img src="'. $src . '" height="' . $height . '" width="' . $width . '" /></a>';
                        echo $image;
                        ?>
                    </div>
				</li>
				<?php
			}
			?>
		</ul>
	</div>

	<?php
} else {
    $pre_order_product = new YITH_Pre_Order_Product( $email->object->id );
	$id = yit_get_base_product_id( $pre_order_product->product );
	$post = get_post( $id );
	$post_type_object = get_post_type_object( $post->post_type );
	if ( ($post_type_object ) && ( $post_type_object->_edit_link )) {
		$link = admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $id ) );
	} else {
		$link = '';
	}
	$date = $pre_order_product->get_for_sale_date();
	?>
	<p><?php echo $body; ?></p>
	<p><?php echo '<a href="' . $link . '">' . $pre_order_product->product->get_title() . '</a> - <span style="font-weight: bold">' . $date . ' ' . $offset_name . '</span>'; ?></p>
	<div>
        <?php
		$dimensions = wc_get_image_size( 'shop_thumbnail' );
		$height     = esc_attr( $dimensions['height'] );
		$width      = esc_attr( $dimensions['width'] );
		$src        = ( $pre_order_product->product->get_image_id() ) ? current( wp_get_attachment_image_src( $pre_order_product->product->get_image_id(), 'shop_catalog' ) ) : wc_placeholder_img_src();

		$image = '<a href="' . $link .'"><img src="'. $src . '" height="' . $height . '" width="' . $width . '" /></a>';
        echo $image;
        ?>
	</div>
	<?php
}

do_action( 'woocommerce_email_footer' );
