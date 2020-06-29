<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract( $field );

$link        = isset( $field['link_text'] ) ? $field['link_text'] : '';
$before_text = isset( $field['before_text'] ) ? $field['before_text'] : '';
$after_text  = isset( $field['after_text'] ) ? $field['after_text'] : '';
$post_id     = isset( $field['post_id'] ) ? $field['post_id'] : '';
?>
<style>
    .forminp-donation-product-link {
        font-size: 13px;
        font-style: italic;
        padding-left: 20px;
    }

</style>


<?php edit_post_link( $link, $before_text, $after_text, $post_id ); ?>

