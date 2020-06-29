<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_view extends WYSIJA_object {

	var $title = "DEFAULT TITLE";

	var $icon  = "icon-edit";

	var $links = array( );

	var $search = array( );

	var $cols_nks = array( ); //correspondance between user_id and user-id once processed

	static $color_coordinates = array( );

	static $cache_color_schemes = array( );

	/**
	 * Color schemes of MailPoet
	 * @var array
	 */
	var $color_schemes = array(
		array( '#21759b', '#6697BF', '#487192', '#284c69', '#000333' ), // like blue
		array( '#388e71', '#2c6f58', '#16523d', '#0d3c2c', '#092e21' ), // like green
		array( '#c97575', '#a76262', '#854f4f', '#743a3a', '#5a2a2a' ), // like red
		array( '#00CC00', '#269926', '#008500', '#39E639', '#67E667' ), // http://colorschemedesigner.com/#2P11Tw0w0w0w0 // green
		array( '#FF0000', '#BF3030', '#A60000', '#FF4040', '#FF7373' ) // http://colorschemedesigner.com/#0011Tw0w0w0w0 // red
	);

	/**
	 * Default font family
	 * @var type
	 */
	var $font_family = array( 'Arial' );

	/**
	 * Default font size in pixel
	 * @var int
	 */
	var $font_size = 12;

	function __construct(){
	  parent::__construct();
	}

	/**
	 * Swap color schemes, to make sure, we don't use 2 colors in a same scheme continously
	 * @return array()
	 */
	protected function swap_color_schemes() {
		if (empty(self::$cache_color_schemes)) {
			// swap colors from axis X => Y, Y => X
			$x   = count($this->color_schemes[0][0]);
			$y   = count($this->color_schemes[0]);
			$tmp = array( );
			foreach ($this->color_schemes as $y => $colors) {
				foreach ($colors as $x => $color) {
					$tmp[$x][$y] = $color;
				}
			}
			self::$cache_color_schemes = $tmp;
		}
		return self::$cache_color_schemes;
	}

	/**
	 * Get one random color from schemes
	 * @return type
	 *
	 * Depreciated
	 */
	public function get_random_color() {
		return $this->get_next_color();
	}

	/**
	 * Reset color to default
	 */
	public function reset_color() {
		$class_name = get_class($this);
		self::$color_coordinates[$class_name] = array( );
	}

	public function get_next_color() {

		$color_schemes = $this->swap_color_schemes();

		$class_name = get_class($this);
		if (empty(self::$color_coordinates[$class_name]))
			self::$color_coordinates[$class_name] = array( 'x' => 0, 'y' => 0 );

		$current_color = $color_schemes[self::$color_coordinates[$class_name]['x']][self::$color_coordinates[$class_name]['y']];

		// find out and set a next color
		$flag			   = false;
		$detected_new_color = false;
		foreach ($color_schemes as $x => $colors) {
			if ($detected_new_color)
				break;
			foreach ($colors as $y => $color) {
				if ($flag) {
					self::$color_coordinates[$class_name]['x'] = $x;
					self::$color_coordinates[$class_name]['y'] = $y;
					$detected_new_color = true;
					break;
				}
				if ($x == self::$color_coordinates[$class_name]['x'] && $y == self::$color_coordinates[$class_name]['y'])
					$flag			   = true;
			}
		}
		if (!$detected_new_color) {
			self::$color_coordinates[$class_name]['x'] = 0;
			self::$color_coordinates[$class_name]['y'] = 0;
		}
		return $current_color;
	}

	/**
	 * Get all colors from schemes
	 * @return type
	 */
	function get_all_colors() {
		$color_schemes = $this->swap_color_schemes(); // this one, to make sure, we don't put same color tone continously

		$tmp = array( );
		foreach ($color_schemes as $colors) {
			$tmp = array_merge($tmp, $colors);
		}
		return $tmp;
	}

	function renderErrorInstall() {
		$this->title = __("Your server's configuration doesn't allow us to complete MailPoet's Installation!", WYSIJA);
		$this->header();
		$this->footer();
	}

	/**
	 *
	 * @param type $type
	 * @param type $data
	 * @param bool $is_module is rendering a module view
	 */
	function render($type, $data, $is_module = false) {
		$this->action = $type;
		if (!$is_module) {
			$this->header($data);
		}
		if ($type !== NULL) {
			$this->$type($data);
		}
		if (!$is_module) {
			$this->footer();
		}
	}

	/**
	 * display all the messages that have queued
	 * @global type $wysija_msg
	 */
	function messages($noglobal = false) {
		$wysija_msg = $this->getMsgs();

		if (isset($wysija_msg['g-updated'])) {
			if (!$noglobal) {
				if (isset($wysija_msg['updated']))
					$wysija_msg['updated'] = array_merge((array)$wysija_msg['updated'], $wysija_msg['g-updated']);
				else
					$wysija_msg['updated'] = $wysija_msg['g-updated'];
			}
			unset($wysija_msg['g-updated']);
		}
		if (isset($wysija_msg['g-error'])) {
			if (!$noglobal) {
				if (isset($wysija_msg['error']))
					$wysija_msg['error'] = array_merge((array)$wysija_msg['error'], $wysija_msg['g-error']);
				else
					$wysija_msg['error'] = $wysija_msg['g-error'];
			}
			unset($wysija_msg['g-error']);
		}
		$wpnonce			 = '<input type="hidden" value="'.wp_create_nonce("wysija_ajax").'" id="wysijax" />';
		if (!$wysija_msg)
			return '<div class="wysija-msg ajax"></div>'.$wpnonce;
		$html				= '<div class="wysija-msg">';
		foreach ($wysija_msg as $level => $messages) {
			$msg_class = '';
			switch ($level) {
				case 'updated':
					$msg_class = 'notice-msg updated';
					break;
				case 'error':
					$msg_class = 'error-msg error';
					break;
				case 'xdetailed-updated':
					$msg_class = 'xdetailed-updated';
					break;
				case 'xdetailed-errors':
					$msg_class = 'xdetailed-errors';
					break;
			}

			$html.='<div class="'.$msg_class.'">';
			$html.='<ul>';

			if (count($messages) > 0) {
				foreach ($messages as $msg) {
					// check type of msg variable
					if (is_array($msg)) {
						$msg = var_export($msg, true);
					}

					// display message
					$html.='<li>'.$msg.'</li>';
				}
			}


			$html.='</ul>';
			$html.='</div>';
		}
		$html.='</div><div class="wysija-msg ajax"></div>'.$wpnonce;

		return $html;
	}

	/**
	 * this function let us generate a nonce which is an encrypted unique word based n the user info and some other stuff.
	 * by default it will create an hidden input nonce field
	 * @param type $params
	 * @param type $get
	 * @return type
	 */
	static function secure($params = array( ), $get = false, $echo = true) {
		$controller = '';
		if (!is_array($params))
			$action	 = $params;
		else {
			$action	  = $params['action'];
			if (isset($params['controller']))
				$controller  = $params['controller'];
			elseif (isset($_REQUEST['page']))
				$controller  = $_REQUEST['page'];
		}
		$nonceaction = $controller.'-action_'.$action;

		if (is_array($params) && isset($params['id']) && $params['id'])
			$nonceaction.='-id_'.$params['id'];

		if ($get) {
			return wp_create_nonce($nonceaction);
		}
		else {
			return wp_nonce_field($nonceaction, '_wpnonce', true, $echo);
		}
	}

	/**
	 * this allows us to get a field class to be validated by when making a form field
	 * @param type $params
	 * @param string $prefixclass
	 * @return string
	 */
	function getClassValidate($params, $returnAttr = false, $prefixclass = "") {
		$class_validate   = '';
		$recognised_types = array( 'email', 'url' );

		if (isset($params['req'])) {
			$class_validate = 'required';
			if (isset($params['type']) && in_array($params['type'], $recognised_types)) {
				$class_validate.=',custom['.$params['type'].']';
			}
		}
		else {
			if (isset($params['type']) && in_array($params['type'], $recognised_types)) {
				$class_validate.='custom['.$params['type'].']';
			}
		}

		if ($prefixclass)
			$prefixclass.=' ';
		if ($class_validate)
			$class_validate = 'validate['.$class_validate.']';
		if (!$returnAttr && $class_validate)
			$class_validate = ' class="'.$prefixclass.$class_validate.'" ';

		return $class_validate;
	}

	/**
	 * central function to return a translated formated date
	 * @param type $val
	 * @param type $format
	 * @return string
	 */
	function fieldListHTML_created_at($val, $format = '') {
		if (!$val)
			return '---';

		//offset the time to the time of the WP site not the server
		$helper_toolbox = WYSIJA::get('toolbox', 'helper');
		// get current time taking timezone into account.

		$val = $helper_toolbox->servertime_to_localtime($val);

		if ($format)
			return date_i18n($format, $val);
		else
			return date_i18n(get_option('date_format'), $val);
	}

	function fieldListHTML_created_at_time($val) {
		return $this->fieldListHTML_created_at($val, get_option('date_format').', '.get_option('time_format'));
	}

}