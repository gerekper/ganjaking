<?php

declare(strict_types=1);

namespace ACP\Exception;

use LogicException;

class InvalidImportFileException extends LogicException
{

    public static function from_invalid_extension(string $extension): self
    {
        return new self(
            sprintf(
                __('Uploaded file does not have a %s extension.', 'codepress-admin-columns'),
                $extension
            )
        );
    }

}