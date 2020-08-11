<?php
// Require constants.
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'constants.php' );

// Require global classes autoloader
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'autoloader.php' );

defined( 'WYSIJA' ) or die( 'Restricted access' );

global $wysija_msg, $wysija_wpmsg;
if ( ! $wysija_msg ) {
	$wysija_msg = array();
}
$wysija_wpmsg = array();

class WYSIJA_object{

	/**
	 * Static variable holding core MailPoet's version
	 * @var array
	 */
	static $version = '2.14';

	function __construct(){}

  /**
	 * Order an array by param name string compare
	 *
	 * @param  array $a  Array with the param to compare
	 * @param  array $b  Array with the param to compare
	 * @return int    Sorting result from strcmp
	 */
	public static function sort_by_name( $a, $b ){
		return strcmp( strtolower( $a['name'] ), strtolower( $b['name'] ) );
	}

	/**
	 * Returns the version of based on a path
	 *
	 * @filter mailpoet/get_version
	 * @filter mailpoet/package
	 *
	 * @param string $path
	 * @return string Version of the package
	 */
	public static function get_version( $path = null ) {
		$version = self::$version;
		// Backwards compatibility for Premium Versions
		if ( ! has_filter( 'mailpoet/get_version', '_filter_mailpoet_premium_version' ) && in_array( $path, array( 'premium', 'wysija-newsletters-premium', 'wysija-newsletters-premium/index.php' ) ) ){
			if ( ! function_exists( 'get_plugin_data' ) ){
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$plugin_data = get_plugin_data( dirname( dirname( plugin_dir_path( __FILE__ ) ) ) . '/wysija-newsletters-premium/index.php' );
			$version = trim( $plugin_data['Version'] );
		}

		return apply_filters( 'mailpoet/get_version', $version, apply_filters( 'mailpoet/package', 'core', $path ) );
	}

	/**
	 * get the current_user data in a safe manner making sure a field exists before returning it's value
	 * @global type $current_user
	 * @param string $field
	 * @return mixed
	 */
	public static function wp_get_userdata( $field = false ) {
		//WordPress globals be careful there
		global $current_user;
		if ( $field ) {
			if ( function_exists( 'wp_get_current_user' ) ) {
				// Here is an exception because of one of the weirdest bug
				// the idea is to make sure we don't call wp_get_current_user() on the wysija_subscribers page when on a multisite
				if ( ! ( isset( $_GET['page'] ) && $_GET['page'] === 'wysija_subscribers' && is_multisite() ) ){
					wp_get_current_user();
				}
			}
			if ( isset( $current_user->{$field} ) ){
				return $current_user->{$field};
			} elseif ( isset( $current_user->data->{$field} ) ){
				return $current_user->data->{$field};
			} else {
				return $current_user;
			}
		}
		return $current_user;
	}

	/**
	 * set a global notice message
	 * @global array $wysija_wpmsg
	 * @param type $msg
	 */
	function wp_notice( $msg ){
		global $wysija_wpmsg;

		//add the hook only once
		if ( ! $wysija_wpmsg ) {
			add_action( 'admin_notices', array( $this, 'wp_msgs' ) );
		}

		//record msgs
		$wysija_wpmsg['updated'][] = $msg;
	}

	/**
	 * set a global error message
	 * @global array $wysija_wpmsg
	 * @param type $msg
	 */
	function wp_error( $msg ){
		global $wysija_wpmsg;

		//add the hook only once
		if ( ! $wysija_wpmsg ){
			add_action( 'admin_notices', array( $this, 'wp_msgs' ) );
		}

		//record msgs
		$wysija_wpmsg['error'][] = $msg;
	}

	/**
	 * prints a global message in the WordPress' backend identified as belonging to wysija
	 * we tend to avoid as much as possible printing messages globally, since this is ugly
	 * and make the administrators immune to beige-yellowish messages :/
	 * @global array $wysija_wpmsg
	 */
	function wp_msgs() {
		global $wysija_wpmsg;
		foreach ( $wysija_wpmsg as $keymsg => $wp2 ){
			$msgs = '<div class="' . $keymsg . ' fade">';
			foreach ( $wp2 as $mymsg ){
				$msgs .= '<p><strong>MailPoet</strong> : ' . $mymsg . '</p>';
			}
			$msgs .= '</div>';
		}

		// This is bad, we should be checking for valid HTML here.
		echo $msgs;
	}

	/**
	 * returns an error message, it will appear as a red pinkish message in our interfaces
	 * @param string $msg
	 * @param boolean $public if set to true it will appear as a full message, otherwise it will appear behind a "Show more details." link
	 * @param boolean $global if set to true it will appear on all of the backend interfaces, not only wysija's own
	 */
	function error( $msg, $public = false, $global = false ){
		$status = 'error';
		if ( $global ){
			$status = 'g-' . $status;
		}
		$this->setInfo( $status, $msg, $public );
	}

	/**
	 * returns a success message, it will appear as a beige yellowish message in our interfaces
	 * @param string $msg
	 * @param boolean $public if set to true it will appear as a full message, otherwise it will appear behind a "Show more details." link
	 * @param boolean $global if set to true it will appear on all of the backend interfaces, not only wysija's own
	 */
	function notice( $msg, $public = true, $global = false ){
		$status = 'updated';
		if ( $global ){
			$status = 'g-' . $status;
		}
		$this->setInfo( $status, $msg, $public );
	}

	/**
	 * store all of the error and success messages in a global variable
	 * @global type $wysija_msg
	 * @param type $status whether this is a success message or an error message
	 * @param type $msg
	 * @param type $public if set to true it will appear as a full message, otherwise it will appear behind a "Show more details." link
	 */
	static function setInfo($status,$msg,$public=false){
		global $wysija_msg;
		if(!$public) {

			if(!isset($wysija_msg['private'][$status])){
				$wysija_msg['private']=array();
				$wysija_msg['private'][$status]=array();
			}
			array_push($wysija_msg['private'][$status], $msg);
		}else{
			if(!isset($wysija_msg[$status]))  $wysija_msg[$status]=array();
			array_push($wysija_msg[$status], $msg);
		}

	}

	/**
	 * read the global function containing all of the error messages and print them
	 * @global type $wysija_msg
	 * @return type
	 */
	function getMsgs(){
		global $wysija_msg;

		if(isset($wysija_msg['private']['error'])){
			$wysija_msg['error'][]=str_replace(array('[link]','[/link]'),array('<a class="showerrors" href="javascript:;">','</a>'),__('An error occurred. [link]Show more details.[/link]',WYSIJA));
		}

		if(isset($wysija_msg['private']['updated'])){
			$wysija_msg['updated'][]=str_replace(array('[link]','[/link]'),array('<a class="shownotices" href="javascript:;">','</a>'),__('[link]Show more details.[/link]',WYSIJA));
		}
		if(isset($wysija_msg['private'])){
			$prv=$wysija_msg['private'];
			unset($wysija_msg['private']);
			if(isset($prv['error']))    $wysija_msg['xdetailed-errors']=$prv['error'];
			if(isset($prv['updated']))    $wysija_msg['xdetailed-updated']=$prv['updated'];
		}
		return $wysija_msg;
	}

	/**
	 * If the current server is Windows-based
	 * @return boolean
	 */
	public static function is_windows() {
		$is_windows = false;
		$windows = array(
			'windows nt',
			'windows',
			'winnt',
			'win32',
			'win'
		);
		$operating_system = strtolower( php_uname( 's' ) );
		foreach ( $windows as $windows_name ) {
			if (strpos($operating_system, $windows_name) !== false) {
				$is_windows = true;
				break;
			}
		}
		return $is_windows;
	}

}


class WYSIJA_help extends WYSIJA_object{
	var $controller = null;

