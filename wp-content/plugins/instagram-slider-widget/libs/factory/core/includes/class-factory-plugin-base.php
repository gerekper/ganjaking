<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Базовый класс для создания нового плагина. Полную реализацию класса смотрите в Wbcr_Factory423_Plugin
 *
 * Документация по классу: https://webcraftic.atlassian.net/wiki/spaces/FFD/pages/392724484
 * Документация по созданию плагина: https://webcraftic.atlassian.net/wiki/spaces/CNCFC/pages/327828
 * Репозиторий: https://github.com/alexkovalevv
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 *
 * @since         1.0.0
 * @package       factory-core
 */
class  Wbcr_Factory423_Base {

	use WBCR\Factory_423\Options;

	/**
	 * Обязательное свойство. Префикс, используется для создания пространство имен.
	 * Чаще всего используется на именования опций в базе данных. Также может быть
	 * использован для именования полей html форм, создания уникальных имен, хуков.
	 * Пример: wrio_
	 *
	 * Для префикса всегда используете нижнее подчеркивание справа!
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Обязательное свойство. Заголовок плагина. Используете в интерфейсе плагина,
	 * может быть использован в уведомлениях для администратора, чтобы пользователь
	 * мог понять, с каким плагином он ведет коммуникацию. Пример: Robin image optimizer
	 *
	 * @var string
	 */
	protected $plugin_title;

	/**
	 * Обязательное свойство. Имя плагина. Используется аналогично префиксу, но с небольшим
	 * отличием. Имя плагина имеет человеку понятную строку, которую можно использовать в
	 * именовании хуков, созданию условной логики. Допустимые символы [A-z0-9_].
	 * Пример: wbcr_clearfy
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * Обязательное свойство. Версия плагина в формате 0.0.0. Допустимые символы [0-9.]
	 *
	 * @var string
	 */
	protected $plugin_version;

	/**
	 * Обязательное свойство. Текстовый домен плагина, используется для подключения файлов
	 * переводов. Рекомендуется использовать slug плагина, идентичный slug в репозитории
	 * Wordpress.org
	 *
	 * @since 4.1.1
	 * @var string
	 */
	protected $plugin_text_domain;

	/**
	 * Обязательное свойство. Информация для поддержки клиента. Для начала работы плагина,
	 * достаточно только указать адрес лендинга в атрибут url. На лендинге должны быть
	 * созданы страницы features, pricing, support, docs. Если страницы (features, pricing,
	 * support, docs) не могут иметь такие же адреса, вы можете наложить карту адресов в
	 * атрибуте pages_map. К примеру: я создал страницу "Pro Features" и она имеет адрес
	 * {site}/premium-features, для pages_map в атрибуте features, я указал, что адрес
	 * страницы со списком функций имеет слаг premium-features. Теперь плагин будет понимать,
	 * что адрес страницы со списком функций будет таким:
	 * https://robin-image-optimizer.webcraftic.com/premium-features.
	 *
	 * Это свойство заполняется для того, чтобы в процессе разработки вы могли использовать
	 * экземпляр класса \WBCR\Factory_423\Entities\Support, для получения информации о сайте плагина.
	 * Тем самым вы избавляете себя от жесткого прописывания ссылок на лендинг плагина и
	 * можете изменить все ссылки в одном месте.
	 *
	 * @var array
	 */
	protected $support_details;

	/**
	 * Включение/отключение обновлений для бесплатного плагина. Если вашего плагина нет в репозитори
	 * Wordpress.org, вы можете включить собственный режим обновлений, например через GitHub или
	 * собственный репозиторий. Если установлено true, плагин будет проверять наличие обновлений
	 * для этого плагина.
	 *
	 * @var bool
	 */
	protected $has_updates = false;

