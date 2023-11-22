<?php

namespace ElementPack;

/**
 * Notices class
 */
class Notices {

	private static $notices = [];

	private static $instance;

	public static function get_instance() {
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __construct() {

		add_action('admin_notices', [$this, 'show_notices']);
		add_action('wp_ajax_element-pack-notices', [$this, 'dismiss']);
	}

	public static function add_notice($args = []) {
		if (is_array($args)) {
			self::$notices[] = $args;
		}
	}

	/**
	 * Dismiss Notice.
	 */
	public function dismiss() {

		$id   = (isset($_POST['id'])) ? esc_attr($_POST['id']) : '';
		$time = (isset($_POST['time'])) ? esc_attr($_POST['time']) : '';
		$meta = (isset($_POST['meta'])) ? esc_attr($_POST['meta']) : '';

		// Valid inputs?
		if (!empty($id)) {

			if ('user' === $meta) {
				update_user_meta(get_current_user_id(), $id, true);
			} else {
				set_transient($id, true, $time);
			}

			wp_send_json_success();
		}

		wp_send_json_error();
	}

	/**
	 * Notice Types
	 */
	public function show_notices() {

		$defaults = [
			'id'               => '',
			'type'             => 'info',
			'show_if'          => true,
			'title'            => '',
			'message'          => '',
			'class'            => 'element-pack-notice',
			'dismissible'      => false,
			'dismissible-meta' => 'transient',
			'dismissible-time' => WEEK_IN_SECONDS,
			'data'             => '',
			'action_link'      => '',
		];

		foreach (self::$notices as $key => $notice) {

			$notice = wp_parse_args($notice, $defaults);

			$classes = ['notice'];

			$classes[] = $notice['class'];
			if (isset($notice['type'])) {
				$classes[] = 'notice-' . $notice['type'];
			}

			// Is notice dismissible?
			if (true === $notice['dismissible']) {
				$classes[] = 'is-dismissible';

				// Dismissable time.
				$notice['data'] = ' dismissible-time=' . esc_attr($notice['dismissible-time']) . ' ';
			}

			// Notice ID.
			$notice_id    = 'element-pack-notice-id-' . $notice['id'];
			$notice['id'] = $notice_id;
			if (!isset($notice['id'])) {
				$notice_id    = 'element-pack-notice-id-' . $notice['id'];
				$notice['id'] = $notice_id;
			} else {
				$notice_id = $notice['id'];
			}

			$notice['classes'] = implode(' ', $classes);

			// User meta.
			$notice['data'] .= ' dismissible-meta=' . esc_attr($notice['dismissible-meta']) . ' ';
			if ('user' === $notice['dismissible-meta']) {
				$expired = get_user_meta(get_current_user_id(), $notice_id, true);
			} elseif ('transient' === $notice['dismissible-meta']) {
				$expired = get_transient($notice_id);
			}

			// Notices visible after transient expire.
			if (isset($notice['show_if'])) {

				if (true === $notice['show_if']) {

					// Is transient expired?
					if (false === $expired || empty($expired)) {
						self::notice_layout($notice);
					}
				}
			} else {

				// No transient notices.
				self::notice_layout($notice);
			}
		}
	}

	/**
	 * Notice layout
	 * @param  array $notice Notice notice_layout.
	 * @return void
	 */
	public static function __old__notice_layout($notice = []) {

?>
		<div id="<?php echo esc_attr($notice['id']); ?>" class="<?php echo esc_attr($notice['classes']); ?>" <?php echo esc_attr($notice['data']); ?>>
			<p>
				<?php echo wp_kses_post($notice['message']); ?>
			</p>
		</div>
	<?php
	}

	/**
	 * New Notice Layout
	 * @param  array $notice Notice notice_layout.
	 * @return void
	 * @since 6.11.3
	 */

	public static function notice_layout($notice = []) {

	?>
		<div id="<?php echo esc_attr($notice['id']); ?>" class="<?php echo esc_attr($notice['classes']); ?>" <?php echo esc_attr($notice['data']); ?>>
			<div class="bdt-notice-wrapper">
				<div class="bdt-notice-icon-wrapper">
					<!-- <i class="eicon-elementor" aria-hidden="true"></i> -->
					<img height="25" width="25" src="<?php echo BDTEP_ASSETS_URL; ?>images/logo.svg">
				</div>

				<div class="bdt-notice-content">
					<?php if (isset($notice['title']) && !empty($notice['title'])) : ?>
						<h2 class="bdt-notice-title"><?php echo wp_kses_post($notice['title']); ?></h2>
					<?php endif; ?>

					<p class="bdt-notice-text"><?php echo wp_kses_post($notice['message']); ?></p>

					<?php if (isset($notice['action_link']) && !empty($notice['action_link'])) : ?>
						<div class="bdt-notice-btn">
							<a href="#">Renew Now</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
<?php
	}
}

Notices::get_instance();
