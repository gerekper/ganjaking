<?php

declare(strict_types=1);

namespace ACP\Storage;

use FilterIterator;
use Iterator;
use SplFileInfo;

final class FileIterator extends FilterIterator
{

    private $extension;

    public function __construct(Iterator $iterator, string $extension)
    {
        parent::__construct($iterator);

        $this->extension = $extension;
    }

    public function accept(): bool
    {
        $file = $this->getInnerIterator()->current();

        return $file instanceof SplFileInfo &&
               $file->isFile() &&
               $file->isReadable() &&
               $file->getSize() &&
               $file->getExtension() === $this->extension;
    }

    public function current(): SplFileInfo
    {
        return parent::current();
    }

}