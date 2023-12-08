<?php
namespace ElementPack\Modules\TimeZone;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Module extends Element_Pack_Module_Base
{

    public function get_name()
    {
        return 'time-zone';
    }

    public function get_widgets()
    {

        $widgets = ['Time_Zone'];

        return $widgets;
    }

}
