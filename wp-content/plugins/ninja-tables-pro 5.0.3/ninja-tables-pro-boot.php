<?php

! defined('WPINC') && die;

defined('NINJATABLESPRO') or define('NINJATABLESPRO', true);
define('NINJAPROPLUGIN_PATH', plugin_dir_path(__FILE__));
define('NINJAPROPLUGIN_URL', plugin_dir_url(__FILE__));
define('NINJATABLESPRO_SORTABLE', true);

spl_autoload_register(function ($class) {

    $match = 'NinjaTablesPro';
    if ( ! preg_match("/\b{$match}\b/", $class)) {
        return;
    }

    $path = plugin_dir_path(__FILE__);

    $file = str_replace(
        ['NinjaTablesPro', '\\', '/App/'],
        ['', DIRECTORY_SEPARATOR, 'app/'],
        $class
    );

    $filePath = (trailingslashit($path) . trim($file, '/') . '.php');

    if (file_exists($filePath)) {
        require $filePath;
    }

});


class NinjaTablesProDependency
{
    public function init()
    {
        $this->injectDependency();
    }

    /**
     * Notify the user about the NinjaTables dependency and instructs to install it.
     */
    protected function injectDependency()
    {
        add_action('admin_notices', function () {

            $pluginInfo = $this->getInstallationDetails();

            $class = 'notice notice-error';

            $install_url_text = 'Click Here to Install the Plugin';

            if ($pluginInfo->action == 'activate') {
                $install_url_text = 'Click Here to Activate the Plugin';
            }

            $message = 'Ninja Tables Pro  Requires Ninja Tables Base Plugin, <b><a href="' . $pluginInfo->url
                       . '">' . $install_url_text . '</a></b>';

            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
        });
    }

    /**
     * Get the NinjaTables plugin installation information e.g. the URL to install.
     *
     * @return \stdClass $activation
     */
    protected function getInstallationDetails()
    {
        $activation = (object)[
            'action' => 'install',
            'url'    => ''
        ];

        $allPlugins = get_plugins();

        if (isset($allPlugins['ninja-tables/ninja-tables.php'])) {
            $url = wp_nonce_url(
                self_admin_url('plugins.php?action=activate&plugin=ninja-tables/ninja-tables.php'),
                'activate-plugin_ninja-tables/ninja-tables.php'
            );

            $activation->action = 'activate';
        } else {
            $api = (object)[
                'slug' => 'ninja-tables'
            ];

            $url = wp_nonce_url(
                self_admin_url('update.php?action=install-plugin&plugin=' . $api->slug),
                'install-plugin_' . $api->slug
            );
        }

        $activation->url = $url;

        return $activation;
    }
}

add_action('init', function () {
    if ( ! defined('NINJA_TABLES_VERSION')) {
        (new NinjaTablesProDependency())->init();
    }
});
