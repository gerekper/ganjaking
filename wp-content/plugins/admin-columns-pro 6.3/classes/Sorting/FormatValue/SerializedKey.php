<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class SerializedKey implements FormatValue
{

    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function format_value($value)
    {
        $data = maybe_unserialize($value);

        return $data[$this->key] ?? null;
    }

}