	static $admin_body_class_runner = false;

	function __construct(){
		add_action( 'widgets_init', array( $this, 'widgets_init' ), 1 );

		// Only load this when ajax is not used
		if ( !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
                        add_action( 'init', array( $this, 'register_scripts' ), 1 );
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
	}

	function WYSIJA_help() { // TODO: remove in next version
	  add_action( 'widgets_init', array( $this, 'widgets_init' ), 1 );

	  // Only load this when ajax is not used
	  if ( !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		add_action( 'init', array( $this, 'register_scripts' ), 1 );
	  }

	  add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	  add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
	}

	function widgets_init() {
		//load the widget file
		require_once(WYSIJA_WIDGETS.'wysija_nl.php');
		register_widget( 'WYSIJA_NL_Widget' );
	}

	public function admin_enqueue_scripts(){
		if ( WYSIJA_ITF ){
			wp_enqueue_script( 'mailpoet-global' );
		}
	}

	public function admin_body_class( $body_class_str ){

		if ( WYSIJA_help::$admin_body_class_runner === true ){
			return $body_class_str;
		}

		WYSIJA_help::$admin_body_class_runner = true;

		global $wp_version;

		$class = array();
		if ( ! empty( $body_class_str ) ){
			$class = explode( ' ', $body_class_str );
		}

		if ( '3.8' === $wp_version ){
			$class[] = 'mp-menu-icon-font';
		}

		if ( version_compare( $wp_version, '3.8', '<' ) ){
			$class[] = 'mp-menu-icon-bg';
		} else {
			$class[] = 'mpoet-ui';
		}

		return implode( ' ', $class );
	}

	function register_scripts(){
		$helper_toolbox = WYSIJA::get('toolbox','helper');
		$wp_language_code = $helper_toolbox->get_language_code();
		$valid_language = array(
			'ar',
			'ca',
			'cs',
			'cz',
			'da',
			'de',
			'el',
			'es',
			'et',
			'fa',
			'fi',
			'fr',
			'he',
			'hr',
			'hu',
			'id',
			'it',
			'ja',
			'lt',
			'nl',
			'no',
			'pl',
			'pt',
			'pt_BR',
			'ro',
			'ru',
			'sv',
			'tr',
			'uk',
			'vi',
			'zh_CN',
			'zh_TW',
		);


		if ( in_array( $wp_language_code, $valid_language ) ) {
			wp_register_script('wysija-validator-lang',WYSIJA_URL.'js/validate/languages/jquery.validationEngine-'.$wp_language_code.'.js', array( 'jquery' ),WYSIJA::get_version(),true );
		}else{
			wp_register_script('wysija-validator-lang',WYSIJA_URL.'js/validate/languages/jquery.validationEngine-en.js', array( 'jquery' ),WYSIJA::get_version(),true );
		}
		wp_register_script('wysija-validator',WYSIJA_URL.'js/validate/jquery.validationEngine.js', array( 'jquery' ),WYSIJA::get_version(),true );
		wp_register_script('wysija-front-subscribers', WYSIJA_URL.'js/front-subscribers.js', array( 'jquery' ),WYSIJA::get_version(),true);

		wp_register_script('wysija-form', WYSIJA_URL.'js/forms.js', array( 'jquery' ),WYSIJA::get_version());
		wp_register_style('validate-engine-css',WYSIJA_URL.'css/validationEngine.jquery.css',array(),WYSIJA::get_version());
		wp_register_script('wysija-admin-ajax', WYSIJA_URL.'js/admin-ajax.js',array(),WYSIJA::get_version());
		wp_register_script('wysija-admin-ajax-proto', WYSIJA_URL.'js/admin-ajax-proto.js',array(),WYSIJA::get_version());
		wp_register_script( 'mailpoet-global', WYSIJA_URL.'js/admin-global.js', array( 'jquery', 'underscore' ), WYSIJA::get_version() );

		$helperUser=WYSIJA::get('user','helper');
		if($helperUser->isCaptchaEnabled()) {
			wp_register_script( 'wysija-recaptcha', 'https://www.google.com/recaptcha/api.js' );
		}

		if(defined('WYSIJA_SIDE') && WYSIJA_SIDE=='front')  wp_enqueue_style('validate-engine-css');

	}


	/**
	 * All the ajax requests are routed through here
	 */
	function ajax() {

		$result_array = array();
		if( !$_REQUEST || !isset( $_REQUEST['controller'] ) || !isset( $_REQUEST['task'] ) ){
			$result_array = array( 'result' => false );
		}else{
			$plugin_requesting_ajax = 'wysija-newsletters';

                        // we override the plugin resquesting ajax if specified in the request
			if( !empty( $_REQUEST['wysijaplugin'] )  ){
                            $plugin_requesting_ajax = preg_replace('#[^a-z0-9\-_]#i','',$_REQUEST['wysijaplugin']);
                        }

                        // fetching the right controller
			$this->controller = WYSIJA::get( $_REQUEST['controller'] , 'controller' , false, $plugin_requesting_ajax );

                        // let's make sure the requested task exist
			if( method_exists( $this->controller , $_REQUEST['task'] ) ){
				$result_array['result'] = call_user_func(array($this->controller, $_REQUEST['task']));
			}else{
				$this->error( 'Method "' . esc_html($_REQUEST['task']) . '" doesn\'t exist for controller : "'.esc_html($_REQUEST['controller']) );
			}
		}

                // get the appended messages triggerred during the task execution
		$result_array['msgs'] = $this->getMsgs();

		// this header will allow ajax request from the home domain, this can be a lifesaver when domain mapping is on
		if(function_exists('home_url')){
                    header('Access-Control-Allow-Origin: '.home_url());
                }

                // let's encode our response in the json format
                $json_data = json_encode($result_array);

		// in some case scenarios our client will have jQuery forcing the jsonp so we need to adapt ourselves
		if(isset($_REQUEST['callback'])) {
                    // special header for json-p
                    header('Content-type: application/javascript');

                    $helper_jsonp = WYSIJA::get('jsonp', 'helper');
                    if($helper_jsonp->isValidCallback($_REQUEST['callback'])) {
                            print $_REQUEST['callback'] . '('.$json_data.');';
                    }
		} else {
                    // standard header for unwrapped classic json response
                    header('Content-type: application/json');
                    print $json_data;
		}
                // our ajax response is printed, no need to let WordPress or 3rd party plugin execute more code
		die();
	}
}


class WYSIJA extends WYSIJA_object{

	function __construct(){
	  parent::__construct();
	}

	/**
	 * function created at the beginning to handle particular cases with WP get_permalink it got much smaller recently
	 * @param int $pageid
	 * @param array $params
	 * @param boolean $simple
	 * @return type
	 */
	public static function get_permalink( $pageid, $params = array(), $simple = false ){
		$hWPtools = WYSIJA::get( 'wp_tools', 'helper' );
		return $hWPtools->get_permalink( $pageid, $params, $simple );
	}

