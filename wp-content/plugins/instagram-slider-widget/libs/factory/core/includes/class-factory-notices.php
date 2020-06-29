<?php

namespace WBCR\Factory_423;

use Wbcr_Factory423_Plugin;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A class to manage notices.
 *
 * @since 1.0.0
 */

/**
 * A group of classes and methods to create and manage notices.
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 *
 * @package       factory-notices
 * @since         1.0.0
 */
class Notices {

	/**
	 * @var Wbcr_Factory423_Plugin
	 */
	protected $plugin;
	/**
	 * @var array
	 */
	protected $notices = [];

	/**
	 * @var array
	 */
	protected $default_where = [
		'plugins',
		'themes',
		'dashboard',
		'edit',
		'settings',
		'dashboard-network',
		'plugins-network',
		'themes-network',
		'settings-network',
	];

	/**
	 * @var array
	 */
	private $dissmised_notices;

	/**
	 * Инициализируем уведомлений сразу после загрузки модуля уведомлений
	 *
	 * @param Wbcr_Factory423_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		//default notices
		//---

		$this->plugin            = $plugin;
		$this->dissmised_notices = $this->plugin->getPopulateOption( 'factory_dismissed_notices', [] );

		add_action( 'current_screen', [ $this, 'currentScreenAction' ] );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_action( 'wp_ajax_' . $this->plugin->getPluginName() . '_dismiss_notice', [
				$this,
				'dismiss_notice'
			] );
		}
	}

	/**
	 * Регистрирует экшены для работы с уведомлениями на текущем экране странице.
	 * Уведомления собираются через фильтр wbcr_factory_admin_notices, если в массиве уведомлений,
	 * хотя бы одно, соответствует условиям в параметре $notice['where'], то метод печает вспомогательные скрипты и уведомления.
	 */
	public function currentScreenAction() {
		/**
		 * @since 2.1.2 - является устаревшим
		 */
		$this->notices = wbcr_factory_423_apply_filters_deprecated( 'wbcr_factory_notices_000_list', [
			$this->notices,
			$this->plugin->getPluginName(),
		], '2.1.2', 'wbcr/factory/admin_notices' );

		/**
		 * @since 2.1.2 - Добавлен, модуль factory_notices_000 был удален. Поэтому в этому хуке мы заменили префикс на factory_423
		 */
		$this->notices = apply_filters( 'wbcr/factory/admin_notices', $this->notices, $this->plugin->getPluginName() );

		if ( count( $this->notices ) == 0 ) {
			return;
		}

		$screen = get_current_screen();

		$has_notices = false;
		foreach ( (array) $this->notices as $notice ) {
			if ( ! isset( $notice['id'] ) ) {
				continue;
			}

			$where = ! isset( $notice['where'] ) || empty( $notice['where'] ) ? $this->default_where : $notice['where'];

			if ( in_array( $screen->base, $where ) && ! $this->is_dissmissed( $notice['id'] ) ) {
				$has_notices = true;
				break;
			};
		}

		if ( $has_notices ) {
			add_action( 'admin_footer', [ $this, 'print_js_code' ] );

			if ( $this->plugin->isNetworkActive() ) {
				if ( current_user_can( 'manage_network' ) ) {
					add_action( 'network_admin_notices', [ $this, 'show_notices' ] );
					add_action( 'admin_notices', [ $this, 'show_notices' ] );
				}
			} else {
				add_action( 'admin_notices', [ $this, 'show_notices' ] );
			}
		}
	}

