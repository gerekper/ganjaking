<?php

namespace ACP\QuickAdd\Controller;

use AC;
use AC\ListScreenRepository\Storage;
use AC\Registerable;
use AC\Type\ListScreenId;
use ACP\QuickAdd\Table;

class AjaxScreenOption implements Registerable
{

    private $storage;

    private $preference_button;

    public function __construct(Storage $storage, Table\Preference\ShowButton $preference_button)
    {
        $this->storage = $storage;
        $this->preference_button = $preference_button;
    }

    public function register(): void
    {
        $this->get_ajax_handler()->register();
    }

    protected function get_ajax_handler(): AC\Ajax\Handler
    {
        $handler = new AC\Ajax\Handler();
        $handler->set_action('acp_new_inline_show_button')
                ->set_callback([$this, 'update_table_option']);

        return $handler;
    }

    public function update_table_option(): void
    {
        $this->get_ajax_handler()->verify_request();

        $list_screen = $this->storage->find(new ListScreenId(filter_input(INPUT_POST, 'layout')));

        if ( ! $list_screen || ! $list_screen->is_user_allowed(wp_get_current_user())) {
            exit;
        }

        echo $this->preference_button->set($list_screen->get_key(), 'true' === filter_input(INPUT_POST, 'value'));
        exit;
    }

}