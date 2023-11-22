<?php

namespace ElementPack\Modules\AudioPlayer\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;
use ElementPack\Utils;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Skin_Poster extends Elementor_Skin_Base {

	public function get_id() {
		return 'bdt-poster';
	}

	public function get_title() {
		return esc_html__('Poster', 'bdthemes-element-pack');
	}

	public function render() {
		$settings = $this->parent->get_settings_for_display();
		$id       = $this->parent->get_id();

		$poster = (!empty($settings['poster'])) ? $settings['poster']['url'] : BDTEP_ASSETS_URL . 'images/audio-thumbnail.svg';
		$align = (!empty($settings['skin_poster_align'])  && $settings['skin_poster_align'] == 'center') ? 'bdt-position-center' : 'bdt-position-center';
		$align = (!empty($settings['skin_poster_align']) && $settings['skin_poster_align'] != 'center') ? 'bdt-position-center-' . $settings['skin_poster_align'] : $align;
?>

		<div class="bdt-audio-player skin-poster bdt-<?php echo esc_attr($settings['layout_style']); ?>">

			<div class="bdt-audio-player-poster bdt-position-cover"></div>

			<?php if ($settings['thumb_style'] == 'yes') : ?>
				<div class="bdt-audio-player-thumb <?php echo $align; ?>">
					<img src="<?php echo esc_url($poster); ?>" alt="<?php echo get_the_title(); ?>">
				</div>
			<?php endif; ?>

			<div class="bdt-audio-info-wrapper">

				<div class="bdt-audio-info">

					<?php if ('' !== $settings['title']) : ?>
						<div class="bdt-audio-player-title">
							<?php echo esc_html($settings['title']); ?>
						</div>
					<?php endif; ?>

					<?php if ('' !== $settings['author_name']) : ?>
						<div class="bdt-audio-player-artist">
							<span><?php echo esc_html__('By: ', 'bdthemes-element-pack'); ?></span>
							<span><?php echo esc_html($settings['author_name']); ?></span>
						</div>
					<?php endif; ?>

				</div>

				<?php $this->parent->render_audio_header(); ?>

				<div id="jp_container_<?php echo esc_attr($id); ?>" class="jp-audio" role="application" aria-label="media player">
					<div class="jp-type-playlist bdt-width-1-1">
						<div class="jp-gui jp-interface">
							<div class="jp-controls bdt-grid bdt-grid-small bdt-flex-middle" bdt-grid>
								<?php $this->parent->render_play_button(); ?>

								<?php $this->parent->render_seek_bar(); ?>

								<?php $this->parent->render_mute_button(); ?>

							</div>
						</div>
					</div>
				</div>

			</div>


		</div>
<?php
	}
}
