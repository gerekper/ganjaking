<?php

namespace ElementPack\Modules\EventsCalendarGrid\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Skin_Annal extends Elementor_Skin_Base {

	public function get_id() {
		return 'annal';
	}

	public function get_title() {
		return __('Annal', 'bdthemes-element-pack');
	}

	public function render_date() {
		if (!$this->parent->get_settings('show_date')) {
			return;
		}

		$start_datetime = tribe_get_start_date();
		$end_datetime = tribe_get_end_date();

		$event_date = tribe_get_start_date(null, false);

?>
		<span class="bdt-event-date">
			<a href="javascript:void(0);" title="<?php esc_html_e('Start Date:', 'bdthemes-element-pack');
								echo esc_html($start_datetime); ?>  - <?php esc_html_e('End Date:', 'bdthemes-element-pack');
																		echo esc_html($end_datetime); ?>">
				<span class="bdt-event-day">
					<?php echo esc_html($event_date); ?>
				</span>
			</a>
		</span>
	<?php
	}

	public function render_meta() {
		$settings = $this->parent->get_settings_for_display();
		if (!$this->parent->get_settings('show_meta')) {
			return;
		}

		$cost         = ($settings['show_meta_cost']) ? tribe_get_formatted_cost() : '';
		$more_icon    = ('yes' == ($settings['show_meta_more_btn']));

	?>

		<?php if (!empty($cost) or $more_icon) : ?>
			<div class="bdt-event-meta bdt-grid">

				<?php if (!empty($cost)) : ?>
					<div class="bdt-width-auto bdt-padding-remove">
						<div class="bdt-event-price">
							<a href="javascript:void(0);"><?php esc_html_e('Cost:', 'bdthemes-element-pack'); ?></a>
							<a href="javascript:void(0);"><?php echo esc_html($cost); ?></a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (!empty($more_icon)) : ?>
					<div class="bdt-width-expand bdt-text-right">
						<div class="bdt-more-icon">
							<a href="javascript:void(0);" bdt-tooltip="<?php echo esc_html('Find out more', 'bdthemes-element-pack'); ?>" class="ep-icon-arrow-right-4" aria-hidden="true"></a>
						</div>
					</div>
				<?php endif; ?>

			</div>
		<?php endif; ?>

	<?php
	}

	public function render_website_address() {
		$settings = $this->parent->get_settings_for_display();

		$address = ($settings['show_meta_location']) ? tribe_address_exists() : '';
		$website = ($settings['show_meta_website']) ? tribe_get_event_website_url() : '';

	?>

		<?php if (!empty($website) or $address) : ?>
			<div class="bdt-address-website-icon">

				<?php if (!empty($website)) : ?>
					<a href="<?php echo esc_url($website); ?>" target="_blank" class="ep-icon-earth" aria-hidden="true"></a>
				<?php endif; ?>

				<?php if ($address) : ?>
					<a href="javascript:void(0);" bdt-tooltip="<?php echo esc_html(tribe_get_full_address()); ?>" class="ep-icon-location" aria-hidden="true"></a>
				<?php endif; ?>

			</div>
		<?php endif; ?>

	<?php

	}

	public function render_loop_item($post) {
		$settings = $this->parent->get_settings_for_display();

	?>
		<div class="bdt-event-item">

			<div class="bdt-event-item-inner">

				<?php $this->render_website_address(); ?>

				<?php $this->parent->render_image(); ?>

				<div class="bdt-event-content">

					<?php $this->render_date(); ?>

					<?php $this->parent->render_title(); ?>

					<?php $this->parent->render_excerpt($post); ?>

					<?php $this->render_meta(); ?>

				</div>

			</div>

		</div>
<?php
	}

	public function render() {

		$settings = $this->parent->get_settings_for_display();

		global $post;

		$start_date = ('custom' == $settings['start_date']) ? $settings['custom_start_date'] : $settings['start_date'];
		$end_date   = ('custom' == $settings['end_date']) ? $settings['custom_end_date'] : $settings['end_date'];

		$query_args = array_filter([
			'start_date'     => $start_date,
			'end_date'       => $end_date,
			'orderby'        => $settings['orderby'],
			'order'          => $settings['order'],
			'eventDisplay' 	 => ('custom' == $settings['start_date'] or 'custom' == $settings['end_date']) ? 'custom' : 'all',
			'posts_per_page' => $settings['limit'],
		]);


		if ('by_name' === $settings['source'] and !empty($settings['event_categories'])) {
			// $query_args['tax_query'] = [
			// 	'taxonomy' => 'tribe_events_cat',
			// 	'field'    => 'slug',
			// 	'terms'    => $settings['event_categories']
			// ];
			$query_args['event_category']    = $settings['event_categories'];
		}

		$query_args = tribe_get_events($query_args);

		$skin_name = 'annal';

		$this->parent->render_header($skin_name);

		foreach ($query_args as $post) {

			$this->render_loop_item($post);
		}

		$this->parent->render_footer();

		wp_reset_postdata();
	}
}
