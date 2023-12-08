<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Settings\ShopOrder\NoteType;
use ACP;
use DateTime;

class Notes extends AC\Column implements ACP\Editing\Editable, AC\Column\AjaxValue
{

    use OrderTitle;

    public function __construct()
    {
        $this->set_type('column-order_note')
             ->set_label(__('Order Notes', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $id = (int)$id;

        if (Settings\ShopOrder\Notes::LATEST_VALUE === $this->get_display_property()) {
            return $this->get_latest_value($id);
        }

        $count = count($this->get_order_notes($id));

        if ($count < 1) {
            return $this->get_empty_char();
        }

        $order = wc_get_order($id);

        return ac_helper()->html->get_ajax_modal_link(
            sprintf(_n('%d note', '%d notes', $count, 'codepress-admin-columns'), $count),
            [
                'title'     => $this->get_order_title($order),
                'edit_link' => $order->get_edit_order_url(),
                'class'     => "-nopadding -w-large",
            ]
        );
    }

    public function get_ajax_value($id)
    {
        $items = [];

        foreach ($this->get_order_notes($id) as $note) {
            $type = $class = null;

            if ($this->is_customer_note($note)) {
                $class = '-customer-note';
                $type = __('Note To Customer', 'codepress-admin-columns');
            } elseif ($this->is_system_note($note)) {
                $class = '-system-note';
                $type = __('System', 'codepress-admin-columns');
            } elseif ($this->is_private_note($note)) {
                $class = '-private-note';
                $type = __('Private', 'codepress-admin-columns');
            }

            $items[] = [
                'date'  => $note->date_created->format('F j, Y - H:i'),
                'note'  => $note->content,
                'type'  => $type,
                'class' => $class,
            ];
        }

        $view = new AC\View([
            'items' => $items,
        ]);

        return $view->set_template('modal-value/order-notes')->render();
    }

    public function get_last_order_note($id)
    {
        $notes = $this->get_order_notes($id);

        return count($notes) > 0 ? reset($notes) : null;
    }

    private function get_latest_value(int $id): string
    {
        $note = $this->get_last_order_note($id);

        return $note
            ? sprintf(
                '<small>%s</small><br>%s',
                DateTime::createFromFormat('Y-m-d H:i:s', $note->date)->format('F j, Y - H:i'),
                $note->content
            )
            : $this->get_empty_char();
    }

    private function get_order_notes(int $order_id): array
    {
        $args = [
            'order_id' => $order_id,
        ];

        $notes = wc_get_order_notes($args);

        switch ($this->get_note_type()) {
            case Settings\ShopOrder\NoteType::CUSTOMER_NOTE :
                return array_filter($notes, [$this, 'is_customer_note']);
            case Settings\ShopOrder\NoteType::PRIVATE_NOTE :
                return array_filter($notes, [$this, 'is_private_note']);
            case Settings\ShopOrder\NoteType::SYSTEM_NOTE :
                return array_filter($notes, [$this, 'is_system_note']);
            default :
                return $notes;
        }
    }

    public function register_settings(): void
    {
        $this->add_setting(new Settings\ShopOrder\NoteType($this));
        $this->add_setting(new Settings\ShopOrder\Notes($this));
    }

    private function is_private_note($note): bool
    {
        return ! $this->is_customer_note($note) && ! $this->is_system_note($note);
    }

    private function is_system_note($note): bool
    {
        return 'system' === $note->added_by;
    }

    private function is_customer_note($note): bool
    {
        return (bool)$note->customer_note;
    }

    private function get_note_type(): string
    {
        return $this->get_setting(Settings\ShopOrder\NoteType::NAME)->get_value();
    }

    private function get_display_property(): string
    {
        return $this->get_setting(Settings\ShopOrder\Notes::NAME)->get_value();
    }

    public function editing()
    {
        switch ($this->get_note_type()) {
            case NoteType::PRIVATE_NOTE :
                return new Editing\ShopOrder\NotesPrivate();
            case NoteType::CUSTOMER_NOTE :
                return new Editing\ShopOrder\NotesToCustomer();
            case NoteType::SYSTEM_NOTE :
                return new Editing\ShopOrder\NotesSystem();
            default:
                return false;
        }
    }

}