	/**
	 * Настройка обновлений для бесплатного плагина. Если вы хотите настроить обновления для
	 * бесплатного плагина через собственный репозиторий (например: github), вам нужно указать имя
	 * репозитория и slug плагина. Slug может быть идентичен имени репозитория в github. Для Wordpress.org
	 * эти настройки не обязательны, так как в wordpress ядре есть встроенные функции для обновлений
	 * плагинов и тем.
	 *
	 * @var array
	 */
	protected $updates_settings = [];

	/**
	 * Включение/отключение премиум версии для плагина. Если вы создаете бесплатный плагин и хотите
	 * реализовать для него премиум версию, вам нужно начать с этого свойства. Если свойство установлено,
	 * как true, при инициализации плагина будут подключены функции лицензирования, проверки обновлений
	 * для премиум версии.
	 *
	 * @var bool
	 */
	protected $has_premium = false;

	/**
	 * Настройки лицензирования
	 *
	 * Лицензирование плагина может быть реализовано для любого провайдера,
	 * к примеру: freemius, codecanyon, templatemonster, вам нужно указать только настройки для
	 * взаимодействия с выбранным вами провайдером. Каждая реализация провайдера лицензий может иметь
	 * индивидуальный настройки, в этом примере приведены настройки для freemius провайдера
	 * WBCR\Factory_423\Premium\Provider > WBCR\Factory_Freemius_111\Premium\Provider
	 *
	 * На текущий момент существует только реализация для freemius провайдера.
	 *
	 * Для премиум плагина вы должны также указать настройки обновлений. Атрибут has_updates
	 * включает/отключает обновления для премиум плагина, в атрибуте updates_settings вы указываете
	 * дополнительные настройки обновлений.
	 *
	 * @var array
	 */
	protected $license_settings = [];

	/**
	 * Переключатель внутренней рекламы в плагине
	 *
	 * Если установить true, то плагин будет показывать рекламу компании в интерфейсе Wordpress.
	 * Рекламный модуль может отображать рекламу внутри инрефейса плагина, на странице dashboard
	 * и создавать сквозные уведомления на всех страницах админ панели Wordpress.
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  4.1.9
	 * @var bool
	 */
	protected $render_adverts = false;

	/**
	 * Настройки внутренней рекламы компании
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  4.1.9
	 * @var array
	 */
	protected $adverts_settings = [];

	/**
	 * Обязательное свойство. Подключаемые модули фреймворка.
	 *
	 * Модули фреймворка позволяют расширять его функциональность.
	 *
	 * @var array {
	 * Array with information about the loadable module
	 *      {type} string $module [0]   Relative path to the module directory
	 *      {type} string $module [1]   Module name with prefix 000
	 *      {type} string $module [2]   Scope:
	 *                                  admin  - Module will be loaded only in the admin panel,
	 *                                  public - Module will be loaded only on the frontend
	 *                                  all    - Module will be loaded everywhere
	 * }
	 */
	protected $load_factory_modules = [
		[ 'libs/factory/bootstrap', 'factory_bootstrap_424', 'admin' ],
		[ 'libs/factory/forms', 'factory_forms_421', 'admin' ],
		[ 'libs/factory/pages', 'factory_pages_423', 'admin' ],
	];

	/**
	 * Не обязательное свойство. Список подключаемых компонентов плагина.
	 *
	 * Компоненты плагина, это независимые плагины, которые расширяют возможности текущего плагина.
	 * Вы должны указать файл для автозагрузки компонента и префикс плагина, чтобы фреймворк
	 * мог обращаться к классам и константам компонентов.
	 *
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  4.2.0 Добавлен
	 * @var array Пример данных
	 *    array(
	 *       'component_ID' => array(
	 *         'autoload' => 'relative_path/autoload_filename.php',
	 *         'plugin_prefix' => 'WPRFX_'
	 *        ),
	 *        // Реальный пример
	 *       'cyrlitera' => array(
	 *          'autoload' => 'components/cyrlitera/clearfy.php',
	 *           'plugin_prefix' => 'WCTR_'
	 *        ),
	 *    )
	 */
	protected $load_plugin_components = [];


