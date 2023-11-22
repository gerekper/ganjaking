<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
$term_id        = isset($data['term_id']) ? $data['term_id'] : '';
$totalslug      = isset($data['term_slug']) ? $data['term_slug'] : '';
$categoryName   = isset($data['term_name']) ? $data['term_name'] : '';
$totalCount     = isset($data['count']) ? $data['count'] : 0;
?>
<li class="template-category-item" data-demo="<?php echo esc_attr($totalslug) ?>">
    <a href="javascript:void(0)">
        <?php echo esc_attr($categoryName) ?>
        <span class="bdt-badge"><?php echo esc_attr($totalCount) ?></span>
    </a>
</li>