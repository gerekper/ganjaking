<?php

namespace ACP\Transient;

use AC\Expirable;
use AC\Storage;

class LicenseCheckTransient implements Expirable
{

    private const CACHE_KEY = 'acp_periodic_license_check';

    /**
     * @var Storage\Timestamp
     */
    protected $timestamp;

    public function __construct(bool $network_only)
    {
        $factory = $network_only
            ? new Storage\NetworkOptionFactory()
            : new Storage\OptionFactory();

        $this->timestamp = new Storage\Timestamp(
            $factory->create(self::CACHE_KEY)
        );
    }

    public function is_expired(int $value = null): bool
    {
        return $this->timestamp->is_expired($value);
    }

    public function delete()
    {
        $this->timestamp->delete();
    }

    /**
     * @param int $expiration Time until expiration in seconds.
     *
     * @return bool
     */
    public function save($expiration)
    {
        // Always store timestamp before option data.
        return $this->timestamp->save(time() + (int)$expiration);
    }

}