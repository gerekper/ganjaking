<?php
/**
 * Builder/FormTemplates Education template for Lite.
 *
 * @since 1.6.6
 *
 * @var array $templates      Templates.
 * @var array $empty_template Single template defaults.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wpforms-setup-title">
	<?php esc_html_e( 'Unlock Pre-Made Form Templates', 'wpforms-lite' ); ?>
	<a href="<?php echo esc_url( wpforms_admin_upgrade_link( 'builder-templates' ) ); ?>"
		target="_blank" rel="noopener noreferrer"
		class="btn-green education-modal"
		data-name="<?php esc_attr_e( 'Pre-Made Form Templates', 'wpforms-lite' ); ?>"
		data-license="pro"
		style="text-transform: uppercase;font-size: 13px;font-weight: 700;padding: 5px 10px;vertical-align: text-bottom;">
		<?php esc_html_e( 'Upgrade', 'wpforms-lite' ); ?>
	</a>
</div>
<p class="wpforms-setup-desc">
	<?php esc_html_e( 'While WPForms Lite allows you to create any type of form, you can speed up the process by unlocking our other pre-built form templates among other features, so you never have to start from scratch again...', 'wpforms-lite' ); ?>
</p>
<div class="wpforms-setup-templates wpforms-clear" style="opacity:0.5;">
	<?php
	foreach ( $templates as $template ) {
		$template = wp_parse_args( $template, $empty_template );

		?>
		<div id="wpforms-template-<?php echo sanitize_html_class( $template['slug'] ); ?>"
			class="wpforms-template education-modal"
			data-name="<?php esc_attr_e( 'Pre-Made Form Templates', 'wpforms-lite' ); ?>"
			data-license="pro">
			<div class="wpforms-template-name wpforms-clear">
				<?php echo esc_html( $template['name'] ); ?>
			</div>
			<div class="wpforms-template-details">
				<p class="desc"><?php echo esc_html( $template['description'] ); ?></p>
			</div>
		</div>
		<?php
	}
	?>
</div>
