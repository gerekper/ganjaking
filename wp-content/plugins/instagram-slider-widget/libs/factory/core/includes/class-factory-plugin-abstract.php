<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Основной класс для создания плагина.
 *
 * Это основной класс плагина. который отвечает за подключение модулей фреймворка, линзирование, обновление,
 * миграции разрабатываемого плагина. При создании нового плагина, вы должны создать основной класс реализующий
 * функции плагина, этот класс будет наследовать текущий.
 *
 * Смотрите подробную инструкцию по созданию плагина и экземпляра основного класса в документации по созданию
 * плагина для Wordpress.
 *
 * Документация по классу: https://webcraftic.atlassian.net/wiki/spaces/FFD/pages/393052164
 * Документация по созданию плагина: https://webcraftic.atlassian.net/wiki/spaces/CNCFC/pages/327828
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 *
 * @since         1.0.0
 * @package       factory-core
 *
 */
abstract class Wbcr_Factory423_Plugin extends Wbcr_Factory423_Base {

	/**
	 * Instance class Wbcr_Factory423_Request, required manages http requests
	 *
	 * @see https://webcraftic.atlassian.net/wiki/spaces/FFD/pages/390561806
	 * @var Wbcr_Factory423_Request
	 */
	public $request;

	/**
	 * @see https://webcraftic.atlassian.net/wiki/spaces/FFD/pages/393936924
	 * @var \WBCR\Factory_423\Premium\Provider
	 */
	public $premium;

	/**
	 * The Bootstrap Manager class
	 *
	 * @var Wbcr_FactoryBootstrap424_Manager
	 */
	public $bootstrap;

	/**
	 * The Bootstrap Manager class
	 *
	 * @var Wbcr_FactoryForms421_Manager
	 */
	public $forms;

	/**
	 * Простой массив со списком зарегистрированных классов унаследованных от Wbcr_Factory423_Activator.
	 * Классы активации используются для упаковки набора функций, которые нужно выполнить во время
	 * активации плагина.
	 *
	 * @var array[] Wbcr_Factory423_Activator
	 */
	protected $activator_class = [];

	/**
	 * Ассоциативный массив со списком уже загруженных модулей фреймворка. Используется для того, чтобы
	 * проверить, каких модули уже были загружены, а какие еще нет.
	 *
	 * @var array
	 */
	private $loaded_factory_modules = [];

	/**
	 * Ассоциативный массив со списком аддонов плагина. Аддоны плагина являются частью одного проекта,
	 * но не как отдельный плагин.
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  4.2.0
	 * @var array
	 */
	private $loaded_plugin_components = [];

	/**
	 * The Adverts Manager class
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  4.1.9
	 * @var WBCR\Factory_Adverts_105\Base
	 */
	private $adverts;

	/**
	 * Инициализирует компоненты фреймворка и плагина.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data          A set of plugin data.
	 *
	 * @param string $plugin_path   A full path to the main plugin file.
	 *
	 * @throws Exception
	 */
	public function __construct( $plugin_path, $data ) {

		parent::__construct( $plugin_path, $data );

		$this->request = new Wbcr_Factory423_Request();
		//$this->route = new Wbcr_Factory423_Route();

		// INIT PLUGIN FRAMEWORK MODULES
		// Framework modules should always be loaded first,
		// since all other functions depend on them.
		$this->init_framework_modules();

		// INIT PLUGIN MIGRATIONS
		$this->init_plugin_migrations();

		// INIT PLUGIN NOTICES
		$this->init_plugin_notices();

		// INIT PLUGIN PREMIUM FEATURES
		// License manager should be installed earlier
		// so that other modules can access it.
		$this->init_plugin_premium_features();

		// INIT PLUGIN UPDATES
		$this->init_plugin_updates();

		// init actions
		$this->register_plugin_hooks();

		// INIT PLUGIN COMPONENTS
		$this->init_plugin_components();
	}

	/**
	 * Устанавливает класс менеджер, которому плагин будет делегировать подключение ресурсов (картинок,
	 * скриптов, стилей) фреймворка.
	 *
	 * @param Wbcr_FactoryBootstrap424_Manager $bootstrap
	 */
	public function setBootstap( Wbcr_FactoryBootstrap424_Manager $bootstrap ) {
		$this->bootstrap = $bootstrap;
	}

	/**
	 * Устанавливает класс менеджер, которому будет делегирована работа с html формами фреймворка.
	 *
	 * @param Wbcr_FactoryForms421_Manager $forms
	 */
	public function setForms( Wbcr_FactoryForms421_Manager $forms ) {
		$this->forms = $forms;
	}

