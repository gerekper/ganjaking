<?php

namespace ACP\Editing\BulkDelete\RequestHandler;

use ACP\Editing\BulkDelete\RequestHandler;
use RuntimeException;
use WP_Post;
use WP_Post_Type;

class Post extends RequestHandler
{

    protected $post_type;

    public function __construct(WP_Post_Type $post_type)
    {
        $this->post_type = $post_type;
    }

    protected function delete($id, array $args = []): void
    {
        $id = (int)$id;

        $do_restore = 'true' === ($args['restore'] ?? null);

        if ($do_restore) {
            $this->restore_post($id);

            return;
        }

        $force_delete = 'true' === ($args['force_delete'] ?? null);

        $this->delete_post($this->post_type->name, $id, $force_delete);
    }

    private function restore_post($id): void
    {
        if ( ! $this->user_can_delete($id)) {
            throw new RuntimeException(__('You have no permission to restore this item.', 'codepress-admin-columns'));
        }

        $post = get_post($id);

        if ('trash' !== $post->post_status) {
            throw new RuntimeException(
                sprintf(__('%s is not in Trash.', 'codepress-admin-columns'), $this->post_type->labels->singular_name)
            );
        }

        if ( ! wp_untrash_post($id)) {
            throw new RuntimeException(__('Error in restoring the item from Trash.'));
        }
    }

    private function delete_post(string $post_type, int $id, bool $force_delete): void
    {
        if ( ! $this->user_can_delete($id)) {
            throw new RuntimeException(__('You have no permission to delete this item.', 'codepress-admin-columns'));
        }

        $this->validate_post_lock($id);

        switch ($post_type) {
            case 'post':
            case 'page':
            case 'attachment':
                $post = wp_delete_post($id, $force_delete);
                break;
            default:
                $post = $force_delete
                    ? wp_delete_post($id, true)
                    : wp_trash_post($id);
        }

        if ( ! $post instanceof WP_Post) {
            throw new RuntimeException(
                sprintf(
                    __('%s has already been deleted.', 'codepress-admin-columns'),
                    $this->post_type->labels->singular_name
                )
            );
        }
    }

    private function user_can_delete(int $post_id): bool
    {
        return current_user_can($this->post_type->cap->delete_posts) && current_user_can(
                $this->post_type->cap->delete_post,
                $post_id
            );
    }

    /**
     * @param int $id
     *
     * @return void
     */
    protected function validate_post_lock($id)
    {
        $user_locked_post = wp_check_post_lock($id);

        if ($user_locked_post) {
            $user = get_userdata($user_locked_post);

            throw new RuntimeException(
                sprintf(
                    __('%s is currently being edited by %s.', 'codepress-admin-columns'),
                    sprintf('"%s"', ac_helper()->string->trim_characters(get_the_title($id), 20, '...')),
                    sprintf('%s (%s)', $user->display_name ?: $user->user_login, $user->ID)
                )
            );
        }
    }

}