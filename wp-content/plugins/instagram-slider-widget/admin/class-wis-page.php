<?php
/**
 * Class of plugin page. Must be registered in file admin/class-prefix-page.php
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 02.12.2018, Webcraftic
 * @see           Wbcr_FactoryPages423_AdminPage
 *
 * @version       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WIS_Page extends Wbcr_FactoryPages423_AdminPage {

	/**
	 * Name of the template to get content of. It will be based on plugins /admin/views/ dir.
	 * /admin/views/tab-{$template_name}.php
	 *
	 * @var string
	 */
	public $template_name = "main";

	/**
	 * Render and return content of the template.
	 * /admin/views/tab-{$template_name}.php
	 *
	 * @return mixed Content of the page
	 */
	public function render ($name = '') {
		if($name == '')	$name = $this->template_name;
		ob_start();
		if ( is_callable( $name ) ) {
			echo call_user_func( $name );
		} elseif ( strpos( $name, DIRECTORY_SEPARATOR ) !== false && ( is_file( $name ) || is_file( $name . '.php' ) ) ) {
			if ( is_file( $name ) ) {
				$path = $name;
			} else {
				$path = $name . '.php';
			}
		} else {
			$path = WIS_PLUGIN_DIR . "/admin/views/tab-{$name}.php";
		}
		if ( ! is_file( $path ) ) {
			return '';
		}
		include $path;
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Show rendered template - $template_name
	 */
	public function indexAction()
	{
		echo $this->render();
	}

}


