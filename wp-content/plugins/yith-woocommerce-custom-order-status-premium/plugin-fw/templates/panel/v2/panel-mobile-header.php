<?php
/**
 * The Template for displaying the Panel Sidebar Header.
 *
 * @var string $header_title
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="yith-plugin-fw__panel__mobile__header">
	<div class="yith-plugin-fw__panel__mobile__header__toggle">
		<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
			<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
		</svg>
	</div>
	<img class="yith-plugin-fw__panel__mobile__header__logo" src="<?php echo esc_url( YIT_CORE_PLUGIN_URL . '/assets/images/yith-logo.svg' ); ?>"/>

	<?php if ( $header_title ) : ?>
		<div class="yith-plugin-fw__panel__mobile__header__title"><?php echo esc_html( $header_title ); ?></div>
	<?php endif; ?>
</div>
