<?php

/*
Plugin Name: MailOptin - Lite
Plugin URI: https://mailoptin.io
Description: Best lead generation, email automation & newsletter plugin.
<<<<<<< HEAD
Version: 1.2.35.3
=======
Version: 1.2.35.0
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
Author: MailOptin Team
Contributors: collizo4sky
Author URI: https://mailoptin.io
Text Domain: mailoptin
Domain Path: /languages
License: GPL2
*/

require __DIR__ . '/vendor/autoload.php';

define('MAILOPTIN_SYSTEM_FILE_PATH', __FILE__);
<<<<<<< HEAD
define('MAILOPTIN_VERSION_NUMBER', '1.2.35.3');
=======
define('MAILOPTIN_VERSION_NUMBER', '1.2.35.0');
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c

add_action('init', 'mo_mailoptin_load_plugin_textdomain', 0);
function mo_mailoptin_load_plugin_textdomain()
{
    load_plugin_textdomain('mailoptin', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

MailOptin\Core\Core::init();
MailOptin\Connections\Init::init();