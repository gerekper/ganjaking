<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$total_templates = visual_composer()->templatesPanelEditor()->loadDefaultTemplates();
$templates_total_count = count( $total_templates );
?>
<div id="vc_no-content-helper" class="vc_welcome vc_ui-font-open-sans">
	<?php
	if ( vc_user_access()->part( 'shortcodes' )->checkStateAny( true, 'custom', null )->get() && vc_user_access_check_shortcode_all( 'vc_row' ) && vc_user_access_check_shortcode_all( 'vc_column' ) ) :
		?>
		<div class="vc_welcome-brand vc_welcome-visible-e">
			<img src="<?php echo esc_url( vc_asset_url( 'vc/logo/wpb-logo.svg' ) ); ?>" alt="">
		</div>
		<div class="vc_welcome-header vc_welcome-visible-e">
			<?php esc_html_e( 'You have blank page', 'js_composer' ); ?>
			<br/>
			<?php esc_html_e( 'Start adding content or templates', 'js_composer' ); ?>
		</div>
		<div class="vc_ui-btn-group vc_welcome-visible-e">
			<?php
			if ( vc_user_access()->part( 'shortcodes' )->checkStateAny( true, 'custom', null )
					->get() && vc_user_access_check_shortcode_all( 'vc_row' ) && vc_user_access_check_shortcode_all( 'vc_column' ) ) :
				?>
				<a id="vc_no-content-add-element"
				   class="vc_general vc_ui-button vc_ui-button-shape-rounded vc_ui-button-info vc_welcome-visible-e"
				   title="<?php esc_attr_e( 'Add Element', 'js_composer' ); ?>"
				   data-vc-element="add-element-action" href="#">
					<i class="vc-composer-icon vc-c-icon-add_element"></i>
					<span><?php esc_html_e( 'Add element', 'js_composer' ); ?></span>
				</a>
				<?php
			endif;
			if ( vc_user_access()->part( 'shortcodes' )->can( 'vc_column_text_all' )->get() ) :
				?>
				<a id="vc_no-content-add-text-block"
				   class="vc_general vc_ui-button vc_ui-button-shape-rounded vc_ui-button-info"
				   data-vc-element="add-text-block-action" href="#"
				   title="<?php esc_attr_e( 'Add text block', 'js_composer' ); ?>">
					<i class="vc-composer-icon vc-c-icon-text-block"></i>
					<span><?php esc_html_e( 'Add Text Block', 'js_composer' ); ?></span>
				</a>
				<?php
			endif;
			if ( $templates_total_count > 0 && vc_user_access()->part( 'templates' )->can()->get() ) :
				?>
				<a id="vc_templates-more-layouts"
				   class="vc_general vc_ui-button vc_ui-button-shape-rounded vc_ui-button-info" href="#">
				<i class="vc-composer-icon vc-c-icon-add_template"></i>
				<span><?php esc_html_e( 'Add template', 'js_composer' ); ?></span>
				</a><?php endif; ?></div>
		<?php
		if ( vc_user_access()->part( 'shortcodes' )->checkStateAny( true, 'custom', null )
				->get() && vc_user_access_check_shortcode_all( 'vc_row' ) && vc_user_access_check_shortcode_all( 'vc_column' ) ) :
			?>
			<div class="vc_welcome-visible-ne">
				<a id="vc_not-empty-add-element" class="vc_add-element-not-empty-button"
				   title="<?php esc_attr_e( 'Add Element', 'js_composer' ); ?>" data-vc-element="add-element-action">
					<i class="vc-composer-icon vc-c-icon-add"></i>
				</a>
			</div>
		<?php endif; ?>
		<p class="vc_ui-help-block vc_welcome-visible-e">
			<?php echo sprintf( esc_html__( 'Don\'t know where to start? Visit our %s.', 'js_composer' ), '<a href="https://kb.wpbakery.com" target="_blank">' . esc_html__( 'knowledge base', 'js_composer' ) . '</a>' ); ?>
		</p>
	<?php endif; ?>
</div>

