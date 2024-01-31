<?php

/**
 * Description of AliexpressToken
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;

class AliexpressToken
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function tokens()
    {
        return get_setting('aliexpress_access_tokens', array());
    }

    public function save($tokens)
    {
        set_setting('aliexpress_access_tokens', $tokens);
    }

    public function add($token)
    {
        $tokens = $this->tokens();
        foreach ($tokens as $t) {
            if ($token['user_id'] == $t['user_id']) {
                return;
            }
        }
        $token['default'] = empty($tokens);
        $tokens[] = $token;

        $this->save($tokens);
    }

    public function del($id)
    {
        $tokens = $this->tokens();
        foreach ($tokens as $k => $t) {
            if ($id == $t['user_id']) {
                unset($tokens[$k]);
            }
        }
        $this->save($tokens);
    }

    public function token($token_id)
    {
        $tokens = $this->tokens();
        foreach ($tokens as $k => $t) {
            if ($id == $t['user_id']) {
                return $t;
            }
        }
        return false;
    }

    public function defaultToken()
    {
        $tokens = $this->tokens();
        $any_available_token = false;
        foreach ($tokens as $k => $t) {
            $any_available_token = $t;
            if ($t['default']) {
                return $t;
            }
        }
        if ($any_available_token !== false) {
            return $any_available_token;
        }
        return false;
    }

}
