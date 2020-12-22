<?php

namespace WBCR\Factory_Adverts_117;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base class for adverts module.
 *
 * Contains methods for retrieving banner data for a specific position.
 * With this class user cat get advert content for a specific position.
 * This class use functional design pattern.
 *
 * @author        Alexander Vitkalov <nechin.va@gmail.com>
 * @author        Alexander Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 *
 * @since         1.0.0 Added
 * @package       factory-adverts
 * @copyright (c) 2019 Webcraftic Ltd
 */
class Base {

	/**
	 * Экзепляр плагина с которым взаимодействует этот модуль
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.1
	 * @var \Wbcr_Factory439_Plugin
	 */
	private $plugin;

	/*
	 * Contain array data with the plugin information and the module settings.
	 * Mainly used to get the name of the plugin and how to get the adverts blocks.
	 *
	 * @since 1.0.0 Added
	 *
	 * @var array   Example: array(
	 *      'dashboard_widget'      => true,
	 *      'right_sidebar'         => true,
	 *      'notice'                => true,
	 *      ...
	 * )
	 *
	 */
	private $settings = [];

	/**
	 * Экземпляр класса для работы API CreativeMotion
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.1
	 * @var \WBCR\Factory_Adverts_117\Creative_Motion_API
	 */
	private $api;

	/**
	 * Сохраняем уже полученные данные, для объектного кеширования
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.1
	 * @var array
	 */
	private $placements = [];

	/**
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.1
	 * @var array
	 */
	private $errors = [];

	/**
	 * Wbcr_Factory_Adinserter constructor.
	 *
	 * - Store plugin information and settings.
	 * - Add filter and actions.
	 * - Include dashboard widget.
	 *
	 * @since 1.0.0 Added
	 *
	 * @param \Wbcr_Factory439_Plugin $plugin
	 */
	public function __construct( \Wbcr_Factory439_Plugin $plugin, $settings ) {
		$this->plugin = $plugin;

		$this->settings = wp_parse_args( $settings, [
			'dashboard_widget'   => false, // show dashboard widget (default: false)
			'right_sidebar'      => false, // show adverts sidebar (default: false)
			'notice'             => false, // show notice message (default: false),
			'business_suggetion' => false,
			'support'            => false
		] );

		$this->api = new Creative_Motion_API( $this->plugin );

		add_filter( 'wbcr/factory/pages/impressive/widgets', [ $this, 'register_plugin_widgets' ], 10, 3 );
		add_action( 'wbcr/factory/admin_notices', [ $this, 'register_plugin_notice' ], 10, 2 );
		add_action( 'current_screen', [ $this, 'register_dashboard_widget' ], 10, 2 );
	}

	/**
	 * Directly get advert content for selected position.
	 *
	 * @since 1.0.1 Rename method. Content should now be printed.
	 * @since 1.0.0 Added
	 *
	 * @param string $position   Custom position name
	 *
	 * @return void
	 */
	public function render_placement( $position = 'right_sidebar' ) {
		$content = '';

		if ( $position ) {
			$content = $this->get_content( $position );
		}

		echo $content;
	}

	/**
	 * Register widgets.
	 *
	 * Depending on the settings, register new widgets.
	 *
	 * @since 1.0.0 Added
	 *
	 * @param array  $widgets    Already existing registered widgets
	 * @param string $position   Position for the widget
	 * @param string $plugin     Plugin object for which the hook is run
	 *
	 * @return array array(
	 *  'adverts_widget'     => '<p></p>',
	 *  'business_suggetion' => '<p></p>',
	 *  'support'            => '<p></p>',
	 *  ...
	 * )
	 */
	public function register_plugin_widgets( $widgets, $position, $plugin ) {
		if ( $plugin->getPluginName() == $this->plugin->getPluginName() && 'right' == $position ) {

			if ( $this->settings['right_sidebar'] ) {
				$content                   = $this->get_content( 'right_sidebar' );
				$widgets['adverts_widget'] = $content;

				if ( empty( $widgets['adverts_widget'] ) ) {
					if ( defined( 'FACTORY_ADVERTS_DEBUG' ) && FACTORY_ADVERTS_DEBUG ) {
						$debug_message = '<div style="background: #fff4f1;padding: 10px;color: #a58074;">';
						$debug_message .= $this->get_debug_message( 'right_sidebar' );
						$debug_message .= '</div>';

						$widgets['adverts_widget'] = $debug_message;
					} else {
						unset( $widgets['adverts_widget'] );
					}
				}
			}

			if ( $this->settings['business_suggetion'] ) {
				$content = $this->get_content( 'business_suggetion' );

				if ( ! empty( $content ) ) {
					$widgets['business_suggetion'] = $content;
				}
			}

			if ( $this->settings['support'] ) {
				$content = $this->get_content( 'support' );

				if ( ! empty( $content ) ) {
					$widgets['support'] = $content;
				}
			}
		}

		return $widgets;
	}

