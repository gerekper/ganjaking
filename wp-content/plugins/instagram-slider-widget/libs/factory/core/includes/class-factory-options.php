<?php

namespace WBCR\Factory_439;

// Exit if accessed directly
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Трейт используется для расширения базового класса плагина Wbcr_Factory439_Base, позволяя работать с опциями плагина.
 *
 * Этот трейт является оберткой для Wordpress функций get_option, get_site_option, update_option, update_site_option,
 * delete_option, delete_site_option. Основная задача была получать, обновлять, удалять опции без использования префиксов,
 * чтобы класс выполнял эту работу за программиста. В дополнение, трейт содержит методы для полной выгрузки всех опций
 * плагина, что позволяет при инициализации плагина автоматически выгрузить все существующие опции плагина в объектный
 * кеш. Все опции, с которыми работает плагин, могут быть отфильтрованы.
 *
 * Документация по трейту: https://webcraftic.atlassian.net/wiki/spaces/FFD/pages/393805831/
 * Документация по созданию плагина: https://webcraftic.atlassian.net/wiki/spaces/CNCFC/pages/327828
 * Репозиторий: https://github.com/alexkovalevv
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 *
 * @since         4.0.8  - Добавлен
 * @package       factory-core
 */
trait Options {

	/**
	 * Абстрактный метод, должен быть реализован в классе использующем этот трейт. Метод позволяет получить префикс
	 * плагина для формирования имен опций в базе данных Wordpress. У опций должно быть свое пространство имен,
	 * иначе может быть конфликт с другими плагинами или с сами ядром Wordpress.
	 *
	 * @since  4.0.8 - Добавлен
	 * @return string Возвращает префикс плагина. Пример: wbcr_clearfy_
	 */
	abstract public function getPrefix();

	/**
	 * Выгружает все опции плагина в объектный кеш. Плагин может получить любую свою опцию без запроса к базе данных.
	 * Метод ускоряет работу плагина, если опций очень много.
	 *
	 * Используется только один раз при инициализации плагина.
	 *
	 * @since 4.0.8 - Добавлен
	 */
	public function loadAllOptions() {
		global $wpdb;

		$is_option_loaded = wp_cache_get( $this->getPrefix() . 'all_options_loaded', $this->getPrefix() . 'options' );

		if ( false === $is_option_loaded ) {
			$result = $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE '{$this->getPrefix()}%'" );

			$options = [];

			if ( ! empty( $result ) ) {
				wp_cache_add( $this->getPrefix() . 'all_options_loaded', 1, $this->getPrefix() . 'options' );

				foreach ( $result as $option ) {
					$value = maybe_unserialize( $option->option_value );
					$value = $this->normalizeValue( $value );

					wp_cache_add( $option->option_name, $value, $this->getPrefix() . 'options' );
					$options[ $option->option_name ] = $value;
				}

				/**
				 * Действие, которое будет выполнено, когда все опции плагина будут выгружены.
				 *
				 * @since 4.0.9  - Добавлен
				 *
				 * @param string $plugin_name   Имя плагина
				 * @param array  $options       Ассоциативный массив опций плагина
				 */
				do_action( 'wbcr/factory/all_options_loaded', $options, $this->plugin_name );
			}
		}
	}

	/**
	 * Выгружает все сетевые опции плагина в объектный кеш. Плагин может получить любую свою опцию без запроса к базе
	 * данных. Метод ускоряет работу плагина, если опций очень много.
	 *
	 * Используется и работает только в режиме мультисайтов, один раз при инициализации плагина.!
	 *
	 * @since 4.0.8  - Добавлен
	 * @return void
	 */
	public function loadAllNetworkOptions() {
		global $wpdb;

		$network_id = (int) get_current_network_id();

		$is_option_loaded = wp_cache_get( $network_id . ":" . $this->getPrefix() . 'all_options_loaded', $this->getPrefix() . 'network_options' );

		if ( false === $is_option_loaded ) {
			wp_cache_add_global_groups( [ $this->getPrefix() . 'network_options' ] );

			$result = $wpdb->get_results( "SELECT meta_key, meta_value FROM {$wpdb->sitemeta} WHERE site_id='{$network_id}' AND meta_key LIKE '{$this->getPrefix()}%'" );

			$options = [];
			if ( ! empty( $result ) ) {
				wp_cache_add( $network_id . ":" . $this->getPrefix() . 'all_options_loaded', 1, $this->getPrefix() . 'network_options' );

				foreach ( $result as $option ) {
					$value = maybe_unserialize( $option->meta_value );
					$value = $this->normalizeValue( $value );

					$cache_key = $network_id . ":" . $option->meta_key;
					wp_cache_add( $cache_key, $value, $this->getPrefix() . 'network_options' );
					$options[ $option->meta_key ] = $value;
				}

				/**
				 *
				 * Действие, которое будет выполнено, когда все сетевые опции плагина будут выгружены.
				 *
				 * @since 4.0.9 - Добавлен
				 *
				 * @param array  $options       Ассоциативный массив опций плагина
				 * @param string $plugin_name   Имя плагина
				 */
				do_action( 'wbcr/factory/all_network_options_loaded', $options, $this->plugin_name );
			}
		}
	}

