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

<div id="yith-plugin-fw__panel__premium-tab">
	<?php $panel->get_template( 'premium-tab-content.php', compact( 'features', 'testimonials', 'pricing', 'landing_page_url', 'free_vs_premium_url', 'show_free_vs_premium' ) ); ?>
</div>
