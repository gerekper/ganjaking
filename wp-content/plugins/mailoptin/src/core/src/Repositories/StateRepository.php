<?php

namespace MailOptin\Core\Repositories;


class StateRepository
{
    protected $option_name;

    public function __construct()
    {
        $this->option_name = 'mo_state_repository';
    }

    public function get($key)
    {
        $bucket = $this->getAll();

        return isset($bucket[$key]) ? $bucket[$key] : [];
    }

    public function set($key, $value)
    {
        if (empty($key) || empty($value)) return false;

        $data       = $this->getAll();
        $data[$key] = $value;

        return update_option($this->option_name, $data);
    }

    public function delete($key)
    {
        if (empty($key)) return false;

        $data = $this->getAll();

        unset($data[$key]);

        return update_option($this->option_name, $data);
    }

    public function getAll()
    {
        return get_option($this->option_name, []);
    }

    /**
     * @return self
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}