	/**
	 * Позволяет получить популярную опцию. В случае если плагин установлен для сети (в режиме мультисайтов),
	 * то метод возвращает опции только для сети, иначе метод возвращает опцию для текущего сайта. Работает
	 * на основе self::getOption и self::getNetworkOption, смотрите полную реализацию в этих методах.
	 *
	 * @since 4.0.8 - Добавлен
	 *
	 * @param string $option_name   Имя опции без префикса.
	 * @param mixed  $default       Значение по умолчанию. Если опции нет в базе данных, будет возвращено это значение. По умолчанию false
	 *
	 * @return mixed Возвращает значение опции, если это сериализованная строка, то автоматически распаковывает ее.
	 */
	public function getPopulateOption( $option_name, $default = false ) {
		if ( $this->isNetworkActive() ) {
			$option_value = $this->getNetworkOption( $option_name, $default );
		} else {
			$option_value = $this->getOption( $option_name, $default );
		}

		/**
		 * Фильтр позволяет отфильтровать возвращаемое значение популярной опции.
		 *
		 * @since 4.0.9 - Добавлен
		 *
		 * @param mixed  $option_value   Значение опции
		 * @param string $option_name    Имя опции
		 * @param mixed  $default        Значение опции по умолчанию
		 */
		return apply_filters( "wbcr/factory/populate_option_{$option_name}", $option_value, $option_name, $default );
	}

	/**
	 * Позволяет получить сетевые опции. Если плагин установлен для сети (в режиме мультисайтов), то
	 * метод возвращает опции только для сети, иначе метод возвращает опцию для текущего сайта.
	 *
	 * Опция вытаскивается из объектного кеша, после выполнения метода self:loadAllNetworkOptions,
	 * а не напрямую из базы данных, из-за чего при работе с некоторыми кеширующими плагинами,
	 * может быть странное поведение в работе плагина.
	 *
	 * @since 4.0.8 - Добавлен
	 *
	 * @param string $option_name   Имя опции без префикса.
	 * @param mixed  $default       Значение по умолчанию. Если опции нет в базе данных, будет возвращено это значение. По умолчанию false
	 *
	 * @return mixed Возвращает значение опции, если это сериализованная строка, то автоматически распаковывает ее.
	 */
	public function getNetworkOption( $option_name, $default = false ) {
		if ( empty( $option_name ) || ! is_string( $option_name ) ) {
			throw new Exception( 'Option name must be a string and must not be empty.' );
		}

		if ( ! is_multisite() ) {
			return $this->getOption( $option_name, $default );
		}

		$this->loadAllNetworkOptions();

		$network_id   = (int) get_current_network_id();
		$cache_key    = $network_id . ':' . $this->getPrefix() . $option_name;
		$option_value = wp_cache_get( $cache_key, $this->getPrefix() . 'network_options' );

		if ( false === $option_value ) {
			$option_value = $default;
		}

		/**
		 * Фильтр позволяет отфильтровать возвращаемое значение сетевой опции.
		 *
		 * @since 4.0.9 - Добавлен
		 *
		 * @param mixed  $option_value   Значение опции
		 * @param string $option_name    Имя опции
		 * @param mixed  $default        Значение опции по умолчанию
		 * @param int    $network_id     ID сети
		 */

		return apply_filters( "wbcr/factory/network_option_{$option_name}", $option_value, $option_name, $default, $network_id );
	}

