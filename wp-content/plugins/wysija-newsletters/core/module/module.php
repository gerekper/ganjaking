<?php

defined('WYSIJA') or die('Restricted access');

class WYSIJA_module extends WYSIJA_control{

	/**
	 * ID of module
	 * @var string
	 */
	protected $name;

	/**
	 * view class of module
	 * @var string
	 */
	public $view;

	/**
	 * instance of view class of module
	 * @var string
	 */
	protected $view_obj;


	/**
	 * action/view of a hook
	 * @var string
	 */

	protected $view_show;

	/**
	 * data which view class will pull from
	 * @var array
	 */
	protected $data;

	protected $extended_plugin='wysija-newsletters';



	/**
	 * Define hook name and list of modules of each hook
	 * @var Array
	 * @todo: implement hook management which allows to manage hooks from admin side
	 */

	protected $is_premium = false;

	public static $hooks = array(
			'hook_stats' => array(
                                'stats_top_newsletters',
				'stats_top_subscribers',
				'stats_top_links',
				'stats_new_subscribers',
				'stats_subscriptions',
				'stats_top_domains'
			),

			// the left block in the page "subscriber detail"
			'hook_subscriber_left' => array(
			),

			// the righ block in the page "subscriber detail"
			'hook_subscriber_right' => array(
				'stats_subscriber'
			),
			'hook_subscriber_bottom' => array(
				'stats_subscribers_std',
			),
			// top of newsletter (viewstats) page
			'hook_newsletter_top' => array(

				'stats_newsletter_std',
				'stats_newsletter',
			),

			// Newsletters >> Newsletter detail: bottom block
			'hook_newsletter_bottom' => array(
				//'stats_newsletter_std',
			),

			// the block "super advanced" in Settings >> Advanced tab
			'hook_settings_super_advanced' => array(
				'archive_std'
			),

			// event: before saving settings (Admin)
			'hook_settings_before_save' => array(
				'archive_std'
			)
		);
	/**
	 * Constructor
	 * This is neccessary to override default action of WYSIJA_control::WYSIJA_control(),
	 * which always tries to load a default view object
	 */
	public function __construct() {
		if (!empty($this->model)){
			$class_name = $this->model;
			$this->model_obj = new $class_name();
			$this->model_obj->limit = 0; // quickfix "Undefined property: WYSIJA_model_statistics::$limit in views\back.php::limitPerPage()"
		}
		$this->get_view_obj($this->extended_plugin);
		if (!empty($this->view_obj) && !empty($this->model_obj)){
			$this->view_obj->model = $this->model_obj;
		}

		$this->data['module_name'] = $this->name;


		$model_config=WYSIJA::get('config','model');
		if($model_config->getValue('premium_key'))
			$this->is_premium = true;
		$this->data['is_premium'] = $this->is_premium;
	}

	/**
	 * get name of module
	 * @return string
	 */
	public function get_name(){
		return $this->name;
	}

	/**
	 * Get unique link to the module and hook. This link will be displayed as an independent page and actually it renders [wysijap] postype
	 * @param string $module_name
	 * @param string $hook_name
	 * @param array $params (key => value, key => value)
	 * @return type
	 */
	public static function get_module_link($module_name, $hook_name, $extended_plugin='wysija-newsletters', Array $params = array()) {
		$model_config=WYSIJA::get('config','model');
		$params = array_merge($params, array(
			'wysija-page' => 1,
			'controller'=>'module',
			'action' => 'init',
			'module' => $module_name,
			'extension' => $extended_plugin,
			'hook' => $hook_name
		));
		return WYSIJA::get_permalink($model_config->getValue('confirm_email_link'),$params);
	}

	/**
	 * Return Hooks List
	 * @param string $hook_name name of hook
	 * @module_name string $module_name name of a specific module
	 * @return Array list of modules
	 */
	public static function get_modules_from_hook($hook_name, $module_name = null){
		$module_list = self::get_hook_module_list();
		$modules = !empty($module_list[$hook_name]) ? $module_list[$hook_name] : array();
		if ($module_name)
			return isset($modules[$module_name]) ? array($modules[$module_name]) : array();
		return $modules;
	}

	/**
	 * Get all registered hooks and modules
	 * @return Array
	 */
	public static function get_hook_module_list(){
		return self::$hooks;
	}

	/**
	 * Execute a hook, module by module, from first one to last one
	 * @param string $hook_name
	 * @param string $params
	 * @param string $extended_plugin
	 *
	 * @todo Performance factor:
	 * We are calling the same method for free / Premium version.
	 * Some modules don't exist at free side.
	 * Some modules don't exist at Premium side.
	 * This fact leads to an other fact: we have to check_exist() in both cases.
	 * Solution 1: cache by using a static attribute, within this class
	 * Solution 2: populate data to an external file (xml), and load that file into this static attribute (with solution 1)
	 */
	public static function execute_hook($hook_name, $params, $extended_plugin='wysija-newsletters'){
		$hook_output = '';
		if (!empty(self::$hooks[$hook_name])){
			foreach (self::$hooks[$hook_name] as $module_name){
				$module = WYSIJA::get($module_name,'module',false,$extended_plugin);
				if(!empty($module) && method_exists($module, $hook_name))
					$hook_output .= $module->$hook_name($params);
			}
		}
		return $hook_output;
	}

	/**
	 * get an instance of a module class
	 * @param string $module_name module to be loaded
	 * @param type $extended_plugin : used only when calling the url from a different plugin it is used watch those files :
	 *                              -core/controller.php line 21, 23 ,24
	 * @return an instance of WYSIJA_module or its derived classes
	 */
	public static function get_instance_by_name($module_name,$extended_plugin='wysija-newsletters'){
		return WYSIJA::get($module_name,'module',false, $extended_plugin);
	}

	/**
	 * Render a view/action
	 * @return string
	 */
	public function render($buffering_output = true){
		if (!empty($this->view))
		{
			if ($buffering_output)
				ob_start();
			if (!$buffering_output)
				return $this->get_view_obj()->render($this->view_show, $this->data, true);
			else{
				$this->get_view_obj()->render($this->view_show, $this->data, true);
				$view = ob_get_contents();
				ob_end_clean();
				return $view;
			}
		}
	}

	/**
	 * initialize WYSIJA_view instance
	 * @return WYSIJA_view
	 */
	protected function get_view_obj(){
		require_once(WYSIJA_CORE.'view.php');
		require_once(WYSIJA_VIEWS.WYSIJA_SIDE.'.php');
		if (empty($this->view_obj)){

			$view_dir=WYSIJA_PLG_DIR.$this->extended_plugin.DS.'modules'.DS.$this->name; // quickfix, @todo
			$class_path=$view_dir.DS.$this->view.'.php';// @todo: check exists
			$class_name = strtoupper('wysija').'_module_view_'.$this->view;
			require_once(WYSIJA_CORE.'view.php');
			require_once($class_path);
			$this->view_obj = new $class_name();
		}
		return $this->view_obj;
	}
}