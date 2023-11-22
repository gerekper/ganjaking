<?php

namespace DynamicOOOS\Sunra\PhpSimple;

require 'simplehtmldom_1_5' . \DIRECTORY_SEPARATOR . 'simple_html_dom.php';
class HtmlDomParser
{
    /**
     * @return \simplehtmldom_1_5\simple_html_dom
     */
    public static function file_get_html()
    {
        return \call_user_func_array('DynamicOOOS\\simplehtmldom_1_5\\file_get_html', \func_get_args());
    }
    /**
     * get html dom from string
     * @return \simplehtmldom_1_5\simple_html_dom
     */
    public static function str_get_html()
    {
        return \call_user_func_array('DynamicOOOS\\simplehtmldom_1_5\\str_get_html', \func_get_args());
    }
}