	/**
	 * Экземпляр класса \WBCR\Factory_423\Entities\Support используется для получения информации
	 * о сайте плагина. Чаще всего используется для получения ссылки на страницу с тарифами или
	 * ссылки на форму обратной связи. Встроен механизм отслеживания по utm меткам.
	 *
	 * @var \WBCR\Factory_423\Entities\Support
	 */
	protected $support;

	/**
	 * Экземпляр класса \WBCR\Factory_423\Entities\Paths используется для получения информации о
	 * путях плагина. Часто используется для получения путей или ссылок на место хранения плагина
	 * или его входного файла.
	 *
	 * @var \WBCR\Factory_423\Entities\Paths
	 */
	protected $paths;

	/**
	 * Абсолютный путь к входному файлу плагина: C://server/site.dev/wp-content/plugins/plugin_name/plugin_name.php
	 *
	 * @var string
	 */
	private $plugin_file;

	/**
	 * Свойство хранит сырые настройки плагина, а также дополнительные настройки, которые не описаны
	 * в интерфейсе класса.
	 *
	 * @var array
	 */
	private $plugin_data;

	/**
	 * Конструктор:
	 * - Заполняет свойства класса из сырых данных плагина
	 * - Выполняет проверку на обязательные настройки
	 * - Инициализирует сущности support и paths
	 *
	 * @since 4.1.1 - добавил две сущности support, paths. Удалил свойства, plugin_build
	 *                plugin_assembly, main_file, plugin_root, relative_path, plugin_url
	 * @since 4.0.8 - добавлена дополнительная логика
	 *
	 * @param string $plugin_file
	 * @param array  $data
	 *
	 * @throws Exception
	 */
	public function __construct( $plugin_file, $data ) {
		$this->plugin_file = $plugin_file;
		$this->plugin_data = $data;

		foreach ( (array) $data as $option_name => $option_value ) {
			if ( property_exists( $this, $option_name ) ) {
				$this->$option_name = $option_value;
			}
		}

		if ( empty( $this->prefix ) || empty( $this->plugin_name ) || empty( $this->plugin_title ) || empty( $this->plugin_version ) || empty( $this->plugin_text_domain ) ) {
			throw new Exception( 'One of the required attributes has not been passed (prefix, plugin_title, plugin_name, plugin_version, plugin_text_domain).' );
		}

		$this->support = new \WBCR\Factory_423\Entities\Support( $this->support_details );
		$this->paths   = new \WBCR\Factory_423\Entities\Paths( $plugin_file );

		// used only in the module 'updates'
		$this->plugin_slug = ! empty( $this->plugin_name ) ? $this->plugin_name : basename( $plugin_file );
	}

	/**
	 * При обновлении фреймворка, некоторые свойства класса были удалены. Однако плагины на старом
	 * фреймворке по прежнему используют удаленные свойства. С помощью этого магического метода мы
	 * добавляем совместимость со старыми плагинами, но при этом выводим предупреждение, что нужно
	 * обновить некоторые свойства.
	 *
	 * @param string $name   Имя свойства класса.
	 *
	 * @return mixed
	 */
	public function __get( $name ) {

		$deprecated_props = [
			'plugin_build',
			'plugin_assembly',
			'main_file',
			'plugin_root',
			'relative_path',
			'plugin_url'
		];

		if ( in_array( $name, $deprecated_props ) ) {
			$deprecated_message = 'In version 4.1.1 of the Factory framework, the class properties ';
			$deprecated_message .= '(' . implode( ',', $deprecated_props ) . ')';
			$deprecated_message .= 'have been removed. To get plugin paths, use the new paths property.' . PHP_EOL;

			$backtrace = debug_backtrace();
			if ( ! empty( $backtrace ) && isset( $backtrace[1] ) ) {
				$deprecated_message .= 'BACKTRACE:(';
				$deprecated_message .= 'File: ' . $backtrace[1]['file'];
				$deprecated_message .= 'Function: ' . $backtrace[1]['function'];
				$deprecated_message .= 'Line: ' . $backtrace[1]['line'];
				$deprecated_message .= ')';
			}

			_deprecated_argument( __METHOD__, '4.1.1', $deprecated_message );

			switch ( $name ) {
				case 'plugin_build':
					return null;
					break;
				case 'plugin_assembly':
					return null;
					break;
				case 'main_file':
					return $this->get_paths()->main_file;
					break;
				case 'plugin_root':
					return $this->get_paths()->absolute;
					break;
				case 'relative_path':
					return $this->get_paths()->basename;
					break;
				case 'plugin_url':
					return $this->get_paths()->url;
					break;
			}
		}

		return null;
	}