	/**
	 * translate the plugin
	 * @staticvar boolean $extensionloaded
	 * @param type $extendedplugin
	 * @return boolean
	 */
	public static function load_lang( $extendedplugin = false ){
		static $extensionloaded = false;

		//we return the entire array of extensions loaded if non is specified
		if ( ! $extendedplugin ) {
			return $extensionloaded;
		}

		//we only need to load this translation loader once on init
		if(!$extensionloaded){
			add_action('init', array('WYSIJA','load_lang_init'));
		}
		//each plugin has a different name
		if ( !$extensionloaded || !isset($extensionloaded[$extendedplugin])) {
			$transstring = null;
			switch($extendedplugin){
				case 'wysija-newsletters':
					$transstring=WYSIJA;
					break;
				case 'wysijashop':
					$transstring=WYSIJASHOP;
					break;
				case 'wysijacrons':
					$transstring=WYSIJACRONS;
					break;
				case 'wysija-newsletters-premium':
					$transstring=WYSIJANLP;
					break;
				case 'get_all':
					return $extensionloaded;
			}

			//store all the required translations to be loaded in the static variable
			if($transstring !== null) {
				$extensionloaded[$extendedplugin] = $transstring;
			}
		}
	}

	/**
	 * check if the user is tech support as this can be used to switch the language back to english when helping our customers
	 * @global type $current_user
	 * @param type $debugmode
	 * @return type
	 */
	public static function is_wysija_admin($debugmode=false){
		//to allow wysija team members to work in english mode if debug is activated
		global $current_user;

		if((int)$debugmode > 0 && empty($current_user)) return true;

		if(isset($current_user->data->user_email) && (strpos($current_user->data->user_email, '@mailpoet.com') !== false)) {
			return true;
		}
		return false;
	}

	/**
	 * this function exists just to fix the issue with qtranslate :/ (it only fix it partially)
	 * @param type $extended_plugin
	 */
	public static function load_lang_init($extended_plugin=false){
		$model_config =  WYSIJA::get('config','model');
		$debug_mode = (int)$model_config->getValue('debug_new');

		if($debug_mode === 0 || ($debug_mode > 0 && WYSIJA::is_wysija_admin($debug_mode) === false)) {
			$extensions_loaded = WYSIJA::load_lang('get_all');
			foreach($extensions_loaded as $extended_plugin => $translation_string){

				// check for translation file overriding from transstring wp-content/languages/wysija-newsletters/wysija-newsletters-en_US.mo
				$filename = WP_CONTENT_DIR.DS.'languages'.DS.$extended_plugin.DS.$translation_string.'-'.get_locale().'.mo';

				if( !file_exists($filename) ){
					// get the translation file in our local file
					$filename = WYSIJA_PLG_DIR.$extended_plugin.DS.'languages'.DS.$translation_string.'-'.get_locale().'.mo';
				}

				// load the translation file with WP's load_textdomain
				if( file_exists( $filename ) ){
					load_textdomain( $translation_string, $filename );
				}
			}
		}
	}

	/**
	 * function to generate objects of different types, managing file requiring in order to be the most efficient
	 * @staticvar array $arrayOfObjects
	 * @param string $name
	 * @param string $type : in which folder do we go and pick the class
	 * @param boolean $force_side : this parameter is almost never set to true,
	 *                              it will be useful for instance if you want to get a back controller
	 *                              from the frontend, it was used maybe in the shop but it can be ignored for wysija-newsletters
	 * @param type $extended_plugin : used only when calling the url from a different plugin it is used watch those files :
	 *                              -core/controller.php line 21, 23 ,24
	 * @param type $load_lang : the load lang is in the get  to be sure we don't forget to load the language file for each plugin at least once
	 *                          the way I see it it could be moved to the index.php of each plugin. for now only wysija-newsletters is translated anyway
	 * @return boolean
	 */
	public static function get($name,$type,$force_side=false,$extended_plugin='wysija-newsletters',$load_lang=true){
		static $array_of_objects;

		if($load_lang)  WYSIJA::load_lang($extended_plugin);

		// store all the objects made so that we can reuse them accross the application if the object is already set we return it immediately
		if(isset($array_of_objects[$extended_plugin][$type.$name])) {
			return $array_of_objects[$extended_plugin][$type.$name];
		}

		// which folder do we pick for controllersand views ? back or front ?
		if($force_side) {
			$side=$force_side;
		} else {
			$side=WYSIJA_SIDE;
		}

		// for each plugin we will define the $extended_constant variable if it's not defined already
		// also we will defined the $extended_plugin_name which corresponds to the folder name and also will be used to build the class to be called
		switch($extended_plugin){
			case 'wysija-newsletters-premium':
				$extended_constant='WYSIJANLP';
				if(!defined($extended_constant)) define($extended_constant,$extended_constant);
				$extended_plugin_name='wysijanlp';
				break;
			case 'wysija-newsletters':
				$extended_constant='WYSIJA';
				if(!defined($extended_constant)) define($extended_constant,$extended_constant);
				$extended_plugin_name='wysija';
				break;
			default :
				$extended_constant=strtoupper($extended_plugin);
				if(!defined($extended_constant)) define($extended_constant,$extended_constant);
				$extended_plugin_name=$extended_plugin;
		}

		// security to protect against dangerous ./../ includes
		$name = preg_replace('#[^a-z0-9_]#i','',$name);

		// this switch will require_once the file needed and build a the class name depending on the parameters passed to the function
		switch($type){
			case 'controller':
				// require the parent class necessary
				require_once(WYSIJA_CORE.'controller.php');

				$ctrdir=WYSIJA_PLG_DIR.$extended_plugin.DS.'controllers'.DS;
				// Require module concept
				require_once(WYSIJA_CORE.'module'.DS.'module.php');

				// if we are doing ajax we don't go to one side, ajax is for frontend or backend in the same folder
				if(defined('DOING_AJAX')) {
					$class_path=$ctrdir.'ajax'.DS.$name.'.php';
				}else {
					// the other controllers are called in a side folder back or front
					$class_path=$ctrdir.$side.DS.$name.'.php';
					// require the side specific controller file
					require_once(WYSIJA_CTRL.$side.'.php');
				}
				$class_name = strtoupper($extended_plugin_name).'_control_'.$side.'_'.$name;
				break;
			case 'view':
				$viewdir=WYSIJA_PLG_DIR.$extended_plugin.DS.'views'.DS;
				// let's get the right path for the view front or back and the right class_name
				$class_path=$viewdir.$side.DS.$name.'.php';
				$class_name = strtoupper($extended_plugin_name).'_view_'.$side.'_'.$name;

				// require the common view file and the side view file
				require_once(WYSIJA_CORE.'view.php');
				require_once(WYSIJA_VIEWS.$side.'.php');
				break;
			case 'helper':
				$helpdir=WYSIJA_PLG_DIR.$extended_plugin.DS.'helpers'.DS;
				$class_path=$helpdir.$name.'.php';
				$class_name = strtoupper($extended_plugin_name).'_help_'.$name;

				break;
			case 'model':
				$modeldir=WYSIJA_PLG_DIR.$extended_plugin.DS.'models'.DS;
				$class_path=$modeldir.$name.'.php';
				$class_name = strtoupper($extended_plugin_name).'_model_'.$name;
				// require the parent class necessary
				require_once(WYSIJA_CORE.'model.php');
				break;
			case 'widget':
				$modeldir=WYSIJA_PLG_DIR.$extended_plugin.DS.'widgets'.DS;
				$class_path=$modeldir.$name.'.php';
				if($name=='wysija_nl') $class_name='WYSIJA_NL_Widget';
				else $class_name = strtoupper($extended_plugin_name).'_widget_'.$name;
				break;

			case 'module':
				$moduledir = WYSIJA_PLG_DIR . $extended_plugin . DS . 'modules' . DS;
				if (file_exists($moduledir . $name . '.php'))
					$class_path = $moduledir . $name . '.php';
				elseif (file_exists($moduledir . $name . DS . $name . '.php'))
					$class_path = $moduledir . $name . DS . $name . '.php';
				else
					return;
				$class_name = strtoupper($extended_plugin_name) . '_module_' . $name;
				// require the parent class necessary
				//require_once(WYSIJA_CORE.'module'.DS.'module.php');
				require_once(WYSIJA_CORE . 'module' . DS . 'statistics_model.php');
				require_once(WYSIJA_CORE . 'module' . DS . 'statistics.php');
				require_once(WYSIJA_CORE . 'module' . DS . 'statisticschart.php');
				require_once(WYSIJA_CORE . 'module' . DS . 'statisticstable.php');

				break;

			default:
				WYSIJA::setInfo('error','WYSIJA::get does not accept this type of file "'.$type.'" .');
				return false;
		}

		if(!file_exists($class_path)) {
                        if(is_admin() && WYSIJA::current_user_can('switch_themes')){
                            WYSIJA::setInfo('error','file has not been recognised '.$class_path);
                            WYSIJA::setInfo('error',$class_name);
                            WYSIJA::setInfo('error',$type);
                        }
			return;
		}

		// require the file needed once and store & return the object needed
		require_once($class_path);
		return $array_of_objects[$extended_plugin][$type.$name]=new $class_name($extended_plugin_name);

	}