	/**
	 * Показывает все зарегистрированные уведомления для текущего плагина.
	 * Уведомления показываются только на определенных страницах через параметр $notice['where'],
	 * если уведомление ранее было скрыто или не соотвествует правилам $notice['where'], оно не будет показано!
	 */
	public function show_notices() {
		if ( count( $this->notices ) == 0 ) {
			return;
		}

		if ( ! current_user_can( 'activate_plugins' ) || ! current_user_can( 'edit_plugins' ) || ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$screen = get_current_screen();

		foreach ( $this->notices as $notice ) {

			if ( ! isset( $notice['id'] ) ) {
				continue;
			}

			$where = ! isset( $notice['where'] ) || empty( $notice['where'] ) ? $this->default_where : $notice['where'];

			if ( in_array( $screen->base, $where ) && ! $this->is_dissmissed( $notice['id'] ) ) {
				$this->show_notice( $notice );
			};
		}
	}

	/**
	 * Показывает уведомление, по переданным параметрам
	 *
	 * @param array $data   - Параметры уведомления
	 *                      $data['id']    - Индентификатор уведомления
	 *                      $data['type'] - Тип уведомления (error, warning, success)
	 *                      $notice['where'] - На каких страницах показывать уведомление ('plugins', 'dashboard', 'edit')
	 *                      $data['text'] - Текст уведомления
	 *                      $data['dismissible'] - Если true, уведомление будет с кнопкой закрыть
	 *                      $data['dismiss_expires'] - Когда снова показать уведомление, нужно указывать время в unix timestamp.
	 *                      Пример time() + 3600 (1ч), уведомление будет скрыто на 1 час.
	 *                      $data['classes'] - Произвольный классы для контейнера уведомления.
	 */
	public function show_notice( $data ) {
		$settings = wp_parse_args( $data, [
			'id'              => null,
			'text'            => null,
			'type'            => 'error',
			'dismissible'     => false,
			'dismiss_expires' => 0,
			'classes'         => []
		] );

		if ( empty( $settings['id'] ) || empty( $settings['text'] ) ) {
			return;
		}

		$plugin_name = str_replace( '_', '-', $this->plugin->getPluginName() );

		$classes = array_merge( [
			'notice',
			'notice-' . $settings['type'],
			$plugin_name . '-factory-notice'
		], $settings['classes'] );

		if ( $settings['dismissible'] ) {
			$classes[] = 'is-dismissible';
			$classes[] = $plugin_name . '-factory-notice-dismiss';
		}
		?>
        <div data-name="wbcr_factory_notice_<?php echo esc_attr( $data['id'] ) ?>" data-expires="<?= esc_attr( $settings['dismiss_expires'] ) ?>" data-nonce="<?php echo wp_create_nonce( $this->plugin->getPluginName() . '_factory_dismiss_notice' ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ) ?>">
			<?= $data['text'] ?>
        </div>
		<?php
	}

	/**
	 * Когда пользователь нажимает кнопку закрыть уведомление,
	 * отправляется ajax запрос с вызовом текущего метода
	 */
	public function dismiss_notice() {
		if ( ! current_user_can( 'activate_plugins' ) || ! current_user_can( 'edit_plugins' ) || ! current_user_can( 'install_plugins' ) ) {
			wp_die( - 1, 403 );
		}

		check_admin_referer( $this->plugin->getPluginName() . '_factory_dismiss_notice', 'nonce' );

		// Имя уведомления (идентификатор)
		$name = $this->plugin->request->post( 'name', null, true );

		// Время в Unix timestamp, по истечению, которого уведомление снова будет показано
		// Если передан 0, то уведомление будет скрыто навсегда
		$expires = $this->plugin->request->post( 'expires', 0, 'intval' );

		if ( empty( $name ) ) {
			wp_send_json_error( [ 'error_message' => 'You must pass the notification "Name"! Action was rejected.' ] );
		}

		$notices = $this->plugin->getPopulateOption( "factory_dismissed_notices", [] );

		if ( ! empty( $notices ) ) {
			foreach ( (array) $notices as $notice_id => $notice_expires ) {
				if ( $notice_expires !== 0 && $notice_expires < time() ) {
					unset( $notices[ $notice_id ] );
				}
			}
		}

		$notices[ $name ] = $expires;

		$this->plugin->updatePopulateOption( 'factory_dismissed_notices', $notices );

		wp_send_json_success();
	}

	/**
	 * Javascript code
	 * Печает в подвале страницы код, для взаимодействия с сервером через ajax,
	 * код используется при нажатии на кнопку закрыть уведомление.             *
	 */
	public function print_js_code() {
		$plugin_name = str_replace( '_', '-', $this->plugin->getPluginName() );

		?>
        <script type="text/javascript">
			jQuery(function($) {

				$(document).on('click', '.<?php echo $plugin_name; ?>-factory-notice-dismiss .notice-dismiss', function() {
					$.post(ajaxurl, {
						'action': '<?php echo $this->plugin->getPluginName(); ?>_dismiss_notice',
						'name': $(this).parent().data('name'),
						'expires': $(this).parent().data('expires'),
						'nonce': $(this).parent().attr('data-nonce')
					});
				});

			});
        </script>
		<?php
	}


	/**
	 * Проверяет скрыто уведоление или нет
	 *
	 * @param string $notice_id   - имя уведомления
	 *
	 * @return bool
	 */
	protected function is_dissmissed( $notice_id ) {
		if ( ! empty( $this->dissmised_notices ) && isset( $this->dissmised_notices[ 'wbcr_factory_notice_' . $notice_id ] ) ) {
			$expires = (int) $this->dissmised_notices[ 'wbcr_factory_notice_' . $notice_id ];

			return $expires === 0 || $expires > time();
		}

		return false;
	}
}
