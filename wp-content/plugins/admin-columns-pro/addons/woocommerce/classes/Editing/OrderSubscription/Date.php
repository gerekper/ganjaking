<?php

namespace ACA\WC\Editing\OrderSubscription;

use ACP;
use ACP\Editing\View;
use Exception;
use RuntimeException;

class Date implements ACP\Editing\Service
{

    private $date_key;

    private $clearable;

    public function __construct(string $date_key, bool $clearable = false)
    {
        $this->date_key = $date_key;
        $this->clearable = $clearable;
    }

    public function get_view(string $context): ?View
    {
        return (new ACP\Editing\View\DateTime())->set_clear_button($this->clearable);
    }

    public function get_value($id)
    {
        return wcs_get_subscription($id)->get_date($this->date_key);
    }

    public function update(int $id, $data): void
    {
        $subscription = wcs_get_subscription($id);

        try {
            $subscription->update_dates([
                $this->date_key => $data,
            ], get_option('timezone_string'));

            $subscription->save();
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }
}
