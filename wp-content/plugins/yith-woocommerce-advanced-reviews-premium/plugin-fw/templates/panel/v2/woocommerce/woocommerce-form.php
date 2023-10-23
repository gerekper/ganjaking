<?php
/**
 * The Template for displaying the WooCommerce form.
 *
 * @var YIT_Plugin_Panel_WooCommerce $panel      The YITH WooCommerce Panel.
 * @var string                       $option_key The current option key ( see YIT_Plugin_Panel::get_current_option_key() ).
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$form_method   = apply_filters( 'yit_admin_panel_form_method', 'POST', $option_key );
$content_class = apply_filters( 'yit_admin_panel_content_class', 'yit-admin-panel-content-wrap', $option_key );
$container_id  = $panel->settings['page'] . '_' . $option_key;

$page_title       = $panel->get_page_title();
$page_description = $panel->get_page_description();
$notices          = $panel->get_notices();
?>

<?php do_action( 'yit_framework_before_print_wc_panel_content', $option_key ); ?>

<div class="yith-plugin-fw__panel__content__page yith-plugin-fw__panel__content__page--options <?php echo esc_attr( $content_class ); ?>">
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
	<form id="plugin-fw-wc" method="<?php echo esc_attr( $form_method ); ?>">

		<div class="yith-plugin-fw__panel__secondary-notices">
			<?php
			foreach ( $notices as $notice ) {
				$notice_type    = $notice['type'] ?? 'info';
				$notice_message = $notice['message'] ?? '';
				yith_plugin_fw_get_component(
					array(
						'type'        => 'notice',
						'notice_type' => $notice_type,
						'message'     => $notice_message,
					)
				);
			}
			?>
		</div>

		<?php $panel->add_fields(); ?>

		<?php wp_nonce_field( 'yit_panel_wc_options_' . $panel->settings['page'], 'yit_panel_wc_options_nonce' ); ?>
		<input type="hidden" name="page" value="<?php echo esc_attr( $panel->settings['page'] ); ?>"/>
		<input type="hidden" name="tab" value="<?php echo esc_attr( $panel->get_current_tab() ); ?>"/>
		<input type="hidden" name="sub_tab" value="<?php echo esc_attr( $panel->get_current_sub_tab() ); ?>"/>
	</form>
	<form id="plugin-fw-wc-reset" method="post">
		<input type="hidden" name="yit-action" value="wc-options-reset"/>
		<input type="hidden" name="yit-reset" value="1"/>
		<?php wp_nonce_field( 'yith_wc_reset_options_' . $panel->settings['page'], 'yith_wc_reset_options_nonce' ); ?>
	</form>
</div>

<?php do_action( 'yit_framework_after_print_wc_panel_content', $option_key ); ?>

<div class="yith-plugin-fw__panel__content__actions">
	<button class="yith-plugin-fw__button yith-plugin-fw__button--primary" id="yith-plugin-fw__panel__content__save"><?php esc_html_e( 'Save Options', 'yith-plugin-fw' ); ?></button>
	<button class="yith-plugin-fw__button yith-plugin-fw__button--secondary" id="yith-plugin-fw__panel__content__reset"><?php esc_html_e( 'Reset Defaults', 'yith-plugin-fw' ); ?></button>
</div>