	/**
	 * Устанавливает класс менеджер, которому будет делегирована работа с объявлениями в Wordpress
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  4.1.9
	 */
	public function set_adverts_manager( $class_name ) {
		if ( empty( $this->adverts ) && $this->render_adverts ) {
			$this->adverts = new $class_name( $this, $this->adverts_settings );
		}
	}

	/**
	 * Устанавливает класс провайдера лицензий
	 *
	 * С помощью этого класса, мы проверяем валидность лицензий и получаем дополнительную информацию
	 * о лицензии и ее покупателе. Класс используется в премиум менеджере.
	 *
	 * @since  4.1.6 - Добавлен
	 *
	 * @param string $name         Имя провайдер
	 * @param string $class_name   Имя класса провайдера
	 */
	public function set_license_provider( $name, $class_name ) {
		if ( ! isset( WBCR\Factory_423\Premium\Manager::$providers[ $name ] ) ) {
			WBCR\Factory_423\Premium\Manager::$providers[ $name ] = $class_name;
		}
	}

	/**
	 * Регистрируем класс репозитория
	 *
	 * С помощью этого класса мы реализиуем доставку и откат обновлений плагина, на сайт пользователя.
	 * Скачиваение премиум версий происходит по защенному каналу. Класс используется в менеджере обновлений.
	 *
	 * @since  4.1.7 - Добавлен
	 *
	 * @param string $name         Имя репозитория
	 * @param string $class_name   Имя класса репозитория
	 */
	public function set_update_repository( $name, $class_name ) {
		if ( ! isset( WBCR\Factory_423\Updates\Upgrader::$repositories[ $name ] ) ) {
			WBCR\Factory_423\Updates\Upgrader::$repositories[ $name ] = $class_name;
		}
	}

	/**
	 * Позволяет получить экземпляр менеджера объявления
	 *
	 * Доступен глобально через метод app(), чаще всего используется для создания точек для ротации
	 * рекламных объявлений.
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.1
	 * @return \WBCR\Factory_Adverts_105\Base
	 */
	public function get_adverts_manager() {
		return $this->adverts;
	}

	/**
	 * Устанавливает текстовый домен для плагина. Текстовый домен берется из заголовка входного
	 * файла плагина.
	 *
	 * @since 4.0.8 - Добавлен
	 *
	 * @see   https://codex.wordpress.org/I18n_for_WordPress_Developers
	 * @see   https://webcraftic.atlassian.net/wiki/spaces/CNCFC/pages/327828 - документация по входному файлу
	 */
	public function set_text_domain() {
		if ( empty( $this->plugin_text_domain ) ) {
			return;
		}

		$locale = apply_filters( 'plugin_locale', is_admin() ? get_user_locale() : get_locale(), $this->plugin_text_domain );

		$mofile = $this->plugin_text_domain . '-' . $locale . '.mo';

		if ( ! load_textdomain( $this->plugin_text_domain, $this->paths->absolute . '/languages/' . $mofile ) ) {
			load_muplugin_textdomain( $this->plugin_text_domain );
		}
	}

	public function newScriptList() {
		return new Wbcr_Factory423_ScriptList( $this );
	}

	public function newStyleList() {
		return new Wbcr_Factory423_StyleList( $this );
	}

	/**
	 * Все страницы плагина создаются через специальную обертку, за которую отвечает модуль
	 * фреймворка pages. Разработчик создает собственный класс, унаследованный от
	 * Wbcr_FactoryPages423_AdminPage, а затем регистрирует его через этот метод.
	 * Метод выполняет подключение класса страницы и регистрирует его в модуле фреймворка
	 * pages.
	 *
	 * Больше информации о создании и регистрации страниц, вы можете узнать из документации по созданию
	 * страниц плагина.
	 *
	 * @see https://webcraftic.atlassian.net/wiki/spaces/CNCFC/pages/222887949 - документация по созданию страниц
	 *
	 * @param string $class_name   Имя регистрируемого класса страницы. Пример: WCL_Page_Name.
	 *                             Регистрируемый класс должен быть унаследован от класса Wbcr_FactoryPages423_AdminPage.
	 * @param string $file_path    Абсолютный путь к файлу с классом страницы.
	 *
	 * @throws Exception
	 */
	public function registerPage( $class_name, $file_path ) {
		// todo: https://webcraftic.atlassian.net/projects/PCS/issues/PCS-88
		//		if ( $this->isNetworkActive() && ! is_network_admin() ) {
		//			return;
		//		}

		if ( ! file_exists( $file_path ) ) {
			throw new Exception( 'The page file was not found by the path {' . $file_path . '} you set.' );
		}

		require_once( $file_path );

		if ( ! class_exists( $class_name ) ) {
			throw new Exception( 'A class with this name {' . $class_name . '} does not exist.' );
		}

		if ( ! class_exists( 'Wbcr_FactoryPages423' ) ) {
			throw new Exception( 'The factory_pages_423 module is not included.' );
		}

		Wbcr_FactoryPages423::register( $this, $class_name );
	}

