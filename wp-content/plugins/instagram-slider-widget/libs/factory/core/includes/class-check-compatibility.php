<?php

/**
 * Проверяет совместимость с плагинами Webcraftic, с версиями php, с версиями Wordpress
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 *
 * @version       1.0.0
 * @since         4.0.8
 */

if ( ! class_exists( 'Wbcr_Factory_Compatibility' ) ) {
	class Wbcr_Factory_Compatibility {

		protected $plugin_prefix;
		protected $plugin_class_prefix;
		protected $plugin_name;
		protected $plugin_title = "(no title)";
		protected $required_php_version = '5.3';
		protected $required_wp_version = '4.2.0';

		function __construct( array $plugin_info ) {
			foreach ( (array) $plugin_info as $property => $value ) {
				$this->$property = $value;
			}

			add_action( 'admin_init', [ $this, 'registerNotices' ] );
		}

		/**
		 * Метод проверяет совместимость плагина с php и wordpress версией
		 *
		 * @return bool
		 */
		public function check() {
			if ( ! $this->isPhpCompatibility() ) {
				return false;
			}

			if ( ! $this->isWpCompatibility() ) {
				return false;
			}

			return true;
		}

		/**
		 * Метод проверяет совместимость плагина с php версией сервера
		 *
		 * @return mixed
		 */
		public function isPhpCompatibility() {
			return version_compare( PHP_VERSION, $this->required_php_version, '>=' );
		}

		/**
		 * Метод проверяет совместимость плагина с Wordpress версией сайта
		 *
		 * @return mixed
		 */
		public function isWpCompatibility() {
			// Get the WP Version global.
			global $wp_version;

			return version_compare( $wp_version, $this->required_wp_version, '>=' );
		}

		/**
		 * Метод возвращает текст уведомления
		 *
		 * @return string
		 */
		public function getNoticeText() {
			$notice_text         = $notice_default_text = '';
			$notice_default_text .= '<b>' . $this->plugin_title . ' ' . __( 'warning', '' ) . ':</b>' . '<br>';

			$notice_default_text .= sprintf( __( 'The %s plugin has stopped.', 'wbcr_factory_clearfy_000' ), $this->plugin_title ) . ' ';
			$notice_default_text .= __( 'Possible reasons:', '' ) . ' <br>';

			$has_one = false;

			if ( ! $this->isPhpCompatibility() ) {
				$has_one     = true;
				$notice_text .= '- ' . sprintf( __( 'You need to update the PHP version to %s or higher!', 'wbcr_factory_423' ), $this->required_php_version ) . '<br>';
			}

			if ( ! $this->isWpCompatibility() ) {
				$has_one     = true;
				$notice_text .= '- ' . sprintf( __( 'You need to update WordPress to %s or higher!', 'wbcr_factory_423' ), $this->required_wp_version ) . '<br>';
			}

			if ( $has_one ) {
				$notice_text = $notice_default_text . $notice_text;
			}

			return $notice_text;
		}

		public function registerNotices() {
			if ( current_user_can( 'activate_plugins' ) && current_user_can( 'edit_plugins' ) && current_user_can( 'install_plugins' ) ) {
				if ( is_multisite() ) {
					add_action( 'network_admin_notices', [ $this, 'showNotice' ] );
				}

				add_action( 'admin_notices', [ $this, 'showNotice' ] );
			}
		}

		public function showNotice() {
			$notice_text = $this->getNoticeText();

			if ( empty( $notice_text ) ) {
				return;
			}

			$notice_text = '<p>' . $this->getNoticeText() . '</p>';

			echo '<div class="notice notice-error">' . apply_filters( 'wbcr/factory/check_compatibility/notice_text', $notice_text, $this->plugin_name ) . '</div>';
		}
	}
}