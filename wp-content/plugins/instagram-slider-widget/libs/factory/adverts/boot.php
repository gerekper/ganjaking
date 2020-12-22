<?php

use WBCR\Factory_Adverts_117\Base;

/**
 * Factory Adverts
 *
 * @author        Alexander Vitkalov <nechin.va@gmail.com>
 * @author        Alexander Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 * @since         1.0.0
 *
 * @package       factory-ad-inserter
 * @copyright (c) 2019, Webcraftic Ltd
 *
 * @version       1.2.3
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

if( defined('FACTORY_ADVERTS_117_LOADED') || (defined('FACTORY_ADVERTS_BLOCK') && FACTORY_ADVERTS_BLOCK) ) {
	return;
}

# Устанавливаем константу, что модуль уже загружен
define('FACTORY_ADVERTS_117_LOADED', true);

# Устанавливаем версию модуля
define('FACTORY_ADVERTS_117_VERSION', '1.1.7');

# Регистрируем тектовый домен, для интернализации интерфейса модуля
load_plugin_textdomain('wbcr_factory_adverts_117', false, dirname(plugin_basename(__FILE__)) . '/langs');

# Устанавливаем директорию модуля
define('FACTORY_ADVERTS_117_DIR', dirname(__FILE__));

# Устанавливаем url модуля
define('FACTORY_ADVERTS_117_URL', plugins_url(null, __FILE__));

require_once(FACTORY_ADVERTS_117_DIR . '/includes/class-rest-request.php');
require_once(FACTORY_ADVERTS_117_DIR . '/includes/class-base.php');

/**
 * @param Wbcr_Factory439_Plugin $plugin
 */
add_action('wbcr_factory_adverts_117_plugin_created', function ($plugin) {
	$plugin->set_adverts_manager("WBCR\Factory_Adverts_117\Base");
});
