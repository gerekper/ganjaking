<?php

namespace ACP\Type;

use LogicException;

final class LicenseKey implements ActivationToken
{

    public const SOURCE_DATABASE = 'database';
    public const SOURCE_CODE = 'code';

    private $key;

    private $source;

    public function __construct(string $key, string $source = null)
    {
        if ( ! self::is_valid($key)) {
            throw new LogicException('Invalid license key.');
        }

        if (self::SOURCE_DATABASE !== $source) {
            $source = self::SOURCE_CODE;
        }

        $this->key = $key;
        $this->source = $source;
    }

    public function get_token(): string
    {
        return $this->key;
    }

    public function get_type(): string
    {
        return 'subscription_key';
    }

    public function get_source(): string
    {
        return $this->source;
    }

    public function equals(LicenseKey $key): bool
    {
        return $this->get_token() === $key->get_token();
    }

    public static function is_valid($key): bool
    {
        return $key && is_string($key) && strlen($key) > 12 && false !== strpos($key, '-');
    }

}