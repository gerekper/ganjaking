<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;
use ACP\Sorting\Type\DataType;

class AspectRatio extends AttachmentMetaData
{

    public function __construct()
    {
        parent::__construct(new FormatValue\AspectRatio(), new DataType(DataType::NUMERIC));
    }

}