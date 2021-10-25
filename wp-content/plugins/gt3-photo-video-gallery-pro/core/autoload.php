<?php

namespace GT3\PhotoVideoGalleryPro;
defined('ABSPATH') OR exit;

class Autoload {
	private static $instance = null;
	private static $inited = false;

	public static function instance(){
		if(is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct(){
		try {
			spl_autoload_register(array( __CLASS__, 'autoload' ));
		} catch(\Exception $e) {
		}
	}

	public static function autoload($className){
		if(false === strpos($className, __NAMESPACE__.'\\')) {
			return;
		}

		$filePathArray  = explode('\\', strtolower($className));
		$filePathArray = array_map(array(__CLASS__,'replace'),$filePathArray);
		$fileNameSearch = $fileName = '';
		if(isset($filePathArray[count($filePathArray)-1])) {
			$file          = strtolower($filePathArray[count($filePathArray)-1]);
			$fileName      = str_replace(array( '_', '--' ), array( '-', '-' ), $file);

			$fileNameParts = explode('-', $fileName);
			if(false !== strpos($fileName, 'trait')) {
				$index = array_search('trait', $fileNameParts);
				unset($fileNameParts[$index]);
				$file           = implode('-', $fileNameParts);
				$fileNameSearch = array( "trait-{$file}.php" );
			} else if(false !== strpos($fileName, 'interface')) {
				$index = array_search('interface', $fileNameParts);
				unset($fileNameParts[$index]);
				$file           = implode('-', $fileNameParts);
				$fileNameSearch = array( "interface-{$file}.php" );
			} else {
				$fileNameSearch = array(
					'index.php',
					"class-{$file}.php"
				);
			}
		}
		$file = self::locate_template($fileNameSearch, $filePathArray);

		if(!empty($file) && stream_resolve_include_path($file)) {
			require_once $file;
		}
	}
	public static function replace($path) {
		return str_replace(array( '_', '--' ), array( '-', '-' ), $path);
	}
	private static function locate_template($fileNameSearch, $filePathArray){
		if(!is_array($fileNameSearch)) {
			$fileNameSearch = array( $fileNameSearch );
		}
		$is_return = '';
		foreach($fileNameSearch as $fileName) {
			if('index.php' === $fileName) {
				$theme_path  = implode('/', $filePathArray).'/';
				$plugin_path = implode('/', array_slice($filePathArray, 2)).'/';
			} else {
				$theme_path  = implode('/', array_slice($filePathArray, 0, count($filePathArray)-1)).'/';
				$plugin_path = implode('/', array_slice($filePathArray, 2, count($filePathArray)-3)).'/';
			}

			$themeFileName  = get_stylesheet_directory().'/'.$theme_path.$fileName;
			$parentFileName = get_template_directory().'/'.$theme_path.$fileName;
			$pluginFileName = __DIR__.'/'.$plugin_path.$fileName;
			$is_return      =
				stream_resolve_include_path($themeFileName) ? $themeFileName :
					(stream_resolve_include_path($parentFileName) ? $parentFileName :
						(stream_resolve_include_path($pluginFileName) ? $pluginFileName :
							''));
			if(!empty($is_return)) {
				break;
			}
		}

		return $is_return;
	}



	public static function isInited() {
		return self::$inited;
	}

	public function Init(){
		self::$inited = true;

		Assets::instance();
		Settings::instance();
		Admin_Menu::instance();
		Elementor\Core::instance();
		VC_modules\Init::instance();
		Usage::instance();

		require_once __DIR__.'/cpt/gallery/init.php';
		require_once __DIR__.'/block/loader.php';

		require_once __DIR__.'/defaults.php';

		add_action(
			'divi_extensions_init',
			array(Divi::class, 'loader')
		);
	}
}

Autoload::instance();
Rest_Api::instance();

