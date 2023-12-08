<?php

namespace ACP\Column\Media;

use AC;
use ACP\ConditionalFormat;
use ACP\Sorting;
use ACP\Sorting\Sortable;

class AspectRatio extends AC\Column\Media\Meta implements Sortable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-aspect-ratio')
             ->set_group('media')
             ->set_label(__('Aspect Ratio', 'codepress-admin-columns'));
    }

    public function get_value($id)
    {
        $ratios = [
            '0.25' => '1:4',
            '0.33' => '1:3',
            '0.5'  => '1:2',
            '0.56' => '9:16',
            '0.6'  => '3:5',
            '0.67' => '2:3',
            '0.75' => '3:4',
            '0.80' => '4:5',
            '1'    => '1:1',
            '1.25' => '5:4',
            '1.33' => '4:3',
            '1.5'  => '3:2',
            '1.66' => '5:3',
            '1.6'  => '16:10',
            '1.78' => '16:9',
        ];

        $decimal = $this->get_aspect_ratio_decimal($id);

        if (array_key_exists((string)$decimal, $ratios)) {
            return $ratios[(string)$decimal];
        }

        return $decimal . ':1';
    }

    private function get_aspect_ratio_decimal($id)
    {
        $meta_data = $this->get_raw_value($id);
        $width = $meta_data['width'] ?? null;
        $height = $meta_data['height'] ?? null;

        if ( ! $width || ! $height) {
            return $this->get_empty_char();
        }

        return round($width / $height, 2);
    }

    public function sorting()
    {
        return new Sorting\Model\Media\AspectRatio();
    }

}