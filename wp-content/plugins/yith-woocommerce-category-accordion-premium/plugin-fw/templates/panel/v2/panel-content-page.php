<?php
/**
 * The Template for displaying the YIT panel form.
 *
 * @var YIT_Plugin_Panel $panel The YIT Panel.
 * @var string           $panel_content_class
 * @var string           $form_method
 * @var string           $option_key
 * @var string           $reset_warning
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$page_title       = $panel->get_page_title();
$page_description = $panel->get_page_description();
?>

<div class="yith-plugin-fw__panel__content__page yith-plugin-fw__panel__content__page--options">
	<div class="yith-plugin-fw__panel__content__page__heading">
		<h1 class="yith-plugin-fw__panel__content__page__title">
			<?php echo wp_kses_post( $page_title ); ?>
		</h1>
		<?php if ( $page_description ) : ?>
			<div class="yith-plugin-fw__panel__content__page__description">
				<?php echo wp_kses_post( $page_description ); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="<?php echo esc_attr( $panel_content_class ); ?>">
		<?php if ( $panel->is_show_form() ) : ?>
			<form id="yith-plugin-fw-panel" method="<?php echo esc_attr( $form_method ); ?>" action="options.php">

				<div class="yith-plugin-fw__panel__secondary-notices">
					<?php $panel->message(); ?>
				</div>

				<?php $panel->get_template( 'panel-settings-sections.php', compact( 'panel' ) ); ?>
				<p>&nbsp;</p>
				<?php settings_fields( 'yit_' . $panel->settings['parent'] . '_options' ); ?>
				<input type="hidden" name="<?php echo esc_attr( $panel->get_name_field( 'option_key' ) ); ?>"
						value="<?php echo esc_attr( $option_key ); ?>"/>
				<input type="hidden" name="page" value="<?php echo esc_attr( $panel->settings['page'] ); ?>"/>
				<input type="hidden" name="tab" value="<?php echo esc_attr( $panel->get_current_tab() ); ?>"/>
				<input type="hidden" name="sub_tab" value="<?php echo esc_attr( $panel->get_current_sub_tab() ); ?>"/>
			</form>
			<form id="yith-plugin-fw-panel-reset" method="post">
				<input type="hidden" name="yit-action" value="reset"/>
				<input type="hidden" name="yit-reset" value="1"/>
			</form>
			<p>&nbsp;</p>
		<?php endif ?>
	</div>
</div>

<div class="yith-plugin-fw__panel__content__actions">
	<button class="yith-plugin-fw__button yith-plugin-fw__button--primary" id="yith-plugin-fw__panel__content__save"><?php esc_html_e( 'Save Options', 'yith-plugin-fw' ); ?></button>
	<button class="yith-plugin-fw__button yith-plugin-fw__button--secondary" id="yith-plugin-fw__panel__content__reset"><?php esc_html_e( 'Reset Defaults', 'yith-plugin-fw' ); ?></button>
</div>