	/**
	 * Произвольные типы записей в плагине, создаются через специальную обертку, за которую отвечает
	 * модуль фреймворка types. Разработчик создает собственный класс, унаследованный от
	 * Wbcr_FactoryTypes000_Type, а затем регистрирует его через этот метод. Метод выполняет
	 * подключение класса с новым типом записи и регистрирует его в модуле фреймворка types.     *
	 *
	 * @param string $class_name   Имя регистрируемого класса страницы. Пример: WCL_Type_Name.
	 *                             Регистрируемый класс должен быть унаследован от класса Wbcr_FactoryTypes000_Type.
	 * @param string $file_path    Абсолютный путь к файлу с классом страницы.
	 *
	 * @throws Exception
	 * @deprecated 4.1.7 You cannot use it!
	 */
	public function registerType( $class_name, $file_path ) {
		throw new Exception( 'As of factory core module 4.1.7, the "registerType" method is deprecated. You cannot use it!' );
	}

	/**
	 * Registers a class to activate the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $className   class name of the plugin activator.
	 *
	 * @return void
	 */
	public function registerActivation( $className ) {
		$this->activator_class[] = $className;
	}

	/* end services region
	/* -------------------------------------------------------------*/

	/**
	 * It's invoked on plugin activation. Don't excite it directly.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function activation_hook() {

		/**
		 * @since 4.1.1 - change  hook name
		 */
		if ( apply_filters( "wbcr/factory_423/cancel_plugin_activation_{$this->plugin_name}", false ) ) {
			return;
		}

		/**
		 * wbcr_factory_423_plugin_activation
		 *
		 * @since 4.1.1 - deprecated
		 */
		wbcr_factory_423_do_action_deprecated( 'wbcr_factory_423_plugin_activation', [
			$this
		], '4.1.1', "wbcr/factory/plugin_activation" );

		/**
		 * wbcr/factory/plugin_activation
		 *
		 * @since 4.1.2 - deprecated
		 */
		wbcr_factory_423_do_action_deprecated( 'wbcr/factory/plugin_activation', [
			$this
		], '4.1.2', "wbcr/factory/before_plugin_activation" );

		/**
		 * wbcr/factory/before_plugin_activation
		 *
		 * @since 4.1.2 - added
		 */
		do_action( 'wbcr/factory/before_plugin_activation', $this );

		/**
		 * # wbcr/factory/plugin_{$this->plugin_name}_activation
		 *
		 * @since 4.1.2 - deprecated
		 */
		wbcr_factory_423_do_action_deprecated( "wbcr/factory/plugin_{$this->plugin_name}_activation", [
			$this
		], '4.1.2', "wbcr/factory/before_plugin_{$this->plugin_name}_activation" );

		/**
		 * wbcr_factory_423_plugin_activation_' . $this->plugin_name
		 *
		 * @since 4.1.1 - deprecated
		 */
		wbcr_factory_423_do_action_deprecated( 'wbcr_factory_423_plugin_activation_' . $this->plugin_name, [
			$this
		], '4.1.1', "wbcr/factory/before_plugin_{$this->plugin_name}_activation" );

		/**
		 * wbcr/factory/plugin_{$this->plugin_name}_activation
		 *
		 * @since 4.1.2 - added
		 */
		do_action( "wbcr/factory/plugin_{$this->plugin_name}_activation", $this );

		if ( ! empty( $this->activator_class ) ) {
			foreach ( (array) $this->activator_class as $activator_class ) {
				$activator = new $activator_class( $this );
				$activator->activate();
			}
		}

		/**
		 * @since 4.1.2 - added
		 */
		do_action( 'wbcr/factory/plugin_activated', $this );

