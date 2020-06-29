<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$body             = ! empty( $email->email_body ) ? $email->email_body : '';
$product          = wc_get_product( $email->object );
$product_id       = yit_get_base_product_id( $product );
$product_name     = version_compare( WC()->version, '3.0.0', '<' ) ? $product->get_formatted_name() : $product->get_name();
$post             = get_post( $product_id );
$post_type_object = get_post_type_object( $post->post_type );
if ( ($post_type_object ) && ( $post_type_object->_edit_link )) {
	$link = admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $product_id ) );
} else {
	$link = '';
}
$link = empty( $link ) ? $product->get_title() : '<a href="' . $link . '">' . $product_name . '</a>';

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php

echo '<p>';
$body = str_replace(
	array(
		'{product_name}'
	),
	array(
		$link
	),
	$body
);
echo $body;
echo '</p>';

?>
<div>
    <?php
        echo $product->get_image();
    ?>
</div>

<?php do_action( 'woocommerce_email_footer' );