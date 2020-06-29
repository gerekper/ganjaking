<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_jsonp extends WYSIJA_object {

    function __construct(){
        parent::__construct();
    }

    /**
     * Is valid callback
     *
     * @param string $callback
     *
     * @return boolean
     */
    public function isValidCallback($callback) {
        $reserved = array(
            'break',
            'do',
            'instanceof',
            'typeof',
            'case',
            'else',
            'new',
            'var',
            'catch',
            'finally',
            'return',
            'void',
            'continue',
            'for',
            'switch',
            'while',
            'debugger',
            'function',
            'this',
            'with',
            'default',
            'if',
            'throw',
            'delete',
            'in',
            'try',
            'class',
            'enum',
            'extends',
            'super',
            'const',
            'export',
            'import',
            'implements',
            'let',
            'private',
            'public',
            'yield',
            'interface',
            'package',
            'protected',
            'static',
            'null',
            'true',
            'false'
        );

        foreach(explode('.', $callback) as $identifier) {
            if(!preg_match('/^[a-zA-Z_$][0-9a-zA-Z_$]*(?:\[(?:".+"|\'.+\'|\d+)\])*?$/', $identifier)) {
                return false;
            }
            if(in_array($identifier, $reserved)) {
                return false;
            }
        }

        return true;
    }
}