	/**
	 * Регистрирует уведомление для текущего плагина
	 *
	 * Мы добавляем уведомления в массив всех уведомлений плагина с ключем 'adverts_notice',
	 * то есть если другие плагины, тоже добавят свои рекламные уведомления, они просто
	 * будут перезаписывать друг друга, в итоге будет отображено только одно рекламное
	 * уведомеление. Это нужно для того, чтобы ограничить пользователя от спама.
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 *
	 * @since  1.0.1 Переделан полностью под интферфейс фреймворка. Используем встроенную систему уведомлений.
	 * @since  1.0.0 Added
	 *
	 * @param array  $notices       Массив со списком всех уведомлений, которые будут напечатыны в админ панели
	 * @param string $plugin_name   Имя плагина, передано для того, чтобы выводить уведомления условно, только для конкретного плагина
	 */
	public function register_plugin_notice( $notices, $plugin_name ) {
		if ( $plugin_name !== $this->plugin->getPluginName() ) {
			return $notices;
		}

		if ( $this->settings['notice'] ) {
			$notice_content = $this->get_content( 'notice' );

			if ( empty( $notice_content ) ) {
				# Информация для отладки
				if ( defined( 'FACTORY_ADVERTS_DEBUG' ) && FACTORY_ADVERTS_DEBUG ) {
					$debug_message = $this->get_debug_message( 'notice' );

					$notices['adverts_notice'] = [
						'id'              => 'adverts_debug',
						'type'            => 'error',
						'dismissible'     => false,
						'dismiss_expires' => 0,
						'text'            => '<p><b>' . $this->plugin->getPluginTitle() . '</b>:<br>' . $debug_message . '</p>'
					];
				}

				return $notices;
			}

			$hash = md5( $notice_content );

			$notices['adverts_notice'] = [
				'id'              => 'adverts_' . $hash,
				'type'            => 'success',
				'dismissible'     => true,
				'dismiss_expires' => 0,
				'text'            => '<p><b>' . $this->plugin->getPluginTitle() . '</b>:<br>' . $notice_content . '</p>'
			];
		}

		return $notices;
	}

	/**
	 * Include dashboard widget
	 *
	 * Include functionality the output of the widget on the dashboard.
	 * Only one dashboard widget must be shown for some plugins with this setting (dashboard_widget).
	 *
	 * @since 1.0.0 Added
	 */
	public function register_dashboard_widget() {
		if ( $this->settings['dashboard_widget'] && current_user_can( 'manage_options' ) ) {
			$current_screen = get_current_screen();

			if ( ! in_array( $current_screen->id, [ 'dashboard', 'dashboard-network' ] ) ) {
				return;
			}

			$content = $this->get_content( 'dashboard_widget' );

			if ( empty( $content ) && defined( 'FACTORY_ADVERTS_DEBUG' ) && FACTORY_ADVERTS_DEBUG ) {
				$content = $this->get_debug_message( 'dashboard_widget' );
			}

			require_once FACTORY_ADVERTS_117_DIR . '/includes/class-dashboard-widget.php';
			new Dashboard_Widget( $this->plugin, $content );
		}
	}

	/**
	 * Позволяет получить сообщение об ошибках
	 *
	 * Метод проверяет последние ошибки, которые могли произойти в результате api запроса.
	 * Если ошибки есть, он выводит предупреждение и список последних ошибок. Если ошибок нет,
	 * метод вернет просто предупреждение, что реклама не настроена.
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.0.1
	 *
	 * @param string $position   Position for the widget
	 *
	 * @return string Возвращает сообщение с последниеми ошибками для отладки
	 */
	private function get_debug_message( $position ) {
		$debug_massage = 'Plugin ads not configured or server unavailable. See full error report below.<br>';

		if ( isset( $this->errors[ $position ] ) && ! empty( $this->errors ) ) {
			$debug_massage .= '<b>Last errors:</b><br>';
			foreach ( $this->errors[ $position ] as $error_code => $error_message ) {
				$debug_massage .= 'Code: ' . $error_code . ' Error: ' . $error_message . '<br>';
			}
		}

		return $debug_massage;
	}

	/**
	 * Get advert content for selected position.
	 *
	 * @since 1.0.1 Полностью переписан
	 * @since 1.0.0 Added
	 *
	 * @param string $position   The position for advert
	 *
	 * @return string
	 */
	private function get_content( $position ) {
		if ( isset( $this->placements[ $position ] ) ) {
			return $this->placements[ $position ];
		}

		$content = $this->api->get_content( $position );

		if ( is_wp_error( $content ) ) {
			$this->errors[ $position ][ $content->get_error_code() ] = $content->get_error_message();

			return null;
		}

		$this->placements[ $position ] = $content;

		return $content;
	}
}
