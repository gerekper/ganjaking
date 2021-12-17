<?php
namespace Perfmatters;

class Config
{
	public static $options;

	//initialize config
	public static function init()
	{
		//load plugin options
		self::$options = get_option('perfmatters_options');
	}
}