		/**
		 * @since 4.1.2 - added
		 */
		do_action( "wbcr/factory/plugin_{$this->plugin_name}_activated", $this );
	}

	/**
	 * It's invoked on plugin deactionvation. Don't excite it directly.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function deactivation_hook() {

		/**
		 * @since 4.1.1 - change  hook name
		 */
		if ( apply_filters( "wbcr/factory_423/cancel_plugin_deactivation_{$this->plugin_name}", false ) ) {
			return;
		}

		/**
		 * wbcr_factory_423_plugin_deactivation
		 *
		 * @since 4.1.1 - deprecated
		 */
		wbcr_factory_423_do_action_deprecated( 'wbcr_factory_423_plugin_deactivation', [
			$this
		], '4.1.1', "wbcr/factory/plugin_deactivation" );

		/**
		 * wbcr/factory/plugin_deactivation
		 *
		 * @since 4.1.2 - deprecated
		 */
		wbcr_factory_423_do_action_deprecated( 'wbcr/factory/plugin_deactivation', [
			$this
		], '4.1.2', "wbcr/factory/before_plugin_deactivation" );

		/**
		 * wbcr/factory/plugin_deactivation
		 *
		 * @since 4.1.2 - added
		 */
		do_action( 'wbcr/factory/plugin_deactivation', $this );

		/**
		 * wbcr_factory_423_plugin_deactivation_ . $this->plugin_name
		 *
		 * @since 4.1.1 - deprecated
		 */
		wbcr_factory_423_do_action_deprecated( 'wbcr_factory_423_plugin_deactivation_' . $this->plugin_name, [
			$this
		], '4.1.1', "wbcr/factory/before_plugin_{$this->plugin_name}_deactivation" );

		/**
		 * wbcr/factory/plugin_{$this->plugin_name}_deactivation
		 *
		 * @since 4.1.2 - deprecated
		 */
		wbcr_factory_423_do_action_deprecated( "wbcr/factory/plugin_{$this->plugin_name}_deactivation", [
			$this
		], '4.1.2', "wbcr/factory/before_plugin_{$this->plugin_name}_deactivation" );

		/**
		 * @since 4.1.2 - added
		 */
		do_action( "wbcr/factory/before_plugin_{$this->plugin_name}_deactivation" );

		if ( ! empty( $this->activator_class ) ) {
			foreach ( (array) $this->activator_class as $activator_class ) {
				$activator = new $activator_class( $this );
				$activator->deactivate();
			}
		}

		/**
		 * @since 4.1.2 - added
		 */
		do_action( 'wbcr/factory/plugin_deactivated', $this );

		/**
		 * @since 4.1.2 - added
		 */
		do_action( "wbcr/factory/plugin_{$this->plugin_name}_deactivated", $this );
	}

	/**
	 * Возвращает ссылку на внутреннюю страницу плагина
	 *
	 * @param string $page_id
	 *
	 * @sicne: 4.0.8
	 * @return string|void
	 * @throws Exception
	 */
	public function getPluginPageUrl( $page_id, $args = [] ) {
		if ( ! class_exists( 'Wbcr_FactoryPages423' ) ) {
			throw new Exception( 'The factory_pages_423 module is not included.' );
		}

		if ( ! is_admin() ) {
			_doing_it_wrong( __METHOD__, __( 'You cannot use this feature on the frontend.' ), '4.0.8' );

			return null;
		}

		return Wbcr_FactoryPages423::getPageUrl( $this, $page_id, $args );
	}

	/**
	 * Загружает аддоны для плагина, как часть проекта, а не как отдельный плагин
	 *
	 * @throws \Exception
	 */
	private function init_plugin_components() {

		$load_plugin_components = $this->get_load_plugin_components();

		if ( empty( $load_plugin_components ) || ! is_array( $load_plugin_components ) ) {
			return;
		}

		foreach ( $load_plugin_components as $component_ID => $component ) {
			if ( ! isset( $this->loaded_plugin_components[ $component_ID ] ) ) {

				if ( ! isset( $component['autoload'] ) || ! isset( $component['plugin_prefix'] ) ) {
					throw new Exception( sprintf( "Component %s cannot be loaded, you must specify the path to the component autoload file and plugin prefix!", $component_ID ) );
				}

				$prefix = rtrim( $component['plugin_prefix'], '_' ) . '_';

				if ( defined( $prefix . 'PLUGIN_ACTIVE' ) ) {
					continue;
				}

				$autoload_file = trailingslashit( $this->get_paths()->absolute ) . $component['autoload'];

				if ( ! file_exists( $autoload_file ) ) {
					throw new Exception( sprintf( "Component %s autoload file not found!", $component_ID ) );
				}

				require_once( $autoload_file );

				if ( defined( $prefix . 'PLUGIN_ACTIVE' ) && class_exists( $prefix . 'Plugin' ) ) {
					$this->loaded_plugin_components[ $component_ID ] = [
						'plugin_dir'     => constant( $prefix . 'PLUGIN_DIR' ),
						'plugin_url'     => constant( $prefix . 'PLUGIN_URL' ),
						'plugin_base'    => constant( $prefix . 'PLUGIN_BASE' ),
						'plugin_version' => constant( $prefix . 'PLUGIN_VERSION' )
					];

					/**
					 * Оповещает внешние приложения, что компонент плагина был загружен
					 *
					 * @param array  $load_plugin_components   Информация о загруженном компоненте
					 * @param string $plugin_name              Имя плагина
					 */
					do_action( "wbcr/factory/component_{$component_ID}_loaded", $this->loaded_plugin_components[ $component_ID ], $this->getPluginName() );
				} else {
					throw new Exception( sprintf( "Сomponent %s does not meet development standards!", $component_ID ) );
				}
			}
		}
	}

	/**
	 * Загружает специальные модули для расширения Factory фреймворка.
	 * Разработчик плагина сам выбирает, какие модули ему нужны для
	 * создания плагина.
	 *
	 * Модули фреймворка хранятся в libs/factory/framework
	 *
	 * @return void
	 * @throws Exception
	 */
	private function init_framework_modules() {

		if ( ! empty( $this->load_factory_modules ) ) {
			foreach ( (array) $this->load_factory_modules as $module ) {
				$scope = isset( $module[2] ) ? $module[2] : 'all';

				if ( $scope == 'all' || ( is_admin() && $scope == 'admin' ) || ( ! is_admin() && $scope == 'public' ) ) {

					if ( ! file_exists( $this->get_paths()->absolute . '/' . $module[0] . '/boot.php' ) ) {
						throw new Exception( 'Module ' . $module[1] . ' is not included.' );
					}

					$module_boot_file = $this->get_paths()->absolute . '/' . $module[0] . '/boot.php';
					require_once $module_boot_file;

					$this->loaded_factory_modules[ $module[1] ] = $module_boot_file;

					do_action( 'wbcr_' . $module[1] . '_plugin_created', $this );
				}
			}
		}

		/**
		 * @since 4.1.1 - deprecated
		 */
		wbcr_factory_423_do_action_deprecated( 'wbcr_factory_423_core_modules_loaded-' . $this->plugin_name, [], '4.1.1', "wbcr/factory_423/modules_loaded-" . $this->plugin_name );

		/**
		 * @since 4.1.1 - add
		 */
		do_action( 'wbcr/factory_423/modules_loaded-' . $this->plugin_name );
	}


	/**
	 * Setups actions related with the Factory Plugin.
	 *
	 * @since 1.0.0
	 */
	private function register_plugin_hooks() {

		add_action( 'plugins_loaded', [ $this, 'set_text_domain' ] );

		if ( is_admin() ) {
			add_filter( 'wbcr_factory_423_core_admin_allow_multisite', '__return_true' );

			register_activation_hook( $this->get_paths()->main_file, [ $this, 'activation_hook' ] );
			register_deactivation_hook( $this->get_paths()->main_file, [ $this, 'deactivation_hook' ] );
		}
	}

	/**
	 * Инициализируем миграции плагина
	 *
	 * @since 4.1.1
	 * @return void
	 * @throws Exception
	 */
	protected function init_plugin_migrations() {
		new WBCR\Factory_423\Migrations( $this );
	}

	/**
	 * Инициализируем уведомления плагина
	 *
	 * @since 4.1.1
	 * @return void
	 */
	protected function init_plugin_notices() {
		new Wbcr\Factory_423\Notices( $this );
	}

	/**
	 * Создает нового рабочего для проверки обновлений и апгрейда текущего плагина.
	 *
	 * @since 4.1.1
	 *
	 * @param array $data
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function init_plugin_updates() {
		if ( $this->has_updates ) {
			new WBCR\Factory_423\Updates\Upgrader( $this );
		}
	}

	/**
	 * Начинает инициализацию лицензирования текущего плагина. Доступ к менеджеру лицензий можно
	 * получить через свойство license_manager.
	 *
	 * Дополнительно создает рабочего, чтобы совершить апгрейд до премиум версии
	 * и запустить проверку обновлений для этого модуля.
	 *
	 * @since 4.1.1
	 * @throws Exception
	 */
	protected function init_plugin_premium_features() {
		if ( ! $this->has_premium || ! $this->license_settings ) {
			$this->premium = null;

			return;
		}

		// Создаем экземляр премиум менеджера, мы сможем к нему обращаться глобально.
		$this->premium = WBCR\Factory_423\Premium\Manager::instance( $this, $this->license_settings );

		// Подключаем премиум апгрейдер
		if ( isset( $this->license_settings['has_updates'] ) && $this->license_settings['has_updates'] ) {
			new WBCR\Factory_423\Updates\Premium_Upgrader( $this );
		}
	}
}

