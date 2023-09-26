<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;

class MetaDataText extends AttachmentMetaData
{

    public function __construct(string $key)
    {
        parent::__construct(new FormatValue\SerializedKey($key));
    }

}