	/**
	 * Метод позволяет получить опцию для текущего сайта. Опция вытаскивается из объектного кеша, после выполнения метода
	 * self:loadAllOptions, а не напрямую из базы данных, из-за чего при работе с некоторыми кеширующими плагинами,
	 * может быть странное поведение в работе плагина.
	 *
	 * @since 4.0.0 - Добавлен
	 * @since 4.0.8 - Полностью переделан
	 *
	 * @param string $option_name   Имя опции без префикса.
	 * @param mixed  $default       Значение по умолчанию. Если опции нет в базе данных, будет возвращено это значение. По умолчанию false
	 *
	 * @return mixed
	 */
	public function getOption( $option_name, $default = false ) {
		if ( empty( $option_name ) || ! is_string( $option_name ) ) {
			throw new Exception( 'Option name must be a string and must not be empty.' );
		}

		$this->loadAllOptions();

		$option_value = wp_cache_get( $this->getPrefix() . $option_name, $this->getPrefix() . 'options' );

		if ( false === $option_value ) {
			$option_value = $default;
		}

		/**
		 * Фильтр позволяет отфильтровать возвращаемое значение опции сайта.
		 *
		 * @since 4.0.9 - Добавлен
		 *
		 * @param mixed  $option_value   Значение опции
		 * @param string $option_name    Имя опции
		 * @param mixed  $default        Значение опции по умолчанию
		 */

		return apply_filters( "wbcr/factory/option_{$option_name}", $option_value, $option_name, $default );
	}

	/**
	 * Позволяет обновить популярную опцию в базе данных. Если плагин установлен для сети (в режиме мультисайтов), то метод обновляет опцию
	 * только в таблице sitemeta, иначе в таблице options для текущего сайта.
	 *
	 * @param string $option_name    Имя опции без префикса.
	 * @param mixed  $option_value   Значение опции. Может принимать массив или объект.
	 */
	public function updatePopulateOption( $option_name, $option_value ) {
		if ( $this->isNetworkActive() ) {
			$this->updateNetworkOption( $option_name, $option_value );
		} else {
			$this->updateOption( $option_name, $option_value );
		}
	}

	/**
	 * Обновляет сетевую опцию в БД таблица sitemeta. После успешного обновления опции в базе данных, метод добавляет опцию в объектный кеш,
	 * чтобы плагин мог приступить к работе с этой опцией незамедлительно.
	 *
	 * @since 4.0.8 - Добавлен
	 *
	 * @param string $option_name    Имя опции без префикса.
	 * @param mixed  $option_value   Значение опции. Может принимать массив или объект.
	 */
	public function updateNetworkOption( $option_name, $option_value ) {
		$network_id = (int) get_current_network_id();
		$cache_key  = $network_id . ':' . $this->getPrefix() . $option_name;
		wp_cache_set( $cache_key, $option_value, $this->getPrefix() . 'network_options' );

		update_site_option( $this->getPrefix() . $option_name, $option_value );

		/**
		 * Действие будет выполнено, когда сетевая опция будет обновлена.
		 *
		 * @since 4.0.8 - Добавлен
		 *
		 * @param string $option_name    Имя опции без префикса.
		 * @param mixed  $option_value   Значение опции. Может принимать массив или объект.
		 */
		do_action( "wbcr/factory/update_network_option", $option_name, $option_value );
	}

	/**
	 * Обновляет опцию сайта в БД таблица options. После успешного обновления опции в базе данных, метод добавляет опцию в объектный кеш,
	 * чтобы плагин мог приступить к работе с этой опцией незамедлительно.
	 *
	 * @since 4.0.0 - Добавлен
	 * @since 4.0.8 - Полностью переделан
	 *
	 * @param string $option_name    Имя опции без префикса.
	 * @param mixed  $option_value   Значение опции. Может принимать массив или объект.
	 *
	 * @return bool
	 */
	public function updateOption( $option_name, $option_value ) {
		wp_cache_set( $this->getPrefix() . $option_name, $option_value, $this->getPrefix() . 'options' );
		$result = update_option( $this->getPrefix() . $option_name, $option_value );

		/**
		 * @since 4.0.8
		 *
		 * @param string $option_name
		 *
		 * @param mixed  $option_value
		 */
		do_action( "wbcr/factory/update_option", $option_name, $option_value );

		return $result;
	}

