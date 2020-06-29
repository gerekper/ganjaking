<?php
/**
 * PHPUnit bootstrap file
 *
 * @package MailOptin\Core
 */

$_tests_dir = getenv('WP_TESTS_DIR');

if (!$_tests_dir) {
    $_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin()
{
    $composer_vendor_in_main_plugin = dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

    if (file_exists($composer_vendor_in_main_plugin)) {
        require dirname(dirname(dirname(__FILE__))) . '/mailoptin.php';
    } else {
        require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
    }
}

tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

global $current_user;
$current_user = new WP_User(1);
$current_user->set_role('administrator');
wp_update_user(array('ID' => 1, 'first_name' => 'Admin', 'last_name' => 'User'));

if (!defined('MAILOPTIN_SYSTEM_FILE_PATH')) {
    define('MAILOPTIN_SYSTEM_FILE_PATH', __FILE__);
}

MailOptin\Core\Core::get_instance();
MailOptin\Core\RegisterActivation\Base::run_install();