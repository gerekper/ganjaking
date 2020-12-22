<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) {
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
abstract class Wbcr_Factory439_Plugin extends Wbcr_Factory439_Base {

	/**
	 * Instance class Wbcr_Factory439_Request, required manages http requests
	 *
	 * @see https://webcraftic.atlassian.net/wiki/spaces/FFD/pages/390561806
	 * @var Wbcr_Factory439_Request
	 */
	public $request;

	/**
	 * @see https://webcraftic.atlassian.net/wiki/spaces/FFD/pages/393936924
	 * @var \WBCR\Factory_439\Premium\Provider
	 */
	public $premium;

	/**
	 * The Bootstrap Manager class
	 *
	 * @var Wbcr_FactoryBootstrap439_Manager
	 */
	public $bootstrap;

	/**
	 * The Bootstrap Manager class
	 *
	 * @var Wbcr_FactoryForms436_Manager
	 */
	public $forms;

	/**
	 * Простой массив со списком зарегистрированных классов унаследованных от Wbcr_Factory439_Activator.
	 * Классы активации используются для упаковки набора функций, которые нужно выполнить во время
	 * активации плагина.
	 *
	 * @var array[] Wbcr_Factory439_Activator
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
	 * @var WBCR\Factory_Adverts_117\Base
	 */
	private $adverts;

	/**
	 * The Logger class
	 *
	 * @author Artem Prihodko <webtemyk@yandex.ru>
	 * @since  4.3.7
	 * @var WBCR\Factory_Logger_000\Logger
	 */
	public $logger;

	/**
	 * Инициализирует компоненты фреймворка и плагина.
	 *
	 * @param array $data A set of plugin data.
	 *
	 * @param string $plugin_path A full path to the main plugin file.
	 *
	 * @throws Exception
	 * @since 1.0.0
	 *
	 */
	public function __construct($plugin_path, $data)
	{

		parent::__construct($plugin_path, $data);

		$this->request = new Wbcr_Factory439_Request();
		//$this->route = new Wbcr_Factory439_Route();

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

		if( wp_doing_ajax() && isset($_REQUEST['action']) ) {
			if( "wfactory-439-intall-component" == $_REQUEST['action'] ) {
				add_action('wp_ajax_wfactory-439-intall-component', [$this, 'ajax_handler_install_components']);
			}

			if( "wfactory-439-prepare-component" == $_REQUEST['action'] ) {
				add_action('wp_ajax_wfactory-439-prepare-component', [$this, 'ajax_handler_prepare_component']);
			}
			if( "wfactory-439-creativemotion-install-plugin" == $_REQUEST['action'] ) {
				add_action('wp_ajax_wfactory-439-creativemotion-install-plugin', [
					$this,
					'ajax_handler_install_creativemotion_plugins'
				]);
			}
		}
	}

	// Ajax Handlers
	// --------------------------------------------------------

	public function ajax_handler_install_components()
	{
		require_once FACTORY_439_DIR . '/ajax/install-addons.php';
		wfactory_439_install_components($this);
	}

	public function ajax_handler_prepare_component()
	{
		require_once FACTORY_439_DIR . '/ajax/install-addons.php';
		wfactory_439_prepare_component($this);
	}

	public function ajax_handler_install_creativemotion_plugins()
	{
		require_once FACTORY_439_DIR . '/ajax/install-addons.php';
		wfactory_439_creativemotion_install_plugin($this);
	}
	// --------------------------------------------------------

	/**
	 * Устанавливает класс менеджер, которому плагин будет делегировать подключение ресурсов (картинок,
	 * скриптов, стилей) фреймворка.
	 *
	 * @param Wbcr_FactoryBootstrap439_Manager $bootstrap
	 */
	public function setBootstap(Wbcr_FactoryBootstrap439_Manager $bootstrap)
	{
		$this->bootstrap = $bootstrap;
	}

	/**
	 * Устанавливает класс менеджер, которому будет делегирована работа с html формами фреймворка.
	 *
	 * @param Wbcr_FactoryForms436_Manager $forms
	 */
	public function setForms(Wbcr_FactoryForms436_Manager $forms)
	{
		$this->forms = $forms;
	}

	/**
	 * Устанавливает класс менеджер, которому будет делегирована работа с объявлениями в Wordpress
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  4.1.9
	 */
	public function set_adverts_manager($class_name)
	{
		if( empty($this->adverts) && $this->render_adverts ) {
			$this->adverts = new $class_name($this, $this->adverts_settings);
		}
	}

	/**
	 * Устанавливает класс менеджер, которому будет делегирована работа с объявлениями в Wordpress
	 *
	 * @param string $class_name Logger class name
	 * @param array  $settings Logger settings
	 *
	 * @author Artem Prihodko <webtemyk@yandex.ru>
	 * @since  4.3.7
	 */
	public function set_logger($class_name, $settings = [])
	{
		if( empty($this->logger) ) {
			$this->logger = new $class_name($this, $settings);
		}
	}

	/**
	 * Устанавливает класс провайдера лицензий
	 *
	 * С помощью этого класса, мы проверяем валидность лицензий и получаем дополнительную информацию
	 * о лицензии и ее покупателе. Класс используется в премиум менеджере.
	 *
	 * @param string $name Имя провайдер
	 * @param string $class_name Имя класса провайдера
	 *
	 * @since  4.1.6 - Добавлен
	 *
	 */
	public function set_license_provider($name, $class_name)
	{
		if( !isset(WBCR\Factory_439\Premium\Manager::$providers[$name]) ) {
			WBCR\Factory_439\Premium\Manager::$providers[$name] = $class_name;
		}
	}

	/**
	 * Регистрируем класс репозитория
	 *
	 * С помощью этого класса мы реализиуем доставку и откат обновлений плагина, на сайт пользователя.
	 * Скачиваение премиум версий происходит по защенному каналу. Класс используется в менеджере обновлений.
	 *
	 * @param string $name Имя репозитория
	 * @param string $class_name Имя класса репозитория
	 *
	 * @since  4.1.7 - Добавлен
	 *
	 */
	public function set_update_repository($name, $class_name)
	{
		if( !isset(WBCR\Factory_439\Updates\Upgrader::$repositories[$name]) ) {
			WBCR\Factory_439\Updates\Upgrader::$repositories[$name] = $class_name;
		}
	}

	/**
	 * Позволяет получить экземпляр менеджера объявления
	 *
	 * Доступен глобально через метод app(), чаще всего используется для создания точек для ротации
	 * рекламных объявлений.
	 *
	 * @return \WBCR\Factory_Adverts_117\Base
	 * @since  1.1
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 */
	public function get_adverts_manager()
	{
		return $this->adverts;
	}

	/**
	 * Устанавливает текстовый домен для плагина. Текстовый домен берется из заголовка входного
	 * файла плагина.
	 *
	 * @since 4.2.5 - Добавлены 2 аргумента $text_domain, $plugin_dir. Теперь protected
	 * @since 4.0.8 - Добавлен
	 *
	 * @see   https://codex.wordpress.org/I18n_for_WordPress_Developers
	 * @see   https://webcraftic.atlassian.net/wiki/spaces/CNCFC/pages/327828 - документация по входному файлу
	 */
	protected function set_text_domain($text_domain, $plugin_dir)
	{
		if( empty($text_domain) || empty($plugin_dir) ) {
			return;
		}

		$locale = apply_filters('plugin_locale', is_admin() ? get_user_locale() : get_locale(), $text_domain);

		$mofile = $text_domain . '-' . $locale . '.mo';

		if( !load_textdomain($text_domain, $plugin_dir . '/languages/' . $mofile) ) {
			load_muplugin_textdomain($text_domain);
		}
	}

	public function newScriptList()
	{
		return new Wbcr_Factory439_ScriptList($this);
	}

	public function newStyleList()
	{
		return new Wbcr_Factory439_StyleList($this);
	}

	/**
	 * Все страницы плагина создаются через специальную обертку, за которую отвечает модуль
	 * фреймворка pages. Разработчик создает собственный класс, унаследованный от
	 * Wbcr_FactoryPages438_AdminPage, а затем регистрирует его через этот метод.
	 * Метод выполняет подключение класса страницы и регистрирует его в модуле фреймворка
	 * pages.
	 *
	 * Больше информации о создании и регистрации страниц, вы можете узнать из документации по созданию
	 * страниц плагина.
	 *
	 * @see https://webcraftic.atlassian.net/wiki/spaces/CNCFC/pages/222887949 - документация по созданию страниц
	 *
	 * @param string $class_name Имя регистрируемого класса страницы. Пример: WCL_Page_Name.
	 *                             Регистрируемый класс должен быть унаследован от класса Wbcr_FactoryPages438_AdminPage.
	 * @param string $file_path Абсолютный путь к файлу с классом страницы.
	 *
	 * @throws Exception
	 */
	public function registerPage($class_name, $file_path)
	{
		// todo: https://webcraftic.atlassian.net/projects/PCS/issues/PCS-88
		//		if ( $this->isNetworkActive() && ! is_network_admin() ) {
		//			return;
		//		}

		if( !file_exists($file_path) ) {
			throw new Exception('The page file was not found by the path {' . $file_path . '} you set.');
		}

		require_once($file_path);

		if( !class_exists($class_name) ) {
			throw new Exception('A class with this name {' . $class_name . '} does not exist.');
		}

		if( !class_exists('Wbcr_FactoryPages438') ) {
			throw new Exception('The factory_pages_438 module is not included.');
		}

		Wbcr_FactoryPages438::register($this, $class_name);
	}

	/**
	 * Произвольные типы записей в плагине, создаются через специальную обертку, за которую отвечает
	 * модуль фреймворка types. Разработчик создает собственный класс, унаследованный от
	 * Wbcr_FactoryTypes000_Type, а затем регистрирует его через этот метод. Метод выполняет
	 * подключение класса с новым типом записи и регистрирует его в модуле фреймворка types.     *
	 *
	 * @param string $class_name Имя регистрируемого класса страницы. Пример: WCL_Type_Name.
	 *                             Регистрируемый класс должен быть унаследован от класса Wbcr_FactoryTypes000_Type.
	 * @param string $file_path Абсолютный путь к файлу с классом страницы.
	 *
	 * @throws Exception
	 * @deprecated 4.1.7 You cannot use it!
	 */
	public function registerType($class_name, $file_path)
	{
		throw new Exception('As of factory core module 4.1.7, the "registerType" method is deprecated. You cannot use it!');
	}

	/**
	 * Registers a class to activate the plugin.
	 *
	 * @param string $className class name of the plugin activator.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function registerActivation($className)
	{
		$this->activator_class[] = $className;
	}

	/* end services region
	/* -------------------------------------------------------------*/

	/**
	 * It's invoked on plugin activation. Don't excite it directly.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function activation_hook()
	{

		/**
		 * @since 4.1.1 - change  hook name
		 */
		if( apply_filters("wbcr/factory_439/cancel_plugin_activation_{$this->plugin_name}", false) ) {
			return;
		}

		/**
		 * wbcr_factory_439_plugin_activation
		 *
		 * @since 4.1.1 - deprecated
		 */
		wbcr_factory_439_do_action_deprecated('wbcr_factory_439_plugin_activation', [
			$this
		], '4.1.1', "wbcr/factory/plugin_activation");

		/**
		 * wbcr/factory/plugin_activation
		 *
		 * @since 4.1.2 - deprecated
		 */
		wbcr_factory_439_do_action_deprecated('wbcr/factory/plugin_activation', [
			$this
		], '4.1.2', "wbcr/factory/before_plugin_activation");

		/**
		 * wbcr/factory/before_plugin_activation
		 *
		 * @since 4.1.2 - added
		 */
		do_action('wbcr/factory/before_plugin_activation', $this);

		/**
		 * # wbcr/factory/plugin_{$this->plugin_name}_activation
		 *
		 * @since 4.1.2 - deprecated
		 */
		wbcr_factory_439_do_action_deprecated("wbcr/factory/plugin_{$this->plugin_name}_activation", [
			$this
		], '4.1.2', "wbcr/factory/before_plugin_{$this->plugin_name}_activation");

		/**
		 * wbcr_factory_439_plugin_activation_' . $this->plugin_name
		 *
		 * @since 4.1.1 - deprecated
		 */
		wbcr_factory_439_do_action_deprecated('wbcr_factory_439_plugin_activation_' . $this->plugin_name, [
			$this
		], '4.1.1', "wbcr/factory/before_plugin_{$this->plugin_name}_activation");

		/**
		 * wbcr/factory/plugin_{$this->plugin_name}_activation
		 *
		 * @since 4.1.2 - added
		 */
		do_action("wbcr/factory/plugin_{$this->plugin_name}_activation", $this);

		if( !empty($this->activator_class) ) {
			foreach((array)$this->activator_class as $activator_class) {
				$activator = new $activator_class($this);
				$activator->activate();
			}
		}

		/**
		 * @since 4.1.2 - added
		 */
		do_action('wbcr/factory/plugin_activated', $this);

		/**
		 * @since 4.1.2 - added
		 */
		do_action("wbcr/factory/plugin_{$this->plugin_name}_activated", $this);
	}

	/**
	 * It's invoked on plugin deactionvation. Don't excite it directly.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function deactivation_hook()
	{

		/**
		 * @since 4.1.1 - change  hook name
		 */
		if( apply_filters("wbcr/factory_439/cancel_plugin_deactivation_{$this->plugin_name}", false) ) {
			return;
		}

		/**
		 * wbcr_factory_439_plugin_deactivation
		 *
		 * @since 4.1.1 - deprecated
		 */
		wbcr_factory_439_do_action_deprecated('wbcr_factory_439_plugin_deactivation', [
			$this
		], '4.1.1', "wbcr/factory/plugin_deactivation");

		/**
		 * wbcr/factory/plugin_deactivation
		 *
		 * @since 4.1.2 - deprecated
		 */
		wbcr_factory_439_do_action_deprecated('wbcr/factory/plugin_deactivation', [
			$this
		], '4.1.2', "wbcr/factory/before_plugin_deactivation");

		/**
		 * wbcr/factory/plugin_deactivation
		 *
		 * @since 4.1.2 - added
		 */
		do_action('wbcr/factory/plugin_deactivation', $this);

		/**
		 * wbcr_factory_439_plugin_deactivation_ . $this->plugin_name
		 *
		 * @since 4.1.1 - deprecated
		 */
		wbcr_factory_439_do_action_deprecated('wbcr_factory_439_plugin_deactivation_' . $this->plugin_name, [
			$this
		], '4.1.1', "wbcr/factory/before_plugin_{$this->plugin_name}_deactivation");

		/**
		 * wbcr/factory/plugin_{$this->plugin_name}_deactivation
		 *
		 * @since 4.1.2 - deprecated
		 */
		wbcr_factory_439_do_action_deprecated("wbcr/factory/plugin_{$this->plugin_name}_deactivation", [
			$this
		], '4.1.2', "wbcr/factory/before_plugin_{$this->plugin_name}_deactivation");

		/**
		 * @since 4.1.2 - added
		 */
		do_action("wbcr/factory/before_plugin_{$this->plugin_name}_deactivation");

		if( !empty($this->activator_class) ) {
			foreach((array)$this->activator_class as $activator_class) {
				$activator = new $activator_class($this);
				$activator->deactivate();
			}
		}

		/**
		 * @since 4.1.2 - added
		 */
		do_action('wbcr/factory/plugin_deactivated', $this);

		/**
		 * @since 4.1.2 - added
		 */
		do_action("wbcr/factory/plugin_{$this->plugin_name}_deactivated", $this);
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
	public function getPluginPageUrl($page_id, $args = [])
	{
		if( !class_exists('Wbcr_FactoryPages438') ) {
			throw new Exception('The factory_pages_438 module is not included.');
		}

		if( !is_admin() ) {
			_doing_it_wrong(__METHOD__, __('You cannot use this feature on the frontend.'), '4.0.8');

			return null;
		}

		return Wbcr_FactoryPages438::getPageUrl($this, $page_id, $args);
	}

	/**
	 * Allows you to get a button to install the plugin component
	 *
	 * @param $component_type
	 * @param $slug
	 * param $premium
	 *
	 * @return \WBCR\Factory_439\Components\Install_Button
	 */
	public function get_install_component_button($component_type, $slug)
	{
		require_once FACTORY_439_DIR . '/includes/components/class-install-component-button.php';

		return new \WBCR\Factory_439\Components\Install_Button($this, $component_type, $slug);
	}

	/**
	 * Allows you to get a button to delete the plugin component
	 *
	 * @param $component_type
	 * @param $slug
	 *
	 * @return \WBCR\Factory_439\Components\Delete_Button
	 */
	public function get_delete_component_button($component_type, $slug)
	{
		require_once FACTORY_439_DIR . '/includes/components/class-delete-component-button.php';

		return new WBCR\Factory_439\Components\Delete_Button($this, $component_type, $slug);
	}

	/**
	 * @param string $component_name
	 *
	 * @return bool
	 */
	public function is_activate_component($component_name)
	{
		if( !is_string($component_name) ) {
			return false;
		}

		$deactivate_components = $this->getPopulateOption('deactive_preinstall_components', []);

		if( !is_array($deactivate_components) ) {
			$deactivate_components = [];
		}

		if( $deactivate_components && in_array($component_name, $deactivate_components) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $component_name
	 *
	 * @return bool
	 */
	public function activate_component($component_name)
	{
		if( $this->is_activate_component($component_name) ) {
			return true;
		}

		do_action('wfactory/pre_activate_component', $component_name);

		$deactivate_components = $this->getPopulateOption('deactive_preinstall_components', []);

		if( !empty($deactivate_components) && is_array($deactivate_components) ) {
			$index = array_search($component_name, $deactivate_components);
			unset($deactivate_components[$index]);
		}

		if( empty($deactivate_components) ) {
			$this->deletePopulateOption('deactive_preinstall_components');
		} else {
			$this->updatePopulateOption('deactive_preinstall_components', $deactivate_components);
		}

		return true;
	}

	/**
	 * @param string $component_name
	 *
	 * @return bool
	 */
	public function deactivate_component($component_name)
	{
		if( !$this->is_activate_component($component_name) ) {
			return true;
		}

		do_action('wfactory/pre_deactivate_component', $component_name);

		$deactivate_components = $this->getPopulateOption('deactive_preinstall_components', []);

		if( !empty($deactivate_components) && is_array($deactivate_components) ) {
			$deactivate_components[] = $component_name;
		} else {
			$deactivate_components = [];
			$deactivate_components[] = $component_name;
		}

		$this->updatePopulateOption('deactive_preinstall_components', $deactivate_components);

		do_action('wfactory/deactivated_component', $component_name);

		return true;
	}

	/**
	 * Загружает аддоны для плагина, как часть проекта, а не как отдельный плагин
	 *
	 * @throws \Exception
	 */
	private function init_plugin_components()
	{

		$load_plugin_components = $this->get_load_plugin_components();

		if( empty($load_plugin_components) || !is_array($load_plugin_components) ) {
			return;
		}

		foreach($load_plugin_components as $component_ID => $component) {
			if( !isset($this->loaded_plugin_components[$component_ID]) ) {

				if( !isset($component['autoload']) || !isset($component['plugin_prefix']) ) {
					throw new Exception(sprintf("Component %s cannot be loaded, you must specify the path to the component autoload file and plugin prefix!", $component_ID));
				}

				$prefix = rtrim($component['plugin_prefix'], '_') . '_';

				if( defined($prefix . 'PLUGIN_ACTIVE') ) {
					continue;
				}

				$autoload_file = trailingslashit($this->get_paths()->absolute) . $component['autoload'];

				if( !file_exists($autoload_file) ) {
					throw new Exception(sprintf("Component %s autoload file not found!", $component_ID));
				}

				$plugin_var_name = strtolower($prefix . 'plugin');
				global $$plugin_var_name;
				$$plugin_var_name = $this;

				require_once($autoload_file);

				if( defined($prefix . 'PLUGIN_ACTIVE') ) {
					$this->loaded_plugin_components[$component_ID] = [
						'plugin_dir' => constant($prefix . 'PLUGIN_DIR'),
						'plugin_url' => constant($prefix . 'PLUGIN_URL'),
						'plugin_base' => constant($prefix . 'PLUGIN_BASE'),
						'text_domain' => constant($prefix . 'TEXT_DOMAIN'),
						'plugin_version' => constant($prefix . 'PLUGIN_VERSION')
					];

					/**
					 * Оповещает внешние приложения, что компонент плагина был загружен
					 *
					 * @param array $load_plugin_components Информация о загруженном компоненте
					 * @param string $plugin_name Имя плагина
					 */
					do_action("wbcr/factory/component_{$component_ID}_loaded", $this->loaded_plugin_components[$component_ID], $this->getPluginName());
				} else {
					throw new Exception(sprintf("Сomponent %s does not meet development standards!", $component_ID));
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
	private function init_framework_modules()
	{

		if( !empty($this->load_factory_modules) ) {
			foreach((array)$this->load_factory_modules as $module) {
				$scope = isset($module[2]) ? $module[2] : 'all';

				if( $scope == 'all' || (is_admin() && $scope == 'admin') || (!is_admin() && $scope == 'public') ) {

					if( !file_exists($this->get_paths()->absolute . '/' . $module[0] . '/boot.php') ) {
						throw new Exception('Module ' . $module[1] . ' is not included.');
					}

					$module_boot_file = $this->get_paths()->absolute . '/' . $module[0] . '/boot.php';
					require_once $module_boot_file;

					$this->loaded_factory_modules[$module[1]] = $module_boot_file;

					do_action('wbcr_' . $module[1] . '_plugin_created', $this);
				}
			}
		}

		/**
		 * @since 4.1.1 - deprecated
		 */
		wbcr_factory_439_do_action_deprecated('wbcr_factory_439_core_modules_loaded-' . $this->plugin_name, [], '4.1.1', "wbcr/factory_439/modules_loaded-" . $this->plugin_name);

		/**
		 * @since 4.1.1 - add
		 */
		do_action('wbcr/factory_439/modules_loaded-' . $this->plugin_name);
	}


	/**
	 * Setups actions related with the Factory Plugin.
	 *
	 * @since 1.0.0
	 */
	private function register_plugin_hooks()
	{

		add_action('plugins_loaded', function () {
			$this->set_text_domain($this->plugin_text_domain, $this->paths->absolute);

			if( !empty($this->loaded_plugin_components) ) {
				foreach($this->loaded_plugin_components as $component) {
					if( empty($component['text_domain']) ) {
						continue;
					}

					$this->set_text_domain($component['text_domain'], $component['plugin_dir']);
				}
			}
		});

		if( is_admin() ) {
			add_filter('wbcr_factory_439_core_admin_allow_multisite', '__return_true');

			register_activation_hook($this->get_paths()->main_file, [$this, 'activation_hook']);
			register_deactivation_hook($this->get_paths()->main_file, [$this, 'deactivation_hook']);
		}
	}

	/**
	 * Инициализируем миграции плагина
	 *
	 * @return void
	 * @throws Exception
	 * @since 4.1.1
	 */
	protected function init_plugin_migrations()
	{
		new WBCR\Factory_439\Migrations($this);
	}

	/**
	 * Инициализируем уведомления плагина
	 *
	 * @return void
	 * @since 4.1.1
	 */
	protected function init_plugin_notices()
	{
		new Wbcr\Factory_439\Notices($this);
	}

	/**
	 * Создает нового рабочего для проверки обновлений и апгрейда текущего плагина.
	 *
	 * @param array $data
	 *
	 * @return void
	 * @throws Exception
	 * @since 4.1.1
	 *
	 */
	protected function init_plugin_updates()
	{
		if( $this->has_updates ) {
			new WBCR\Factory_439\Updates\Upgrader($this);
		}
	}

	/**
	 * Начинает инициализацию лицензирования текущего плагина. Доступ к менеджеру лицензий можно
	 * получить через свойство license_manager.
	 *
	 * Дополнительно создает рабочего, чтобы совершить апгрейд до премиум версии
	 * и запустить проверку обновлений для этого модуля.
	 *
	 * @throws Exception
	 * @since 4.1.1
	 */
	protected function init_plugin_premium_features()
	{
		if( !$this->has_premium || !$this->license_settings ) {
			$this->premium = null;

			return;
		}

		// Создаем экземляр премиум менеджера, мы сможем к нему обращаться глобально.
		$this->premium = WBCR\Factory_439\Premium\Manager::instance($this, $this->license_settings);

		// Подключаем премиум апгрейдер
		if( isset($this->license_settings['has_updates']) && $this->license_settings['has_updates'] ) {
			new WBCR\Factory_439\Updates\Premium_Upgrader($this);
		}
	}
}

