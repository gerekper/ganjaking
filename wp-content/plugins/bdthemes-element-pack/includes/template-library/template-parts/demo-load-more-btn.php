<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if($this->totalPage > 1 ): ?>

    <a href="javascript:void(0)" data-clicked="0" data-total="<?php echo esc_attr($this->totalPage) ?>" data-paged="<?php echo esc_attr($paged) ?>" class="load_more_btn bdt-button bdt-button-primary bdt-width-medium"> Loading More Items... </a>

<?php endif; ?>