<?php

namespace ACP\Column\Post;

use AC;
use AC\View;
use ACP\ConditionalFormat;
use ACP\Export;
use ACP\Sorting;

class Images extends AC\Column
    implements Sorting\Sortable, AC\Column\AjaxValue, Export\Exportable, ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-images');
        $this->set_label(__('Images', 'codepress-admin-columns'));
    }

    public function sorting()
    {
        return new Sorting\Model\Post\ImageFileSizes();
    }

    public function export()
    {
        return new Export\Model\Post\ImageFileSizes($this);
    }

    private function get_image_sizes(array $urls): array
    {
        return array_filter(array_map([ac_helper()->image, 'get_local_image_size'], $urls));
    }

    public function get_value($id)
    {
        $id = (int)$id;

        $urls = $this->get_image_urls($id);
        $count = count($urls);

        if ($count < 1) {
            return $this->get_empty_char();
        }

        $sizes = $this->get_image_sizes($urls);

        if ( ! $sizes) {
            return $this->get_empty_char();
        }

        $total_size = ac_helper()->file->get_readable_filesize(array_sum($sizes));

        return ac_helper()->html->get_ajax_modal_link(
            sprintf(_n('%d image', '%d images', $count, 'codepress-admin-columns'), $count),
            [
                'title'     => strip_tags(get_the_title($id)) ?: $id,
                'edit_link' => get_edit_post_link($id),
                'id'        => $id,
                'class'     => '-w-large',
            ],
            ac_helper()->html->rounded($total_size)
        );
    }

    public function get_ajax_value($id)
    {
        $id = (int)$id;

        $view = new View([
            'title' => get_the_title($id),
            'items' => $this->get_image_items($id),
        ]);

        return $view->set_template('modal-value/images')
                    ->render();
    }

    private function get_image_items(int $id): array
    {
        $items = [];

        foreach ($this->get_image_urls($id) as $url) {
            $size = ac_helper()->image->get_local_image_size($url);

            if ( ! $size) {
                continue;
            }

            $dimensions = null;
            $extension = null;
            $edit_url = null;
            $filename = basename($url);
            $alt = $filename;
            $image_src = $url;

            $info = ac_helper()->image->get_local_image_info($url);

            if ($info) {
                $dimensions = $info[0] . ' x ' . $info[1];
                $extension = image_type_to_extension($info[2], false);
            }

            $attachment_id = ac_helper()->media->get_attachment_id_by_url($url, true);

            if ($attachment_id) {
                $edit_url = get_edit_post_link($attachment_id);
                $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
            }

            $items[] = [
                'img_src'    => $image_src,
                'alt'        => $alt,
                'filename'   => $filename,
                'filetype'   => $extension,
                'filesize'   => ac_helper()->file->get_readable_filesize($size),
                'dimensions' => $dimensions,
                'edit_url'   => $edit_url,
            ];
        }

        return $items;
    }

    public function get_raw_value($id)
    {
        return $this->get_image_sizes($this->get_image_urls((int)$id));
    }

    private function get_image_urls(int $id): array
    {
        $string = ac_helper()->post->get_raw_field('post_content', $id);

        /**
         * Parsed content for images.
         *
         * @param string $string
         * @param int    $id
         * @param int    $this
         *
         * @return string
         */
        $string = (string)apply_filters('ac/column/images/content', $string, $id, $this);

        return array_unique(ac_helper()->image->get_image_urls_from_string($string));
    }

}