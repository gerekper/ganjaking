<?php
/**
 * List table empty template
 *
 * @package YITH\FAQPluginForWordPress\Admin\Views\ListTable
 * @var $attrs array The template settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="yfwp-admin-no-posts">
	<div class="yfwp-admin-no-posts-container">
		<?php if ( isset( $attrs['icon'] ) ) : ?>
			<div class="yfwp-admin-no-posts-logo">
				<img src="<?php echo esc_url( $attrs['icon'] ); ?>">
			</div>
		<?php endif; ?>
		<div class="yfwp-admin-no-posts-text">
			<span>
				<strong><?php echo esc_html( $attrs['message'] ); ?></strong>
			</span>
			<p><?php echo esc_html( $attrs['submessage'] ); ?></p>
		</div>
		<?php if ( ! empty( $attrs['cta_button_text'] ) ) : ?>
			<a href="<?php echo ! empty( $attrs['cta_button_href'] ) ? esc_url( $attrs['cta_button_href'] ) : '#'; ?>" class="yith-add-button"><?php echo esc_html( $attrs['cta_button_text'] ); ?></a>
		<?php endif; ?>
	</div>
</div>