	/**
	 * При обновлении фреймворка, некоторые методы класса были удалены. Однако плагины на старом фреймворке
	 * по прежнему используют удаленные методы. С помощью этого магического метода мы добавляем совместимость
	 * со старыми плагинами, но при этом выводим предупреждение, что нужно обновить некоторые методы.
	 *
	 * @param string $name        Имя метода класса.
	 * @param array  $arguments   Массив аргументов метода класса.
	 *
	 * @return stdClass|null
	 * @throws Exception
	 */
	public function __call( $name, $arguments ) {

		$deprecated_methods = [
			'getPluginBuild',
			'getPluginAssembly',
			'getPluginPathInfo'
		];

		if ( in_array( $name, $deprecated_methods ) ) {
			$deprecated_message = 'In version 4.1.1 of the Factory framework, methods (' . implode( ',', $deprecated_methods ) . ') have been removed.';

			$backtrace = debug_backtrace();
			if ( ! empty( $backtrace ) && isset( $backtrace[1] ) ) {
				$deprecated_message .= 'BACKTRACE:(';
				$deprecated_message .= 'File: ' . $backtrace[1]['file'];
				$deprecated_message .= 'Function: ' . $backtrace[1]['function'];
				$deprecated_message .= 'Line: ' . $backtrace[1]['line'];
				$deprecated_message .= ')';
			}

			_deprecated_argument( __METHOD__, '4.1.1', $deprecated_message );

			if ( 'getPluginPathInfo' == $name ) {
				$object = new stdClass;

				$object->main_file     = $this->get_paths()->main_file;
				$object->plugin_root   = $this->get_paths()->absolute;
				$object->relative_path = $this->get_paths()->basename;
				$object->plugin_url    = $this->get_paths()->url;

				return $object;
			}
		}

		throw new Exception( "Method {$name} does not exist" );
	}

	/**
	 * Проверяет, включен ли премиум для этого плагина или нет.
	 *
	 * @return bool Возвращает true, если премиум пакет включен для этого плагина.
	 * См. Wbcr_Factory423_Base::has_premium
	 */
	public function has_premium() {
		return $this->has_premium;
	}

	/**
	 * Позволяет получить заголовок плагина.
	 *
	 * @return string Возвращает заголовок плагина. См. Wbcr_Factory423_Base::plugin_title
	 */
	public function getPluginTitle() {
		return $this->plugin_title;
	}

	/**
	 * Позволяет получить префикс плагина.
	 *
	 * @return string Возвращает префикс плагина.См. Wbcr_Factory423_Base::prefix
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * Позволяет получить имя плагина.
	 *
	 * @return string Возвращает имя плагина. См. Wbcr_Factory423_Base::plugin_name
	 */
	public function getPluginName() {
		return $this->plugin_name;
	}

	/**
	 * Позволяет получить версию плагина.
	 *
	 * @return string Возвращает версию плагина. См. Wbcr_Factory423_Base::plugin_version
	 */
	public function getPluginVersion() {
		return $this->plugin_version;
	}

