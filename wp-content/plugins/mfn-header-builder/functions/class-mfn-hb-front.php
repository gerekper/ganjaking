<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Mfn_HB_Front
{
	private $builder = false;

	/**
	 * Mfn_HB_Front constructor
	 */

	public function __construct()
	{

		require_once dirname(__FILE__) . '/class-mfn-hb-helper.php';
		require_once dirname(__FILE__) . '/class-mfn-hb-styles.php';
		require_once dirname(__FILE__) . '/class-mfn-hb-items.php';

		// init

		$this->init();

		if (! is_array($this->builder)) {
			return false;
		}

		// Hook to use when enqueuing items that are meant to appear on the front end.
		add_action('wp_enqueue_scripts', array( $this, 'enqueue' ));

		// It runs in Muffin Header
		add_action('mfn_header', array( $this, 'the_builder' ), 10, 0);
	}

	/**
	 * Enqueue styles and scripts
	 */

	public function enqueue()
	{
		wp_enqueue_style('mfn-hb', plugins_url('assets/style.css', __FILE__));
		wp_enqueue_script('mfn-hb', plugins_url('assets/scripts.js', __FILE__), false, MFN_HB_VERSION, true);

		Mfn_HB_Styles::add_inline_style($this->builder);
	}

	/**
	 * Get and set initial values
	 */

	private function init()
	{

		// get builder options

		$this->builder = json_decode(get_site_option('mfn_header_builder'), true);

		// debug mode

		if (isset($_GET['mfn-debug'])) {
			print_r($this->builder);
		}
	}

	/**
	 * Show BUILDER
	 */

	public function the_builder()
	{

		// builder is empty

		if (! is_array($this->builder)) {
			return false;
		}

		// status

		$status = array(
			'desktop' => array(
				'default' => 'custom',
				'sticky' 	=> $this->builder['desktopSticky']['grid']['status'],
			),
			'tablet' => array(
				'default' => $this->builder['tablet']['grid']['status'],
				'sticky' 	=> $this->builder['tabletSticky']['grid']['status'],
			),
			'mobile' => array(
				'default' => $this->builder['mobile']['grid']['status'],
				'sticky' 	=> $this->builder['mobileSticky']['grid']['status'],
			),
		);

		// output

		echo '<header class="mhb-grid" data-desktop="'. esc_attr(implode(' ', $status['desktop'])) .'" data-tablet="'. esc_attr(implode(' ', $status['tablet'])) .'" data-mobile="'. esc_attr(implode(' ', $status['mobile'])) .'">';

			echo '<div class="mhb-placeholder"></div>';

			foreach ($this->builder as $device_name => $device) {

				if (isset($device['grid']['status']) && ('custom' != $device['grid']['status'])) {
					continue; // skip grid status auto and off
				}

				$device_name = Mfn_HB_Helper::camel_to_other($device_name, ' ');

				// class

				$class = array( $device_name );
				$options = $device['grid']['options'];

				if (strpos($device_name, 'sticky') === false) {
					$class[] = 'default';
				}

				if ($options['layout']) {
					$class[] = $options['layout'];
				}

				if (!empty($options['headerOnTop'])) {
					$class[] = 'on-top';
				}

				$class = implode(' ', $class);

				// output -----

				echo '<div class="mhb-view '. esc_attr($class) .'">';

					$this->the_row('action-bar', $device['actionBar']);
						$this->the_row('first-row', $device['firstRow']);
					$this->the_row('second-row', $device['secondRow']);

				echo '</div>';
			}

		echo '</header>';
	}

	/**
	 * Show ROW
	 */

	private function the_row($type, $attr)
	{

		// show rows: row disabled

		if (('first-row' != $type) && (! $attr['active'])) {
			return false;
		}

		// output -----

		echo '<div class="mhb-row '. $type .'">';
			echo '<div class="mhb-row-wrapper container">';

				$this->the_column('left', $attr['items']['left']);
					$this->the_column('center', $attr['items']['center']);
				$this->the_column('right', $attr['items']['right']);

			echo '</div>';
		echo '</div>';
	}

	private function the_column($name, $items)
	{
		echo '<div class="mhb-col '. $name .'">';

		if (is_array($items)) {
			foreach ($items as $item) {
				$this->the_item($item['name'], $item['uuid'], $item['form']);
			}
		}

		echo '</div>';
	}

	private function the_item($type, $uuid, $attr)
	{

		// type

		$type = Mfn_HB_Helper::camel_to_snake($type);

		// output -----

		echo '<div class="mhb-item mhb-'. $type .' mhb-custom-'. $uuid .'">';

			if (method_exists('Mfn_HB_Items', $type)) {
				echo Mfn_HB_Items::$type($attr);
			}

		echo '</div>';
	}
}
