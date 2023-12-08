<?php

namespace ACP\Sorting\Controller;

use AC\Ajax;
use AC\ListScreenFactory;
use AC\ListScreenRepository\Storage;
use AC\Registerable;
use AC\Type\ListScreenId;
use ACP\Sorting\UserPreference;

class AjaxResetSorting implements Registerable
{

    private $storage;

    private $list_screen_factory;

    public function __construct(Storage $storage, ListScreenFactory $list_screen_factory)
    {
        $this->storage = $storage;
        $this->list_screen_factory = $list_screen_factory;
    }

    public function register(): void
    {
        $this->get_ajax_handler()->register();
    }

    private function get_ajax_handler(): Ajax\Handler
    {
        $handler = new Ajax\Handler();
        $handler
            ->set_action('acp_reset_sorting')
            ->set_callback([$this, 'handle_reset']);

        return $handler;
    }

    public function handle_reset()
    {
        $this->get_ajax_handler()->verify_request();

        $list_screen = null;
        $list_id = filter_input(INPUT_POST, 'layout');
        $list_key = filter_input(INPUT_POST, 'list_screen');

        if (ListScreenId::is_valid_id($list_id)) {
            $list_screen = $this->storage->find(new ListScreenId($list_id));
        } elseif ($list_key && $this->list_screen_factory->can_create($list_key)) {
            $list_screen = $this->list_screen_factory->create($list_key);
        }

        if ( ! $list_screen) {
            wp_send_json_error();
        }

        $preference = new UserPreference\SortType($list_screen->get_storage_key());

        wp_send_json_success($preference->delete());
    }

}