	/**
	 * Позволяет удалять популярную опцию в базе данных. Если плагин установлен для сети (в режиме мультисайтов), то метод удаляет опцию
	 * только в таблице sitemeta, иначе в таблице options для текущего сайта.
	 *
	 * @since 4.0.0 - Добавлен
	 *
	 * @param string $option_name   Имя опции без префикса.
	 */
	public function deletePopulateOption( $option_name ) {
		if ( $this->isNetworkActive() ) {
			$this->deleteNetworkOption( $option_name );
		} else {
			$this->deleteOption( $option_name );
		}
	}

	/**
	 * Удаляет сетевую.опцию в БД таблица sitemeta, а если опция есть в кеше, индивидуально удаляет опцию из кеша.
	 *
	 * @since 4.0.0 - Добавлен
	 *
	 * @param string $option_name   Имя опции без префикса.
	 *
	 * @return bool Возвращает true, если опция удалена успешно, false в случае ошибки.
	 */
	public function deleteNetworkOption( $option_name ) {
		$network_id   = (int) get_current_network_id();
		$cache_key    = $network_id . ':' . $this->getPrefix() . $option_name;
		$delete_cache = wp_cache_delete( $cache_key, $this->getPrefix() . 'network_options' );

		$delete_opt1 = delete_site_option( $this->getPrefix() . $option_name );

		return $delete_cache && $delete_opt1;
	}

	/**
	 * Удаляет опцию сайта в БД таблица options, а если опция есть в кеше, индивидуально удаляет опцию из кеша.
	 *
	 * @since 4.0.0 - Добавлен
	 *
	 * @param string $option_name   Имя опции без префикса.
	 *
	 * @return bool Возвращает true, если опция удалена успешно, false в случае ошибки.
	 */
	public function deleteOption( $option_name ) {
		$delete_cache = wp_cache_delete( $this->getPrefix() . $option_name, $this->getPrefix() . 'options' );

		// todo: удалить, когда большая часть пользователей обновятся до современных релизов
		$delete_opt1 = delete_option( $this->getPrefix() . $option_name . '_is_active' );
		$delete_opt2 = delete_option( $this->getPrefix() . $option_name );

		return $delete_cache && $delete_opt1 && $delete_opt2;
	}

	/**
	 * Сбрасывает объектный кеш. Может использоваться для перезагрузки опций плагина и Wordpress в целом.
	 *
	 * @since 4.0.0 - Добавлен
	 * @return bool Возвращает true, если кеш сброшен успешно, false в случае ошибки.
	 */
	public function flushOptionsCache() {
		return wp_cache_flush();
	}

	/**
	 * Позволяет получить полное имя опции с префиксом. Может быть использовано в тех случаях, где нужно получить
	 * полное имя опции.
	 *
	 * @since 4.0.0 - Добавлен
	 *
	 * @param string $option_name   Имя опции без префикса.
	 *
	 * @return null|string Возвращает имя опции с префиксом. Например wbcr_clearfy_{options_name}
	 */
	public function getOptionName( $option_name ) {
		$option_name = trim( rtrim( $option_name ) );
		if ( empty( $option_name ) || ! is_string( $option_name ) ) {
			return null;
		}

		return $this->getPrefix() . $option_name;
	}

	/**
	 * Позволяет нормализовать данные. В некоторых методах этого трейта, ожидаются данные определенного типа, чтобы
	 * выполнить различные логические операции. Как раз в этом случае этот метод можно использовать, чтобы привести
	 * все сырые данные в строгий тип. Такое решение позволит избежать ошибок в работе программиста.
	 *
	 * @since 4.0.0 - Добавлен
	 *
	 * @param mixed $data   Данные, которые нужно нормализовать.
	 *
	 * @return mixed Возвращает нормализованное значение.
	 *               - Если передана строка "true" или "false" вернет булево значение.
	 *               - Если передана строка "1" или "0" вернет число.
	 */
	public function normalizeValue( $data ) {
		if ( is_string( $data ) ) {
			$check_string = rtrim( trim( $data ) );

			if ( $check_string == "1" || $check_string == "0" ) {
				return intval( $data );
			} else if ( $check_string === 'false' ) {
				return false;
			} else if ( $check_string === 'true' ) {
				return true;
			}
		}

		return $data;
	}
}
