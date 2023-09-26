<?php

declare(strict_types=1);

namespace ACP\Exception;

use LogicException;

class ControllerException extends LogicException
{

    public static function from_invalid_action(string $action): self
    {
        return new self(sprintf('The action %s is not defined.', $action));
    }

}