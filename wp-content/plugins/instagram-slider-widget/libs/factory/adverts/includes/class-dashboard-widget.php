<?php

namespace WBCR\Factory_Adverts_117;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adverts Dashboard Widget.
 *
 * Adds a widget with a banner or a list of news.
 *
 * @author        Alexander Vitkalov <nechin.va@gmail.com>
 * @author        Alexander Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 *
 * @since         1.0.0 Added
 * @package       factory-adverts
 * @copyright (c) 2019 Webcraftic Ltd
 */
class Dashboard_Widget {

	/**
	 * Контент, который должен быть напечатан внутри дашбоард виджета
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.1
	 * @var string
	 */
	private $content;

	/**
	 * Экзепляр плагина с которым взаимодействует этот модуль
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.1
	 * @var \Wbcr_Factory439_Plugin
	 */
	private $plugin;

	/**
	 * Dashboard_Widget constructor.
	 *
	 * Call parent constructor. Registration hooks.
	 *
	 * @since 1.0.0 Added
	 *
	 * @param \Wbcr_Factory439_Plugin $plugin
	 * @param string                  $content
	 */
	public function __construct( \Wbcr_Factory439_Plugin $plugin, $content ) {

		$this->plugin  = $plugin;
		$this->content = $content;

		if ( ! empty( $this->content ) ) {
			if ( $this->plugin->isNetworkActive() && $this->plugin->isNetworkAdmin() ) {
				add_action( 'wp_network_dashboard_setup', [ $this, 'add_dashboard_widgets' ], 999 );

				return;
			}

			add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widgets' ], 999 );
		}
	}

	/**
	 * Add the News widget to the dashboard.
	 *
	 * @since 1.0.0 Added
	 */
	public function add_dashboard_widgets() {
		$widget_id = 'wbcr-factory-adverts-widget';

		wp_add_dashboard_widget( $widget_id, $this->plugin->getPluginTitle() . ' News', [
			$this,
			'print_widget_content'
		] );

		$this->sort_dashboard_widgets( $widget_id );
	}

	/**
	 * Create the function to output the contents of the Dashboard Widget.
	 *
	 * @since 1.0.0 Added
	 */
	public function print_widget_content() {
		?>
        <div class="wordpress-news hide-if-no-js">
            <div class="rss-widget">
				<?php echo $this->content; ?>
            </div>
        </div>
		<?php

	}

	/**
	 * Сортируем виджеты на странице дашбоард
	 *
	 * Виджеты должны быть в таком порядке, чтобы наш виджет был выше всех.
	 *
	 * @author        Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @author        Alexander Vitkalov <nechin.va@gmail.com>
	 *
	 * @since         1.0.2 Добавлена поддержка мультисайтов
	 * @since         1.0.0 Добавлен
	 *
	 * @param string $widget_id   ID нашего виджета
	 */
	private function sort_dashboard_widgets( $widget_id ) {
		global $wp_meta_boxes;

		$location = $this->plugin->isNetworkAdmin() ? 'dashboard-network' : 'dashboard';

		$normal_core   = $wp_meta_boxes[ $location ]['normal']['core'];
		$widget_backup = [ $widget_id => $normal_core[ $widget_id ] ];
		unset( $normal_core[ $widget_id ] );
		$sorted_core = array_merge( $widget_backup, $normal_core );

		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_core;
	}
}
