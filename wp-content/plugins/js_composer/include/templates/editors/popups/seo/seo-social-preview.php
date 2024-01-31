<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * @var array $seo_settings
 * @var WP_Post | null $post
 * @var int $post
 * @var int $post_id
 * @var Vc_Post_Seo $vc_post_seo
 * @var string $network_slug
 * @var string $network_name
 * @var int $image_id
 */
$title = $network_name . __( ' share preview', 'js_composer' );
$id = 'wpb-' . $network_slug . '-preview';
$src = $vc_post_seo->get_image_by_id( $image_id );
$display = empty( $src ) ? '' : 'display: none;';
?>

<div class="wpb_element_label"><?php echo esc_html( $title ); ?></div>
<div id="<?php echo esc_attr( $id ); ?>" class="wpb-social-net-preview">
	<img src="<?php echo esc_attr( $src ); ?>" alt="">

	<div class="wpb-social-placeholder-image" style="<?php echo esc_attr( $display ); ?>"></div>
	<div class="wpb-preview-content">
		<p class="wpb-preview-author">
			<a href="#">
				<?php echo esc_html( $vc_post_seo->base_url( $post_id ) ); ?>
			</a>
		</p>
		<p class="vc_social-title line-clamp line-clamp-2">
			<?php
			if ( empty( $seo_settings[ 'social-title-' . strtolower( $network_name ) ] ) ) {
				esc_html_e( 'Example Title', 'js_composer' );
			} else {
				echo esc_html( $seo_settings[ 'social-title-' . strtolower( $network_name ) ] );
			}
			?>
		</p>
		<p class="vc_social-description truncate">
			<?php
			if ( empty( $seo_settings[ 'social-description-' . strtolower( $network_name ) ] ) ) {
				esc_html_e( 'Example description', 'js_composer' );
			} else {
				echo esc_html( $seo_settings[ 'social-description-' . strtolower( $network_name ) ] );
			}
			?>
		</p>
	</div>
</div>
