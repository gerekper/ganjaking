<?php

namespace GT3\PhotoVideoGallery;

defined('ABSPATH') OR exit;

class Autoload {
	/**
	 * @param string $className
	 */
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

	/**
	 * @param string|array $fileNameSearch
	 * @param array        $filePathArray
	 *
	 * @return string
	 */
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
}

try {
	spl_autoload_register(array( Autoload::class, 'autoload' ));
} catch(\Exception $e) {
}
