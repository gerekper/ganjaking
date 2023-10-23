<?php
/**
 * Preset filters list - Upgrade note
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Admin
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $hide_upgrade_note_url string
 * @var $do_widget_upgrade_url string
 * @var $demo_video_url        string
 * @var $doc_url               string
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<script type="text/template" id="tmpl-yith-wcan-upgrade-note">
	<div class="wc-backbone-modal yith-wcan-upgrade-note yith-plugin-ui">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<button class="modal-close modal-close-link dashicons dashicons-no-alt">
					<span class="screen-reader-text">Close modal panel</span>
				</button>
				<article>
					<h3 class="modal-title">
						<?php echo esc_html( _x( 'Use the new filter preset feature for your filters', '[ADMIN] Upgrade note modal title', 'yith-woocommerce-ajax-navigation' ) ); ?>
					</h3>
					<p>
						<?php
						echo wp_kses_post(
							_x(
								'From the version 4.0 we built a powerful system that allows you to create unlimited preset of filters. After the preset configuration, you can incorporate it in your shop using the widget, the shortcode, the Gutenberg block or the Elementor widget.',
								'[ADMIN] Upgrade note modal content',
								'yith-woocommerce-ajax-navigation'
							)
						);

						echo '&nbsp;';

						if ( $demo_video_url ) {
							echo wp_kses_post(
								sprintf(
								// translators: 1. Url to demo video. 2. Url to docs.
									_x(
										'We suggest you to <a href="%1$s">check this video</a> or <a href="%2$s">read the documentation</a> to learn how to use this new feature.',
										'[ADMIN] Upgrade note modal content',
										'yith-woocommerce-ajax-navigation'
									),
									$demo_video_url,
									$doc_url
								)
							);
						} else {
							echo wp_kses_post(
								sprintf(
								// translators: 1. Url to docs.
									_x(
										'We suggest you to <a href="%1$s">read the documentation</a> to learn how to use this new feature.',
										'[ADMIN] Upgrade note modal content',
										'yith-woocommerce-ajax-navigation'
									),
									$doc_url
								)
							);
						}
						?>
					</p>
					<p>
						<?php
						echo wp_kses_post(
							_x(
								'We offer a tool to automatically convert the widget filters of your shop in a preset. If you convert the widgets now all the settings of your widget will be moved inside the filter presets table.<br/><b>Don’t worry, no data will be lost during the process.</b>',
								'[ADMIN] Upgrade note modal content',
								'yith-woocommerce-ajax-navigation'
							)
						)
						?>
					</p>
					<p>
						<?php
						echo wp_kses_post(
							_x(
								'After converting the widgets into presets, go to <b>WordPress Dashboard -> Appearance -> Widgets</b> and replace classic widgets with new <b>YITH AJAX Filter Preset</b>, selecting the appropriate preset for current sidebar.',
								'[ADMIN] Upgrade note modal content',
								'yith-woocommerce-ajax-navigation'
							)
						)
						?>
					</p>
					<p>
						<a href="<?php echo esc_url( $do_widget_upgrade_url ); ?>" role="button" class="button-primary confirm"><?php echo esc_html( _x( 'Convert your widgets in a preset now', '[ADMIN] Upgrade note modal button label', 'yith-woocommerce-ajax-navigation' ) ); ?></a>
						<a href="<?php echo esc_url( $hide_upgrade_note_url ); ?>" role="button" class="dismiss"><?php echo esc_html( _x( 'No thanks', '[ADMIN] Upgrade note modal dismiss label', 'yith-woocommerce-ajax-navigation' ) ); ?></a>
					</p>
				</article>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