	/**
	 * log function to spot some strange issues when sending emails for instance
	 * @param type $key
	 * @param type $data
	 * @param type $category
	 * @return type
	 */
	public static function log($key='default',$data='empty',$category='default'){
		$config=WYSIJA::get('config','model');

		if((int)$config->getValue('debug_new')>1 && $category && $config->getValue('debug_log_'.$category)){

			$optionlog=get_option('wysija_log');


			$optionlog[$category][(string)microtime(true)][$key]=$data;

			WYSIJA::update_option('wysija_log' , $optionlog);
		}
		return false;
	}

	/**
	 * the filter to add option to the cron frequency instead of being stuck with hourly, daily and twicedaily...
	 * we can add filters but we cannot delete other values such as the default ones, as this might break other plugins crons
	 * @param type $param
	 * @return type
	 */
	public static function filter_cron_schedules( $param ) {
		$frequencies=array(
			'one_min' => array(
				'interval' => 60,
				'display' => __( 'Once every minute',WYSIJA)
				),
			'two_min' => array(
				'interval' => 120,
				'display' => __( 'Once every two minutes',WYSIJA)
				),
			'five_min' => array(
				'interval' => 300,
				'display' => __( 'Once every five minutes',WYSIJA)
				),
			'ten_min' => array(
				'interval' => 600,
				'display' => __( 'Once every ten minutes',WYSIJA)
				),
			'fifteen_min' => array(
				'interval' => 900,
				'display' => __( 'Once every fifteen minutes',WYSIJA)
				),
			'thirty_min' => array(
				'interval' => 1800,
				'display' => __( 'Once every thirty minutes',WYSIJA)
				),
			'two_hours' => array(
				'interval' => 7200,
				'display' => __( 'Once every two hours',WYSIJA)
				),
			'eachweek' => array(
				'interval' => 604800,
				'display' => __( 'Once a week',WYSIJA)
				),
			'each28days' => array(
				'interval' => 2419200,
				'display' => __( 'Once every 28 days',WYSIJA)
				),
			);

		return array_merge($param, $frequencies);
	}

	/**
	 * scheduled task for sending the emails in the queue, the frequency is set in the settings
	 */
	public static function croned_queue( $check_scheduled_newsletter = true) {

		// check the scheduled tasks only if it's a standard WP scheduled task free only
		if($check_scheduled_newsletter){
			WYSIJA::check_scheduled_newsletters();
		}

		$model_config = WYSIJA::get('config','model');
		// check that the 2000 limit is not passed and process the queue

		if((int)$model_config->getValue('total_subscribers') < 2000 ){
			$helper_queue = WYSIJA::get('queue','helper');
			$helper_queue->report=false;
			WYSIJA::log('croned_queue process',true,'cron');

			$helper_queue->process();
		}

	}

	public static function check_scheduled_newsletters(){
		$last_scheduled_check = get_option('wysija_last_scheduled_check');

		// if the latest post notification check was done more than five minutes ago let's check it again
		if(empty($last_scheduled_check) || ( time() > ($last_scheduled_check + 300) ) ){
			// create the scheduled automatic post notifications email if there are any
			$helper_autonews = WYSIJA::get('autonews','helper');
			$helper_autonews->checkPostNotif();

			// queue the scheduled newsletter also if there are any
			$helper_autonews->checkScheduled();
			WYSIJA::update_option('wysija_last_scheduled_check', time());
		}

		// send daily report about emails sent
		$model_config = WYSIJA::get('config','model');
		if($model_config->getValue('emails_notified_when_dailysummary')){
			$helper_notification = WYSIJA::get('notifications','helper');
			$helper_notification->send_daily_report();
		}

	}


	/**
	 * everyday we make sure not to leave any trash files
	 * remove temporary files
	 */
	public static function croned_daily() {

		@ini_set('max_execution_time',0);

		/*user refresh count total*/
		$helper_user = WYSIJA::get('user','helper');
		$helper_user->refreshUsers();

		/*user domain generation*/
		$helper_user->generate_domains();

		/*clear temporary folders*/
		$helper_file = WYSIJA::get('file','helper');
		$helper_file->clear();

		/*clear queue from unsubscribed*/
		$helper_queue = WYSIJA::get('queue','helper');
		$helper_queue->clear();


	}

	// Weekly cron
	public static function croned_weekly() {

		@ini_set('max_execution_time',0);

		$model_config = WYSIJA::get('config','model');

		// If enabled, flag MixPanel sending on next page load.
		if ($model_config->getValue('analytics') == 1) {
			$model_config->save(array('send_analytics_now' => 1));
		}

	}

	// Monthly cron
	public static function croned_monthly() {

		@ini_set('max_execution_time',0);

		$model_config = WYSIJA::get('config','model');

		/* send daily report about emails sent */
		if ($model_config->getValue('sharedata')) {
			$helper_stats = WYSIJA::get('stats','helper');
			$helper_stats->share();
		}

	}

	/**
	 * when we deactivate the plugin we clear the WP install from those cron records
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook('wysija_cron_queue');
		wp_clear_scheduled_hook('wysija_cron_bounce');
		wp_clear_scheduled_hook('wysija_cron_daily');
		wp_clear_scheduled_hook('wysija_cron_weekly');
		wp_clear_scheduled_hook('wysija_cron_monthly');
	}


	/**
	 * wysija's redirect allows to save some variables for the next page load such as notices etc..
	 * @global type $wysija_msg
	 * @global type $wysija_queries
	 * @global type $wysija_queries_errors
	 * @param type $location
	 */
	public static function redirect($location) {
		//save the messages
		global $wysija_msg,$wysija_queries,$wysija_queries_errors;
		WYSIJA::update_option('wysija_msg',$wysija_msg);
		WYSIJA::update_option('wysija_queries',$wysija_queries);
		WYSIJA::update_option('wysija_queries_errors',$wysija_queries_errors);

		// make sure we encode square brackets as wp_redirect will strip them off
		$location = str_replace(array('[', ']'), array('%5B', '%5D'), $location);

		// redirect to specified location
		wp_redirect($location);
		exit;
	}

