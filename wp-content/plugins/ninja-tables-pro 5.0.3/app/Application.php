<?php

namespace NinjaTablesPro\App;

class Application
{
    public function __construct($app)
    {
        $this->boot($app);
        $this->loadPluginTextDomain();
    }

    public function boot($app)
    {
        $router = $app->router;
        require_once NINJAPROPLUGIN_PATH . 'app/Hooks/actions.php';
        require_once NINJAPROPLUGIN_PATH . 'app/Hooks/filters.php';
        require_once NINJAPROPLUGIN_PATH . 'app/Http/Routes/api.php';
        require_once NINJAPROPLUGIN_PATH . 'boot/ninja-tables-pro-global-functions.php';
    }

    protected function loadPluginTextDomain()
    {
        load_plugin_textdomain(
            'ninja-tables-pro',
            false,
            dirname(plugin_basename(NINJAPRO_PLUGIN_FILE)) . '/language/'
        );
    }
}
