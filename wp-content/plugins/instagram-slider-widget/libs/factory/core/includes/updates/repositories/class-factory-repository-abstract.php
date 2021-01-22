<?php

namespace WBCR\Factory_442\Updates;

// Exit if accessed directly
use Wbcr_Factory442_Plugin;

if( !defined('ABSPATH') ) {
	exit;
}

/**
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 * @copyright (c) 2018 Webraftic Ltd
 * @version       1.0
 */
abstract class Repository {

	/**
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * @var Wbcr_Factory442_Plugin
	 */
	protected $plugin;

	/**
	 * Repository constructor.
	 *
	 * @param Wbcr_Factory442_Plugin $plugin
	 * @param array $settings
	 */
	abstract public function __construct(Wbcr_Factory442_Plugin $plugin, array $settings = []);

	/**
	 * @return void
	 */
	abstract public function init();

	/**
	 * @return bool
	 */
	abstract public function need_check_updates();

	/**
	 * @return mixed
	 */
	abstract public function is_support_premium();

	/**
	 * @return string
	 */
	abstract public function get_download_url();

	/**
	 * @return string
	 */
	abstract public function get_last_version();
}