	/**
	 * custom post type for wysija is call wysijap as in wysija's post
	 */
	public static function create_post_type() {

		//by default there is url rewriteing on wysijap custom post, though in one client case I had to deactivate it.
		//as this is rare we just need to set this setting to activate it
		//by default let's deactivate the url rewriting of the wysijap confirmation page because it is breaking in some case.
		$show_interface=false;
		if(defined('WYSIJA_DBG') && WYSIJA_DBG>1) $show_interface=true;
		register_post_type( 'wysijap',
			array(
					'labels' => array(
							'name' => __('MailPoet page'),
							'singular_name' => __('MailPoet page')
					),
			'public' => true,
			'has_archive' => false,
			'show_ui' =>$show_interface,
			'show_in_menu' =>$show_interface,
			'rewrite' => false,
			'show_in_nav_menus'=>false,
			'can_export'=>false,
			'publicly_queryable'=>true,
			'exclude_from_search'=>true,
			)
		);

		if(!get_option('wysija_post_type_updated')) {
			$modelPosts=new WYSIJA_model();
			$modelPosts->tableWP=true;
			$modelPosts->table_prefix='';
			$modelPosts->table_name='posts';
			$modelPosts->noCheck=true;
			$modelPosts->pk='ID';
			if($modelPosts->exists(array('post_type'=>'wysijapage'))){
				$modelPosts->update(array('post_type'=>'wysijap'),array('post_type'=>'wysijapage'));
				flush_rewrite_rules( false );
			}
			WYSIJA::update_option('wysija_post_type_updated',time());
		}

		if(!get_option('wysija_post_type_created')) {
			flush_rewrite_rules( false );
			WYSIJA::update_option('wysija_post_type_created',time());
		}

	}

	/**
	 * wysija update_option function is very similar to WordPress' one but it
	 * can also manage new options not automatically loaded each time
	 * @param type $option_name
	 * @param type $newvalue
	 * @param type $defaultload this parameter is the advantage other Wp's update_option here
	 */
	public static function update_option($option_name,$newvalue,$defaultload='no'){
		if ( get_option( $option_name ) != $newvalue ) {
			update_option( $option_name, $newvalue );
		} else {
			add_option( $option_name, $newvalue, '', $defaultload );
		}
	}

	/**
	 * When a WordPress user is added we also need to add it to the subscribers list
	 * @param type $user_id
	 * @return type
	 */
	public static function hook_add_WP_subscriber($user_id) {
		$data=get_userdata($user_id);


		//check first if a subscribers exists if it doesn't then let's insert it
		$model_config=WYSIJA::get('config','model');
		$model_user=WYSIJA::get('user','model');
		$model_user->getFormat=ARRAY_A; // there is one case where we were getting an object instead of an array
		$subscriber_exists=$model_user->getOne(array('user_id'),array('email'=>$data->user_email));

		$first_name=$data->first_name;
		$last_name=$data->last_name;
		if(!$data->first_name && !$data->last_name) $first_name=$data->display_name;

		$model_user->reset();
		if($subscriber_exists){
			$user_id=$subscriber_exists['user_id'];
			// we need to update the current subscriber using it's id
			$model_user->update(array('wpuser_id'=>$data->ID,'firstname'=>$first_name,'lastname'=>$last_name),array('user_id'=>$user_id));
		}else{
			$model_user->noCheck=true;
			$user_id=$model_user->insert(array('email'=>$data->user_email,'wpuser_id'=>$data->ID,'firstname'=>$first_name,'lastname'=>$last_name,'status'=>$model_config->getValue('confirm_dbleoptin')));
		}

		$model_user_list=WYSIJA::get('user_list','model');
		$model_user_list->insert(array('user_id'=>$user_id,'list_id'=>$model_config->getValue('importwp_list_id'),'sub_date'=>time()),true);

		$helper_user=WYSIJA::get('user','helper');
		$helper_user->sendAutoNl($user_id,$data,'new-user');
		return true;
	}

	/**
	 * when a WordPress user is updated we also need to update the corresponding subscriber
	 * @param type $user_id
	 * @return type
	 */
	public static function hook_edit_WP_subscriber($user_id) {
		$data=get_userdata($user_id);

		//check first if a subscribers exists if it doesn't then let's insert it
		$model_user=WYSIJA::get('user','model');
		$model_config=WYSIJA::get('config','model');
		$model_user_list=WYSIJA::get('user_list','model');
		$model_user->getFormat = ARRAY_A;
		$subscriber_exists=$model_user->getOne(array('user_id'),array('email'=>$data->user_email));

		$model_user->reset();

		$first_name=$data->first_name;
		$last_name=$data->last_name;
		if(!$data->first_name && !$data->last_name) $first_name=$data->display_name;

		if($subscriber_exists){
			$user_id=$subscriber_exists['user_id'];

			$model_user->update(array('wpuser_id'=>$data->ID, 'email'=>$data->user_email,'firstname'=>$first_name,'lastname'=>$last_name),array('user_id'=>$user_id));

			$result=$model_user_list->getOne(false,array('user_id'=>$user_id,'list_id'=>$model_config->getValue('importwp_list_id')));
			$model_user_list->reset();
			if(!$result)
				$model_user_list->insert(array('user_id'=>$user_id,'list_id'=>$model_config->getValue('importwp_list_id'),'sub_date'=>time()));
		}else{
			//chck that we didnt update the email
			$subscriber_exists=$model_user->getOne(false,array('wpuser_id'=>$data->ID));

			if($subscriber_exists){
				$user_id=$subscriber_exists['user_id'];

				$model_user->update(array('email'=>$data->user_email,'firstname'=>$first_name,'lastname'=>$last_name),array('wpuser_id'=>$data->ID));

				$result=$model_user_list->getOne(false,array('user_id'=>$user_id,'list_id'=>$model_config->getValue('importwp_list_id')));
				$model_user_list->reset();
				if(!$result)
					$model_user_list->insert(array('user_id'=>$user_id,'list_id'=>$model_config->getValue('importwp_list_id'),'sub_date'=>time()));
			}else{
				$model_user->noCheck=true;
				$user_id=$model_user->insert(array('email'=>$data->user_email,'wpuser_id'=>$data->ID,'firstname'=>$first_name,'lastname'=>$last_name,'status'=>$model_config->getValue('confirm_dbleoptin')));
				$model_user_list->insert(array('user_id'=>$user_id,'list_id'=>$model_config->getValue('importwp_list_id'),'sub_date'=>time()));
			}
		}
		return true;
	}

	/**
	 * when a wp user is deleted we also delete the subscriber corresponding to it
	 * @param type $user_id
	 */
	public static function hook_del_WP_subscriber($user_id) {
		$model_config=WYSIJA::get('config','model');
		$model_user=WYSIJA::get('user','model');
		$data = $model_user->getOne(array('email','user_id'),array('wpuser_id'=>$user_id));
		if(isset($data['email'])) {
			$model_user->delete(array('email'=>$data['email']));
		}
		if(isset($data['user_id'])) {
			$model_user = WYSIJA::get('user_list','model');
			$model_user->delete(array('user_id'=>$data['user_id'],'list_id'=>$model_config->getValue('importwp_list_id')));
		}
	}


	public static function hook_auto_newsletter_refresh($post_id) {
		$helper_autonews = WYSIJA::get('autonews', 'helper');
		$helper_autonews->refresh_automatic_content();

		return true;
	}