	/**
	 * Позволяет получить список подключаемых к плагином компонентов
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  4.2.0
	 * @return array
	 */
	public function get_load_plugin_components() {
		return $this->load_plugin_components;
	}

	/**
	 * Предоставляет доступ к сырым данным плагина. Может быть полезен, если вы хотите получить
	 * какие-то данные не описанные в интерфейсе этого плагина.
	 *
	 * @param string $attr_name   Имя атрибута, который нужно получить. Идентично ключу в массиве
	 *                            Wbcr_Factory423_Base::plugin_data
	 *
	 * @return null
	 */
	public function getPluginInfoAttr( $attr_name ) {
		if ( isset( $this->plugin_data[ $attr_name ] ) ) {
			return $this->plugin_data[ $attr_name ];
		}

		return null;
	}

	/**
	 * Предоставляет доступ к экземпляру класса \WBCR\Factory_423\Entities\Support.
	 *
	 * @return \WBCR\Factory_423\Entities\Support
	 */
	public function get_support() {
		return $this->support;
	}

	/**
	 * Предоставляет доступ к экземпляру класса \WBCR\Factory_423\Entities\Paths.
	 *
	 * @return \WBCR\Factory_423\Entities\Paths
	 */
	public function get_paths() {
		return $this->paths;
	}

	/**
	 * Позволяет получить сырые данные плагина в виде объекта StdClass.
	 *
	 * @return object Возвращает объект с сырыми данными плагина. См. Wbcr_Factory423_Base::plugin_data
	 */
	public function getPluginInfo() {
		return (object) $this->plugin_data;
	}

	/**
	 * Проверяет права пользователя
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  4.2.0 Добавлен
	 * @return bool
	 */
	public function current_user_can( $capability = 'manage_options' ) {
		// Просмотр страниц: read_pages
		// Просмотр уведомлений: read_notices
		// Редактирование: edit_forms

		if ( 'manage_options' == $capability && is_multisite() && $this->isNetworkActive() ) {
			$capability = 'manage_network';
		}

		return current_user_can( $capability );
	}

	/**
	 * Проверят, находится ли пользователь в панели усправления сетью сайтов
	 *
	 * @since 4.0.8 Добавлен
	 *
	 * @return bool
	 */
	public function isNetworkAdmin() {
		return is_multisite() && is_network_admin();
	}

	/**
	 * Проверяет активирован ли плагин для сети. Если проект работает в режиме мультисайтов..
	 *
	 * @since 4.0.8 Добавлен
	 * @return bool Если true, плагин активирован для сети или в текущий момент активируется для сети.
	 */
	public function isNetworkActive() {
		// Makes sure the plugin is defined before trying to use it
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		$activate = is_plugin_active_for_network( $this->get_paths()->basename );

		if ( ! $activate && $this->isNetworkAdmin() && isset( $_GET['action'] ) && $_GET['action'] == 'activate' ) {
			return isset( $_GET['networkwide'] ) && 1 == (int) $_GET['networkwide'];
		}

		return $activate;
	}

	/**
	 * Позволяет получить все активные сайты сети. Если проект работает в режиме мультисайтов.
	 *
	 * @since 4.0.8
	 * @return array|int
	 */
	public function getActiveSites( $args = [ 'archived' => 0, 'mature' => 0, 'spam' => 0, 'deleted' => 0 ] ) {
		global $wp_version;

		if ( version_compare( $wp_version, '4.6', '>=' ) ) {
			return get_sites( $args );
		} else {
			$converted_array = [];

			$sites = wp_get_sites( $args );

			if ( empty( $sites ) ) {
				return $converted_array;
			}

			foreach ( (array) $sites as $key => $site ) {
				$obj = new stdClass();
				foreach ( $site as $attr => $value ) {
					$obj->$attr = $value;
				}
				$converted_array[ $key ] = $obj;
			}

			return $converted_array;
		}
	}
}
