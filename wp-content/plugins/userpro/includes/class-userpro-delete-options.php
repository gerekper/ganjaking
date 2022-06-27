<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class UP_DeleteOptions
 *
 * @todo : Not useful class. Remove it on next release.
 */
class UP_DeleteOptions extends UP_Data
{
    /**
     * UserPro options names
     * @var array
     */
    public $options_names = array();

    /**
     * Set object options
     *
     * @since 4.9.31
     * @param $option
     */
    public function setOption($option)
    {
        $this->options_names[] = $option;
    }

    /**
     * Get object options
     *
     * @since 4.9.31
     * @return array
     */
    public function getOptions()
    {
        return $this->options_names;
    }

}
