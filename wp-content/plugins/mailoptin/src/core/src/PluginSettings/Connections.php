<?php

namespace MailOptin\Core\PluginSettings;


class Connections
{
    protected $cpp_settings;

    public function __construct($cpp_settings)
    {
        $this->cpp_settings = $cpp_settings;
    }

    /**
     * Handles retrieval of a connection settings probably added by an extension not defined above.
     *
     * @param string $name
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return isset($this->cpp_settings[$name]) ? $this->cpp_settings[$name] : '';
    }

    /**
     * @return Connections
     */
    public static function instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self(get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME));
        }

        return $instance;
    }
}