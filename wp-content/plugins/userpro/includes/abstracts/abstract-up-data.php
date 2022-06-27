<?php

if (!defined('ABSPATH')) {
    exit;
}

abstract class UP_Data
{
    /**
     * Core object data
     * @since 4.9.31
     *
     * @var array
     */
    protected $data = array();

    /**
     * Object options
     *
     * @var array
     */
    protected $option = array();

    /**
     * Core object option name
     *
     * @var string
     */
    protected $option_name = '';
    /**
     * UserPro options names
     *
     * @var array
     */
    protected $options_names = array();

    /**
     * Change object option
     *
     * @var null
     */
    protected $changed_option = null;

    /**
     * Object user id
     *
     * @since 4.9.31
     */
    protected $user_id = 0;

    protected $meta_data = null;

    /**
     * Get object options
     * @since 4.9.31
     * @var string $key
     * @var string $prefix
     * @return mixed
     */
    public function getOption($key = null, $prefix = null)
    {
        // if key empty take core object option name
        if (empty($key)) {
            $key = $this->option_name;
        }

        if ($prefix === UP_PREFIX) {

            $up_options = get_option(UP_PREFIX);
            $this->option = $up_options[$key];
        } else {

            $this->option = get_option($key);
        }

        return $this->option;
    }

    /**
     * Get object meta data
     *
     * @param $key
     * @param $id
     * @param $type
     * @since 4.9.31
     * @return mixed
     */
    public function getMetaData($key = null, $id = null, $type = '')
    {

        if (is_numeric($id) && $id > 0) {
            $this->meta_data = get_user_meta($id);
        }

        if (is_numeric($id) && $id > 0 && !empty($key)) {
            $this->meta_data = get_user_meta($id, $key, true);
        }

        // if object already have id
        if (empty($id) && is_numeric($this->user_id)) {
            $this->meta_data = get_user_meta($this->user_id);
        }
        // get post meta data
        if (isset($key) && isset($id) && $type === 'post') {
            $this->meta_data = get_post_data($id, $key);
        }

        return $this->meta_data;
    }

    /**
     * Get User Meta data by meta key.
     *
     * @param $key
     * @param $single boolean
     * @since 4.9.31
     * @return mixed
     */
    public function getUserMeta($key, $single = false)
    {
        $user_meta_data = get_user_meta($this->user_id, $key, $single);

        return $user_meta_data;
    }

    /**
     * Update WordPress option value.
     *
     * @since 4.9.31
     * @param string $key
     */
    public function updateOption($key = null)
    {
        if (empty($key)) {
            $key = $this->option_name;
        }
        update_option($key, $this->changed_option);
    }

    /**
     * Method for delete option.
     * @since 4.9.31
     */
    public function deleteOption()
    {
        delete_option($this->option_name);
    }

    /**
     * Delete UserPro options
     *
     * @since 4.9.31
     */
    public function deleteOptions()
    {
        foreach ($this->options_names as $option) {

            delete_option($option);
        }
    }

    /**
     * Set User Id for object.
     * @since 4.9.31
     * @param $id
     */
    public function setUserId($id)
    {
        $this->user_id = absint($id);
    }

    /**
     * Get User Id.
     * @since 4.9.31
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}