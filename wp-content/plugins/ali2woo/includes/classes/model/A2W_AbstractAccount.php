<?php

/* * class
 * Description of A2W_AbstractAccount
 *
 * @author Andrey
 * 
 * @position: 1
 */

if (!class_exists('A2W_AbstractAccount')) {

    abstract class A2W_AbstractAccount {
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
}