	/**
	 * post notification transition hook, know when a post really gets published
	 * @param type $new_status
	 * @param type $old_status
	 * @param type $post
	 * @return type
	 */
	public static function hook_postNotification_transition($new_status, $old_status, $post) {
		//we run some process only if the status of the post changes from something to publish
		if($new_status === 'publish' && $old_status !== $new_status) {
			$model_email = WYSIJA::get('email', 'model');
			$emails = $model_email->get(false, array('type' => 2, 'status' => array(1, 3, 99)));

			if(!empty($emails)) {
				//we loop through all of the automatic emails
				foreach($emails as $key => $email) {

					//we will try to give birth to a child email only if the automatic newsletter is a post notification email and in immediate mode
					if(is_array($email) && $email['params']['autonl']['event'] === 'new-articles' && $email['params']['autonl']['when-article'] === 'immediate') {
						// set default include/exclude categories
						$include_category_ids = array();
						$exclude_category_ids = array();

						// ALC need to check for post_type on each block of the autoposts
						$wj_data = maybe_unserialize( base64_decode( $email['wj_data'] ) );
						$post_types = array();
						$has_alc_blocks = false;

						foreach ( $wj_data['body'] as $block_key => $block ){
							if ( $block['type'] !== 'auto-post' ){
								continue;
							}

							$has_alc_blocks = true;

							// get post type and post categories from block parameters
							foreach( $block['params'] as $param_data ) {
								if(in_array($param_data['key'], array('post_type', 'cpt'))  && strlen(trim($param_data['value'])) > 0) {
									// store post type
									$post_types[] = trim($param_data['value']);
								} else if($param_data['key'] === 'category_ids' && strlen(trim($param_data['value'])) > 0) {
									// store post category ids
									$include_category_ids = array_map('intval', explode(',', trim($param_data['value'])));
								}
							}
						}

						if ( $has_alc_blocks === true && ! in_array( $post->post_type, $post_types ) ) {
							continue;
						}

						// get post categories
						$helper_wp_tools = WYSIJA::get('wp_tools', 'helper');
						$taxonomies = $helper_wp_tools->get_post_category_ids($post);

						// assume the post has to be sent
						$do_send_post = true;

						// post categories have to match at least one of the email's included categories
						$include_intersection = array_intersect($taxonomies, $include_category_ids);
						if(!empty($include_category_ids) && empty($include_intersection)) {
							$do_send_post = false;
						}

						$exclude_intersection = array_intersect($taxonomies, $exclude_category_ids);
						// post categories should not match any one of the email's excluded categories
						if(!empty($exclude_category_ids) && !empty($exclude_intersection)) {
							$do_send_post = false;
						}

						if($do_send_post) {
							WYSIJA::log('post_transition_hook_give_birth', array(
								'post_id' => $post->ID,
								'post_title' => $post->post_title,
								'newsletter' => $email,
								'old_status' => $old_status,
								'new_status' => $new_status
							),'post_notif');

							$model_email->reset();
							$model_email->give_birth($email, $post->ID);
						}
					}
				}
			}

			// we check for automatic latest content widget in automatic newsletter
			$helper_autonews = WYSIJA::get('autonews', 'helper');
			$helper_autonews->refresh_automatic_content();
		}

		return true;
	}

	/**
	 * uninstall process not used
	 */
	public static function uninstall(){
		$helperUS=WYSIJA::get('uninstall','helper');
		$helperUS->uninstall();
	}

	/**
	 * this function is run when wysija gets activated
	 * there is no installation process here, all is about checking the global status of the app
	 */
	public static function activate(){
		$encoded_option=get_option('wysija');
		$installApp=false;
		if($encoded_option){
			$values=unserialize(base64_decode($encoded_option));
			if(isset($values['installed'])) $installApp=true;
		}

		//test again for plugins on reactivation
		if($installApp){
			$helper_import=WYSIJA::get('plugins_import','helper');
			$helper_import->testPlugins();

			//resynch wordpress list
			$helper_user=WYSIJA::get('user','helper');
			$helper_user->synchList($values['importwp_list_id']);
		}
	}

	/**
	 * the is_plugin_active functions from WordPress sometimes are not loaded so here is one that works for single and multisites anywhere in the code
	 * @param type $pluginName
	 * @return type
	 */
	public static function is_plugin_active($pluginName){
		$arrayactiveplugins=get_option('active_plugins');
		//we check in the list of the site options if the plugin is activated
		if(in_array($pluginName, $arrayactiveplugins)) {
			//plugin is activated for that site
			return true;
		}

		//if this is a multisite it might not be activated in the site option but network activated though
		if(is_multisite()){
			$plugins = get_site_option('active_sitewide_plugins');
			//plugin is activated for that multisite
			if(isset($plugins[$pluginName])){
				return true;
			}
		}
		return false;
	}

	/**
	 * make sure that the current user has the good access rights corresponding to its role
	 * @global type $current_user
	 * @return type
	 */
	public static function update_user_caps(){
		global $current_user;

		if(empty($current_user) && function_exists('wp_get_current_user')) wp_get_current_user();
		if(empty($current_user)) return false;
		$current_user->get_role_caps();

		return true;
	}

	 /**
	 * test whether the plugin is a beta version or not
	 * 2.4.4.4  is a beta
	 * 2.4.4 is a bug fix release
	 * 2.4 is a feature release
	 * @param string $plugin_name
	 */
	public static function is_beta($plugin_name=false){
		// exceptions
		$not_beta_versions = array('2.5.9.1', '2.5.9.2', '2.5.9.3', "2.5.9.4");
		$mailpoet_version = WYSIJA::get_version($plugin_name);
		if(in_array($mailpoet_version, $not_beta_versions)) return false;

		// standard way of defining a beta version
		if(count(explode('.', $mailpoet_version)) > 3 ) return true;
		return false;
	}

	/**
	 * depending where it's used the base function from WordPress doesn't work, so this one will work anywhere
	 * @param type $capability
	 * @return type
	 */
	public static function current_user_can($capability){
		if(!$capability) return false;
		WYSIJA::update_user_caps();
		if(!current_user_can($capability)) return false;
		return true;
	}

	/**
	 * this function get and sets the cron schedules when MailPoet's own cron system is active
	 * @staticvar type $cron_schedules
	 * @param type $schedule
	 * @return type
	 */
	public static function get_cron_schedule($schedule='queue'){
		static $cron_schedules;

		//if the cron schedules are already loaded statically then we just have to return the right schedule value
		if(!empty($cron_schedules)){
			if($schedule=='all') return $cron_schedules;
			if(isset($cron_schedules[$schedule])) {
				return $cron_schedules[$schedule];
			}else{
				WYSIJA::set_cron_schedule($schedule);
				return false;
			}
		}else{
			//this is the first time this function is executed so let's get them from the db and store them statically
			$cron_schedules=get_option('wysija_schedules',array());
			if(!empty($cron_schedules)){
				if(isset($cron_schedules[$schedule]))   return $cron_schedules[$schedule];
				else    return false;
			}else{
				WYSIJA::set_cron_schedule();
				return false;
			}
		}
		return false;
	}

