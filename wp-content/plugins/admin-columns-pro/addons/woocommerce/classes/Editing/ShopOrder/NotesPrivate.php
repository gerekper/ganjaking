<?php

namespace ACA\WC\Editing\ShopOrder;

use ACA\WC;
use ACP;
use ACP\Editing\View;
use WC_DateTime;

class NotesPrivate implements ACP\Editing\Service
{

    public function update(int $id, $data): void
    {
        $request_notes = (array)$data;

        $current_notes = [];
        foreach ($this->get_value($id) as $note) {
            $current_notes[$note['id']] = $note;
        }

        $request_note_ids = wp_list_pluck($request_notes, 'id');
        $current_note_ids = array_keys($current_notes);
        $deleted_note_ids = array_diff($current_note_ids, $request_note_ids);

        // Delete
        array_map('wc_delete_order_note', $deleted_note_ids);

        foreach ($request_notes as $note) {
            if (empty($note['content'])) {
                continue;
            }

            // Create
            if ($note['id'] < 0) {
                wc_create_order_note(
                    $id,
                    $note['content'],
                    false,
                    true
                );
                continue;
            }

            // Update
            if (in_array($note['id'], $current_note_ids)) {
                $current_comment = $current_notes[$note['id']];

                if ($current_comment['content'] === $note['content']) {
                    continue;
                }

                wp_update_comment([
                    'comment_ID'      => $note['id'],
                    'comment_content' => $note['content'],
                ]);
            }
        }
    }

    public function get_view(string $context): ?View
    {
        return self::CONTEXT_SINGLE === $context
            ? (new WC\Editing\View\Notes())->set_mode('private')
            : null;
    }

    private function get_date_formatted(WC_DateTime $date)
    {
        return sprintf(
            __('%1$s at %2$s', 'woocommerce'),
            $date->date_i18n(wc_date_format()),
            $date->date_i18n(wc_time_format())
        );
    }

    public function get_value(int $id)
    {
        $notes = wc_get_order_notes([
            'order_id' => $id,
            'type'     => 'internal',
        ]);

        // Ignore system notes
        $notes = array_filter($notes, function ($note) {
            return 'system' !== $note->added_by;
        });

        $value = [];

        foreach ($notes as $note) {
            $value[] = [
                'added_by' => $note->added_by,
                'content'  => $note->content,
                'id'       => $note->id,
                'date'     => $note->date_created instanceof WC_DateTime ? $this->get_date_formatted(
                    $note->date_created
                ) : null,
            ];
        }

        return $value;
    }

}
