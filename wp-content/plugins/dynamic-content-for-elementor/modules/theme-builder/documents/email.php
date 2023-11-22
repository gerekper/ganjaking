<?php

namespace Elementor\Modules\Library\Documents;

use Elementor\Modules\Library\Documents;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
/**
 *
 * Email library template
 *
 */
class Email extends \Elementor\Modules\Library\Documents\Library_Document
{
    public static function get_properties()
    {
        $properties = parent::get_properties();
        $properties['support_kit'] = \true;
        return $properties;
    }
    public static function get_name_static()
    {
        return 'dce_email';
    }
    public function get_name()
    {
        return self::get_name_static();
    }
    public static function get_title()
    {
        return __('Email', 'dynamic-content-for-elementor');
    }
}