	/**
	 * return the frequency for each cron task needed by MailPoet
	 * @return type an array of frequencies
	 */
	public static function get_cron_frequencies(){
		$model_config = WYSIJA::get('config','model');
		$helper_forms = WYSIJA::get('forms','helper');

		if(is_multisite() && $model_config->getValue('sending_method')=='network'){
		   $sending_emails_each = $model_config->getValue('ms_sending_emails_each');
		}else{
		   $sending_emails_each = $model_config->getValue('sending_emails_each');
		}

		$queue_frequency = $helper_forms->eachValuesSec[$sending_emails_each];
		$bounce_frequency = 99999999999999;
		if(isset($helper_forms->eachValuesSec[$model_config->getValue('bouncing_emails_each')])){
			$bounce_frequency = $helper_forms->eachValuesSec[$model_config->getValue('bouncing_emails_each')];
		}
		return array('queue'=>$queue_frequency,'bounce'=>$bounce_frequency,'daily'=>86400,'weekly'=>604800,'monthly'=>2419200);
	}

	/**
	 * set the next cron schedule
	 * TODO : needs probably to make the difference of running process for the next schedule, so that there is no delay(this is only problematic on some slow servers)
	 * @param string $schedule
	 * @param int $last_saved
	 * @param boolean $set_running
	 * @return boolean
	 */
	public static function set_cron_schedule($schedule = false , $last_saved = 0 , $set_running = false){
		$cron_schedules = array();

		$start_time = $last_saved;
		if(!$start_time)    $start_time = time();
		$processes = WYSIJA::get_cron_frequencies();
		if(!$schedule){
			foreach($processes as $process => $frequency){
				$next_schedule = $start_time + $frequency;
				$prev_schedule = 0;
				if(isset($cron_schedules[$process]['running']) && $cron_schedules[$process]['running']) $prev_schedule=$cron_schedules[$process]['running'];
				$cron_schedules[$process]=array(
					'next_schedule' => $next_schedule,
					'prev_schedule' => $prev_schedule,
					'running' => false);
			}
		}else{
			$cron_schedules = WYSIJA::get_cron_schedule('all');
			if($set_running){
				 $cron_schedules[$schedule]['running'] = $set_running;
			}else{
				 $running = 0;
				if(isset($cron_schedules[$schedule]['running'])) $running = $cron_schedules[$schedule]['running'];
				// if the process is not running or has been running for more than 15 minutes then we set the next_schedule date
				$process_frequency = $processes[$schedule];

				if(!$running || ( time() > ($running + $process_frequency) ) ){

					$next_schedule = $start_time + $process_frequency;
					// if the next schedule is already behind, we give it 30 seconds before it can triggers again
					if( $next_schedule < $start_time ){
						$next_schedule = $start_time + 30;
					}

					$cron_schedules[$schedule] = array(
							'next_schedule' => $next_schedule,
							'prev_schedule' => $running,
							'running' => false);
				}
			}
		}
		WYSIJA::update_option( 'wysija_schedules' , $cron_schedules , 'yes' );
		return true;
	}

	/**
	 * check that there is no passed schedules that need to be executed now
	 * @return void
	 */
	public static function cron_check() {

		$cron_schedules = WYSIJA::get_cron_schedule('all');
		if(empty($cron_schedules)) return;
		else{
			$processes = WYSIJA::get_cron_frequencies();

			$updated_sched = false;
			foreach($cron_schedules as $schedule => &$params){
					$running = 0;
					$time_now = time();
					if(isset($params['running'])) $running = $params['running'];
					//if the process has timedout we reschedule the next execution
					if($running && ( $time_now> ($running + $processes[$schedule]) ) ){
						//WYSIJA::setInfo('error','modifying next schedule for '.$proc);
						$process_frequency = $processes[$schedule];

						$next_schedule = $running + $process_frequency;
						// if the next schedule is already behind, we give it 30 seconds before it can trigger again
						if( $next_schedule < $time_now ){
							$next_schedule = $time_now + 30;
						}
						$params=array(
								'next_schedule' => $next_schedule,
								'prev_schedule' => $running,
								'running' => false);
						$updated_sched=true;
					}
			}
			if($updated_sched){
				//WYSIJA::setInfo('error','updating scheds');
				WYSIJA::update_option( 'wysija_schedules' , $cron_schedules , 'yes' );
			}

		}

		$time_now = time();
		$processesToRun = array();
		foreach($cron_schedules as $schedule => $scheduled_times){
			if(strpos($schedule, '(bounce handling not activated)')!==false) continue;
                        if( !isset($processes[$schedule]) ) continue;
			$process_frequency = $processes[$schedule];
			if( ( !$scheduled_times['running'] || (int)$scheduled_times['running'] + $process_frequency < $time_now ) && $scheduled_times['next_schedule'] < $time_now){
				$processesToRun[] = $schedule;
			}
		}

		$model_config = WYSIJA::get('config','model');
		$page_view_trigger = (int)$model_config->getValue('cron_page_hit_trigger');
		if(!empty($processesToRun) && $page_view_trigger === 1){
			//call the cron url
			// do not call that more than once per 5 minutes attempt at reducing the CPU load for some users
			// http://wordpress.org/support/topic/wysija-newsletters-slowing-down-my-site-1
			$last_cron_time_plus_5min = (int)get_option('wysija_last_php_cron_call') + (5*60);

			if($last_cron_time_plus_5min < time()){
				$cron_url = site_url( 'wp-cron.php').'?'.WYSIJA_CRON.'&action=wysija_cron&process='.implode(',',$processesToRun).'&silent=1';
				$cron_request = apply_filters( 'cron_request', array(
						'url' => $cron_url,
						'args' => array( 'timeout' => 0.01, 'blocking' => false, 'sslverify' => apply_filters( 'https_local_ssl_verify', true ) )
				) );

				wp_remote_post( $cron_url, $cron_request['args'] );
				WYSIJA::update_option('wysija_last_php_cron_call', time());
			}


		}
	}

	/**
	 * Function somehow necessary to avoid some conflicts in windows server and WordPress autoload of plugins language file
	 * @param type $boolean
	 * @param type $domain
	 * @param type $mofile
	 * @return boolean
	 */
	public static function override_load_textdomain($boolean, $domain, $mofile){
			$extensionloaded=WYSIJA::load_lang('get_all');

			if(isset($extensionloaded[$domain]) && !@file_exists($mofile)){
				return true;
			}

			return false;
	}

