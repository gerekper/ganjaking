<?php
/**
 * The post category default template
 *
 * @since      6.4.0
 */
defined( 'ABSPATH' ) || die;

global $porto_post_image_size;

if ( empty( $porto_post_image_size ) ) {
	$porto_post_image_size = 'full';
}

$term = get_queried_object();
$cat_img_id = porto_get_image_id( esc_url( get_metadata( $term->taxonomy, $term->term_id, 'category_image', true ) ) );
$post_class = 'porto-cat';
if ( ! empty( $post_classes ) ) {
	$post_class .= ' ' . trim( $post_classes );
}
?>
<div class="<?php echo esc_attr( $post_class ); ?>">
<?php if ( $cat_img_id ) : ?>
	<figure>
		<?php echo wp_get_attachment_image( $cat_img_id, $porto_post_image_size ); ?>
	</figure>
<?php endif; ?>
<h3 class="porto-post-title"><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php esc_html( $term->name ); ?></a></h3>
</div>
