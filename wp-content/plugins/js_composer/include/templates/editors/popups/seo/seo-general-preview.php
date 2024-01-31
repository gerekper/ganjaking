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
 * @var string $permalink_structure
 */
?>

<div class="page-preview-container">
	<div class="page-preview">
		<div class="preview-title">
			<span class="dashicons dashicons-admin-site"></span>
			<div class="url-container">
				<p class="preview-name truncate">
					<b><?php esc_html_e( 'Example', 'js_composer' ); ?></b>
				</p>
				<div class="truncate url-container-inner">
					<span id="vc_seo-url">
						<?php echo esc_html( $vc_post_seo->base_url( $post_id ) ); ?>
					</span>
					<span> ></span>
					<span id="vc_seo-slug">
						<?php echo empty( $permalink_structure ) ? '' : esc_html( empty( $post->post_name ) ? '' : $post->post_name ); ?>
					</span>
				</div>
			</div>
			<div id="preview-dots" data-focus="vc_seo-slug-field">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
				</svg>
			</div>
		</div>
		<div class="content-container">
			<h1 id="vc_seo-title" data-focus="vc_seo-title-field" class="line-clamp line-clamp-2">
				<?php
				if ( empty( $seo_settings['title'] ) ) {
					esc_html_e( 'Example Title', 'js_composer' );
				} else {
					echo esc_html( $seo_settings['title'] );
				}
				?>
			</h1>
			<p id="vc_description-container" data-focus="vc_seo-description-field" class="line-clamp line-clamp-4">
				<span id="vc_seo-date">
					<?php
					if ( empty( $post->post_date ) ) {
						echo esc_html( date( 'F d, Y' ) );
					} else {
						echo esc_html( date_i18n( 'F j, Y', strtotime( $post->post_date ) ) );
					}
					?>
				</span>
				<span>-</span>
				<span id="vc_seo-description">
					<?php
					if ( empty( $seo_settings['description'] ) ) {
						esc_html_e( 'Example description', 'js_composer' );
					} else {
						echo esc_html( $seo_settings['description'] );
					}
					?>
				</span>
			</p>
		</div>
	</div>
</div>
