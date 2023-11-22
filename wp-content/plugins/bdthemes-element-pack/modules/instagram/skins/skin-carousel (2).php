<?php

namespace ElementPack\Modules\Instagram\Skins;

use Elementor\Controls_Manager;
use Elementor\Skin_Base;
use ElementPack\Base\Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Skin_Carousel extends Skin_Base
{

	public function get_id()
	{
		return 'bdt-instagram-carousel';
	}

	public function get_title()
	{
		return esc_html__('Carousel', 'bdthemes-element-pack');
	}

	public function render()
	{
		$settings  = $this->parent->get_settings_for_display();

		$options   = get_option('element_pack_api_settings');
		$access_token    = (!empty($options['instagram_access_token'])) ? $options['instagram_access_token'] : '';

		$instagram_app_secret = (!empty($options['instagram_app_secret'])) ? $options['instagram_app_secret'] : '';

		if (!$instagram_app_secret) {
			element_pack_alert('Ops! You did not set Instagram App Secret in element pack settings!');

			return;
		}

		$data = $this->parent->get_instagram_data($instagram_app_secret);

		if (!$access_token) {
			element_pack_alert('Ops! You did not set Instagram Access Token in element pack settings!');
			return;
		}


		$this->parent->add_render_attribute('instagram-wrapper', 'class', 'bdt-instagram bdt-instagram-carousel');
		$this->parent->add_render_attribute('instagram-wrapper', 'data-bdt-slider', '');

		$this->parent->add_render_attribute('instagram-carousel', 'class', 'bdt-grid bdt-slider-items');

		$this->parent->add_render_attribute('instagram-carousel', 'class', 'bdt-grid-' . esc_attr($settings["column_gap"]));

		$columns_mobile = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 2;
		$columns_tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 3;
		$columns 		= isset($settings['columns']) ? $settings['columns'] : 4;

		$this->parent->add_render_attribute('instagram-carousel', 'class', 'bdt-child-width-1-' . esc_attr($columns) . '@m');
		$this->parent->add_render_attribute('instagram-carousel', 'class', 'bdt-child-width-1-' . esc_attr($columns_tablet) . '@s');
		$this->parent->add_render_attribute('instagram-carousel', 'class', 'bdt-child-width-1-' . esc_attr($columns_mobile));

		if ('yes' == $settings['show_lightbox']) {
			$this->parent->add_render_attribute('instagram-carousel', 'data-bdt-lightbox', 'animation:' . $settings['lightbox_animation'] . ';');
			if ($settings['lightbox_autoplay']) {
				$this->parent->add_render_attribute('instagram-carousel', 'data-bdt-lightbox', 'autoplay: 500;');

				if ($settings['lightbox_pause']) {
					$this->parent->add_render_attribute('instagram-carousel', 'data-bdt-lightbox', 'pause-on-hover: true;');
				}
			}
		}

		$this->parent->add_render_attribute(
			[
				'instagram-wrapper' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'action'              => 'element_pack_instagram_ajax_load',
							'show_link'           => ($settings['show_link']) ? true : false,
							'show_lightbox'       => ($settings['show_lightbox']) ? true : false,
							'current_page'        => 1,
							'load_more_per_click' => 4,
							'item_per_page'       => $settings["items"]["size"],
						]))
					]
				]
			]
		);


?>
		<div <?php echo $this->parent->get_render_attribute_string('instagram-wrapper'); ?>>

			<?php if ($settings['show_follow_me']) :

				$insta_user = get_transient('ep_instagram_user');
				$username = (isset($insta_user) && !empty($insta_user['username'])) ? $insta_user['username'] : '';

			?>

				<div class='bdt-instagram-follow-me bdt-position-z-index bdt-position-center'>
					<a href='https://www.instagram.com/<?php echo esc_html($username);  ?>'><?php echo esc_html($settings['follow_me_text']); ?> <?php echo esc_html($username);  ?></a>
				</div>

			<?php endif; ?>

			<div <?php echo $this->parent->get_render_attribute_string('instagram-carousel'); ?>>

				<?php
				$limit = 1;
				foreach ($data as $item) { ?>

					<div class="bdt-instagram-item-wrapper feed-type-video bdt-first-column">
						<div class="bdt-instagram-item bdt-transition-toggle bdt-position-relative bdt-scrollspy-inview bdt-animation-fade">
							<div class="bdt-instagram-thumbnail">
								<?php if ('VIDEO' == $item->media_type) : ?>
									<video src="<?php echo $item->media_url; ?>" title="Image by: <?php echo $item->username; ?>">
									<?php else : ?>
										<img src="<?php echo $item->media_url; ?>" alt="Image by: <?php echo $item->username; ?>" loading="lazy">
									<?php endif; ?>
							</div>

							<?php
							if ($settings['show_lightbox'] or $settings['show_link']) :
								$target_href = (isset($settings['show_link']) && ($settings['show_link'] == 'yes')) ? $item->permalink : $item->media_url;
								$target_blank = (isset($settings['target_blank']) && ('yes' == $settings['target_blank'])) ? '_blank' : '_self';
							?>
								<a target="<?php echo esc_attr($target_blank ); ?>" href="<?php echo esc_url($target_href); ?>" data-elementor-open-lightbox="no">
									<?php if ($settings['show_overlay']) : ?>
										<div class="bdt-transition-fade bdt-inline-clip bdt-position-cover bdt-overlay bdt-overlay-default ">

											<?php if ($settings['show_link_icon']) : ?>
												<?php if ('VIDEO' == $item->media_type) : ?>
													<span class='bdt-position-center ep-icon-play'></span>
												<?php else : ?>
													<span class='bdt-position-center ep-icon-plus'></span>
												<?php endif; ?>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</a>
							<?php endif; ?>
						</div>
					</div>


				<?php
					if ($limit++ == $settings['items']['size']) {
						break;
					}
				}

				?>

			</div>

			<a class='bdt-position-center-left bdt-position-small bdt-hidden-hover bdt-visible@m' href='#' data-bdt-slidenav-previous data-bdt-slider-item='previous'></a>
			<a class='bdt-position-center-right bdt-position-small bdt-hidden-hover bdt-visible@m' href='#' data-bdt-slidenav-next data-bdt-slider-item='next'></a>


		</div>

<?php
	}
}
