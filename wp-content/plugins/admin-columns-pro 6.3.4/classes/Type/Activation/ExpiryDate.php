<?php

declare(strict_types=1);

namespace ACP\Type\Activation;

use DateTime;

class ExpiryDate
{

    /**
     * @var DateTime `null` is lifetime
     */
    private $expiry_date;

    /**
     * @var DateTime
     */
    private $current_date;

    public function __construct(DateTime $expiry_date = null)
    {
        $this->expiry_date = $expiry_date;
        $this->current_date = new DateTime();
    }

    public function exists(): bool
    {
        return null !== $this->expiry_date;
    }

    public function get_value(): DateTime
    {
        return $this->expiry_date;
    }

    public function is_expired(): bool
    {
        if ($this->is_lifetime()) {
            return false;
        }

        return $this->expiry_date && $this->expiry_date < $this->current_date;
    }

    public function is_lifetime(): bool
    {
        if (null === $this->expiry_date) {
            return false;
        }

        $lifetime_end_date = DateTime::createFromFormat('Y-m-d', '2037-12-30');

        if ( ! $lifetime_end_date) {
            return false;
        }

        return $this->expiry_date > $lifetime_end_date;
    }

    public function get_expired_seconds(): int
    {
        return $this->current_date->getTimestamp() - $this->expiry_date->getTimestamp();
    }

    public function get_remaining_seconds(): int
    {
        return $this->expiry_date->getTimestamp() - $this->current_date->getTimestamp();
    }

    public function get_remaining_days(): float
    {
        return (float)($this->get_remaining_seconds() / DAY_IN_SECONDS);
    }

    public function is_expiring_within_seconds(int $seconds): bool
    {
        return $this->get_remaining_seconds() < $seconds;
    }

    public function get_human_time_diff(): string
    {
        return human_time_diff($this->expiry_date->getTimestamp(), $this->current_date->getTimestamp());
    }

}