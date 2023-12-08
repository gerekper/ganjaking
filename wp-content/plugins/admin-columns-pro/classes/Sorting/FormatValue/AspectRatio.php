<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class AspectRatio implements FormatValue
{

    public function format_value($value)
    {
        $data = maybe_unserialize($value);

        return isset($data['width'], $data['height'])
            ? round((int)$data['width'] / (int)$data['height'], 2)
            : null;
    }

}
