<?php

declare(strict_types=1);

namespace ACP\Migrate\Preference;

use AC\Preferences\Site;
use AC\Type\ListScreenId;

class PreviewMode
{

    private $storage;

    public function __construct()
    {
        $this->storage = new Site('migrate_preview_mode');
    }

    public function set_active(ListScreenId $id): void
    {
        $this->storage->set('list_screen', (string)$id);
    }

    public function set_inactive(): void
    {
        $this->storage->delete('list_screen');
    }

    public function is_active(ListScreenId $id = null): bool
    {
        if ( ! $this->has_list_screen_id()) {
            return false;
        }

        return ! $id || $this->get_list_screen_id()->equals($id);
    }

    private function has_list_screen_id(): bool
    {
        return ListScreenId::is_valid_id($this->storage->get('list_screen'));
    }

    private function get_list_screen_id(): ListScreenId
    {
        return new ListScreenId($this->storage->get('list_screen'));
    }

}