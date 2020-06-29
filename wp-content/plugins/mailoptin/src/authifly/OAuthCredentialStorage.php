<?php

namespace Authifly\Storage;

class OAuthCredentialStorage implements StorageInterface
{
    /**
     * stores the OAuth1 credentials for subsequent lookups.
     */
    protected $credentials = [];

    /**
     * Initiate a new storage
     *
     * @param null|array $credentials
     */
    public function __construct($credentials = null)
    {
        if (isset($credentials)) {
            $this->credentials = $credentials;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $key = strtolower($key);

        if (!empty($this->credentials[$key])) {
            return $this->credentials[$key];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $key = strtolower($key);

        $this->credentials[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->credentials = [];
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $key = strtolower($key);

        if (isset($this->credentials[$key])) {
            unset($this->credentials[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMatch($key)
    {
        $key = strtolower($key);

        if (count($this->credentials)) {
            foreach ($this->credentials as $k => $v) {
                if (strstr($k, $key)) {
                    unset($this->credentials[$k]);
                }
            }
        }
    }
}