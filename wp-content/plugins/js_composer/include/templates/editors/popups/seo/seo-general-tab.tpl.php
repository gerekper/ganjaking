<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * @var array $seo_settings
 * @var WP_Post | null $post
 * @var int $post_id
 * @var Vc_Post_Seo $vc_post_seo
 * @var string $permalink_structure
 */
?>

<div id="vc_ui-seo-general">
	<div class="vc_row">
		<div class="vc_col-sm-12 vc_column">
			<div class="wpb_element_label"><?php esc_html_e( 'Focus keyphrase', 'js_composer' ); ?></div>
			<div class="edit_form_line">
				<input name="focus-keyphrase" class="wpb-textinput" type="text" value="<?php echo empty( $seo_settings['focus-keyphrase'] ) ? '' : esc_attr( $seo_settings['focus-keyphrase'] ) ?>" id="vc_focus-keyphrase-field" placeholder="">
			</div>
		</div>
		<div class="vc_col-sm-12 vc_column">
			<div class="wpb_element_label"><?php esc_html_e( 'Preview as:', 'js_composer' ); ?></div>
			<div class="vc-preview-radio">
				<div>
					<input type="radio" id="mobile-result" name="preview-as" value="mobile" checked>
					<label for="mobile-result"><?php esc_html_e( 'Mobile result', 'js_composer' ); ?></label>
				</div>
				<div>
					<input type="radio" id="desktop-result" name="preview-as" value="desktop">
					<label for="desktop-result"><?php esc_html_e( 'Desktop result', 'js_composer' ); ?></label>
				</div>
			</div>
			<?php
			vc_include_template(
				'editors/popups/seo/seo-general-preview.php',
				[
					'seo_settings' => $seo_settings,
					'post' => $post,
					'post_id' => $post_id,
					'vc_post_seo' => $vc_post_seo,
					'permalink_structure' => $permalink_structure,
				]
			);
			?>
		</div>
		<div class="vc_col-sm-12 vc_column">
			<div class="wpb_element_label"><?php esc_html_e( 'SEO title', 'js_composer' ); ?></div>
			<div class="edit_form_line">
				<?php
				wpb_add_ai_icon_to_text_field( 'textfield', 'vc_seo-title-field' );
				?>
				<input name="title" data-preview="vc_seo-title" class="wpb-textinput" type="text" value="<?php echo empty( $seo_settings['title'] ) ? '' : esc_attr( $seo_settings['title'] ) ?>" id="vc_seo-title-field" placeholder="">
			</div>
		</div>
		<div class="vc_col-sm-12 vc_column">
			<div class="wpb_element_label"><?php esc_html_e( 'Slug', 'js_composer' ); ?></div>
			<div class="edit_form_line">
				<input name="slug" data-preview="vc_seo-slug" class="wpb-textinput" type="text" value="<?php echo empty( $permalink_structure ) ? '' : esc_attr( get_post_field( 'post_name', $post ) ); ?>" id="vc_seo-slug-field" placeholder="">
			</div>
		</div>
		<div class="vc_col-sm-12 vc_column">
			<div class="wpb_element_label"><?php esc_html_e( 'Meta description', 'js_composer' ); ?></div>
			<div class="edit_form_line">
				<?php
				wpb_add_ai_icon_to_text_field( 'textarea', 'vc_seo-description-field' );
				?>
				<textarea name="description" data-preview="vc_seo-description" class="wpb-textinput" id="vc_seo-description-field"><?php echo empty( $seo_settings['description'] ) ? '' : esc_attr( $seo_settings['description'] ) ?></textarea>
			</div>
		</div>
	</div>
</div>