	/**
	 * function to rewrite the path of the file if the file doesn't exist
	 * @param type $mofile
	 * @param type $domain
	 * @return type
	 */
	public static function load_textdomain_mofile($mofile, $domain){
		$extensionloaded=WYSIJA::load_lang('get_all');

		if(isset($extensionloaded[$domain]) && !file_exists($mofile)){
			return WYSIJA_PLG_DIR.$domain.DS.'languages'.DS.$extensionloaded[$domain].'-'.get_locale().'.mo';
		}
		return $mofile;
	}
}

// subscribers/wp-user synch hooks
add_action('user_register', array('WYSIJA', 'hook_add_WP_subscriber'), 1);
add_action('added_existing_user', array('WYSIJA', 'hook_add_WP_subscriber'), 1);
add_action('profile_update', array('WYSIJA', 'hook_edit_WP_subscriber'), 1);
// for standard blog
add_action('delete_user', array('WYSIJA', 'hook_del_WP_subscriber'), 1);
// for multisite
add_action('deleted_user', array('WYSIJA', 'hook_del_WP_subscriber'), 1);
add_action('remove_user_from_blog', array('WYSIJA', 'hook_del_WP_subscriber'), 1);

// Load the Upgrader Class
add_action('init', array('WJ_Upgrade', 'hook'), 9);

// post notif trigger
add_action('transition_post_status', array('WYSIJA', 'hook_postNotification_transition'), 1, 3);
// refresh auto newsletter content when a post is modified
//add_action('save_post', array('WYSIJA', 'hook_auto_newsletter_refresh'), 1, 1);
add_action('delete_post', array('WYSIJA', 'hook_auto_newsletter_refresh'), 1, 1);

// add image size for emails
add_image_size('wysija-newsletters-max', 600, 9999);

$modelConf=WYSIJA::get('config','model');
if($modelConf->getValue('installed_time')){

	// START all that concerns the CRON
	// make sure we check when is the schedule due with wysija's cron
	if($modelConf->getValue('cron_manual')){
		// if WP cron tasks are still set, we clear them
		if(wp_get_schedule('wysija_cron_queue'))    WYSIJA::deactivate();

		// set the crons schedule for each process
		WYSIJA::get_cron_schedule();

		// check that there is no late cron schedules if we are using wysija's cron option and that the cron option is triggerred by any page view
		if(!isset($_REQUEST['process'])){
			WYSIJA::cron_check();
		}

		// this action is triggerred only by a cron job
		// if we're entering the wysija's cron part, it should end here
		if(isset($_REQUEST['action']) && $_REQUEST['action']=='wysija_cron'){
			// priority is hundred so that the messages such as unsubscribe or view in your browser have time to be translated(they get translated around 96, 97)
			add_action('init', 'init_wysija_cron',100);

			function init_wysija_cron(){
				$hCron=WYSIJA::get('cron','helper');
				$hCron->run();
			}
		}

	// make sure the scheduled tasks are recorded when using WordPress' cron
	}else{

		// filter to add new possible frequencies to the cron
		add_filter( 'cron_schedules', array( 'WYSIJA', 'filter_cron_schedules' ) );

		// action to handle the scheduled tasks in wysija
		add_action( 'wysija_cron_queue', array( 'WYSIJA', 'croned_queue' ) );
		add_action( 'wysija_cron_daily', array( 'WYSIJA', 'croned_daily' ) );
		add_action( 'wysija_cron_weekly', array( 'WYSIJA', 'croned_weekly' ) );
		add_action( 'wysija_cron_monthly', array( 'WYSIJA', 'croned_monthly' ) );

		// same with the weekly task
		if(!wp_next_scheduled('wysija_cron_weekly')){
			wp_schedule_event( $modelConf->getValue('last_save') , 'eachweek', 'wysija_cron_weekly' );
		}
		// the monthly task...
		if(!wp_next_scheduled('wysija_cron_monthly')){
			wp_schedule_event( $modelConf->getValue('last_save') , 'each28days', 'wysija_cron_monthly' );
		}

		// the daily task...
		if(!wp_next_scheduled('wysija_cron_daily')){
			wp_schedule_event( $modelConf->getValue('last_save') , 'daily', 'wysija_cron_daily' );
		}


		if(is_multisite()){

			// in the case of multisite and the network's method we schedule with a different frequency
			// this option contains the list of sites already scheduled
			$ms_wysija_bounce_cron = get_site_option('ms_wysija_bounce_cron');
			global $blog_id;

			// if this blog is not recorded in our wysija_sending_cron option then we clear its scheduled so that we can reinitialize it
			if(!$ms_wysija_bounce_cron || !isset($ms_wysija_bounce_cron[$blog_id])){
				wp_clear_scheduled_hook('wysija_cron_bounce');
				WYSIJA::set_cron_schedule('queue');
				$ms_wysija_bounce_cron[$blog_id] = 1;
				update_site_option('ms_wysija_bounce_cron',$ms_wysija_bounce_cron);
			}

		}

		// if the bounce task is not scheduled then we initialize it
		if(!wp_next_scheduled('wysija_cron_bounce')){
			wp_schedule_event( $modelConf->getValue('last_save') , $modelConf->getValue('bouncing_emails_each'), 'wysija_cron_bounce' );
		}

		// and  the queue processing task ...
		// if we are in a multisite case we make sure that the ms frequency hasn't been changed, if it has we reset it
		if(is_multisite() && $modelConf->getValue('sending_method')=='network'){
			// in the case of multisite and the network's method we schedule with a different frequency
			// this option contains the list of sites already scheduled
			$ms_wysija_sending_cron=get_site_option('ms_wysija_sending_cron');
			global $blog_id;

			// if this blog is not recorded in our wysija_sending_cron option then we clear its scheduled so that we can reinitialize it
			if(!$ms_wysija_sending_cron || !isset($ms_wysija_sending_cron[$blog_id])){
				wp_clear_scheduled_hook('wysija_cron_queue');
				WYSIJA::set_cron_schedule('queue');
				$ms_wysija_sending_cron[$blog_id]=1;
				update_site_option('ms_wysija_sending_cron',$ms_wysija_sending_cron);
			}
		}


		// simply schedule the queue
		if(!wp_next_scheduled('wysija_cron_queue')){

			// in the case of multisite and the network's method we schedule with a different frequency
			if(is_multisite() && $modelConf->getValue('sending_method')=='network'){
				$sending_emails_each=$modelConf->getValue('ms_sending_emails_each');
			}else{
			   $sending_emails_each=$modelConf->getValue('sending_emails_each');
			}
			wp_schedule_event( $modelConf->getValue('last_save') , $sending_emails_each, 'wysija_cron_queue' );
		}

	}// END all that concerns the CRON

	// filter fixing a bug with automatic load_text_domain_from WP didn't understand yet why this was necessary...
	// somehow wp_register_script(which is irrelevant) was triggerring this kind of notice
	// Warning: is_readable() [function.is-readable]: open_basedir restriction in effect. File(C:\Domains\website.com\wwwroot\web/wp-content/plugins/C:\Domains\website.com\wwwroot\web\wp-content\plugins\wysija-newsletters/languages/wysija-newsletters-en_US.mo) is not within the allowed path(s): (.;C:\Domains\;C:\PHP\;C:\Sites\;C:\SitesData\;/) in C:\Domains\website.com\wwwroot\web\wp-includes\l10n.php on line 339
	// the only solution is to make sure on our end that the file exists and rewrite it if necessary
	add_filter( 'override_load_textdomain', array( 'WYSIJA', 'override_load_textdomain' ), 10, 3);
	add_filter('load_textdomain_mofile',  array( 'WYSIJA', 'load_textdomain_mofile' ), 10, 2);
}

register_deactivation_hook(WYSIJA_FILE, array( 'WYSIJA', 'deactivate' ));
register_activation_hook(WYSIJA_FILE, array( 'WYSIJA', 'activate' ));
add_action( 'init', array('WYSIJA','create_post_type') );

// check for PHP version and display a warning notice if it's <5.3
if ( version_compare( PHP_VERSION , '5.3' , '<' ) &&
  !get_option("wysija_dismiss_update_notice") &&
  empty($_SERVER['HTTP_X_REQUESTED_WITH'])
) {

  $a = new WYSIJA_object();
  $a->notice(__("Your version of PHP is outdated. If you don't upgrade soon, new versions of MailPoet won't work.")
			 . "<br />"
			 . str_replace( array('[link]', '[/link]'), array('<a href="https://support.mailpoet.com/knowledgebase/how-to-prepare-my-site-for-mailpoet-3-0/" target="_blank" >', '</a>'), __("[link]Read how to update your version of PHP.[/link]")
             . "<br /><br />"
             . str_replace( array('[link]', '[/link]'), array('<a href="javascript:;" class="wysija_dismiss_update_notice">', '</a>'), __("[link]Dismiss[/link] this notice."))
             ), true, true);
}

// launch application
$helper = WYSIJA::get(WYSIJA_SIDE,'helper');
