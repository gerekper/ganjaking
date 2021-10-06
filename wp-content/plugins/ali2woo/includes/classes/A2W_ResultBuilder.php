<?php

/**
 * Description of A2W_ResultBuilder
 *
 * @author andrey
 */
if (!class_exists('A2W_ResultBuilder')) {

    class A2W_ResultBuilder {

        public static function build($state, $message = "", $object = false, $json=false) {
            $result = array('state' => $state);
            if ($message) {
                $result['message'] = $message;
            }
            if ($object) {
                if (is_array($object)) {
                    foreach ($object as $key => $value) {
                        if ($key != 'state' && $key != 'message') {
                            $result[$key] = $value;
                        }
                    }
                } else {
                    $result['object'] = $object;
                }
            }
            return $json?json_encode($result):$result;
        }

        public static function buildOk($object = false, $json=false) {
            return self::build("ok", false, $object, $json);
        }

        public static function buildError($message, $object = false, $json=false) {
            return self::build("error", $message, $object, $json);
        }

        public static function buildWarn($message, $object = false, $json=false) {
            return self::build("warn", $message, $object, $json);
        }

    }

}