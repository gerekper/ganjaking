<?php

namespace MailOptin\Core\PluginSettings;

/**
 * @method string remove_plugin_data()
 * @method string mailoptin_affiliate_url()
 * @method string switch_customizer_loader()
 * @method string company_name()
 * @method string company_address()
 * @method string company_address_2()
 * @method string company_city()
 * @method string company_state()
 * @method string company_zip()
 * @method string company_country()
 * @method string disable_impression_tracking()
 * @method string recaptcha_score()
 * @method string recaptcha_type()
 * @method string recaptcha_site_key()
 * @method string recaptcha_site_secret()
 */
class Settings
{
    protected $settings_data;

    public function __construct()
    {
        $this->settings_data = get_option(MAILOPTIN_SETTINGS_DB_OPTION_NAME, []);
    }

    public function from_name()
    {
        return str_replace('&#039;', "'", $this->settings_data['from_name']);
    }

    public function from_email()
    {
        return $this->settings_data['from_email'];
    }

    public function reply_to()
    {
        if ( ! empty($this->settings_data['reply_to'])) {
            return $reply_to = $this->settings_data['reply_to'];
        } else {
            return $reply_to = $this->settings_data['from_email'];
        }
    }

    /**
     * Handles retrieval of a plugin settings probably added by an extension not defined above.
     *
     * @param string $name
     * @param mixed $arguments
     *
     * @return string
     */
    public function __call($name, $arguments)
    {
        $default = isset($arguments[0]) ? $arguments[0] : '';

        return isset($this->settings_data[$name]) ? $this->settings_data[$name] : $default;
    }

    /**
     * Update value of a setting.
     *
     * @param $key
     * @param $value
     */
    public function update($key, $value)
    {
        $data       = $this->settings_data;
        $data[$key] = $value;

        update_option(MAILOPTIN_SETTINGS_DB_OPTION_NAME, $data);
    }

    /**
     * delete a setting.
     *
     * @param $key
     */
    public function delete($key)
    {
        $data = $this->settings_data;
        unset($data[$key]);

        update_option(MAILOPTIN_SETTINGS_DB_OPTION_NAME, $data);
    }

    /**
     * @return Settings|null
     */
    public static function instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }

}