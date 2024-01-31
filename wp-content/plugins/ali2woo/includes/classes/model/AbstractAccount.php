<?php

/* * class
 * Description of AbstractAccount
 *
 * @author Ali2Woo Team
 * 
 * @position: 1
 */

namespace Ali2Woo;

abstract class AbstractAccount {
    protected static $_instance = null;

    protected function __construct() { }

    abstract static public function getInstance();

    abstract public function getDeeplink($hrefs);

    protected function getNormalizedLink($href){
        preg_match('/([0-9]+)\.html/', $href, $match);
        $ext_id = $match[1];
        $href = str_replace("{$ext_id}/", "", $href);

        return $href;
    }
}
