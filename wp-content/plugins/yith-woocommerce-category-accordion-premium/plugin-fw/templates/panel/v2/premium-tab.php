<?php
/**
 * The Template for displaying the Premium tab.
 *
 * @var array                                         $features             List of premium features
 * @var array                                         $testimonials         List of testimonials
 * @var array                                         $pricing              Pricing details.
 * @var string                                        $landing_page_url     The premium landing page URL.
 * @var string                                        $free_vs_premium_url  The free vs premium URL.
 * @var bool                                          $show_free_vs_premium Show free VS premium link flag.
 * @var YIT_Plugin_Panel|YIT_Plugin_Panel_WooCommerce $panel                The panel.
 * @package YITH\PluginFramework\Templates
 * @since   3.9.0
 */

defined( 'ABSPATH' ) || exit;

?>

<div id="yith-plugin-fw__panel__premium-tab" class="yith-plugin-fw__panel__content__page">
	<div class="yith-plugin-fw__panel__content__page__heading">
		<h1 class="yith-plugin-fw__panel__content__page__title"><?php echo esc_html_x( 'Upgrade to unlock premium features', 'Premium Tab', 'yith-plugin-fw' ); ?></h1>
		<div class="yith-plugin-fw__panel__content__page__description">
			<?php echo esc_html_x( 'Check out the advanced features you can get by upgrading to premium!', 'Premium Tab', 'yith-plugin-fw' ); ?>
		</div>
	</div>
	<div class="yith-plugin-fw__panel__content__page__container">
		<?php $panel->get_template( 'premium-tab-content.php', compact( 'features', 'testimonials', 'pricing', 'landing_page_url', 'free_vs_premium_url', 'show_free_vs_premium' ) ); ?>
	</div>
</div>
