<?php

declare(strict_types=1);

namespace ACP\Storage\Decoder;

use AC\ListScreen;
use AC\ListScreenFactory;
use AC\Plugin\Version;
use ACP\Exception\NonDecodableDataException;
use DateTime;

final class Version510 extends BaseDecoder implements ListScreenDecoder
{

    private $list_screen_factory;

    public function __construct(array $encoded_data, ListScreenFactory $list_screen_factory)
    {
        parent::__construct($encoded_data);

        $this->list_screen_factory = $list_screen_factory;
    }

    public function get_version(): Version
    {
        return new Version('5.1.0');
    }

    public function has_list_screen(): bool
    {
        if ( ! isset($this->encoded_data['type'])) {
            return false;
        }

        if ( ! $this->list_screen_factory->can_create((string)$this->encoded_data['type'])) {
            return false;
        }

        return true;
    }

    public function get_list_screen(): ListScreen
    {
        if ( ! $this->has_required_version() || ! $this->has_list_screen() ) {
            throw new NonDecodableDataException($this->encoded_data);
        }

        $settings = [
            'list_id'     => $this->encoded_data['id'],
            'columns'     => $this->encoded_data['columns'] ?? [],
            'preferences' => $this->encoded_data['settings'] ?? [],
            'title'       => $this->encoded_data['title'] ?? '',
            'date'        => DateTime::createFromFormat('U', (string)$this->encoded_data['updated']),
        ];

        return $this->list_screen_factory->create($this->encoded_data['type'], $settings);
    }

}