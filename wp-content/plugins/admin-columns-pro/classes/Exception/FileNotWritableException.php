<?php

declare(strict_types=1);

namespace ACP\Exception;

use AC\ListScreen;
use AC\Type\ListScreenId;
use RuntimeException;

class FileNotWritableException extends RuntimeException
{

    public static function from_saving_list_screen(ListScreen $list_screen): self
    {
        return new self(sprintf('Failed to save ListScreen with id %s to file.', $list_screen->get_id()));
    }

    public static function from_removing_list_screen(ListScreen $list_screen): self
    {
        return new self(
            sprintf('Failed to delete the file containing ListScreen with id %s.', $list_screen->get_id())
        );
    }

    public static function from_saving_segment(ListScreenId $id): self
    {
        return new self(
            sprintf('Failed to save segment for ListScreen with id %s to file.', $id)
        );
    }

    public static function from_removing_segment(ListScreenId $id): self
    {
        return new self(
            sprintf('Failed to delete the file for ListScreen with id %s containing segments.', $id)
        );
    }

}