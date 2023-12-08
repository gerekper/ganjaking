<?php

namespace ACP\Check;

use AC\Message\Notice;
use AC\Registerable;
use AC\Screen;
use ACP\Admin\Feedback;

class Beta
    implements Registerable
{

    private $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function register(): void
    {
        add_action('ac/screen', [$this, 'register_notice']);
    }

    public function render(): void
    {
        echo $this->feedback->render();
    }

    public function scripts(): void
    {
        foreach ($this->feedback->get_assets()->all() as $asset) {
            $asset->enqueue();
        }
    }

    public function register_notice(Screen $screen): void
    {
        if ( ! $screen->is_list_screen() && ! $screen->is_admin_screen()) {
            return;
        }

        $notice = new Notice($this->get_message());
        $notice->set_type(Notice::WARNING)
               ->register();

        add_action('admin_footer', [$this, 'render']);
        add_action('admin_enqueue_scripts', [$this, 'scripts']);
    }

    protected function get_message(): string
    {
        return implode(' ', [
            sprintf(__('You are using a beta version of %s.', 'codepress-admin-columns'), 'Admin Columns Pro'),
            sprintf(
                __('If you have feedback or have found a bug, please %s.', 'codepress-admin-columns'),
                sprintf(
                    '<a href="#" data-ac-modal="feedback">%s</a>',
                    __('leave us a message', 'codepress-admin-columns')
                )
            ),
        ]);
    }

}